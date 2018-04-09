<?php


function
safely_get_value_of_global_variables ($name, $key)
{
  return (isset ($name[$key]) ) ? ($name[$key]) : ('');
}

class DuneSystem
{
  public static $properties = array ();
};
DuneSystem::$properties['plugin_name']      = safely_get_value_of_global_variables ($_ENV, 'PLUGIN_NAME');
DuneSystem::$properties['install_dir_path'] = safely_get_value_of_global_variables ($_ENV, 'PLUGIN_INSTALL_DIR_PATH');
DuneSystem::$properties['tmp_dir_path']     = safely_get_value_of_global_variables ($_ENV, 'PLUGIN_TMP_DIR_PATH');
DuneSystem::$properties['plugin_www_url']   = safely_get_value_of_global_variables ($_ENV, 'PLUGIN_WWW_URL');
DuneSystem::$properties['plugin_cgi_url']   = safely_get_value_of_global_variables ($_ENV, 'PLUGIN_CGI_URL');
DuneSystem::$properties['data_dir_path']    = safely_get_value_of_global_variables ($_ENV, 'PLUGIN_DATA_DIR_PATH');

$PLAYLIST_DIR = DuneSystem::$properties['data_dir_path'] . '/playlists/';

class do_config
{
    const PROTOCOL_VERSION = 5;
    public static $ROOT_DIR = "/tmp/mnt/storage/";
    public static $PLAYLIST_DIR = '/flashdata/playlists/';
}
do_config::$PLAYLIST_DIR = DuneSystem::$properties['data_dir_path'] . '/playlists/';
class HD
{
	public static function http_get_document($url, $opts = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 	0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    25);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,    1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_TIMEOUT,           25);
        curl_setopt($ch, CURLOPT_USERAGENT,         "Mozilla/5.0 (Windows NT 6.1; rv:25.0) Gecko/20100101 Firefox/25.0");
		curl_setopt($ch, CURLOPT_ENCODING,          1);
        curl_setopt($ch, CURLOPT_URL,               $url);
        if (isset($opts))
        {
            foreach ($opts as $k => $v)
                curl_setopt($ch, $k, $v);
        }
        $content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $content;
    }
	public static function get_data_path() {
		static $link = null;
		if (is_null($link)){
			$link = DuneSystem::$properties['data_dir_path'];
			if (file_exists($link . '/data_dir_path'))
				$link = smbtree::get_folder_info ('data_dir_path');
		}
		return $link;
	}
	public static function get_items($path) {
		$items = array();
		if ($path=='data_dir_path')
			$link = DuneSystem::$properties['data_dir_path'] . '/'. $path;
		else
			$link = self::get_data_path() . '/'. $path;
		if (file_exists($link))
			$items = unserialize(file_get_contents($link));
		return $items;
	}
	
	public static function save_items($path, $items){
		if ($path=='data_dir_path')
			$link = DuneSystem::$properties['data_dir_path'] . '/'. $path;
		else
			$link = self::get_data_path() . '/'. $path;
		file_put_contents ($link, serialize($items));
	}
	
	public static function save_items_tmp($path, $items) {
		$link = DuneSystem::$properties['tmp_dir_path']. '/' .$path;
		file_put_contents ($link, serialize($items));
	}
	
	public static function get_items_tmp($path) {
		$items = array();
		$data_path = DuneSystem::$properties['tmp_dir_path']. '/' .$path;
		if (file_exists($data_path))
			$items = unserialize(file_get_contents($data_path));
		return $items;
	}
	
	public static function save_item($path, $item) {
		$link = self::get_data_path() . '/'. $path;
		file_put_contents ($link, $item);
	}
	
	public static function get_item($path){
		$item = '';
		$link = self::get_data_path() . '/'. $path;
		if (file_exists($link))
			$item = file_get_contents($link);
		return $item;
	}
	
	public static function save_item_tmp($path, $item) {
		$link = DuneSystem::$properties['tmp_dir_path'] . '/'. $path;
		file_put_contents ($link, $item);
	}
	public static function get_item_tmp($path) {
		$item = '';
		$link = DuneSystem::$properties['tmp_dir_path'] . '/'. $path;
		if (file_exists($link))
			$item = file_get_contents($link);
		return $item;
	}
	public static function get_ip_address()
    {
        static $ip_address = null;
        
        if (is_null($ip_address))
        {
            preg_match_all('/inet'.(false ? '6?' : '').' addr: ?([^ ]+)/', `ifconfig`, $ips);
			if ($ips[1][0]!= '127.0.0.1')
				$ip_address = $ips[1][0];
			else if ($ips[1][1]!= '127.0.0.1')
				$ip_address = $ips[1][1];
        }
        
        return $ip_address;
    }
}
class smbtree
{
  private $cmd            = 'smbtree';
  private $descriptorspec = array ();
  private $cwd            = '/';
  private $env            = array ();
  private $smbtree_output = '';
  private $return_value   = 0;
  private $no_pass        = true;
  private $debuglevel     = 0;

