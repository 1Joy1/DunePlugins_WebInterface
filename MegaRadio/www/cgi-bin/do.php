<?
error_reporting (E_ALL);
require_once "do_config.php";

$ip_address = HD::get_ip_address();
file_get_contents("http://$ip_address/cgi-bin/do?cmd=ir_code&ir_code=A05FBF00");
$mode_ground = (HD::get_item('mode_path') !='') ? HD::get_item('mode_path') : 'mosaic-mode';
$item ='';
$r_path = "http://$ip_address/plugins/".DuneSystem::$properties['plugin_name']."/";
$install_dir_path = DuneSystem::$properties['install_dir_path'];
$my_playlist = HD::get_items('my_playlist');
foreach ($my_playlist as $id => $line){
    if (preg_match('/Stream:\|(.*)/', $line['info'], $matches))
        $description = strval($matches[1]);
    else
        $description = strval($line['info']);
    $caption = strval($line['name']);
    if (preg_match("|plugin_file|",$line['poster_url']))
        $icon_url = $r_path . str_replace('plugin_file://www/', "", $line['poster_url']);
    else if (preg_match("|do_img\?img=(.*)|",$line['poster_url'], $m))
        $icon_url = $m[1];
    else
        $icon_url = $line['poster_url'];
    $streaming_url = strval( base64_encode($id));

    $item .= '
    <li class="item">
        <div class="image">
            <a href="#" data-streamurl="' . $streaming_url . '" class="tune-in-link">
                <span class="overlay"></span>
                <img src="' . $icon_url . '" alt="' . $caption . '" class="complete">
                <span class="tune-in icon-play-circle"></span>
            </a>
        </div>
        <div class="info">
            <h3 class="title">
                <a href="#" data-streamurl="' . $streaming_url . '">' . $caption . '</a>
            </h3>
            <span class="track">' . $description . '</span>
        </div>
    </li>';
}
?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device=width, initial-scale=1 maximum-scale=1">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<title><?echo DuneSystem::$properties['plugin_name'];?></title>

<link rel="shortcut icon" href="<?echo $r_path.'favicon.png';?>">
<link rel="icon" href="<?echo $r_path.'favicon.png';?>">

<link href="<?echo $r_path;?>css/modal-contact-form.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?echo $r_path;?>css/index.css" media="screen" rel="stylesheet" type="text/css">

<script src="<?echo $r_path;?>js/jquery-1.11.3.min.js" type="text/javascript"></script>
<script src="<?echo $r_path;?>js/index.js?173" type="text/javascript"></script>

</head>
<body>
    <header id="main-header">
        <div class="navbar-brand" >
            <img src="<?echo $r_path;?>img/logo.png">
            <a href="#">
                MegaRadio
            </a>
        </div>

        <div class="right">
            <nav class="button-dune-controll">
                <ul>
                    <li class="nothidden">
                        <a href="#" data-ircode="BC43BF00" data-ipaddr="<?echo $ip_address;?>" class="power button ir-code">Power on/off</a>
                    </li>
                    <li>
                        <a href="#" data-ircode="AC53BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">Volume- <span class="glyphicon glyphicon-volume-down"></a>
                    </li>
                    <li>
                        <a href="#" data-ircode="B946BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">Mute <span class="glyphicon glyphicon-volume-off"></a>
                    </li>
                    <li>
                        <a href="#" data-ircode="AD52BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">Volume+ <span class="glyphicon glyphicon-volume-up"></a>
                    </li>
                    <li>
                        <a href="#" data-ircode="E619BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">Stop <span class="glyphicon glyphicon-stop"></a>
                    </li>
                    <li>
                        <a href="#" data-ircode="9F60BF00" data-ipaddr="<?echo $ip_address;?>" class="record button ir-code">Record <span class="glyphicon glyphicon-record"></a>
                    </li>
                    <li>
                        <a class="signup button" href = "javascript:void(0)" onclick = "document.getElementById('envelope').style.display='block';">Add Radio station <span class="glyphicon glyphicon-plus"></a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="envelope" class="envelope">
            <a class="close-btn" title="Закрыть" href="javascript:void(0)" onclick = "document.getElementById('envelope').style.display='none';"></a>
            <div class="titlestation">Add Radio station</div>
            <form method="post" action="send.php">
                <input type="text" name="path" onclick="this.value='';" onfocus="this.select()" onblur="this.value=!this.value?'* Station Path':this.value;" value="* Station Path" class="station-path" required />
                <input type="text" name="name" onclick="this.value='';" onfocus="this.select()" onblur="this.value=!this.value?'* Station Name':this.value;" value="* Station Name" class="station-name" required />
                <input type="text" name="image" onclick="this.value='';" onfocus="this.select()" onblur="this.value=!this.value?'* Image Path':this.value;" value="* Image Path" class="image-path" required />
                <input type="text" name="info" onclick="this.value='';" onfocus="this.select()" onblur="this.value=!this.value?'* Station Description':this.value;" value="* Station Description" class="station-info" required />
                <input type="submit" name="send" value="SEND" class="send-station">
            </form>
    </div>

    <div id="root">
        <div id="full-width-region" class="content-area full-width channel-browser <?echo $mode_ground;?>">
            <section>
                <h2 class="section-title">Radio Channels</h2>
                <div class="channel-list no-touch">
                    <ul>
                        <?echo $item;?>
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <div class="footer">
        <div class="upfotter">
            <div class="tappercroping">
                <div class="tapperform">
                    <span class="glyphicon glyphicon-chevron-up"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <nav class="button-dune-controll">
                <ul>
                    <li>
                        <a href="#" data-ircode="AC53BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">
                            <span class="glyphicon glyphicon-volume-down"></span>-</a>
                    </li>
                    <li>
                        <a href="#" data-ircode="B946BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">
                            <span class="glyphicon glyphicon-volume-off"></span></a>
                    </li>
                    <li>
                        <a href="#" data-ircode="AD52BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">
                            <span class="glyphicon glyphicon-volume-up"></span>+</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row">
            <nav class="button-dune-controll">
                <ul>
                    <li>
                        <a href="#" data-ircode="E619BF00" data-ipaddr="<?echo $ip_address;?>" class="signup button ir-code">
                            <span class="glyphicon glyphicon-stop"></span></a>
                    </li>
                    <li>
                        <a href="#" data-ircode="9F60BF00" data-ipaddr="<?echo $ip_address;?>" class="record button ir-code">
                            <span class="glyphicon glyphicon-record"></span></a>
                    </li>
                    <li>
                        <a class="signup button" href="javascript:void(0)" onclick="document.getElementById('envelope').style.display='block';document.getElementById('fade').style.display='block'"><span class="glyphicon glyphicon-plus"></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</body>
</html>