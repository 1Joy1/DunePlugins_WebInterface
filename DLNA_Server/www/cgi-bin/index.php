<?
require_once "LogWebInterface.php";

require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/settings.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";


$withV6 = true;
preg_match_all('/inet'.($withV6 ? '6?' : '').' addr: ?([^ ]+)/', `ifconfig`, $ips);
$ip_address = array_shift($ips[1]);
$plug_n = (DuneSystem::$properties['plugin_name']);
$r_path = "http://$ip_address/plugins/$plug_n/";


$saved_items = HD::get_items('folder');
$settings = new Settings;
$settings->init();
$online = HD::checkOnline();
$autostart = HD::get_item('autostart');
$current_lang = $settings->getInterface_language();
if($online) {
    $dlna_web_status = HD::get_dlna_web_status();
}
if ($current_lang == 'german') {
    $current_lang = 'de';
} elseif ($current_lang == 'russian') {
    $current_lang = 'ru';
} else {
    $current_lang = 'en';
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Dune Plugin DLNA Server</title>
        <meta name="viewport" content="width=device=width, initial-scale=1 maximum-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        
        <link rel="shortcut icon" href="<?echo $r_path;?>img/favicon.png">
        <link rel="icon" href="<?echo $r_path;?>img/favicon.png">
    
        <link rel="stylesheet" href="<?echo $r_path;?>css/bootstrap-4.0.0.css" media="screen">
        <link rel="stylesheet" href="<?echo $r_path;?>css/material-swich-1.0.0.css" media="screen">
        
        <script src="<?echo $r_path;?>js/jquery-3.2.1.min.js"></script>
        <script src="<?echo $r_path;?>js/bootstrap-4.0.0.min.js"></script>
        <script src="<?echo $r_path;?>js/jquery-lang.js" charset="utf-8" type="text/javascript"></script>
        <script src="<?echo $r_path;?>js/langpack/langpack-1.0.0.js" charset="utf-8" type="text/javascript"></script>
        <script src="<?echo $r_path;?>js/index-1.0.0.js"></script>
        <script type="text/javascript">
            var plugin_path = "<?echo $r_path;?>";
        // Create language switcher instance
            var lang = new Lang();
        
            lang.init({
                defaultLang: 'en',
                currentLang: '<?echo $current_lang;?>',
            });
        </script>    
    </head>
    
    
    <body>
        <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
            <div class="container">
                <a href="#" class="navbar-brand"><img class="logobrand" src="<?echo $r_path;?>img/logo.png"/>Dune Plugin DLNA Server</a>       
            </div>
        </div>
    
        <!-- Content -->
        <div class="container" style="margin-top: 80px;">
        
            <!-- Device Name block -->
            <div class="page-header" id="banner">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">                
                        <p class="lead" lang="en">Device Name:</p>
                        <h1 class="dev-name" lang="en"><?echo $settings->getFriendly_name() == ""?"Non set":$settings->getFriendly_name();?></h1>
                    </div>
                </div>
            </div>
    
            <!-- Scan status block -->
            <div class="card bg-dark border-primary mb-3" >
                <div class="card-header"><h5 lang="en">Scan status.</h5></div>
                <div class="card-body">
                    <h4 id="dlna-web-status-scan" class="card-title invisible" lang="en">&nbsp;</h4>
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-4 text-center">
                            <div class="card border-primary mb-3">
                                <div class="card-header card-header-status-scan" lang="en">Video</div>
                                <div class="card-body">
                                    <img src="<?echo $r_path;?>img/video_small.png?123">
                                    <span id="dlna-web-status-video" class="badge badge-secondary bage-status-scan d-none"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-4 text-center">
                            <div class="card border-primary mb-3">
                                <div class="card-header card-header-status-scan" lang="en">Audio</div>
                                <div class="card-body">
                                    <img src="<?echo $r_path;?>img/audio_small.png?123">
                                    <span id="dlna-web-status-audio" class="badge badge-secondary bage-status-scan d-none"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-4 text-center">
                            <div class="card border-primary mb-3">
                                <div class="card-header card-header-status-scan" lang="en">Pictures</div>
                                <div class="card-body">
                                    <img src="<?echo $r_path;?>img/image_small.png?123">
                                    <span id="dlna-web-status-picture" class="badge badge-secondary bage-status-scan d-none"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Settings block -->
            <div class="card bg-dark border-primary mb-3" >
                <div class="card-header"><h5 lang="en">Settings.</h5></div>            
                <ul class="list-group list-group-flush plugin-control">
                    <li class="list-group-item text-<?echo $online ? 'success' : 'danger'?>" lang="en">
                        Server On/Off
                        <div class="material-switch float-right">
                            <input id="off-on-control" name="off_on" type="checkbox" <?echo $online?'checked':''?>/>
                            <label for="off-on-control" class="bg-success"></label>
                        </div>
                    </li>
                    <li class="list-group-item" lang="en">
                        Autostart
                        <div class="material-switch float-right">
                            <input id="autostart-control" name="autostart" type="checkbox" <?echo $autostart==='yes'?'checked':''?>/>
                            <label for="autostart-control" class="bg-info"></label>
                        </div>
                    </li>
                    <li class="list-group-item" lang="en">
                        Autodiscovery of new files:
                        <div class="material-switch float-right">
                            <input id="autosearch-new-file-control" name="autosearch_new_file" type="checkbox" <?echo $settings->getInotify()==='yes'?'checked':''?>/>
                            <label for="autosearch-new-file-control" class="bg-info"></label>
                        </div>
                    </li>
                    <li class="list-group-item" lang="en">
                        Strictly follow the DLNA-standard:
                        <div class="material-switch float-right">
                            <input id="dlna-standart-control" name="dlna_standart" type="checkbox" <?echo $settings->getStrict_dlna()==='yes'?'checked':''?>/>
                            <label for="dlna-standart-control" class="bg-info"></label>
                        </div>
                    </li>
                    <li class="list-group-item" lang="en">
                        Logging mode:
                        <div class="material-switch float-right">
                            <input id="loging-control" name="loging" type="checkbox" <?echo $settings->getLog_level()==='on'?'checked':''?>/>
                            <label for="loging-control" class="bg-info"></label>
                        </div>
                    </li>
                </ul>            
            </div>
            
            <!-- Management directory block -->
            <div class="card bg-dark border-primary mb-3" >
                <div class="card-header">
                    <div class="row">
                        <div class="col-8"><h5 lang="en">Management directory.</h5></div>
                        <div class="col-4">
                            <button id="btn-call-modal" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modal-for-folder" lang="en">Add directory</button>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">                
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col" lang="en">Name</th>
                                <th scope="col" lang="en">Path</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="manag-fold-tabl">
                        <?foreach($saved_items as $items) {?>
                            <tr class="folder">
                                <th scope="row" style="width: 100px;"><img class="folder-ico" src="<?echo $r_path;?>img/folder_b.png"></th>
                                <td><?echo $items['name'];?></td>
                                <td class="path-folder"><?echo $items['path'];?></td>
                                <td>
                                    <a class="folder-dell block float-right" href="#">
                                        <img class="minus-ico" src="<?echo $r_path;?>img/minus-b.png">
                                    </a>
                                </td>
                            </tr>
                        <?}?>
                            
                        </tbody>
                    </table>                 
                        
                    <div class="row align-items-center justify-content-center">    
                            <button id="rescan-db" type="button" class="btn btn-warning" lang="en">Scan and update the database</button>
                    </div>
                </div>            
            </div>
            
            
            <!-- Large modal -->
            <div class="modal fade add-folder-modal-lg" id="modal-for-folder" tabindex="-1" role="dialog" aria-labelledby="add-folder-modal" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="add-folder-modal" lang="en">Resurses on DuneHD.</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="tools-block-general">
                                <div id="fild-dune-resurs-path" class="tools-display">/</div>
                                <div class="tools-block">
                                    <!--Download Ajax data-->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" lang="en" id="add-folder-button">Add directory</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" lang="en">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            
            
            <!-- Copyright footer -->
            <div class="text-center" style="margin:30px 0px 10px 0px"><small>© Web interface design & development by !Joy!</small></div>
            
        </div>
        
        <!-- Notifies Alert -->
        <div style="position:fixed; z-index:2000; width:80%; bottom:20px; margin-left:10%;margin-right:10%;">
            <div class="row row-fixed-top">
                <div id="notifies_alert" class="col-12">
                    <!--JS generated content-->
                </div>
            </div>
        </div>
        
    </body>
</html>
