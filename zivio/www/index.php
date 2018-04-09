<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startur_errors', 1);

require_once("JsonLoader.php");
?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="viewport" content="width=device=width, initial-scale=1" />
    <title>Web Interface Plugins Collection</title>
    <meta charset="utf-8">

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/helper.js"></script>


  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/card.css">

</head>
<body>
    <nav class="navbar navbar-fixed-top bg-primary">
        <div class="container">
            <div style="" class="navbar-header">
                <!-- Branding Image -->
                <a class="navbar-brand" href="#">
                    <img src="img/logo.png" style="float: left;">Entware</a>
                <div style="float: inherit; margin-top: 30px;margin-left:40px;">
                    <h2 class="hidden-xs" style="font-weight: 600">Web Interface Plugins Collection</h2>
                </div>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                </ul>

                <!-- Right Side Of Navbar -->

            </div>
        </div>
    </nav>

    <div class="container container-indent">
<?
//$installed_plugins = file_get_contents('/tmp/run/installed_plugins.xml');

$json_loader = new JsonLoader('JSON/web_interface_plugins.json');

$web_interface_plugins = $json_loader->getConfig();

foreach($web_interface_plugins as $key => $plugin){

    //if (preg_match($plugin->name, $installed_plugins)) {
        $html_id = mb_strtolower(str_replace(' ', '', $plugin->name));
?>
            <div id="p_<? echo $html_id ?>"class="plugin-card col-sm-6 col-md-4">
                <div class="features-div">
                    <a href="<? echo $plugin->location; ?>">
                        <div class="image-div">
                            <h2 class="plugin-name"><? echo $plugin->visible_name; ?></h2>
                            <div class="icon">
                                <img class="icon-img" src="img/default.png">
                                <img class="overlay" src="<? echo $plugin->icon_path; ?>">
                            </div>
                        </div>
                    </a>
                    <hr>
<?
        if (!$plugin->control) {
            /*echo '                      <div class="btn-group btn-group-justified invisible">
                        <a type="button" class="btn btn-lg btn-success disabled">Старт</a>
                        <a type="button" class="btn btn-lg btn-danger disabled">Стоп</a>
                    </div>'."\n";*/
            echo '                      <div>
                <a href="' . $plugin->location .'">
                    <b style="display: block;height: 46px;font-size: 12px;">Прямое управление плагином не предусмотренно. Перейдите к веб-интерфейсу.</b>
                </a>
            </div>'."\n";

        } else {

            $status = $plugin->control->status_start;

            echo '                      <div class="plugin-control btn-group btn-group-justified">
                        <a type="button" class="btn btn-lg btn-success' . ($status?' disabled':' ') . '" href="'
                        . $plugin->control->action_start . '">Старт</a>
                        <a type="button" class="btn btn-lg btn-danger' . ($status?' ':' disabled') . '" href="'
                        . $plugin->control->action_stop . '">Стоп</a>
                    </div>'."\n";
        }
?>
                    <hr>
                </div>
            </div>
<?
}
?>
    </div>

<!-- Notifies Alert -->
    <div style="position: fixed;z-index: 2000;width: 80%;bottom: 10px;margin-left: 10%;margin-right: 10%;">
        <div class="row row-fixed-top">
            <div id="notifies_alert"></div>
        </div>
    </div>
<body>


