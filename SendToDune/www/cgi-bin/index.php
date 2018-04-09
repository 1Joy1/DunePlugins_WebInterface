<?
error_reporting (E_ALL);
require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";
$link_api = link_api();
if (file_exists($link_api))
require $link_api;
$withV6 = true;
    preg_match_all('/inet'.($withV6 ? '6?' : '').' addr: ?([^ ]+)/', `ifconfig`, $ips);
    $ip_address = array_shift($ips[1]);
    $plug_n = (DuneSystem::$properties['plugin_name']);
    $r_path = "http://$ip_address/plugins/$plug_n/";
    $result = HD::get_items('movie_id');
    $shuffle = (HD::get_item('shuffle') !='') ? HD::get_item('shuffle') : 1;
    if ($shuffle == 0) {
        $shuffle_ch = 'checked';  //Включён
    } else {
        $shuffle_ch = '';
    }
    $loop = (HD::get_item('loop') !='') ? HD::get_item('loop') : 1;
    if ($loop == 0) {
        $loop_ch = 'checked';  //Включён
    } else {
        $loop_ch = '';
    }
    $ad_pls = (HD::get_item('ad_pls') !='') ? HD::get_item('ad_pls') : 1;
    if ($ad_pls == 0) {
        $ad_pls_ch = 'checked';  //Включён
    } else {
        $ad_pls_ch = '';
    }
    $yt_indx = (HD::get_item('yt_indx') !='') ? HD::get_item('yt_indx') : 1;
    if ($yt_indx == 0) {
        $yt_indx_ch = 'checked';  //Включён
    } else {
        $yt_indx_ch = '';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="viewport" content="width=device=width, initial-scale=1">

    <title><?echo DuneSystem::$properties['plugin_name'];?></title>
    <link rel="shortcut icon" href="<?echo $r_path.'favicon.png';?>">
    
    <link rel="stylesheet" href="<?echo $r_path;?>css/bootstrap-3.3.4.min.css" />
    <link rel="stylesheet" href="<?echo $r_path;?>css/index-16.02.18.css" />
    
    <script type="text/javascript" src="<?echo $r_path;?>js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="<?echo $r_path;?>js/jquery-ui-1.11.4.js"></script>
    <script type="text/javascript" src="<?echo $r_path;?>js/bootstrap-3.3.4.min.js"></script>
    <script type="text/javascript" src="<?echo $r_path;?>js/jquery.jeditable.mini.js"></script>
    <script type="text/javascript" src="<?echo $r_path;?>js/jquery.json-2.2.min.js"></script>
    <script type="text/javascript" src="<?echo $r_path;?>js/init-16.02.18.js"></script>
    <script type="text/javascript" src="<?echo $r_path;?>js/index-16.02.18.js"></script>
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
    <div class="container">
        <img src="<?echo $r_path;?>img/logo.png" style="height:65px; float:left;"/>
        <div style="margin-top: 10px;" class="navbar-header">
            <!-- Branding Image -->
            <button type="button" data-toggle="collapse" data-target="#app-navbar-collapse" class="navbar-toggle collapsed" aria-expanded="false"><span class="sr-only">Toggle Navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></button>
            <a style="font-weight: bold; font-size: 30px; padding-top: 26px;" class="navbar-brand" href="#">
                SendToDune
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
            </ul>     
            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                
                <!-- Меню Плагина -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="font-weight: bold;padding: 22px;border-left: #242424 4px groove;border-right: #242424 4px ridge;">Настройки плагина<span class="caret"></span></a>
                    <ul id="menu_plugin_control" class="list-group dropdown-menu dropdown-inverse dropdown-not-close" role="menu" style="min-width: 350px;">
                        <li class="list-group-item-bordertop dropdown-inverse">
                            Вкл/Выкл Перемешивание (Shuffle)
                            <div class="material-switch material-switch-inverse pull-right">
                                <input id="shuffle_switch" name="shuffle" type="checkbox" <?echo $shuffle_ch;?>/>
                                <label for="shuffle_switch" class="label-info"></label>
                            </div>
                        </li>
                        <li class="list-group-item-bordertop dropdown-inverse">
                            Вкл/Выкл Повтор списка (Loop)
                            <div class="material-switch material-switch-inverse pull-right">
                                <input id="loop_switch" name="loop" type="checkbox" <?echo $loop_ch;?>/>
                                <label for="loop_switch" class="label-info"></label>
                            </div>
                        </li>

                        <li class="list-group-item-bordertop dropdown-inverse">
                            Добавлять в плейлист
                            <div class="material-switch material-switch-inverse pull-right">
                                <input id="ad_pls" name="ad_pls" type="checkbox" <?echo $ad_pls_ch;?>/>
                                <label for="ad_pls" class="label-info"></label>
                            </div>
                        </li>
                        <li class="list-group-item-bordertop dropdown-inverse">
                            Учитывать index Youtube плейлиста
                            <div class="material-switch material-switch-inverse pull-right">
                                <input id="yt_indx" name="yt_indx" type="checkbox" <?echo $yt_indx_ch;?>/>
                                <label for="yt_indx" class="label-info"></label>
                            </div>
                        </li>
                    </ul>
                </li>                        
                        
                <!-- Меню Play list -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="font-weight: bold;padding: 22px;border-left: #242424 4px groove;border-right: #242424 4px ridge;">Настройки плейлиста<span class="caret"></span></a>
                    <ul class="dropdown-menu dropdown-inverse-nohover playlist-menu dropdown-not-close" role="menu">
                        <li>
                            <ul class="playlist-menu-list">
                                <li >
                                    <form action='save.php'>
                                        <button class="btn btn-sm btn-primary" type='submit'><i class="glyphicon glyphicon-export"></i> Экспорт плейлиста</button>
                                    </form>
                                </li>
                                
                                <li>
                                    <form id="fileform" enctype="multipart/form-data" action="file.php" method="POST">
                                        <div class="btn btn-sm">
                                            <div class="input-group input-group-sm">
                                            <label class="input-group-btn">
                                                <span id="fileform_open_file" class="btn btn-primary btn-sm" data-toggle="tooltip" data-original-title="" data-trigger="manual">
                                                    <i class="glyphicon glyphicon-import"></i>&nbsp;Импорт плейлиста
                                                    <input name="userfile" type="file" accept="text/plain" style="display: none;">
                                                </span>
                                            </label> 
                                        
                                            <span class="input-group-btn">
                                                <button id="send_file_form" type="submit" title="Загрузить" class="btn btn-success">
                                                    <i class="glyphicon glyphicon-share-alt"></i>
                                                </button>
                                            </span>
                                            </div>
                                        </div>
                                    </form>
                                </li>
                                
                                <li >
                                    <form action='start.php' method="post">
                                        <button class="btn btn-sm btn-primary" name="start" value="all"><i class="glyphicon glyphicon-play"></i> Запустить текущий плейлист</button>
                                    </form>
                                </li>
                                
                                <li >
                                    <form action='setup.php' method="post">
                                        <button class="btn btn-sm btn-danger" name="movie_id" value="0"><i class="glyphicon glyphicon-trash"></i> Очистить плейлист</button>
                                    </form>                             
                                </li>
                                
                                <li >
                                    <form id="changeOrder" method="post" action="changeorder.php">
                                        <button class="btn btn-sm btn-primary" type="submit"><i class="glyphicon glyphicon-ok"></i> Сохранить сортировку**</button>
                                    </form>
                                    
                                </li>
                            </ul>
                        </li>
                        <li>
                            <span><small>**Сохранить сортировку - используется после изменения очередности в плейлисте</small></span>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php
    $withV6 = true;
    preg_match_all('/inet'.($withV6 ? '6?' : '').' addr: ?([^ ]+)/', `ifconfig`, $ips);
    $ip_address = array_shift($ips[1]);
    $plug_n = (DuneSystem::$properties['plugin_name']);
    $r_path = "http://$ip_address/plugins/$plug_n/";
    if (isset($error)) {
        echo '
<div class="container text-center" style="margin-top:  100px;">
    <h2>'.$error.'</h2>
</div>';
    } else {
        if (count($result)>0) {
            echo '
<div class="container" style="margin-top:100px; word-wrap:break-word;">
    <div class="col-sm-12 col-md-11 centered">
    <div class="row">
        <ul class="list-group" id="sortable">';
            $i=0;
            foreach ($result as $k => $v) {
                echo    '
            <li id="note_'.$i.'" class="editable list-group-item note-list">';
                $img = '';
                if (isset($v['video_image'])){
                    $img = '<img src="'.$v['video_image'].'" style="width: 100%;" />';
                    $img = str_replace("plugin_file://www/", $r_path, $img);
                }
                if (isset($v['video_title']))
                    $ix = $v['video_title'];
                else
                    $ix = $v['id'];

                echo '
                <a href="#" style="position:  absolute;margin: -10px 0 0 -15px;z-index:10">
                    <img src="'.$r_path.'img/delete.png" alt="Удалить">
                </a>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2">'.$img.'</div>
                        <div class="col-sm-10">
                            <h4><span class="badge">' . ($i+1) .  '.</span><span> </span><span class="note" id="n_'.$i.'">'.$ix.'</span></h4>
                            <a href="'.$v['id'].'"><h5>'.$v['id'].'</h5></a>
                            <div class="col-sm-2 item-btn-play">
                                <form action="start.php" method="post" style="display: ' . ($ad_pls == 0?'block':'none') . ';">
                                    <button class="btn btn-primary" style="width: 100%;" name="start" value="'.$i.'">
                                        <i class="glyphicon glyphicon-play"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </li>';
                $i++;
            }
            echo '
        </ul>
    </div></div>
</div>';
        } else {
            echo '
<div class="container text-center" style="margin-top:  100px;">
    <h2>Записи отстутствуют</h2>
    <p>Добавить итемы можно через расширения для Firefox/chrome/opera 15+ или андроид Приложение Sendtodune</p>
</div>';
        }
    }
    function link_api(){
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
</body>
</html>