<?
require_once "LogWebInterface.php";

require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/settings.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";

header('Content-Type: application/json');

if(isset($_GET['command']) && $_GET['command'] === 'get_settings') {
    $settings = new Settings;
    
    $settings->init();
    
    $current_settings = array();
    $current_settings['friendly_name'] = $settings->getFriendly_name();
    $current_settings['inotify'] = $settings->getInotify();
    $current_settings['strict_dlna'] = $settings->getStrict_dlna();
    $current_settings['log_level'] = $settings->getLog_level();
    
    $server_on = HD::checkOnline();
    
    $autostart = HD::get_item('autostart');
    
    if ($server_on) {
        $dlna_web_status = HD::get_dlna_web_status();
    } else {
        $dlna_web_status = null;
    }
    
    $result = array('status' => 'ok', 'server_on' => $server_on, 'current_settings' => $current_settings, 'dlna_web_status' => $dlna_web_status, 'autostart' => $autostart);
    
    echo json_encode($result);
}

if(isset($_POST['command']) && isset($_POST['value'])) {

    $settings = new Settings;
    
    $settings->init();
    
    if($_POST['command'] === 'set_off_on' && ($_POST['value'] === 'true' || $_POST['value'] === 'false')) {
        echo json_encode(setPowerServer($_POST['value'], $settings));
    }
    
    if($_POST['command'] === 'set_autostart' && ($_POST['value'] === 'yes' || $_POST['value'] === 'no')) {
        echo json_encode(setAutostart($_POST['value'], $settings));
    }

    if($_POST['command'] === 'set_inotify' && ($_POST['value'] === 'yes' || $_POST['value'] === 'no')) {
        echo json_encode(setInotify($_POST['value'], $settings));
    }
    
    if($_POST['command'] === 'set_dlna_standart' && ($_POST['value'] === 'yes' || $_POST['value'] === 'no')) {
        echo json_encode(setStrictDlna($_POST['value'], $settings));
    }
    
    if($_POST['command'] === 'set_loging' && ($_POST['value'] === 'on' || $_POST['value'] === 'off')) {
        echo json_encode(setLogLevel($_POST['value'], $settings));
    }
}

if(isset($_POST['command']) && $_POST['command'] === 'rescan_db') {
    echo json_encode(rescanDB());    
}

function rescanDB()
{
    $cmd = DuneSystem::$properties['install_dir_path'] . '/bin/ctl.sh force-restart';
    
    if ($cmd) {
        $state = shell_exec($cmd);
        
        if(strpos($state, 'Starting minidlna: OK')) {
            $result = array('status' => 'ok', 'error' => '',);
        } else {
            $result = array('status' => 'error', 'error' => $state,);
        }
    } else {
        $result = array('status' => 'error', 'error' => 'Server is already in the requested state.',);
    }
    
    return $result;
}

function setPowerServer($value, $settings) 
{
    $curent_state = HD::checkOnline() ? 'true' : 'false';
    $cmd = null;
    
    if ($value === 'true' && $curent_state === 'false') {
        $cmd = DuneSystem::$properties['install_dir_path'] . '/bin/ctl.sh start';
    }
    
    if ($value === 'false' && $curent_state === 'true') {
        $cmd = DuneSystem::$properties['install_dir_path'] . '/bin/ctl.sh stop';
    }
    
    if ($cmd) {
        $state = shell_exec($cmd);
        if(strpos($state, 'OK')) {
            $result = array('status' => 'ok', 'error' => '', 'value' => $value,);
        } else {
            $result = array('status' => 'error', 'error' => $state, 'value' => $value,);
        }
    } else {
        $result = array('status' => 'error', 'error' => 'Server is already in the requested state.', 'value' => $value,);
    }
    
    return $result;
}

function setAutostart($value, $settings) 
{
    HD::save_item('autostart' , $value);
    
    if(HD::get_item('autostart') == $value) {
        $result = array('status' => 'ok', 'error' => '', 'value' => $value,);
    } else {
        $result = array('status' => 'error', 'error' => '', 'value' => $value,);
    }
    
    return $result;
}

function setInotify($value, $settings) 
{
    $settings->setInotify($value);
    
    if($settings->getInotify() == $value) {
        $result = array('status' => 'ok', 'error' => '', 'value' => $value,);
    } else {
        $result = array('status' => 'error', 'error' => '', 'value' => $value,);
    }
    
    return $result;
}

function setStrictDlna($value, $settings) 
{
    $settings->setStrict_dlna($value);
    
    if($settings->getStrict_dlna() == $value) {
        $result = array('status' => 'ok', 'error' => '', 'value' => $value,);
    } else {
        $result = array('status' => 'error', 'error' => '', 'value' => $value,);
    }
    
    return $result;
}

function setLogLevel($value, $settings) 
{
    $settings->setLog_level($value);
    
    if($settings->getLog_level() == $value) {
        $result = array('status' => 'ok', 'error' => '', 'value' => $value,);
    } else {
        $result = array('status' => 'error', 'error' => '', 'value' => $value,);
    }
    
    return $result;
}
?>