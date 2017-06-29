<?php
if (isset($_GET['action']) and !empty($_GET['action'])) {
	$_POST = $db->sanitizer($_POST);
	if ($_GET['action'] =="update") {
		$continue = TRUE;
		if(filter_var($_POST['update_tilbud_pris'], FILTER_VALIDATE_INT ) == FALSE and $continue){
			$db->save_message("Tilbudsprisen skal være et tal");
			$continue = FALSE;
			
		}
		// $start_vardi = intval($_POST['start_aar']).intval($_POST['start_maaned']).intval($_POST['start_dag']);
		// $slut_vardi = intval($_POST['slut_aar']).intval($_POST['slut_maaned']).intval($_POST['slut_dag']);
		// if ( $start_vardi > $slut_vardi) {
		// 	$db->save_message("Startdato bør være før slutdato!".$start_vardi."    ".$slut_vardi);
		// 	$continue = FALSE;
		// }
		if (!$continue) {
			
			header('location: index.php?side='.$_GET["side"].'');
			exit();
		}
		


		$start_dato = $_POST['start_aar']."-".$_POST['start_maaned']."-".$_POST['start_dag'];
		$slut_dato = $_POST['slut_aar']."-".$_POST['slut_maaned']."-".$_POST['slut_dag'];
		if(!empty($_POST['paa_tilbud']) and  $_POST['paa_tilbud'] == 1 and !empty($_POST['update_tilbud_pris'])  ){
			$db->query($db->update_sql("tilbud", array("tilbud_status" => 1,"tilbud_pris" => $_POST['update_tilbud_pris'], "tilbud_start" => $start_dato, "tilbud_slut" => $slut_dato ), array("fk_produkt_id" => $_POST['produkt_id']) ));
			$db->save_message("Tilbud opdateret");
		}else{
			$db->query($db->update_sql("tilbud", array("tilbud_status" => 0), array("fk_produkt_id" => $_POST['produkt_id']) ));
			$db->save_message("Tilbud dekativeret");
		}
		$db->save_message("Opdateret!");
	}elseif ($_GET['action'] =="delete") {
		$delete_sql = "DELETE  
		FROM `tilbud` 
		WHERE `fk_produkt_id` =".$_POST['produkt_id']."";
		$db->query($delete_sql);
		$db->save_message("Tilbud slettet!");
	}
header("location: index.php?side=".$_GET['side']);
exit();
}
$alle_produkter_sql = "SELECT * FROM `produkt` 
	INNER JOIN `billed` ON `billed`.`fk_produkt_id` = `produkt_id`
	INNER JOIN `tilbud` ON `tilbud`.`fk_produkt_id` = `produkt_id`
	ORDER BY `tilbud_id` DESC";
$alle_produkter_result = $db->query($alle_produkter_sql);
echo "<h5>".$db->load_message()."</h5>";
while ($produkt = mysqli_fetch_assoc($alle_produkter_result)) {

?>
<section>
<form action="?side=<?php echo $_GET['side'];?>&action=update" method="post" enctype="multipart/form-data">
	<input type="hidden" name="produkt_id" value="<?php echo $produkt['produkt_id'];?>">
	<h3><?php echo $produkt['produkt_type'];?>  <?php echo $produkt['produkt_navn'];?></h3>
	<img src="../img/produkter/large/<?php echo $produkt["billed_filnavn"]?>" ></br>
	<h3>Normal pris:   <?php echo $produkt['produkt_pris']; ?> ,-</h3></br>

<?php
if ($produkt['tilbud_status'] > 0 ) {
	?>
	<label for="paa_pris">På tilbud: </label>
	<input type="checkbox" id="paa_tilbud" name="paa_tilbud" value="1" checked></br><?php
}else{
?>
<label for="paa_pris">På tilbud: </label>
<input type="checkbox" id="paa_tilbud" name="paa_tilbud" value="1"></br>

<?php

}

?>	
	<label for="update_tilbud_pris">Tilbudspris: </label>
	<input type="input" id="update_tilbud_pris" name="update_tilbud_pris" value="<?php echo $produkt['tilbud_pris']; ?>"></br>
	<label>Start dato:</label>
	<select name="start_dag">
<?php

	$zero = "";
	for ($i=1; $i < 32 ; $i++) { 
		if ($i < 10) {
			$zero = 0;
		}

		if (substr($produkt['tilbud_start'], 8, 2) == $i) {
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
		<option value="01" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>januar</option>
		<option value="02" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>februar</option>
		<option value="03" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>marts</option>
		<option value="04" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>april</option>
		<option value="05" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>maj</option>
		<option value="06" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>juni</option>
		<option value="07" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>juli</option>
		<option value="08" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>august</option>
		<option value="09" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>september</option>
		<option value="10" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>oktober</option>
		<option value="11" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>november</option>
		<option value="12" <?php  $selected = ($m == substr($produkt['tilbud_start'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>december</option>

	</select>
	<select name="start_aar">
<?php

	for ($i=2015; $i < 2071 ; $i++) { 
		if (substr($produkt['tilbud_start'], 0, 4) == $i) {
			$selected = "selected";
		}else{
			$selected = "";
		}
		echo '<option value="'.$zero.$i.'" '.$selected.'>'.$zero.$i.'</option>';

	}
?>
	</select>

</br>
	<label>Slut dato :</label>
	<select name="slut_dag">
<?php

	$zero = "";
	for ($i=1; $i < 32 ; $i++) { 
		if ($i < 10) {
			$zero = 0;
		}

		if (substr($produkt['tilbud_slut'], 8, 2) == $i) {
			$selected = "selected";
		}else{
			$selected = "";
		}
		echo '<option value="'.$zero.$i.'" '.$selected.'>'.$zero.$i.'</option>';
		$zero = "";
	}
?>

	</select>
	<select name="slut_maaned">
<?php $m = 1; ?>
		<option value="01" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>januar</option>
		<option value="02" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>februar</option>
		<option value="03" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>marts</option>
		<option value="04" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>april</option>
		<option value="05" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>maj</option>
		<option value="06" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>juni</option>
		<option value="07" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>juli</option>
		<option value="08" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>august</option>
		<option value="09" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>september</option>
		<option value="10" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>oktober</option>
		<option value="11" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>november</option>
		<option value="12" <?php  $selected = ($m == substr($produkt['tilbud_slut'] , 5, 2)) ? " selected " : ""; echo $selected ; ++$m;?>>december</option>

	</select>
	<select name="slut_aar">
<?php

	for ($i=2015; $i < 2071 ; $i++) { 
		if (substr($produkt['tilbud_slut'], 0, 4) == $i) {
			$selected = "selected";
		}else{
			$selected = "";
		}
		echo '<option value="'.$zero.$i.'" '.$selected.'>'.$zero.$i.'</option>';

	}
?>
	</select>

</br>



	<input type="submit" value="Opdater">
	<button type="submit" onclick="return confirm('Er du sikker ? \n\n Dette kan ikke gøres om !')" formaction="?side=<?php echo $_GET['side'];?>&action=delete">Slet</button>
</form>

</section>
<hr>
<?php
}
echo "</br><hr></br>";
?>