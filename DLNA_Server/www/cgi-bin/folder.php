<?
require_once "LogWebInterface.php";

require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/settings.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/media_url.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/fs_storages.php";

header('Content-Type: application/json');


if(isset($_POST['command']) && isset($_POST['value'])) {
    
    $settings = new Settings;
    
    $settings->init();
    
    if($_POST['command'] === 'dell_folder') {
        $input_media_url = $_POST['value'];
        echo json_encode(deleteFolder($input_media_url, $settings));
    }
    
    if($_POST['command'] === 'add_folder') {
        $input_media_url = $_POST['value'];
        echo json_encode(addFolder($input_media_url, $settings));
    }
    
    if($_POST['command'] === 'get_folders_from_this_path') {
        $input_media_url = $_POST['value'];
        echo json_encode(getFoldersFromThisPath($input_media_url));
    }
}

function deleteFolder($input_media_url, $settings) 
{
    $result = array('status' => 'error', 'error' => '', 'folders' => HD::get_items('folder'), 'media_dir' => $settings->getMedia_dir());
    
    if(array_key_exists($input_media_url, HD::get_items('folder'))) {
        $saved_items = HD::get_items('folder');
        unset($saved_items[$input_media_url]);
        HD::save_items('folder', $saved_items);
    }
    
    if(array_search($input_media_url, $settings->getMedia_dir()) !== false) {
        $array = $settings->getMedia_dir();
        $keys = array_keys($array, $input_media_url);
        foreach($keys as $key) {
            unset($array[$key]);
        }
        $settings->setMedia_dir($array);
    }
    
    if(!array_key_exists($input_media_url, HD::get_items('folder')) && array_search($input_media_url, $settings->getMedia_dir()) === false) {
            $result = array('status' => 'ok', 'error' => '', 'folders' => HD::get_items('folder'), 'media_dir' => $settings->getMedia_dir());
        }
    return $result;
}

function addFolder($input_media_url, $settings)
{
    $result = array('status' => 'error', 'error' => '', 'folders' => HD::get_items('folder'), 'media_dir' => $settings->getMedia_dir(), 'resurs' => null, 'added' => false);
    $fs = FS::get_instance();
    $fs->do_reload_storage_list(true);
    $added = false;
    $prepared = false;
    foreach($fs->storages as $storage) {
        if (!empty($storage->mount_path)) {
            if ($storage->mount_path === $input_media_url) {
                $resurs = array(
                                'name' => $storage->name,
                                'path' => $storage->mount_path,
                                'kind' => $storage->kind,
                                'id' => $storage->id,
                                'caption' => $storage->name
                                );
                $prepared = true;
            } elseif (strpos($input_media_url, $storage->mount_path) === 0) {
                $resurs = array(
                                'name' => HD::basename($input_media_url),
                                'path' => $input_media_url,
                                'kind' => 'folder',
                                'id' => $storage->id,
                                'caption' => basename($input_media_url)
                                );
                $prepared = true;
            }
        }
    }

    
    if(!array_key_exists($input_media_url, HD::get_items('folder')) && $prepared === true) {
        $saved_items = HD::get_items('folder');
        $saved_items[$input_media_url] = $resurs;
        HD::save_items('folder', $saved_items);
    }
    
    if(array_search($input_media_url, $settings->getMedia_dir()) === false && array_key_exists($input_media_url, HD::get_items('folder'))) {
        $array = $settings->getMedia_dir();
        $array[] = $input_media_url;
        $settings->setMedia_dir($array);
        $added = true;
    }
    
    if(array_key_exists($input_media_url, HD::get_items('folder')) && array_search($input_media_url, $settings->getMedia_dir()) !== false) {
        $result = array('status' => 'ok', 'error' => '', 'folders' => HD::get_items('folder'), 'media_dir' => $settings->getMedia_dir(), 'resurs' => $resurs, 'added' => $added);        
    }
    return $result;   
}

function getFoldersFromThisPath($input_media_url)
{
    $fs = FS::get_instance();
    $fs->do_reload_storage_list(true);
    $resurses = array();
    
    if ($input_media_url === "") {
        foreach($fs->storages as $storage) {
            if ($storage->kind !== 'usb' && $storage->kind !== 'tangox_flash_card') {
                $icon = 'hdd';
            } elseif ($storage->kind === 'tangox_flash_card') {
                $icon = 'sd_card';
            } else {
                $icon = $storage->kind;
            }
            $resurses[] = array(
                            'resurs_name' => $storage->name,
                            'resurs_path' => $storage->mount_path,
                            'resurs_icon' => $icon
                          );
        }
    } else {
        $folders = scandir($input_media_url);
        if ($folders) {
            foreach ($folders as $folder) {
                if ($folder == '.' || $folder == '..') {
                    continue;
                }

                if (is_dir($input_media_url . '/' . $folder)) {
                    $folder_path = $input_media_url . '/' . $folder;
                    $folder_name = HD::basename($folder);
                    
                    $resurses[] = array(
                                    'resurs_name' => $folder_name,
                                    'resurs_path' => $folder_path,
                                    'resurs_icon' => 'folder'
                                );
                }           
            }
        }
    }
    $result = array('status' => 'ok', 'error' => '', 'resurses' => $resurses);
    return $result;
}
?>