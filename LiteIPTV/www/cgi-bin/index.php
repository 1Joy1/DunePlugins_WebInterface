<?
error_reporting (E_ALL);
ini_set('display_errors', 1);
ini_set('display_startur_errors', 1);

require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";
$m3u_files = HD::get_m3u();
$cutname_list = HD::get_items('cutname_list');
$group_defs = HD::get_items('grups_id');
if(array_key_exists('sorted', $_GET))
    $chnmbr_list = array_flip (HD::get_items('chnmbr_list'));
$my_channels_id = HD::get_items('my_channels_id');
$channels_id_parsed = array_merge(
    HD::get_items('vsetv_channels_id'),
    $my_channels_id
);
$channels_name = array_flip (array_merge(
    HD::get_items('vsetv_channels'),
    HD::get_items('vsetv_list')
));
$data_path = SmartConfig::get_data_path();
$group_names = SmartConfig::get_group();
$namesArr = array(
'%tr%t22' => 'Общие',
'%tr%t23' => 'Познавательные',
'%tr%t24' => 'Новостные',
'%tr%t25' => 'Развлекательные',
'%tr%t26' => 'Детские',
'%tr%t27' => 'Фильмы и сериалы',
'%tr%t28' => 'Музыкальные',
'%tr%t29' => 'Спортивные',
'%tr%t30' => 'Мужские',
'%tr%t31' => 'Взрослые',
'%tr%t32' => 'Региональные',
'%tr%t33' => 'Религиозные',
'%tr%t34' => 'Радио',
'%tr%t35' => 'HD каналы',
'%tr%t291' => 'Без группы'
);
?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title><?echo DuneSystem::$properties['plugin_name'];?></title>
    <meta charset="utf-8">

  <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/jquery.min-1.11.3.js"></script>
  <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/jquery-ui-1.11.4.js"></script>
  <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/bootstrap.min.js"></script>
  <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/bootstrap-select.min.js"></script>
  <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/index-1.1.0.js"></script>
  
  <link rel="shortcut icon" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/favicon.png">
  <link rel="stylesheet" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/css/global-1.0.1.css">

</head>
<body>
    <!-- Модаль-->
    <div class="modal fade" id="set_vsetvid_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" style="margin-top: 100px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" align="center"></h2>
                </div>

                <div class="modal-body">
                    <div id="req_content" align="center">
                            <!--Kakoi-to kontent-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ########## -->
    <!-- Модаль с хелпом-->
    <div class="modal fade" id="help_modal" tabindex="-1" role="dialog" aria-labelledby="modal Help" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin-top: 100px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h2 class="modal-title" align="center">Руководство для пользователя.</h2>
                </div>
                <div class="modal-body">
                    <div align="center">
                        Когда найдётся добрый человек, который отважится внести вклад в разработку плагина и напишет инструкцию для веб интерфейса.<br>
                        Здесь будет руководство для пользователя.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ########## -->
    
    <nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
            <div class="container">
                <div style="margin-top: 10px;" class="navbar-header">
                    <!-- Branding Image -->
                    <a style="font-weight: bold; font-size: 40px;" class="navbar-brand" href="index">
                        LiteIPTV <span style="color: rgb(255, 255, 255); font-size: 20px;">Dune plugin</span>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="font-size: 16px;font-weight: bold;padding: 22px;border-left: #242424 4px groove;border-right: #242424 4px ridge;">Меню<span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-inverse" role="menu" style="width: 510px;">
                                <li>
                                    <a href="index">Режим редактирования каналов (без дубликатов каналов).</a>
                                </li>
                                <li>
                                    <a href="index?full">Режим редактирования каналов (полный список с дубликатами каналов).</a>
                                </li>
                                <li>
                                    <a href="index?sorted">Перейти в режим сортировки каналов.</a>
                                </li>
                                <li>
                                    <a href="arhive">Перейти в режим редактирования архивов.</a>
                                </li>
                                <li>
                                    <a href="playlists">Менеджер плейлистов по ссылке.</a>
                                </li>
                                <li class="divider"></li>
                                <li><div style="padding: 3px 20px;">
                                    <div style="display: inline-block;">Список ID c сайта vsetv.com обновлен: <?echo date("d.m.Y", HD::get_item('vsetv_date'))?></div>
                                    <div style="display: inline-block;margin-left: 40px;">
                                        <form name="form" method="POST" style="">
                                            <input name="upd" value="Обновить" onclick="vsetvIDupd();return false;" style="" type="submit">
                                        </form>
                                    </div>
                                </div>
                                </li>
                                <li><div style="padding: 3px 20px;">
                                    <div style="display: inline-block;">Удалить из названия канала: </div>
                                    <div style="display: inline-block;">
                                        <form name="form" method="POST">
                                            <input id="cutstr" size="23" type="text">
                                            <input name="set" value="Set" onclick="cutset();return false;" type="submit">
                                        </form>
                                    </div>
                                    </div>
                                </li>
                                <li>
                                    <div style="padding: 3px 20px; cursor: pointer" data-toggle="modal" data-target="#help_modal">Руководство пользователя.</div>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul style="margin: 10px 10px 0px;" class="nav navbar-nav navbar-right">
                        <div>
                            <select style="width: 25%;float: left;" class="form-control" id="pagination">
                                <option value="0" selected="">Весь список</option>
                            </select>
                            <input style="width: 73%; float: right;" class="form-control" id="search" placeholder="Поиск по таблице" type="text">
                        </div>
                        <div style="color: aliceblue;<?php echo array_key_exists('sorted', $_GET) ? 'visibility: hidden;' : '';?>"><span style="font-weight:bold;">Фильтры: </span><span style="color:red;">9999</span> - каналы без vsetvID, <span style="color:red;">8888</span> - каналы без групп, <span style="color:red;">no_logo</span> - каналы без лого. </div>
                    </ul>
                </div>
            </div>
    </nav>
    
    <!-- Прелоадер -->
    <div id="notice" style="position: fixed;text-align: center;width: 100%;font-weight: bold;top: 100px;">
        <h1>Загрузка списка каналов.</h1>
        <h4>В зависимости от колличества каналов загрузка может длиться продолжительное время.</h4>
        <img src="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/img/ajax-loader2.gif">
        <br>При большом списке каналов, может наблюдаться плохая озывчивость страницы<br>Для улучшения отзывчивости используйте фильтры.
    </div>
    
    <!-- Заголовок таблицы -->
