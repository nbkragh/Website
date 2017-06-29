

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
			$db->query("UPDATE `forside` SET `forside_text` = '".$_POST['update_text']."'");
			if (!empty($_FILES['billed']['tmp_name'][0]) ) {
				$_FILES['billed']['name'] = $db->sanitizer($_FILES['billed']['name']);

				$billeder = new IMG($_FILES['billed'], 1);
				
				$old_billede_result = $db->query("SELECT `billed_id`, `billed_filnavn`
				FROM `billed` 
				WHERE `billed_id` =".$_POST['billed_id']."
				"
				);
				$old_billede = mysqli_fetch_row($old_billede_result);
				
					$update = $db->update_sql("billed", array("billed_filnavn" => $billeder->billedfiler['new_name'][0] ), array("billed_id" => $_POST['billed_id']));
					if($db->query($update)){
						$billeder->saveIMG("../img/site", 290, 215);
						
						if(is_file("../img/site/".$old_billede[1])){
							unlink("../img/site/".$old_billede[1]);
						}
					}else{
						$db->save_message($billeder->billedfiler['old_name'][0]." blev ikke gemt...");	
					}
			}
			$db->save_message("Opdateret!");
		}
header("location: index.php?side=".$_GET['side']."&type=".$type."");
exit();
}


$forside_sql = "SELECT * FROM `forside` INNER JOIN `billed` WHERE `forside`.`fk_billed_id` = `billed`.`billed_id` LIMIT 1 ";
$forside_result = $db->query($forside_sql);

$forside = mysqli_fetch_assoc($forside_result);
echo "<h5>".$db->load_message()."</h5>";
?>

<section>
<form action="?side=<?php echo $_GET['side'];?>&action=update" method="post" enctype="multipart/form-data">
	<label for="update_text">Forsidetekst: </label>
	<textarea id="update_text" name="update_text" rows="20" cols="90" required><?php echo $forside['forside_text']; ?></textarea></br>

	<input type="hidden" name="billed_id" value="<?php echo $forside['billed_id']; ?>">
	<img src="../img/site/<?php echo $forside['billed_filnavn']; ?>" max-height="200"></br>
	<label for="update_billede">Erstat Billede</label>
	<input type="file" id="update_billede" name="billed[]"></br>

	<input type="submit" value="Opdater">
</form>
</section>