  public function __construct ()
  {
    $this->cmd = '/tango/firmware/bin/smbtree ';
    $this->cwd = '/tmp';

    $this->descriptorspec = array
      (
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
      );
    $this->env = array ('LD_LIBRARY_PATH' => '/tango/firmware/lib');

    $no_pass = true;
  }
  
  private function get_auth_options ()
  {
    if ($this->is_no_pass ())
      return '-N';

    return '';
  }

  private function get_debug_level ()
  {
    return '--debuglevel ' . $this->debuglevel;
  }

  private function is_no_pass ()
  {
    return $this->no_pass;
  }

  /*
   * @return 0 if success
   */
  private function execute ($args = '')
  {
    $process = proc_open ($this->cmd
      . $this->get_auth_options () . ' '
      . $this->get_debug_level () . ' '
      . $args,
      $this->descriptorspec, $pipes, $this->cwd, $this->env);

    if (is_resource ($process))
    {
      //fclose ($pipes[0]);
      
      $this->smbtree_output = stream_get_contents ($pipes[1]);
      fclose ($pipes[1]);
      fclose ($pipes[2]);
    
      // Важно закрывать все каналы перед вызовом
      // proc_close во избежание мертвой блокировки
      $this->return_value = proc_close ($process);
    }

    return $this->return_value;
  }

  private function parse_smbtree_output ($input_lines)
  {
    $output = array ();

    if (!strlen ($input_lines))
      return array ();

    $output_lines = explode ("\n", $input_lines);
    if ($output_lines == false)
      return array ();

    foreach ($output_lines as $line)
    {
      if (strlen ($line))
      {
        $detail_info = explode ("\t", $line);

        if (count ($detail_info))
        {
		  $q = isset($detail_info[1]) ? $detail_info[1] : '';
		  $output[$detail_info[0]] = array
            (
              'name'     => $detail_info[0],
              'comment'  => $q,
            );
        }
      }
    }

    return $output;
  }

  public function get_xdomains ()
  {
    if ($this->execute ('--xdomains') != 0)
      return array ();

    return $this->parse_smbtree_output ($this->smbtree_output);
  }

  public function get_domains ()
  {
    if ($this->execute ('--domains') != 0)
      return array ();

    return $this->parse_smbtree_output ($this->smbtree_output);
  }

  public function get_workgroup_servers ($domain)
  {
    if ($this->execute ('--workgroup-servers=' . $domain) != 0)
      return array ();

    return $this->parse_smbtree_output ($this->smbtree_output);
  }

  public function get_server_shares ($server)
  {
    if ($this->execute ('--server-shares=' . $server) != 0)
      return array ();

    return $this->parse_smbtree_output ($this->smbtree_output);
  }
  public static function get_df_smb ()
  {
	$df_smb = array();
	$df_smb_exec = shell_exec ("df |grep /tmp/mnt/smb");
	preg_match_all('|(.*?) .*\%\s/tmp/mnt/smb/(.*)|', $df_smb_exec, $match);
	foreach ($match[2] as $k => $v)
		$df_smb[$match[1][$k]]=$v;
	return $df_smb;
  }
  public function get_network_folder_smb ()
  {
	$d = array();
	$network = parse_ini_file('/config/network_folders.properties',true);
	foreach ($network as $k => $v){
	preg_match("|(.*)\.(.*)|", $k, $match);
	$network_folder[$match[2]][$match[1]] = $v;
	}
	foreach ($network_folder as $k => $v){
		if ($v['type']==0){
			$dd['foldername'] =$v['name'];
			if ($v['user'] != '')
			$dd['user'] =$v['user'];
			if ($v['password'] != '')
			$dd['password'] =$v['password'];
			$d[$v['server']][$v['directory']] = $dd;
		}
	}
	return $d;
  }
  public function get_server_shares_smb ()
  {
	$d=array();
	$data = self::get_xdomains ();
	foreach ($data as $domain){
		$data = self::get_workgroup_servers ($domain['name']);
		 foreach ($data as $shares)
		 $d[$shares['name']] = self::get_server_shares ($shares['name']);
	}
	return $d;
  }
  
