<?
error_reporting (E_ALL);
require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";
header('Content-Type: application/json');

//////////////////////////////////////////Set groups ID//////////////////////////////////////
if ((isset($_POST['action']))&&($_POST['action'] == 'grups_id')){
	$group_defs = HD::get_items('grups_id');
	$group_defs[$_POST['vsetvID']] = $_POST['grID'];
    if (HD::save_items('grups_id', $group_defs)== true)
		$result = array('status' => 'ok', 'error' => '',);
	else
		$result = array('status' => 'error', 'error' => 'Сохранение не удалось!',);
    echo json_encode($result);
}
/////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////Set vsetv of id namber//////////////////////////////////////
if ((isset($_POST['action']))&&($_POST['action'] == 'save_vsetvid')){

	$my_channels_id = HD::get_items('my_channels_id');

	$channels_name = array_flip (array_merge(
		HD::get_items('vsetv_channels'),
		HD::get_items('vsetv_list')
	));

	$group_defs = HD::get_items('grups_id');

	$updated = 'false';

	if (($_POST['vsetvID'] == '')||($_POST['vsetvID'] == 0)) {

		if (isset($my_channels_id [$_POST['id_key']])) {
			unset($my_channels_id [$_POST['id_key']]);
			$updated = 'true';
		}

	} else {

		if (array_key_exists(str_replace('_HD', "", $_POST['vsetvID']), $channels_name)) {

			$my_channels_id [$_POST['id_key']] = $_POST['vsetvID'];
			$updated = 'true';

		} else {

			$updated = 'error';
		}
	}

	HD::save_items('my_channels_id', $my_channels_id);

	$channels_id_parsed = array_merge(
		HD::get_items('vsetv_channels_id'),
		$my_channels_id
	);

	if (array_key_exists($_POST['id_key'], $channels_id_parsed)) {
		$chanel_id = $channels_id_parsed[$_POST['id_key']];
		$title = $channels_name[str_replace('_HD', "", $chanel_id)];
		$icon_path = HD::get_html_icon_path(str_replace('_HD', "", $chanel_id));
		$icon_path_upload = SmartConfig::get_data_path() . '/logo/'. str_replace('_HD', "", $chanel_id) .'.png';
		$group_id = (array_key_exists(str_replace('_HD', "", $chanel_id), $group_defs)) ? $group_defs[str_replace('_HD', "", $chanel_id)] : 8888;
	}  else {
		$chanel_id = '9999';
		$title = 'N/A';
		$icon_path = HD::get_html_icon_path($_POST['id_key']);
		$icon_path_upload = SmartConfig::get_data_path() . '/logo/'. $_POST['id_key'] .'.png';
		$group_id = (array_key_exists($_POST['id_key'], $group_defs)) ? $group_defs[$_POST['id_key']] : 8888;
	}

	if (isset($my_channels_id[$_POST['id_key']])) {
		$manual = 'true';
	} else {
		$manual = 'false';
	}

	if (($icon_path == 'plugin_file://logo/0.png')||($icon_path == 'plugin_file://logo/00.png'))
		$icon_status = 'no_logo';
	else if (preg_match('|plugin_file|', $icon_path))
		$icon_status = 'in plugin';
	else
		$icon_status = 'in DATA';

	$resp = array('updated'=>$updated,
		          'title'=>$title,
		          'chanel_id'=>$chanel_id,
		          'group_id'=>$group_id,
		          'manual'=>$manual,
		          'icon_status'=>$icon_status,
		      	  'icon_path_upload'=>$icon_path_upload,
	              'icon_src'=>str_replace ('127.0.0.1', HD::get_ip_address(), DuneSystem::$properties['plugin_cgi_url']) . "do_img?img=" . str_replace ('plugin_file://', DuneSystem::$properties['install_dir_path'] . '/', $icon_path));

	echo json_encode($resp);
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////Cut name chanel/////////////////////////////////////////
if ((isset($_POST['action']))&&($_POST['action'] == 'save_cutname')){
	if ($_POST['cutstr'] != ''){
		$cutname_list = HD::get_items('cutname_list');
		$cutname_list[] = trim($_POST['cutstr']);
		HD::save_items('cutname_list', $cutname_list);
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Update vsetvID/////////////////////////////////////////
if ((isset($_POST['action']))&&($_POST['action'] == 'vsetvIDupd')){
	
	$doc = iconv('WINDOWS-1251', 'UTF-8',HDc::http_get_document('http://www.vsetv.com/channels.html'));
	preg_match_all('|<option value=channel_(.*?)>(.*?)</option>|ms', $doc, $matches);
	foreach ($matches[2] as $k => $v){
		$vsetv_channels_id[HD::get_id_key($v)] = $matches[1][$k];
		$vsetv_channels[$v] = $matches[1][$k];
	}
	HD::save_items('vsetv_channels_id', $vsetv_channels_id);
	HD::save_items('vsetv_channels', $vsetv_channels);
	HD::save_item('vsetv_date', time());
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Save Sorted///////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'save_sorted') {
	if (HD::save_items('chnmbr_list', $_POST['soted_chanels'])== true)
		$result = array('status' => 'ok', 'error' => '', 'sorted_chanels' => $_POST['soted_chanels']);
	else
		$result = array('status' => 'error', 'error' => 'Сохранение не удалось!',);
    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Clear Sorted//////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'clear_sorted') {
	if (HD::save_items('chnmbr_list', '')== true)
		$result = array('status' => 'ok', 'error' => '', 'sorted_chanels' => $_POST['soted_chanels']);
	else
		$result = array('status' => 'error', 'error' => 'Сохранение не удалось!',);
    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Save Arhive///////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'save_arhive') {
	$arc_channels = HD::get_items('arc_channels');
	if ($_POST['url'] != ''){
		$arc_channels[$_POST['idkey']] = array(
		'url' => $_POST['url'],
		'day' => $_POST['day'],
		'ua' => $_POST['ua']
		);
	}else if ($_POST['url'] == ''){
		if (isset($arc_channels[$_POST['idkey']]))
			unset($arc_channels[$_POST['idkey']]);
	}
	if (HD::save_items('arc_channels', $arc_channels)== true)
		$result = array('status' => 'ok', 'error' => '',);
	else
		$result = array('status' => 'error', 'error' => 'Сохранение не удалось!',);
    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Clear Arhive//////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'clear_arhive') {
	if (HD::save_items('arc_channels', '')== true)
		$result = array('status' => 'ok', 'error' => '',);
	else
		$result = array('status' => 'error', 'error' => 'Сохранение не удалось!',);
    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
               ///////////////////Обработчики для плейлистов////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////Save Playlist//////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'save_playlist') {
	$id = $_POST['id'];
    $link = DuneSystem::$properties['data_dir_path'] . '/pl_folder';
	$pl_folders = unserialize(file_get_contents($link));
	if (isset($pl_folders[$id])){
		$pl_folders[$id]['pl_url'] = $_POST['url'];
		if (file_put_contents ($link, serialize($pl_folders)) !== false)
			$result = array('status' => 'ok', 'error' => '', 'id' => $id);
		else
			$result = array('status' => 'error', 'error' => 'Редактирование не удалось! pl_folder не сохранен',);
	}else
		$result = array('status' => 'error', 'error' => 'Редактирование не удалось! id не найден',);
    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Delete Playlist//////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'dell_playlist') {
	$id = $_POST['id'];
	$link = DuneSystem::$properties['data_dir_path'] . '/pl_folder';
	$pl_folders = unserialize(file_get_contents($link));
	if (isset($pl_folders[$id])){
		unset($pl_folders[$id]);
		if (file_put_contents ($link, serialize($pl_folders)) !== false)
			$result = array('status' => 'ok', 'error' => '',);
		else
			$result = array('status' => 'error', 'error' => 'Удаление не удалось! pl_folder не сохранен',);
	}else
		$result = array('status' => 'error', 'error' => 'Удаление не удалось! id не найден',);
	
    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Add Playlist//////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'add_new_playlist') {
	if (!preg_match('|^http|',$_POST['url']))
		$result = array('status' => 'error', 'error' => 'Добавление не удалось! Укажите http/https ссылку!',);
	else{
		$link = DuneSystem::$properties['data_dir_path'] . '/pl_folder';
		$pl_folders = array_values(unserialize(file_get_contents($link)));
		$id = count ($pl_folders);
		$pl_folders[$id]['pl_url'] = $_POST['url'];
		if (file_put_contents ($link, serialize($pl_folders)) !== false)
			$result = array('status' => 'ok', 'error' => '', 'id' => $id);
		else
			$result = array('status' => 'error', 'error' => 'Добавление не удалось! pl_folder не сохранен',);
	}
    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////Hide Playlist//////////////////////////////////////////
if(isset($_POST['action']) && $_POST['action'] === 'hide_playlist') {
	$link = DuneSystem::$properties['data_dir_path'] . '/stopList';
	$stopList = json_decode(file_get_contents($link), true);
	if (isset($stopList[$_POST['url']]))
		unset($stopList[$_POST['url']]);
	else
		$stopList[$_POST['url']] = 1;
	if (file_put_contents ($link, json_encode($stopList)) !== false)
		$result = array('status' => 'ok', 'error' => '', 'id' => $_POST['url']);
	else
		$result = array('status' => 'error', 'error' => 'stopList не сохранен',);

    echo json_encode($result);
}
///////////////////////////////////////////////////////////////////////////////////////////////
?>