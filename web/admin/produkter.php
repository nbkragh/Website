<?php
if(!isset($_SESSION)){ 
    session_start(); 
}
if ($_GET["side"] == "brandeovne" ) {
  $kategori = "Brændeovne";
  $label_navn = "Model:";
  $label_type = "Mærke:";

}elseif($_GET["side"] == "tilbehor" ){
	$kategori = "Tilbehør";
	$label_navn = "Navn:";
	$label_type = "Type:";


}
$get_typer = $db->query("SELECT `produkt_type` FROM `produkt` WHERE `produkt_kategori` = '".$kategori."' GROUP BY `produkt_type` ORDER BY `produkt_id` ");
if (!empty($_GET['type'])) {
	$type = $_GET['type'];
}else{

	$first_type = mysqli_fetch_row($get_typer);
	$type = $first_type[0];
}

if (isset($_GET['action']) and !empty($_GET['action'])) {
	
	if ($_GET['action'] =="insert") {

		$_POST = $db->sanitizer($_POST);
		$continue = TRUE;
		foreach ($_POST as $key => $value) {
			if (empty($value) AND $key !=='nyt_detaljer' ) {
				$db->save_message("Der mangler en eller flere oplysninger");
				$continue = FALSE;
			}
		}
		if(filter_var($_POST['nyt_pris'], FILTER_VALIDATE_INT ) == FALSE AND $continue){
			$db->save_message("Prisen skal være et tal");
			$continue = FALSE;
		}	
		if (empty($_FILES['billed']) AND $continue ) {
			$db->save_message("Der mangler et billede!");
			$continue = FALSE;
		}

					
		if($continue){
			$insert_content = array("produkt_type" => $type, 
				"produkt_navn" => $_POST["nyt_navn"], 
				"produkt_kategori" => $kategori, 
				"produkt_beskrivelse" => $_POST["nyt_beskrivelse"],
				"produkt_detaljer" => $_POST["nyt_detaljer"],
				"produkt_pris" => $_POST["nyt_pris"]
			);
			if(!$db->query($db->insert_sql("produkt", $insert_content))){

			
				$db->save_message("Der skete en fejl; prøv igen!");
				$db->save_forminput();
				header('location: index.php?side='.$_GET["side"].'&type='.$type.'');
				exit();
			};
			$produkt_id = mysqli_insert_id($db->connect);
			if ( !empty($_FILES['billed']['tmp_name']) ) {

				$_FILES['billed']['name'] = $db->sanitizer($_FILES['billed']['name']);
				
				$billeder = new IMG($_FILES['billed'], 1);
				
				$billede_query = "INSERT INTO `billed` (`billed_filnavn`, `fk_produkt_id`) VALUES ('".$billeder->billedfiler['new_name'][0]."', ".$produkt_id.")";
				if(!$db->query($billede_query)){
					$db->save_forminput();
					$db->save_message($billeder->billedfiler['old_name'][$key]." blev ikke gemt...");
					header('location: index.php?side='.$_GET["side"].'&type='.$type.'');
					exit();
				}
				$billeder->saveIMG("../img/produkter/large", 300, 600);
				$billeder->saveIMG("../img/produkter/small", 100, 100);
			}
				
				
			$db->save_message("Tilføjet!");
		}else{
			$db->save_forminput();
			header('location: index.php?side='.$_GET["side"].'&type='.$type.'');
			exit();
		}
		
		
		
	}elseif($_GET['action'] =="update") {
		$_POST = $db->sanitizer($_POST);
		$continue = TRUE;
		foreach ($_POST as $key => $value) {
			if (empty($value) AND $key !=='update_detaljer' ) {
				$db->save_message("Der mangler en eller flere oplysninger");
				$continue = FALSE;
			}
		}
		if(filter_var($_POST['update_pris'], FILTER_VALIDATE_INT ) == FALSE AND $continue){
			$db->save_message("Prisen skal være et tal");
			$continue = FALSE;
		}
		// $_POST['paa_tilbud'] == 1 and (!empty($_POST['update_tilbud_pris']) or !empty($_POST['update_tilbud_start']) or !empty($_POST['update_tilbud_slut']))
		if(!empty($_POST['paa_tilbud']) AND $_POST['paa_tilbud'] == 1 ){

			$test_tilbud = $db->query("SELECT * FROM `tilbud` WHERE `fk_produkt_id` = ".$_POST['produkt_id']."");

			print_r($_POST);
			if (mysqli_num_rows($test_tilbud) > 0 ) {
				$db->query($db->update_sql("tilbud", array("tilbud_status" => 1), array("fk_produkt_id" => $_POST['produkt_id']) ));
			}else{
				$db->query($db->insert_sql("tilbud", array("tilbud_pris" => $_POST['update_pris'], "tilbud_status" => 1, "fk_produkt_id" => $_POST['produkt_id'])));
			}

		}else{
			$test_tilbud = $db->query("SELECT * FROM `tilbud` WHERE `fk_produkt_id` = ".$_POST['produkt_id']."");
			print_r($_POST);
			if (mysqli_num_rows($test_tilbud) > 0 ) {
				$db->query($db->update_sql("tilbud", array("tilbud_status" => 0), array("fk_produkt_id" => $_POST['produkt_id']) ));
			}
		}
		if($continue){
			$update_content = array("produkt_type" => $type, 
				"produkt_navn" => $_POST["update_navn"], 
				"produkt_kategori" => $kategori, 
				"produkt_beskrivelse" => $_POST["update_beskrivelse"],
				"produkt_detaljer" => $_POST["update_detaljer"],
				"produkt_pris" => $_POST["update_pris"]
			);
			$db->query($db->update_sql("produkt", $update_content, array("produkt_id" => $_POST['produkt_id'])));
			

			if (!empty($_FILES['billed']['tmp_name'][0]) ) {
				$_FILES['billed']['name'] = $db->sanitizer($_FILES['billed']['name']);

				$billeder = new IMG($_FILES['billed'], 1);
				
				$old_billede_result = $db->query("SELECT `billed_id`, `billed_filnavn`
				FROM `billed` 
				WHERE `fk_produkt_id` =".$_POST['produkt_id']."
				"
				);
				$old_billede = mysqli_fetch_row($old_billede_result);
				
					$update = $db->update_sql("billed", array("billed_filnavn" => $billeder->billedfiler['new_name'][0] ), array("fk_produkt_id" => $_POST['produkt_id']));
					if($db->query($update)){
						$billeder->saveIMG("../img/produkter/large", 300, 600);
						$billeder->saveIMG("../img/produkter/small", 100, 100);
						if(is_file("../img/produkter/large/".$old_billede[1])){
							unlink("../img/produkter/large/".$old_billede[1]);
							unlink("../img/produkter/small/".$old_billede[1]);
						}
					}else{
						$db->save_message($billeder->billedfiler['old_name'][0]." blev ikke gemt...");	
					}
			}
		$db->save_message("Opdateret!");
		}else{
			header('location: index.php?side='.$_GET["side"].'&type='.$type.'');
			exit();
		}
	}elseif ($_GET['action'] =="delete") {
		$_POST = $db->sanitizer($_POST);
		$delete_result = $db->query("SELECT `billed_filnavn`
		FROM `billed` 
		WHERE `billed_id` =".$_POST['billed_id'].""
		);
		$billede = mysqli_fetch_row($result);
		if ($billede !==NULL) {
			if(is_file("../img/produkter/large/".$billede[0])){
				unlink("../img/produkter/large/".$billede[0]);
				unlink("../img/produkter/small/".$billede[0]);
			}
		}
		$delete_sql = "DELETE  
		FROM `billed` 
		WHERE `billed_id` =".$_POST['billed_id']."";
		$db->query($delete_sql);

		$delete_sql = "DELETE  
		FROM `tilbud` 
		WHERE `fk_produkt_id` =".$_POST['produkt_id']."";
		$db->query($delete_sql);

		$delete_sql = "DELETE  
		FROM `produkt` 
		WHERE `produkt_id` =".$_POST['produkt_id']."";
		$db->query($delete_sql);
		$db->save_message("Slettet!");

	}
header("location: index.php?side=".$_GET['side']."&type=".$type."");
exit();
}

