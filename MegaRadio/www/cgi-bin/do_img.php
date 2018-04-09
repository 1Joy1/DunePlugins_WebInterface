<?
require_once "do_config.php";
if (isset($_GET["img"])){
	if (preg_match('|\.png|i',$_GET["img"]))
		header('Content-Type: image/png');
	else
		header('Content-Type: image/jpeg');
	$doc = HD::http_get_document($_GET["img"]);
	// if ((preg_match("|GIF89a4|",$doc))||($doc == false))
		// $doc =  file_get_contents(DuneSystem::$properties['install_dir_path'] . '/icons/poster_none.png');
	echo $doc;
}