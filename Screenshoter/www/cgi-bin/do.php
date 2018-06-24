<?
require_once "do_config.php";
header('Content-Type: application/json');
if (isset($_GET['action']))
	$result = get_action($_GET['action']);
else if (isset($_POST['action'])){
	if (($_POST['action'] == 'del_el')&&(isset($_POST['href']))){
		$tmp = explode(DuneSystem::$properties['plugin_name'], $_POST['href']);
		unlink (DuneSystem::$properties['install_dir_path'] . '/www' . $tmp[1]);
		$result = array('status' => 'ok');
	}
}else
	$result = array('status' => 'error', 'error' => 'action false!');
 echo json_encode($result);

function get_action($action){
	if (file_exists("/D"))
		$path = "/D";
	else if (file_exists("/sdcard"))
		$path = "/sdcard";
	else
		return array('status' => 'error', 'error' => 'No directories found!');
	if ($path == true){
		$d = get_storage_size($path, 15000);
		if($d['free_space'] != 1)
			return array('status' => 'error', 'error' => $d['str'] . ' free space false!');
		if (!file_exists("$path/screenshots"))
			exec ("mkdir $path/screenshots");
		$path = "$path/screenshots";
	}
	if ($action == 'screenshot'){
		$screenPath = $path . "/". get_time() .".png";
		exec ("screencap -p $screenPath");
		return array('status' => 'ok', 'screenPath' => $screenPath);
	}else if ($action == 'rec'){
		$items['screenPath'] = $screenPath = $path . "/". get_time() .".mp4";
		$items['time_start'] = time();
		HD::save_items_tmp('rec_data', $items);
		exec (DuneSystem::$properties['install_dir_path'] . "/bin/rec.sh $screenPath > /dev/null &");// exec ('nohup screenrecord --size 1280x720 '.$screenPath.' &');
		return array('status' => 'ok');
	}else if ($action == 'stop_rec'){
		$pid = exec ('pidof screenrecord');
		$items = HD::get_items_tmp('rec_data');
		if ($pid == true)
			exec ("kill -2 $pid");
		return array('status' => 'ok', 'screenPath' => $items->screenPath);
	}else if ($action == 'status_rec'){
		$items = HD::get_items_tmp('rec_data');
		$pid = exec ('pidof screenrecord');
		if ($pid == true){
			$tts = 180 - (time() - $items->time_start);
			if ($tts < 0)
				$tts = 0;
			return array('status' => 'rec', 'tts' => $tts);
		}else
			return array('status' => 'stop', 'screenPath' => $items->screenPath);
		
	}else if ($action == 'get_save_content'){
		if ((file_exists("/D/screenshots"))&&(!file_exists(DuneSystem::$properties['install_dir_path'] . "/www/dss")))
			exec ("ln -s /D/screenshots " . DuneSystem::$properties['install_dir_path'] . "/www/dss");
		if ((file_exists("/sdcard/screenshots"))&&(!file_exists(DuneSystem::$properties['install_dir_path'] . "/www/sdss")))
			exec ("ln -s /sdcard/screenshots " . DuneSystem::$properties['install_dir_path'] . "/www/sdss");
		$arr = array();
		if (file_exists("/D/screenshots"))
			$arr = get_files ("/D/screenshots", 'dss', $arr);
		if (file_exists("/sdcard/screenshots"))
			$arr = get_files ("/sdcard/screenshots", 'sdss', $arr);
		$result['screen_collection'] = $arr;
		return $result;
	}else
		return array('status' => 'error', 'error' => 'action unknown!');
}
function get_time (){
	$airDate = exec('date');
	$date = new DateTime($airDate);
	return $date->format('YmdGis');
}
function get_files ($path, $folder, $arr){
	$files = scandir($path);
	unset($files[0], $files[1]);
	foreach ($files as $v){
		if (preg_match('|\.png$|', $v)){
			$arr[$v]['src']= str_replace('127.0.0.1', HD::get_ip_address(), DuneSystem::$properties['plugin_www_url']) ."$folder/$v";
			$arr[$v]['type'] = "img";
		}
		if (preg_match('|\.mp4$|', $v)){
			$arr[$v]['src']= str_replace('127.0.0.1', HD::get_ip_address(), DuneSystem::$properties['plugin_www_url']) ."$folder/$v";
			$arr[$v]['type'] = "video";
			$arr[$v]['fake_img'] = str_replace('127.0.0.1', HD::get_ip_address(), DuneSystem::$properties['plugin_www_url']) .'/img/video.png';
		}
	}
	natsort($arr);
	return $arr;
}
function get_storage_size($path, $arg=null){
	$d[0] = disk_free_space($path);
	$d[1] = disk_total_space($path);
	foreach ($d as $bytes){
		$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
		$base = 1024;
		$class = min((int)log($bytes , $base) , count($si_prefix) - 1);
		$size[] = sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class];
	}
	if ($arg==true){
		$arr['str'] = $size[0] . '/' . $size[1];
		if ($arg < $d[0])
			$arr['free_space'] = true;
		else
			$arr['free_space'] = false;
		return $arr;
	}
	return $size[0] . '/' . $size[1];
}