echo '<nav><ul class="types">';

mysqli_data_seek($get_typer,0);
while($type_row = mysqli_fetch_assoc($get_typer) ){
	$local = '';
	if ($type == $type_row['produkt_type']) {
		$local = 'style="background-color: darkgrey;"';
	}
	echo '<li '.$local.'><a href="index.php?side='.$_GET['side'].'&type='.$type_row['produkt_type'].'">'.$type_row['produkt_type'].'</a></li>';
}
echo '</ul></nav></br>';
echo "<h5>".$db->load_message()."</h5>";
echo "<h4>Indsæt ny:</h4>";
echo '<section style="border: 2px solid grey;background-color:#eefeec;">';
?>
<form action="?side=<?php echo $_GET['side'];?>&type=<?php echo $type; ?>&action=insert" method="post" enctype="multipart/form-data">

	<label for="nyt_navn"><?php echo $label_navn; ?></label>
	<input type="text" id="nyt_navn" name="nyt_navn" value="<?php echo $db->load_forminput('nyt_navn'); ?>" required></br>
	<label for="nyt_beskrivelse">Beskrivelse:</label>
	<textarea id="nyt_beskrivelse" name="nyt_beskrivelse" rows="5" cols="80" required><?php echo $db->load_forminput('nyt_beskrivelse'); ?></textarea></br>
	<label for="nyt_detaljer">Detaljer:</label>
	<textarea id="nyt_detaljer"  name="nyt_detaljer" rows="10" cols="40"><?php echo $db->load_forminput('nyt_detaljer'); ?></textarea></br>
	<label for="nyt_pris">Pris: </label>
	<input type="text" id="nyt_pris" name="nyt_pris" value="<?php echo $db->load_forminput('nyt_pris'); ?>" required></br>
	<label for="nyt_billede">Billede:</label>
	<input type="file" id="nyt_billede" name="billed[]" required/></br>

	<input type="submit" value="Tilføj ny">