<?php
    if (array_key_exists('sorted', $_GET)) {
        $page_name = 'Режим сортировки каналов';
        $page_name_description = 'Этот режим работает при выборе в настройках плагина, сортировки каналов, "по номеру очередности" и "№ + алфавит"';
    } elseif (array_key_exists('full', $_GET)) {
        $page_name = 'Полный список с дубликатами каналов';
        $page_name_description = 'Это полный список каналов, агрегированный из всех подключенных плей листов.';
    } else {
        $page_name = 'Cписок без повторяющихся имен каналов';
        $page_name_description = 'Это отфильтрованный список, в котором все повторяющиеся по имени каналы отображены как один канал.';
    }
?>
    <div id="page_name" style="margin-top: 80px;margin-left: auto;margin-right: auto; text-align: center; display:none">
        <h4 style="font-weight:bold; margin-bottom: 0px;;"><?php echo $page_name;?></h4>
        <span style="<?php echo array_key_exists('sorted', $_GET) ? 'color:red;' : ''; ?>"><?php echo $page_name_description;?></span><?php echo array_key_exists('sorted', $_GET) ? '<br><span>Для применения изменений в плагине нажмите POP UP и выберите "Обновить список каналов".</span>':'';?>
    </div>
    
    <!-- Таблица -->
    <table id="mytable" style="margin-top:10px; margin-left:auto; margin-right:auto; display:none" border cellspacing="0">
        <thead>
            <tr>
                <th <?php echo array_key_exists('sorted', $_GET) ? 'class="th-off"' : ''?>>№</th>
                <td align="center">Name</td>
                <td align="center">Group</td>
                <th <?php echo array_key_exists('sorted', $_GET) ? 'class="th-off"' : ''?>>Gr.ID</th>
                <td align="center">To copy</td>
                <th <?php echo array_key_exists('sorted', $_GET) ? 'class="th-off"' : ''?>>vsetvID</th>
                <td align="center" colspan="2" <?php echo array_key_exists('sorted', $_GET) ? 'style="display:none"' : ''?>>Set vsetvID <a href = "http://www.vsetv.com/channels.html" target = "_blank">vsetv.com</a></td>
                <td>m</td>
                <td align="center">add IMG (PNG default 75x55)</td>
                <td align="center" colspan="2">img</td>
            </tr>
        </thead>
        
        <tbody id="table_body">
<?php

