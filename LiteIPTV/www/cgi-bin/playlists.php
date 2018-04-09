<?php
error_reporting (E_ALL);
require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";
?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title><?php echo DuneSystem::$properties['plugin_name'];?></title>
    <meta charset="utf-8">

  <script src="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/js/jquery.min-1.11.3.js"></script>
  <script src="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/js/jquery-ui-1.11.4.js"></script>
  <script src="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/js/bootstrap.min.js"></script>
  <script src="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/js/bootstrap-select.min.js"></script>
  <script src="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/js/playlists-1.0.1.js"></script>
  
  <link rel="shortcut icon" href="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/favicon.png">
  <link rel="stylesheet" href="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/css/global-1.0.1.css">

</head>
<body>
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
                                <li>
                                    <div style="padding: 3px 20px; cursor: pointer" data-toggle="modal" data-target="#help_modal">Руководство пользователя.</div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                </div>
            </div>
    </nav>
    
    <!-- Заголовок таблицы -->
    <div id="page_name" style="margin-top: 80px;margin-left: auto;margin-right: auto; text-align: center;">
        <h4 style="font-weight:bold; margin-bottom: 0px;">Менеджер плейлистов</h4>
        <span>В этом разделе можно добавить, удалить или отредактировать http/https ссылки на плейлисты.</span>
        <br><span>Для применения изменений в плагине нажмите POP UP и выберите "Обновить список каналов".</span>
    </div>
    
    <!-- Поле для ввода нового плейлиста -->
    <div id="add-new-playlist" style="width: 100%; text-align: center; margin-top: 40px;">
        <input id="new-playlist-fild" type="text" placeholder="Введите ссылку на плейлист и нажмите добавить." size="100"/>
        <button class="batton-add-playlist">Добавить плейлист.</button>
    </div>
    
    <!-- Таблица -->
    <table id="mytable" style="margin-top:10px; margin-left:auto; margin-right:auto;" border cellspacing="0">
        <thead>
            <tr>
                <th>№</th>
                <td align="center" style="width: 50em;">Cсылки на плейлисты.</td>
				<td style="width: 9em;"></td>
                <td style="width: 9em;"></td>
                <td style="width: 9em;"></td>
            </tr>
        </thead>
        <tbody id="table_body">
<?php
$i=1;
$link = DuneSystem::$properties['data_dir_path'] . '/pl_folder';
$pl_folders = unserialize(file_get_contents($link));
$m3u_files = HD::get_m3u(1);
$stopList = array();
if (file_exists(DuneSystem::$properties['data_dir_path'] . '/stopList'))
	$stopList = json_decode(file_get_contents(DuneSystem::$properties['data_dir_path'] . '/stopList'), true);
foreach ($pl_folders as $k => $v){
	if (isset($v['pl_url'])){
		if (isset($m3u_files[$v['pl_url']]))
			unset($m3u_files[$v['pl_url']]);
		if (isset($stopList[$v['pl_url']])){
			$buttons= 'popen';
			$cpt = 'Включить';
            $activ_stat = ' non-activ';
		}else{
			
			$buttons= 'phide';
			$cpt = 'Отключить';
            $activ_stat = '';
		}
echo '
            <tr class="row-tv-list">
                <td class="index-number">'.$i.'</td>
                <td class="playlists">
					<input class="playlists-id" type="hidden" value = "'.$k. '">
                    <input class="playlists-fild' . $activ_stat . '" type="text" value="'.$v['pl_url'].'" size="100" disabled/>
                </td> 
				<td class="phide" align="center">
                    <button class="'.$buttons.'-buttons" style="width: 9em;">'.$cpt.'</button>
                </td> 
                <td class="edit" align="center">
                    <button class="edit-buttons" style="width: 9em;">Редактировать</button>
                </td>                
                <td class="dell" align="center">
                    <button class="dell-buttons" style="width: 9em;">Удалить</button>
                </td>
            </tr>';
			$i++;
	}
}
foreach($m3u_files as $k => $v){
	if (isset($stopList[$v])){
		$buttons= 'popen';
		$cpt = 'Включить';
        $activ_stat = ' non-activ';
	}else{
		
		$buttons= 'phide';
		$cpt = 'Отключить';
        $activ_stat = '';
	}
	echo '
            <tr class="row-tv-list">
                <td class="index-number">'.$i.'</td>
                <td class="playlists">
                    <input class="playlists-fild' . $activ_stat . '" type="text" value="'.$v.'" size="100" disabled/>
                </td>
				<td class="phide" align="center">
                    <button class="'.$buttons.'-buttons" style="width: 9em;">'.$cpt.'</button>
                </td> 
                <td class="edit" align="center"></td>                
                <td class="dell" align="center"></td>
            </tr>';
			$i++;
}
?>

        </tbody>
    </table>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
</body>