<?php
if (isset($_GET['action']) and !empty($_GET['action'])) {
	
	if ($_GET['action'] =="insert") {

		$_POST = $db->sanitizer($_POST);
		$continue = TRUE;
		foreach ($_POST as $key => $value) {
			if (empty($value) AND $value ) {
				$db->save_message("Der mangler tekst");
				$continue = FALSE;
			}
		}	
		if($continue){
			if(!$db->query("INSERT INTO `nyhed` (`nyhed_overskrift`, `nyhed_tekst`, `nyhed_dato`) VALUES ('".$_POST['nyt_noverskrift']."','".$_POST['nyt_indhold']."', NOW()) ")){
				$db->save_message("Der skete en fejl; prøv igen!");
				$db->save_forminput();
				header('location: index.php?side='.$_GET["side"].'&type='.$type.'');
				exit();
			};
			
					
			$db->save_message("Nyhed tilføjet!");
		}
		
	}elseif($_GET['action'] =="update") {
		$_POST = $db->sanitizer($_POST);
		$continue = TRUE;
		foreach ($_POST as $key => $value) {
			if (empty($value) AND $value ) {
				$db->save_message("Der mangler tekst");
				$continue = FALSE;
			}
		}
		$start_dato = $_POST['start_aar']."-".$_POST['start_maaned']."-".$_POST['start_dag'];
		if($continue){
			$update_content = array("nyhed_overskrift" => $_POST['update_overksrift'], 
				"nyhed_tekst" => $_POST['update_text'], "nyhed_dato" =>$start_dato
			);
			$db->query($db->update_sql("nyhed", $update_content, array("nyhed_id" => $_POST['nyhed_id'])));
			
		}else{
			header('location: index.php?side='.$_GET["side"]);
			exit();
		}
		$db->save_message("Opdateret!");

	}elseif ($_GET['action'] =="delete") {
		$_POST = $db->sanitizer($_POST);
		$delete_sql = "DELETE  
		FROM `nyhed` 
		WHERE `nyhed_id` =".$_POST['nyhed_id']."";
		$db->query($delete_sql);
		$db->save_message("Slettet!");

	}
header("location: index.php?side=".$_GET['side']);
exit();
}

echo "<h5>".$db->load_message()."</h5>";
echo '<section style="border: 2px solid grey;background-color:#eefeec;">';
?>
<h4>Indsæt ny:</h4>
<form action="?side=<?php echo $_GET['side'];?>&action=insert" method="post" enctype="multipart/form-data">

	<label for="nyt_overskrift">Overskrift: </label>
	<input type="text" id="nyt_overskrift" name="nyt_noverskrift" value="<?php echo $db->load_forminput('nyt_overskrift'); ?>" required></br>
	<label for="nyt_indhold">Beskrivelse:</label>
	<textarea id="nyt_indhold" name="nyt_indhold" rows="5" cols="80" required><?php echo $db->load_forminput('nyt_indhold'); ?></textarea></br>


	<input type="submit" value="Tilføj ny">
</form>
<?php
echo "</section></br><hr></br>";

$nyheder_sql = "SELECT * FROM `nyhed` 
	ORDER BY `nyhed_dato` DESC";
$nyheder_result = $db->query($nyheder_sql);

while ($nyheder = mysqli_fetch_assoc($nyheder_result)) {

?>
<section>
<form action="?side=<?php echo $_GET['side'];?>&action=update" method="post" enctype="multipart/form-data">
	<input type="hidden" name="nyhed_id" value="<?php echo $nyheder['nyhed_id'];?>">
	<label>Dato :</label> <?php echo substr($nyheder['nyhed_dato'], 8, 2)."-".substr($nyheder['nyhed_dato'], 5, 2)."-".substr($nyheder['nyhed_dato'], 0, 4) ;?></br>
	<label>Ny dato: </label>
	<select name="start_dag">
<?php

	$zero = "";
	for ($i=1; $i < 32 ; $i++) { 
		if ($i < 10) {
			$zero = 0;
		}

		if (substr($nyheder['nyhed_dato'], 8, 2) == $i) {
			$selected = "selected";
		}else{
			$selected = "";
		}
		echo '<option value="'.$zero.$i.'" '.$selected.'>'.$zero.$i.'</option>';
		$zero = "";
	}
?>

	</select>
	<select name="start_maaned">
<?php $m = 1; ?>
		<option value="01" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>januar</option>
		<option value="02" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>februar</option>
		<option value="03" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>marts</option>
		<option value="04" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>april</option>
		<option value="05" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>maj</option>
		<option value="06" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>juni</option>
		<option value="07" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>juli</option>
		<option value="08" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>august</option>
		<option value="09" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>september</option>
		<option value="10" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>oktober</option>
		<option value="11" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>november</option>
		<option value="12" <?php  $selected = ($m == substr($nyheder['nyhed_dato'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>december</option>

	</select>
	<select name="start_aar">
<?php

	for ($i=2015; $i < 2071 ; $i++) { 
		if (substr($nyheder['nyhed_dato'], 0, 4) == $i) {
			$selected = "selected";
		}else{
			$selected = "";
		}
		echo '<option value="'.$zero.$i.'" '.$selected.'>'.$zero.$i.'</option>';

	}
?>
	</select>

</br>
	<label for="update_overksrift">Overskrift</label>
	<input type="text" name="update_overksrift" width="200" value="<?php echo $nyheder['nyhed_overskrift'];?>"></br>
</br>
	
	
	<label for="update_text">Indhold:</label>
	<textarea id="update_text" name="update_text" rows="5" cols="80" required><?php echo $nyheder['nyhed_tekst']; ?></textarea></br>

	<input type="submit" value="Opdater">
	<button type="submit" onclick="return confirm('Er du sikker ? \n\n Dette kan ikke gøres om !')" formaction="?side=<?php echo $_GET['side'];?>&action=delete">Slet</button>
</form>

</section>
<?php
echo "</br><hr></br>";
}

?>
