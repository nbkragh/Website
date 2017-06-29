<?php
if(!isset($_SESSION)){ 
    session_start(); 
}	
if (isset($_GET['action']) and !empty($_GET['action'])) {
	
	if ($_GET['action'] =="insert") {
		if ( !empty($_FILES['billed']['tmp_name']) ) {

			$_FILES['billed']['name'] = $db->sanitizer($_FILES['billed']['name']);
			
			$billeder = new IMG($_FILES['billed'], 1);

			foreach ($billeder->billedfiler["tmp_name"] as $key => $value) {
				$query = "INSERT INTO `billed` (`billeder_filnavn`, `fk_planter_id`) VALUES ('".$billeder->billedfiler['new_name'][$key]."', 1)";
				if(!$db->query($query)){
					$db->save_message($billeder->billedfiler['old_name'][$key]." blev ikke gemt...");
					header("location: index.php?side=img_upload");
					exit();
				}
			}
			$billeder->saveIMG("../img_test", 200, 200);
			$billeder->saveIMG("../img_test/thumb", 100, 100);
			$billeder->saveIMG("../img_test/ekstra", 100, 50);

			if(!empty($billeder->error)){
				$error_bill = "";
				foreach ($billeder->error as $key => $value) {
					$error_bill .= $value." kunne ikke uploades - Prøv at bruge et andet </br>";
				}
				$db->save_message($error_bill);
			}
			
		};
		
		
		
	}elseif($_GET['action'] =="update") {
		$_POST = $db->sanitizer($_POST);
		if (!empty($_FILES['billed']['tmp_name'][0]) ) {
			$_FILES['billed']['name'] = $db->sanitizer($_FILES['billed']['name']);

			$billeder = new IMG($_FILES['billed'], 1);
			
			$old_billede_result = $db->query("SELECT `billeder_id`, `billeder_filnavn`
			FROM `billeder` 
			WHERE `billeder_id` =".$_POST['billede_id']."
			"
			);
			$old_billede = mysqli_fetch_row($old_billede_result);
			if ($old_billede < 1) {
				$new_sql = $db->query($db->query("INSERT INTO `billeder` (`billede_navn`, `fk_planter_id`) VALUES ('".$billede->billedfiler['new_name'][0]."', 1)"));
				if($new_sql){
					$billeder->saveIMG("../img_test", 200, 200);
					$billeder->saveIMG("../img_test/thumb", 100, 100);
					$billeder->saveIMG("../img_test/ekstra", 100, 50);
				}else{
					$db->save_message($billeder->billedfiler['old_name'][0]." blev ikke gemt...");
					header("location: index.php?side=img_upload");
					exit();
				}
			}else{
				$update = $db->update_sql("billeder", array("billeder_filnavn" => $billeder->billedfiler['new_name'][0], "fk_planter_id" => 1 ), array("billeder_id" => $_POST['billede_id']));
				if($db->query($update)){
					$billeder->saveIMG("../img_test", 200, 200);
					$billeder->saveIMG("../img_test/thumb", 100, 100);
					$billeder->saveIMG("../img_test/ekstra", 100, 50);
					if(is_file("../img_test/".$old_billede[1])){
						unlink("../img_test/".$old_billede[1]);
						unlink("../img_test/ekstra/".$old_billede[1]);
						unlink("../img_test/thumb/".$old_billede[1]);
					}
				}else{
					$db->save_message($billeder->billedfiler['old_name'][0]." blev ikke gemt...");
					
				}
			}

		}else{
			$db->save_message("Husk at vælge et billede");
		}
	}elseif ($_GET['action'] =="delete") {
		$_POST = $db->sanitizer($_POST);
		$result = $db->query("SELECT `billeder_filnavn`
		FROM `billeder` 
		WHERE `billeder_id` =".$_POST['billede_id'].""
		);
		$billede = mysqli_fetch_row($result);
		if ($billede !==NULL) {
			if(is_file("../img_test/".$billede[0])){
				unlink("../img_test/".$billede[0]);
				unlink("../img_test/ekstra/".$billede[0]);
				unlink("../img_test/thumb/".$billede[0]);
			}
		}
		$delete_sql = "DELETE  
		FROM `billeder` 
		WHERE `billeder_id` =".$_POST['billede_id']."";
		$db->query($delete_sql);
	}
			header("location: index.php?side=img_upload");
			exit();
}
echo "<h3>".$db->load_message()."</h3>"
?>
<form action="?side=img_upload&action=insert" method="post" enctype="multipart/form-data">
	<label for="billede_nyt">Nyt Billede</label>
	<input type="file" id="billede_nyt" name="billed[]" multiple required/> <!-- fjern "multiple" hvis der ikke skal kunne uploades flere billeder -->

	<input type="submit" value="upload">
</form>
<?php
	
$alle_bill_sql = "SELECT * FROM `billeder` ORDER BY `billeder_id` DESC";
$alle_bill_result = $db->query($alle_bill_sql);
while ($billede = mysqli_fetch_assoc($alle_bill_result)) {

?>
<form action="?side=img_upload&action=update" method="post" enctype="multipart/form-data">
<img src="../img_test/<?php echo $billede["billeder_filnavn"]?>" >
<img src="../img_test/thumb/<?php echo $billede["billeder_filnavn"]?>" >
<img src="../img_test/ekstra/<?php echo $billede["billeder_filnavn"]?>" >
<input type="hidden" name="billede_id" value="<?php echo $billede['billeder_id'];?>">
<label for="billede_edit">Erstat Billede</label>
<input type="file" id="billede_edit" name="billed[]">

<input type="submit" value="erstat">
<button type="submit" onclick="return confirm('Er du sikker ? \n\n Dette kan ikke gøres om !')" formaction="?side=img_upload&action=delete">Slet</button>
</form>
<?php
}
?>