    public function get_ip_network_folder_smb ()
  {
	$d=array();
	$network_folder_smb = self::get_network_folder_smb ();
	foreach ($network_folder_smb as $k => $v)
	{
		if (!preg_match('@((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)@', $k)){
			$out = shell_exec ('export LD_LIBRARY_PATH=/firmware/lib:$LD_LIBRARY_PATH&&/firmware/bin/nmblookup '.$k.' -S');
			if (preg_match('/(.*) (.*)<00>/', $out, $matches)){
				$ip = '//'. $matches[1] . '/';
				if ($matches[2] == $k)
					foreach ($v as $key => $vel){
						$d[$ip.$key] = $vel;
					}
			}
		}else{
			foreach ($v as $key => $vel){
						$d['//'.$k.'/'.$key] = $vel;
					}
		}
	}
	return $d;
  }
  
  public function get_ip_server_shares_smb ()
  {
	$d=array();
	$my_ip = HD::get_ip_address();
	$server_shares_smb = self::get_server_shares_smb ();
	foreach ($server_shares_smb as $k => $v)
	{
		$out = shell_exec ('export LD_LIBRARY_PATH=/firmware/lib:$LD_LIBRARY_PATH&&/firmware/bin/nmblookup '.$k.' -R');
		if (preg_match('/(.*) (.*)<00>/', $out, $matches)){
		if ($my_ip == $matches[1])
			continue;
		$ip = '//'. $matches[1] . '/';
		if ($matches[2] == $k)
			foreach ($v as $key => $vel){
				$vel['foldername'] = $key . ' in ' . $k;
				$d[$ip.$key] = $vel;
			}
		}
	}
	return $d;
  }
  
