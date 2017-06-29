<?php



if (isset($_GET['action']) and !empty($_GET['action']) and $_GET['action'] =="update") {

		$_POST = $db->sanitizer($_POST);
		$continue = TRUE;

		foreach ($_POST as $key => $value) {
			if (empty($value)) {
				$db->save_message("Der mangler tekst!");
				$continue = FALSE;
			}
		}
		if ($continue) {
			$content = array("kontakt_firmanavn"=>$_POST['update_firmanavn'],
				"kontakt_adresse"=>$_POST['update_adresse'],
				"kontakt_postnr"=>$_POST['update_postnr'],
				"kontakt_tlf"=>$_POST['update_tlf'],
				"kontakt_email"=>$_POST['update_email'],
			);
			$db->query($db->update_sql("kontakt", $content));
			$db->save_message("Opdateret!");
		}


header("location: index.php?side=".$_GET['side']);
exit();
}


$kontakt_sql = "SELECT * FROM `kontakt`  LIMIT 1 ";
$kontakt_result = $db->query($kontakt_sql);

$kontakt = mysqli_fetch_assoc($kontakt_result);
echo "<h5>".$db->load_message()."</h5>";
?>

<section>
<form action="?side=<?php echo $_GET['side'];?>&action=update" method="post" enctype="multipart/form-data">
	<label for="update_firmanavn">Firmanavn: </label>
	<input type="text" name="update_firmanavn"  value="<?php echo $kontakt['kontakt_firmanavn'];?>"></br>
	<label for="update_adresse">Adresse: </label>
	<input type="text" name="update_adresse"  value="<?php echo $kontakt['kontakt_adresse'];?>"></br>
	<label for="update_postnr">Postnr. & by: </label>
	<input type="text" name="update_postnr"  value="<?php echo $kontakt['kontakt_postnr'];?>"></br>
	<label for="update_tlf">Telefon nr.: </label>
	<input type="text" name="update_tlf"  value="<?php echo $kontakt['kontakt_tlf'];?>"></br>
	<label for="update_email">Email: </label>
	<input type="text" name="update_email"  value="<?php echo $kontakt['kontakt_email'];?>"></br>


	<input type="submit" value="Opdater">
</form>
</section>