</form>
<?php
echo "</section></br><hr></br>";

$alle_produkter_sql = "SELECT * FROM `produkt` 
	INNER JOIN `billed` ON `billed`.`fk_produkt_id` = `produkt_id`
	LEFT JOIN `tilbud` ON `tilbud`.`fk_produkt_id` = `produkt_id`
	WHERE `produkt_kategori` = '".$kategori."' AND `produkt_type` = '".$type."'
	ORDER BY `produkt_id` DESC";
$alle_produkter_result = $db->query($alle_produkter_sql);

while ($produkt = mysqli_fetch_assoc($alle_produkter_result) ) {

?>
<section>
<form action="?side=<?php echo $_GET['side'];?>&type=<?php echo $type; ?>&action=update" method="post" enctype="multipart/form-data">
	<input type="hidden" name="produkt_id" value="<?php echo $produkt['produkt_id'];?>">

	<label for="update_navn"><?php echo $label_navn; ?></label>
	<input type="text" name="update_navn" value="<?php echo htmlspecialchars($produkt['produkt_navn']);?>"></br>

	<label for="update_beskrivelse">Beskrivelse:</label>
	<textarea id="update_beskrivelse" name="update_beskrivelse" rows="5" cols="80" required><?php echo $produkt['produkt_beskrivelse']; ?></textarea></br>
	<label for="nyt_detaljer">Detaljer:</label>
	<textarea id="update_detaljer" name="update_detaljer" rows="10" cols="40"><?php echo $produkt['produkt_detaljer']; ?></textarea></br>

	<label for="update_pris">Pris: </label>
	<input type="text" id="update_pris" name="update_pris" value="<?php echo $produkt['produkt_pris']; ?>" required></br></br>

<?php
if (!empty($produkt['tilbud_id']) AND $produkt['tilbud_status'] > 0 ) {
	?>
	<label for="paa_pris">På tilbud: </label>
	<input type="checkbox" id="paa_tilbud" name="paa_tilbud" value="1" checked>

<?php
}else{
?>
<label for="paa_pris">På tilbud: </label>
<input type="checkbox" id="paa_tilbud" name="paa_tilbud" value="1">

<?php
}

if (!empty($produkt['tilbud_pris'])) {
	echo 'Tilbudspris: '.$produkt['tilbud_pris'];
}
?>

	

	</br>
	<input type="hidden" name="billed_id" value="<?php echo $produkt['billed_id']; ?>">
	<img src="../img/produkter/large/<?php echo $produkt["billed_filnavn"]?>" max-height="200"></br>
	<label for="update_billede">Erstat Billede</label>
	<input type="file" id="update_billede" name="billed[]"></br>

	<input type="submit" value="Opdater">
	<button type="submit" onclick="return confirm('Er du sikker ? \n\n Dette kan ikke gøres om !')" formaction="?side=<?php echo $_GET['side'];?>&type=<?php echo $type; ?>&action=delete">Slet</button>
</form>

</section>
<?php
echo "</br><hr></br>";
}

?>
