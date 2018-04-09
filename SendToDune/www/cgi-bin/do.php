<?
error_reporting (E_ALL);
require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";
$link_api = '/tmp/plugins/link_api.php';
$func = 'close();';
if (!file_exists($link_api))
	link_api();
require $link_api;
$ad_pls = (HD::get_item('ad_pls') !='') ? HD::get_item('ad_pls') : 1;
$shuffle = (HD::get_item('shuffle') !='') ? HD::get_item('shuffle') : 1;
$yt_indx = (HD::get_item('yt_indx') !='') ? HD::get_item('yt_indx') : 1;
preg_match_all('/inet'.(true ? '6?' : '').' addr: ?([^ ]+)/', `ifconfig`, $ips);
$ip_address = array_shift($ips[1]);
$r_path = "http://$ip_address/plugins/".DuneSystem::$properties['plugin_name']."/";
if (isset($_GET["quality"])){
	$video_quality = (HD::get_item('video_quality') !='') ? HD::get_item('video_quality') : 1;
	if ($_GET["quality"] == "hi")
		$quality = 4;
	if ($_GET["quality"] == "mid")
		$quality = 3;
	if ($_GET["quality"] == "lo")
		$quality = 2;
	if ($video_quality != $quality)
		HD::save_item('video_quality', $quality);
}
if (isset($_GET["stop"])){
	if ($_GET["stop"] == 1)
		$qb= 0;
	else
		$qb = 1;
	if ($ad_pls != $qb)
		HD::save_item('ad_pls', $qb);
}
if (isset($_GET["media_url"]))
	$media_url[] = $_GET["media_url"];
if (isset($_GET["url"]))
	$media_url[] = $_GET["url"];
$media_url[0] = str_replace("http://$ip_address", 'http://127.0.0.1', $media_url[0]);
$plugin_vod_series_ndx = false;
$values = SmartConfig::parse_video($media_url);
if (($yt_indx == 0)&&($shuffle == 1)&&(preg_match('|index=(\d*)|', $media_url[0], $match)))
	$plugin_vod_series_ndx = $match [1];
if ($ad_pls == 0)
	$movie_ids = HD::get_items('movie_id');
$i = 0;
if ($values == true){
	foreach ($values as $k => $v){
		$i++;
		if (preg_match('|http:\/\/127\.0\.0\.1:9000|', $k))
			$func = "document.location.href = 'http://$ip_address:9000';";
		$movie_ids[] = array(
			'id' => $k,
			'video_image' => $v['video_image'],
			'video_title' => $v['video_title']
		);
		if (($plugin_vod_series_ndx == true)&&($plugin_vod_series_ndx == $i))
			HD::save_item_tmp('ytb_series_ndx', count($movie_ids)-1);
	}
	HD::save_item_tmp('chpl', 1);
	HD::save_items('movie_id', $movie_ids);
	if (($ad_pls == 1)||(HD::get_item_tmp('state')=='0')||(HD::get_item_tmp('state')==false)){
		file_get_contents("http://127.0.0.1/cgi-bin/do?cmd=launch_media_url&media_url=plugin_launcher://{name=SendToDune}");
		HD::save_item_tmp('state', 1);
	}
}else
	$media_url[0] = '<font color="red" >Err!!!</font> Link is not supported!!!';
function link_api()
{
	$upd = false;
	$link = '/tmp/plugins/link_api.php';
	$vers1 = file_get_contents('http://dune-club.info/plugins/update/link_api.txt');
	if ($vers1 == true){
		preg_match('|##(.*?)##|', $vers1, $matches1);
		if (file_exists($link)){
			$vers2 = file_get_contents($link);
			preg_match('|##(.*?)##|', $vers2, $matches2);
			if ($matches1[1]>$matches2[1])
				$upd = true;
		}
		if ((($matches1[1] == true)&&($upd == true)) || (!file_exists($link))){
			$data = fopen($link,"w");
				if (!$data)
				hd_print ("$link save false");
				fwrite($data, $vers1);
				@fclose($data);
		}
	}	
	return $link;
}

?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?echo DuneSystem::$properties['plugin_name'];?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<meta property="fb:app_id" data-channel-url="/" id="fb-meta">
<link rel="shortcut icon" href="<?echo $r_path.'favicon.png';?>">
</head>
<body>
<script>function func() {
	<?echo $func;?>
}
setInterval(func, 1000);
</script>
<h1><?echo $media_url[0];?></h1>
</body>
</html>