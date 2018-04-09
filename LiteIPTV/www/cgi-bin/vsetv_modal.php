<?
error_reporting (E_ALL);
require_once "do_config.php";
require_once DuneSystem::$properties['install_dir_path']."/lib/utils.php";
require_once DuneSystem::$properties['install_dir_path']."/Smart_config.php";

$channels = array_merge(
	HD::get_items('vsetv_channels'),
	HD::get_items('vsetv_list')
);
$channels_id_parsed = array_merge(
	HD::get_items('vsetv_channels_id'),
	HD::get_items('my_channels_id')
);
$id_key = $_POST['id_key'];
$_id = array_key_exists($id_key,$channels_id_parsed) ? $channels_id_parsed[$id_key] : $id_key;
$id = str_replace('_HD', "", $_id);
$caption = $_POST['caption'];
$b = mb_substr($caption ,0,1,"UTF-8");
$search = mb_strtolower($caption, 'UTF-8');
$search = str_replace(array('нтв+','(a la carte)','(твоё тв)','(на модерации)','нтв плюс'),	'', $search);
if (preg_match("/\s/",$search)){
	$tmp = explode(" ", $search);
	if((iconv_strlen($tmp[0], "UTF-8")) >= 3)
		$search=$tmp[0];
	else
		$search=$tmp[1];
}
$ch_ops = array();
$ch_ops2 = array();
$ch_ops3 = array();

$search = preg_quote($search);
foreach($channels as $k => $v){
	$ch_ops[$v] = "$k => ". $v;
	if (preg_match("|$search|i",mb_strtolower($k, 'UTF-8')))
		$ch_ops2[$v] = "$k => ". $v;
	if (preg_match("|^$b|i", $k))
		$ch_ops3[$v] = "$k => ". $v;
}


if (mb_strlen($_id,'UTF-8') == 32)
	$header = $caption;
else
	$header = "$caption (vsetvID: $_id)";
echo '<a href = "http://www.vsetv.com/channels.html" target = "_blank">vsetvID co vsetv.com</a>';
echo "<table border cellspacing='0'><tr><td align='center' colspan='2'>$header</td></tr></thead><tbody>";
echo '<tr><td>HD/SD версия канала: </td><td>Да <input id="modal_hd_sd" type="checkbox"></td></tr>';
echo '<tr><td>Похожие: </td><td>
			<select id="modal_similar" class="form-control" style="width:100%">';
			$selected = !isset($ch_ops2[$id]) ? "selected" : "";
			echo '<option value="0"; ' . $selected . '>Выберите vsetvID</option>';
			foreach ($ch_ops2 as $k => $v){
				if ($k == $id)
					echo '<option value="'.$k.'" selected>'.$v.'</option>';
				else
					echo '<option value="'.$k.'">'.$v.'</option>';
			}
		echo '</select>
</td></tr>';
echo '<tr><td>Первая буква: </td><td>

			<select id="modal_fist_letter" class="form-control" style="width:100%">';

			$selected = !isset($ch_ops3[$id]) ? "selected" : "";
			echo '<option value="0"; ' . $selected . '>Выберите vsetvID</option>';

			foreach ($ch_ops3 as $k => $v){
				if ($k == $id)
					echo '<option value="'.$k.'" selected>'.$v.'</option>';
				else
					echo '<option value="'.$k.'">'.$v.'</option>';
			}
		echo '</select>
</td></tr>';
echo '<tr><td>Все : </td><td>

			<select class="selectpicker" id="modal_all_list_ch" data-live-search="true">';

			$selected = !isset($ch_ops[$id]) ? "selected" : "";
			echo '<option value="0"; ' . $selected . '>Выберите vsetvID</option>';

			foreach ($ch_ops as $k => $v){
				if ($k == $id)
					echo '<option value="'.$k.'" selected>'.$v.'</option>';
				else
					echo '<option value="'.$k.'">'.$v.'</option>';
			}
		echo '</select>
</td></tr>';

echo '</tbody></table>';

echo '<form id="modal_set_vsetv_id_form" style="margin-top:20px">
        <input type="hidden" name="id_key" value = "'.$id_key. '">
        <input type="hidden" name="vsetvID" value = "'.$id. '">
        <input type="hidden" name="action" value = "save_vsetvid">
        <div id="modal_fin_selected" style="font-weight:bold; display:none"></div>
		<input class="btn btn-success" type="submit" value="Сохранить">
		</form>';
?>