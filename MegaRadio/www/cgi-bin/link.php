<?
error_reporting (E_ALL);
require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";
$link_api = link_api();
if (file_exists($link_api))
require $link_api;
$video_quality = (HD::get_item('video_quality') !='') ? HD::get_item('video_quality') : 1;
$playback_url =  base64_decode($_GET["n1"]);

if (preg_match('|youtu|', $playback_url))
	$url = mUrl::get_link($playback_url, $video_quality);
else
	$url = SmartConfig::get_radio_stream($playback_url);
file_put_contents('/tmp/www/mr.m3u', $url);
	file_get_contents("http://127.0.0.1/cgi-bin/do?cmd=start_playlist_playback&media_url=http://127.0.0.1/mr.m3u");
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