<?
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startur_errors', 1);
require_once "do_config.php";
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Screenshoter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/img/favicon.png">
    <link rel="icon" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/img/favicon.png?123">
    <link rel="stylesheet" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/css/bootstrap-4.0.0.css" media="screen">
    <link rel="stylesheet" href="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/css/index.css" media="screen">

    <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/jquery-3.2.1.min.js"></script>
    <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/bootstrap-4.0.0.min.js"></script>
    <script src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/js/index.js"></script>


  </head>
  <body>
    <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
        <div class="container">
            <a href="#" class="navbar-brand"><img class="logobrand" src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/img/logo.png"/>Screenshoter</a>
        </div>
    </div>

    <!-- Content -->
    <div class="container" style="margin-top: 110px;">
        <!-- Controll button block -->
        <div class="page-header">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <button id="screenshot-but" type="button" class="btn btn-info btn-lg btn-block">Сделать скриншот</button>
                    <button id="rec-stop-but" type="button" class="btn btn-primary btn-lg btn-block">Записать видео</button>
                    <div class="text-center"><small>Запись видео - функция эксперементальная. Может работать не во всех плеерах, Качество записанного материала, очень посредственно.</small></div>
                </div>
            </div>
        </div>

        <hr style="margin-top: 30px; margin-bottom: 30px;">

        <!-- Screenshot collection block -->

        <div class="card border-dark mb-3" >
            <div class="card-header"><h5>Сохранённый контент</h5></div>
            <div class="card-body">
                <div id="save_content" class="row">

                </div>
            </div>
        </div>
		<div class="text-center"><a href="http://piccy.info/" target="_blank">Хостинг для картинок http://piccy.info/</a></div>
        <hr style="margin-top: 30px; margin-bottom: 30px;">

    </div>

    <!-- Notifies Alert -->
    <div style="position:fixed; z-index:2000; width:80%; bottom:20px; margin-left:10%;margin-right:10%;">
        <div class="row row-fixed-top">
            <div id="notifies_alert" class="col-12">
                <!--JS generated content-->
            </div>
        </div>
    </div>


    <!-- Footer -->
    <footer class="page-footer bg-light font-small fixed-bottom col-lg-12 col-md-12 col-sm-12">
         <!-- Copyright -->
        <div class="text-center"><small>© 2018 Web interface design & development by !Joy!</small></div>
    </footer>

   <audio  id="screen-sound" src="http://<? echo HD::get_ip_address(); ?>/plugins/<? echo DuneSystem::$properties['plugin_name']; ?>/img/screenshot.mp3"></audio>
  </body>
</html>
