<?
require_once "do_config.php";
if (isset($_GET["img"])){
	if (preg_match('|\.png|i',$_GET["img"]))
		header('Content-Type: image/png');
	else
		header('Content-Type: image/jpeg');
	echo file_get_contents($_GET["img"]);
}