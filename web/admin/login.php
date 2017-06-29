<?php
if(!isset($_SESSION)){ 
    session_start();
};
if (!empty($_GET['access']) and $_GET['access'] =="try") {

	$_POST = $db->sanitizer($_POST);

	$admin_navn = $_POST['bruger_navn'];
	$admin_kode = md5($_POST['kodeord'].$_POST['bruger_navn']."jSgDsdf7Fa3sbæ2");
	$login_query = "SELECT `admin_id`,`admin_navn`, `admin_kode` FROM `admin` WHERE `admin_navn` = '".$admin_navn."' AND `admin_kode` = '".$admin_kode."' ";
	
	$result = $db->query($login_query);
	if (mysqli_num_rows($result) == 1) {

	    $bruger = mysqli_fetch_assoc($result);
		$_SESSION['bruger']['id'] = $bruger['admin_id'];
		$_SESSION['bruger']['navn'] = $bruger['admin_navn'];
		header('location: index.php');
		exit();
	}else{
		$db->save_message("Loginfejl: Navn og Kodeord stemmer ikke overens");
		$db->save_forminput();
	}

}elseif(!empty($_GET['access']) and $_GET['access'] =="logout"){
	session_unset();
	session_destroy();
    $_SESSION = array();
	header('location: index.php');
	exit();
};


if (!isset($_SESSION['bruger']['id']) or !isset($_SESSION['bruger']['navn'])  ) {
?>
<h5><?php echo $db->load_message(); ?></h5>
<form action="?side=login&access=try" method="post" id="login">
	<label for="brugernavn">Brugernavn: </label><input type="text" id="brugernavn" name="bruger_navn" value="<?php echo $db->load_forminput('bruger_navn'); ?>"></br>
	<label for="kodeord">Kodeord: </label><input type="password" id="kodeord" name="kodeord" value="<?php echo $db->load_forminput('kodeord'); ?>"></br>
	<input type="submit" value="Login">
</form>	
</div>
</body>
</html>
<?php

// echo "</br>".md5("1234"."admin"."jSgDsdf7Fa3sbæ2");
mysqli_close($db->connect);
unset($db);
exit();	
}





?>