$i = 10000;
$foreach_arr = $array_unique_key_id = array();
foreach ($m3u_files as &$iptv_item){
    $m3u_lines = array();
    if (preg_match('/http:|https:/', $iptv_item)){
        $otps[CURLOPT_TIMEOUT] = 10;
        $web_pls = HDc::http_get_document($iptv_item, $otps);
        $web_pls = str_replace("\r", "", $web_pls);
        $m3u_lines = explode ("\n", $web_pls);
    } elseif (file_exists($iptv_item)) {
        $m3u_lines = file($iptv_item, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
    foreach ($m3u_lines as $line){
        if (!preg_match('//u', $line))
            $line = iconv('WINDOWS-1251', 'UTF-8', $line);
        $line = trim($line);
        if (preg_match('/\.m3u$/i', $line)){
            if (!isset($m3u_files[$line]))
                $m3u_files[$line] = $line;
            continue;
        }
        if ($line =='')
            continue;
        if (preg_match('|#EXTM3U|i', $line))
            continue;
        if (preg_match('/^#EXTVLCOPT:|^#EXTGRP:|^#EXT-INETRA/', $line))
            continue;
        if (preg_match('/^#EXTINF:[^,]*,(.+)$/', $line, $matches)) {
            $caption = trim($matches[1]);
            continue;
        }
        if (count($cutname_list) > 0)
            $caption = str_ireplace($cutname_list, '',$caption);

        $id = HD::get_id_key($caption);
        $id_key = array_key_exists($id, $channels_id_parsed) ? $channels_id_parsed[$id] : $id;
        // *****Не допускаем дублирования каналов******//////
        if(in_array($id_key, $array_unique_key_id) && !array_key_exists('full', $_GET)) {
            continue;
        }
        array_push($array_unique_key_id, $id_key);
        /////////////////////////////////////////////////////
        $caption = ($caption == '') ? $line : $caption;
        if((array_key_exists('sorted', $_GET))&&(isset($chnmbr_list[$id_key]))){
            $n = $chnmbr_list[$id_key];
        }else
            $n = $i;
        $foreach_arr[$n]['id_key']  = $id_key;
        $foreach_arr[$n]['caption'] = $caption;
        $foreach_arr[$n]['line']    = $line;
        $foreach_arr[$n]['md5']     = $id;
        $i++;   
    }
}
if (count($foreach_arr) > 0){
	ksort($foreach_arr);
	foreach (array_values($foreach_arr) as $n => $v) {
			$i = $n + 1;
			$id_key     = $v['id_key'];
			$caption    = $v['caption'];
			$line       = $v['line'];
			$md5        = $v['md5'];
			$id = str_replace('_HD', "", $id_key);
			$group_id = (array_key_exists($id, $group_defs)) ? $group_defs[$id] : 8888;
			
			if (mb_strlen($id_key,'UTF-8') == 32){
				$ttl= 'title="N/A" bgcolor="#FF0000"';
				$header = 9999;
			} else {
				$header = $id_key;
				if (isset($channels_name[$id]))
					$ttl= 'title="'.$channels_name[$id].'"';
			}
			
			$icon_path = HD::get_html_icon_path($id_key);
			if (($icon_path == 'plugin_file://logo/0.png')||($icon_path == 'plugin_file://logo/00.png')) {
				$icon_stat_bgcolor = 'bgcolor="#FF0000';
				$icon_stat_text = 'no_logo';
			} elseif (preg_match('|plugin_file|', $icon_path)) {
				$icon_stat_bgcolor = '';
				$icon_stat_text = 'in plugin';
			} else {
				$icon_stat_bgcolor = 'bgcolor="#008000"';
				$icon_stat_text = 'in DATA';
			}
			
			echo '
				<tr class="row-tv-list" id="rowId_'.$i. '" data-idkey="'. $id_key .'">
					<td class="index-number">'.$i . '. </td>
					<td class="chanel-name">'.$caption.'</td>
					<td class="select-group">
						<form class="select-group-form">
							<select class="select-group-selector" data-saved-value="'. $group_id .'" name="'.$id. '">';
				foreach ($group_names as $k => $v){
					if (isset($namesArr[$v]))
						$v = $namesArr[$v];
						echo '
								<option value="' . $k . ($k == $group_id?'" selected>':'">') . $v . '</option>';
				}
			echo '
							</select>
						</form>
					</td>
					<td class="group-id" bgcolor="' . ($group_id == 8888 ? '#FF0000' : '#008000') . '">'.$group_id.'</td>
					<td class="stream-link"><a href="'.$line.'" target="_blank">Stream</a></td>
					<td class="vsetv-id" '.$ttl.'>'.$header. '</td>';
					
			if (!array_key_exists('sorted', $_GET)) {
				echo '
					<td class="set-vsetv-id">
						<form class="set-vsetv-id-form">
							<input type="hidden" name="id_key" value = "'.$md5. '">
							<input type="hidden" name="action" value = "save_vsetvid">
							<input type="text" name="vsetvID" size="3"/>
							<input type="submit" value="Set">
						</form>
					</td>
					<td class="select-vsetv-id">
						<form class="select-vsetv-id-form">
							<input type="hidden" name="id_key" value = "'.$md5. '">
							<input type="hidden" name="caption" value = "'.$caption. '">
							<input type="submit" name="Set" value="Set VsetvID">
						</form>
					</td>';
			};
			
			echo '
					<td class="flag-manual"' . (isset($my_channels_id[$md5]) ? ' bgcolor="#008000">m' : '>') . '</td>
					<td class="img-upload">
						<form class="img-upload-form" enctype="multipart/form-data">
							<input type="file" name="'. $data_path . '/logo/'.$id.'.png" accept="image/*,image/png">
							<input type="hidden" name="id_key" value = "'.$md5. '">
							<input type="submit" value="Add">
						</form>
					</td>
					<td class="status-logo" ' . $icon_stat_bgcolor . '">' . $icon_stat_text . '</td>
					<td class="img-logo">
						<img style="height:30px" src="'.str_replace ('127.0.0.1', HD::get_ip_address(), DuneSystem::$properties['plugin_cgi_url']) . "do_img?img=" . str_replace ('plugin_file://', DuneSystem::$properties['install_dir_path'] . '/', HD::get_html_icon_path($id_key)) . '"alt="'.$caption.'">
					</td>
				</tr>'."\n";
	}
}
?>
        </tbody>
    </table>
    <br><br><br><br><br><br>
</body>