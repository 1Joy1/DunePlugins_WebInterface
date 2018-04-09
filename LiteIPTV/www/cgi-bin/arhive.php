<?php
error_reporting (E_ALL);
require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";
$m3u_files = HD::get_m3u();
$cutname_list = HD::get_items('cutname_list');
$channels_id_parsed = array_merge(
    HD::get_items('vsetv_channels_id'), 
    HD::get_items('my_channels_id')
);
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
  <script src="http://<?php echo HD::get_ip_address(); ?>/plugins/<?php echo DuneSystem::$properties['plugin_name']; ?>/js/arhive-1.1.0.js"></script>
  
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
                                    <div style="padding: 3px 20px;">
                                        <div style="display: inline-block;">Очистить ссылки на архивы на всех каналах</div>
                                        <button id="clear-all-archive" style="color: black; display: inline-block;margin-left: 90px;">Очистить</button>
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
                        <div style="color: aliceblue; visibility: hidden;"><span style="font-weight:bold;">Фильтры: </span><span style="color:red;">9999</span> - каналы без vsetvID, <span style="color:red;">8888</span> - каналы без групп, <span style="color:red;">no_logo</span> - каналы без лого. </div>
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
    <div id="page_name" style="margin-top: 80px;margin-left: auto;margin-right: auto; text-align: center; display:none">
        <h4 style="font-weight:bold; margin-bottom: 0px;;">Режим редактирования архивов</h4>
        <span>В этом разделе можно добавить ссылки на архивы</span>
        <br><span>Для применения изменений в плагине нажмите POP UP и выберите "Обновить список каналов".</span>
    </div>
    <!-- Таблица -->
    <table id="mytable" style="margin-top:10px; margin-left:auto; margin-right:auto; display:none" border cellspacing="0">
        <thead>
            <tr>
                <th>№</th>
                <td align="center"></td>
                <th align="center">Name</th>
                <td align="center">Cсылка на архив</td>
                <td align="center">Кол-во дней</td>
                <td align="center">UserAgent (Не обязательно) r11+</td>
                <td></td>
            </tr>
        </thead>
        <tbody id="table_body">
<?php
$ii = 1;

$array_unique_key_id = array();
$arc_channels = HD::get_items('arc_channels');
foreach ($m3u_files as &$iptv_item){
    $m3u_lines = array();
    if (preg_match('/http:|https:/', $iptv_item)){
        $otps[CURLOPT_TIMEOUT] = 10;
        $web_pls = HDc::http_get_document($iptv_item, $otps);
        $web_pls = str_replace("\r", "", $web_pls);
        $m3u_lines = explode ("\n", $web_pls);
    }else if (file_exists($iptv_item))
        $m3u_lines = file($iptv_item, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
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
        $id_key = array_key_exists($id,$channels_id_parsed) ? $channels_id_parsed[$id] : $id;
        
        // *****Не допускаем дублирования каналов******//////
        if(in_array($id_key, $array_unique_key_id)) {
            continue;
        }
        array_push($array_unique_key_id, $id_key);
        /////////////////////////////////////////////////////
        
        $caption = ($caption == '') ? $line : $caption;
        
        //////////костыль лисапедов////////////////////////
        if (isset($arc_channels[$id_key]['url'])){
            $font_class = 'font-bold';
            $row_class = 'set';
            $url_value = $arc_channels[$id_key]['url'];
            $fild_stat = 'disabled';
            $button_save_edite = 'edit';
            $button_save_edite_caption = 'Редактировать';
        } else {
            $font_class = '';
            $row_class = '';
            $url_value = '';
            $fild_stat = '';
            $button_save_edite = 'save';
            $button_save_edite_caption = 'Сохранить';
        }            

        if (isset($arc_channels[$id_key]['ua'])) {
            $ua_value = $arc_channels[$id_key]['ua'];
        } else {
            $ua_value = '';
        }

        if (isset($arc_channels[$id_key]['day'])){
            $_day = '';
            for ($i = 1; $i<21; $i++){
                if ($arc_channels[$id_key]['day'] == $i)
                    $_day .= '<option value="'.$i.'" selected>'.$i.'</option>';
                else
                    $_day .= '<option value="'.$i.'">'.$i.'</option>';
            }
        }else{
            $_day = '<option value="1" selected>1</option>';
            for ($i = 2; $i<21; $i++)
                $_day .= '<option value="'.$i.'">'.$i.'</option>';
        }
        /////////////////////////////////////////////////////
        
        echo '
            <tr class="row-tv-list ' . $row_class . '" id="rowId_'.$ii. '" data-idkey="'. $id_key .'">
                <td class="index-number">'.$ii . '. </td>
                <td><img style="height:30px" src="'.str_replace ('127.0.0.1', HD::get_ip_address(), DuneSystem::$properties['plugin_cgi_url']) . "do_img?img=" . str_replace ('plugin_file://', DuneSystem::$properties['install_dir_path'] . '/', HD::get_html_icon_path($id_key)) . '"alt="'.$caption.'"></td>
                <td class="chanel-name ' . $font_class . '">'.$caption.'</td>
                <td class="arhivs-url">
                    <input class="arhivs-url-fild ' . $row_class . '" type="text" name="arhivs-url" placeholder="Cсылка на архив" value="' . $url_value . '" size="50" ' . $fild_stat . '/>
                </td>
                <td class="arhivs-coll-day" align="center">
                    <select class="arhivs-coll-day-fild" ' . $fild_stat . '>
                        '.$_day.'
                    </select>
                </td>
                <td class="arhivs-ua">
                    <input class="arhivs-ua-fild ' . $row_class . '" type="text" placeholder="UserAgent" value="' . $ua_value . '" size="26" ' . $fild_stat . '/>
                </td>
                <td class="arhivs-buttons" align="center">
                    <button class="arhivs-buttons-' . $button_save_edite . '" style="width: 9em;">' . $button_save_edite_caption . '</button>
                </td>
            </tr>';
            
        $ii++;
    }
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