  public static function get_mount_smb ($ip_smb)
  {
	$d = array();
	foreach ($ip_smb as $k => $vel){
		$df_smb = self::get_df_smb ();
		if (isset($df_smb[$k])){
			$d['/tmp/mnt/smb/'.$df_smb[$k]]['foldername'] = $vel['foldername'];
			$d['/tmp/mnt/smb/'.$df_smb[$k]]['ip'] = $k;
			if ((isset($vel['user']))&&($vel['user'] != ''))
				$d['/tmp/mnt/smb/'.$df_smb[$k]]['user'] = $vel['user'];
			if ((isset($vel['user']))&&($vel['password'] != ''))
				$d['/tmp/mnt/smb/'.$df_smb[$k]]['password'] = $vel['password'];
		}else{
			$q = false;
			if (count($df_smb)>0)
				$n = count($df_smb);
			else $n = 0;
			$fn = '/tmp/mnt/smb/'. $n;
			if (!file_exists($fn)){
				mkdir($fn,0777,true);
			}
				$username=$password='guest';
				if ((isset($vel['user']))&&($vel['user'] != ''))
					$username=$vel['user'];
				if ((isset($vel['user']))&&($vel['password'] != ''))
					$password=$vel['password'];
				$q = exec("mount -t cifs -o username=$username,password=$password,posixpaths,rsize=32768,wsize=130048 $k \"$fn\" 2>&1 &");
			if ($q==true){
				$d['err_'.$vel['foldername']]['foldername'] = $vel['foldername'];
				$d['err_'.$vel['foldername']]['ip'] = $k;
				$d['err_'.$vel['foldername']]['err'] = trim($q);
				if ((isset($vel['user']))&&($vel['user'] != ''))
					$d['err_'.$vel['foldername']]['user'] = $vel['user'];
				if ((isset($vel['user']))&&($vel['password'] != ''))
					$d['err_'.$vel['foldername']]['password'] = $vel['password'];
			}else{
				$d[$fn]['foldername'] = $vel['foldername'];
				$d[$fn]['ip'] = $k;
				if ((isset($vel['user']))&&($vel['user'] != ''))
					$d[$fn]['user'] = $vel['user'];
				if ((isset($vel['user']))&&($vel['password'] != ''))
					$d[$fn]['password'] = $vel['password'];
			}
		}
			
	}
	return $d;
  }
  public function get_smb_infs ($item=false)
  {
	$q = 1;
	$path = DuneSystem::$properties['data_dir_path'].'/smb_setup';
	if ($item==false){
	if (file_exists($path))
		$q = file_get_contents($path);
	}else{
		$data = fopen($path,"w");
		if (!$data)
		return ActionFactory::show_title_dialog("Не могу записать items Что-то здесь не так!!!");
		fwrite($data, $item);
		@fclose($data);
		$q =  $item;
	}
	return $q;
  }
 public function get_mount_all_smb ()
  {
	$q = self::get_smb_infs ();
	if ($q!=3)
		$a = self::get_mount_smb (self::get_ip_network_folder_smb());
	if ($q==1)
		return $a;
	$b = self::get_mount_smb (self::get_ip_server_shares_smb());
	if ($q==3)
		return $b;
	return array_merge($b,$a);	
  }
  public function get_network_folder_nfs ()
  {
	$d = array();
	$network = parse_ini_file('/config/network_folders.properties',true);
	foreach ($network as $k => $v){
	preg_match("|(.*)\.(.*)|", $k, $match);
	$network_folder[$match[2]][$match[1]] = $v;
	}
	foreach ($network_folder as $k => $v){
		if ($v['type']==1){
			$p = 'udp';
			if ($v['protocol']==1)
				$p = 'tcp';
			$nfs[$v['server'].':'.$v['directory']]['foldername'] = $v['name'];
			$nfs[$v['server'].':'.$v['directory']]['protocol'] =$p;
		}
	}
	return $nfs;
  }
  public function get_df_nfs ()
  {
	$df_nfs = array();
	$df_nfs_exec = shell_exec ("mount |grep /tmp/mnt/network");
	preg_match_all('|(.*?)\son\s/tmp/mnt/network/(.*?)\s|', $df_nfs_exec, $match);
	foreach ($match[2] as $k => $v)
		$df_nfs[$match[1][$k]]=$v;
	return $df_nfs;
  }
  public function get_mount_nfs ()
  {
	$d = array();
	$ip_nfs = self::get_network_folder_nfs ();
	foreach ($ip_nfs as $k => $vel){
		$df_nfs = self::get_df_nfs ();
		if (isset($df_nfs[$k])){
			$d['/tmp/mnt/network/'.$df_nfs[$k]]['foldername'] = $vel['foldername'];
			$d['/tmp/mnt/network/'.$df_nfs[$k]]['ip'] = $k;
			$d['/tmp/mnt/network/'.$df_nfs[$k]]['protocol'] = $vel['protocol'];
		}else{
			$q = false;
			if (count($df_nfs)>0)
				$n = count($df_nfs);
			else $n = 0;
			$fn = '/tmp/mnt/network/'. $n;
			if (!file_exists($fn)){
				mkdir($fn,0777,true);
			}
			$q = shell_exec("mount -t nfs -o ".$vel['protocol']." $k $fn 2>&1");
			if ($q==true){
				$d['err_'.$vel['foldername']]['foldername'] = $vel['foldername'];
				$d['err_'.$vel['foldername']]['ip'] = $k;
				$d['err_'.$vel['foldername']]['protocol'] = $vel['protocol'];
				$d['err_'.$vel['foldername']]['err'] = trim($q);
			}else{
				$d[$fn]['foldername'] = $vel['foldername'];
				$d[$fn]['ip'] = $k;
				$d[$fn]['protocol'] = $vel['protocol'];
			}
		}
			
	}
	return $d;
  }
  public static function get_folder_info ($param)
  {
	$select_folder = false;
	if (file_exists(DuneSystem::$properties['data_dir_path'] .'/'. $param)){
		$save_folder = unserialize(file_get_contents(DuneSystem::$properties['data_dir_path'] .'/'. $param));
		if (isset($save_folder['filepath']))
			$select_folder = $save_folder['filepath'];
		else
			foreach ($save_folder as  $k => $v){
				if ((isset($v['foldername']))&&(isset($v['user'])))
					$q = self::get_mount_smb ($save_folder);
				if ((isset($v['foldername']))&&(!isset($v['user'])))
					$q = self::get_mount_nfs ();
				$select_folder = key($q).$v['foldername'];
			}
	}
	return $select_folder;
  }
  public static function get_bug_platform_kind()
	{
		static $bug_platform_kind = null;
		
		if (is_null($bug_platform_kind))
		{
			$arr = file('/tmp/run/versions.txt'); $v = '';
			foreach($arr as $line)
			{
				if ( stristr($line, 'platform_kind=') )
				{
					$v = trim( substr($line, 14) );
				}
			}
			$platform_kind = $v;
			if (($platform_kind =='8672')||($platform_kind =='8673')||($platform_kind =='8758'))
				$bug_platform_kind = true;
			else
				$bug_platform_kind = false;
			
		}
		return $bug_platform_kind;
	}
}