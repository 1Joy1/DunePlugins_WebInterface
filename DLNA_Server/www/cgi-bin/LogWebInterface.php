<?
require_once "do_config.php";

if ($logfile = LogWebInterface::getLogFile()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('display_startur_errors', 0);
    
    ini_set('log_errors', 'On');
    ini_set('error_log', $logfile);
}

function hd_print($mess)
{
    LogWebInterface::printLog($mess);
}

class LogWebInterface
{

    const PATH_SEARCH_DISK = "/tmp/mnt/storage";
    
    const LOG_FOLDER = "dune_plugin_logs";


    public static function getLogFile()
    {
        $log_file_name = DuneSystem::$properties['plugin_name'] . "_web_interface.log";
        $mount_storages = scandir(self::PATH_SEARCH_DISK);
        
        if (is_array($mount_storages)) {        
            foreach ($mount_storages as $folder) {
            
                if ($folder == '.' || $folder == '..') {
                    continue;
                }
                
                if (is_dir(self::PATH_SEARCH_DISK . '/' . $folder)) {
                    $inner_folders = scandir(self::PATH_SEARCH_DISK . '/' . $folder);

                    if (is_array($inner_folders)) {
                        if (array_search(self::LOG_FOLDER, $inner_folders) !== false) {
                            if (file_exists(self::PATH_SEARCH_DISK . '/' . $folder . '/' . self::LOG_FOLDER . '/' . $log_file_name)) {
                                if (filesize(self::PATH_SEARCH_DISK . '/' . $folder . '/' . self::LOG_FOLDER . '/' . $log_file_name) > 1048576) {
                                    file_put_contents(self::PATH_SEARCH_DISK . '/' . $folder . '/' . self::LOG_FOLDER . '/' . $log_file_name, '');
                                }
                            }
                            return self::PATH_SEARCH_DISK . '/' . $folder . '/' . self::LOG_FOLDER . '/' . $log_file_name;
                        }
                    }
                }
            }
        }
        return false;
    }
    
    public static function nowTime($fmt = null)
    {
        $ts = time();
        
        if (is_null($fmt))
            $fmt = 'd-M:Y H:i:s';

        $dt = new DateTime('@' . $ts);
        return $dt->format($fmt);
    }
    
    
    public static function printLog($str)
    {
        $path = self::getLogFile();
        
        if ($path) {
            $str = '[' . self::nowTime() . '] WEB INTERFACE: ' . $str;
            file_put_contents($path, $str . "\n", FILE_APPEND);
        }
    }
}
?>