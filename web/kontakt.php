<?php
if (!empty($_GET['action']) and $_GET['action'] == "insert") {


	$_POST = $db->sanitizer($_POST);
	$continue = TRUE;
	foreach ($_POST as $key => $value) {
		if (empty($value) AND $key!=='firmanavn' AND $key !=='tlf' ) {
			$db->save_message("<h3>Der mangler en eller flere oplysninger, evt. et spørgsmål</h3>");
			$continue = FALSE;

		}
	}
	
	if(!empty($_POST['tlf']) and filter_var($_POST['tlf'], FILTER_VALIDATE_INT, array("options" => array("min_range"=>00000000,"max_range"=>99999999)) ) == FALSE AND $continue){
		$db->save_message("<h3>Telefonnummer bør kun indeholde 8 tal, uden mellemrum</h3>");
		$continue = FALSE;

	}
	
	if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)  == FALSE AND $continue){
		$db->save_message("<h3>Emailen er ikke korrekt!</h3>");
		$continue = FALSE;

	}
	
	if ($continue) {
		
		$content = array(
			"ask_person" => $_POST['person'],
			"ask_firma" => $_POST['firmanavn'],
			"ask_tlf" => $_POST['tlf'],
			"ask_email" => $_POST['email'],
			"ask_text" => $_POST['ask'],
			);
		$kontakt_result = $db->query($db->insert_sql("ask", $content));
		$db->save_message("<h3>Tak for din henvendelse!!</h3>");
	}else{
		$db->save_forminput();
		
		header('location: index.php?side='.$_GET["side"].'');
		exit();
	}

header('location: index.php?side='.$_GET["side"].'');
exit();
	
}
 $kontakt_oplysninger_sql = "SELECT * FROM `kontakt` LIMIT 1";
 $kontakt_oplysninger_result = $db->query($kontakt_oplysninger_sql);
 $kontakt = mysqli_fetch_assoc($kontakt_oplysninger_result);
?>

<h3></h3><h2>KONTAKT</h2>


<section class="kontakt">
	<?php echo $db->load_message() ?>
	<img src="img/site/danmarkskort.gif" width="320" height="380">
	<p><?php echo $kontakt['kontakt_firmanavn']?></p>
	<p><?php echo $kontakt['kontakt_adresse']?></p>
	<p><?php echo $kontakt['kontakt_postnr']?></p>
	<p><?php echo $kontakt['kontakt_tlf']?></p>
	<p><?php echo $kontakt['kontakt_email']?></p>
	</br>
	<form action="index.php?side=kontakt&action=insert" method="post">
		<p>Evt. firmanavn</p>
		<input name="firmanavn" type="text" value="<?php echo $db->load_forminput('firmanavn'); ?>" >
		<p>Kontaktperson</p>
		<input name="person" type="text"  value="<?php echo $db->load_forminput('person'); ?>" required>
		<p>Telefon</p>
		<input name="tlf" type="number" value="<?php echo $db->load_forminput('tlf'); ?>">
		<p>E-mail</p>
		<input name="email" type="email"  value="<?php echo $db->load_forminput('email'); ?>" required>
		<p>Spørgsmål</p>
		<textarea name="ask" rows="3" col="7" value="<?php echo nl2br($db->load_forminput('ask')); ?>"></textarea>
		<input type="submit" value="Ok">
	</form>

	<div class="heighter"></div>
</section>

