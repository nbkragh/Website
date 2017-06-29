<nav>
	<ul>

	<?php
	$_GET = $db->sanitizer($_GET);

	$hovedmenu = array(
		"FORSIDE"=> "forside", 
		"BRÆNDEOVNE" => "Brændeovne",
		"TILBEHØR" =>"Tilbehør",
		"KONTAKT" => "kontakt",
		"NYHEDER" => "nyheder");
	if(!isset($_SESSION)){ 
	    session_start(); 
    };

	foreach ($hovedmenu as $key => $value) {

	echo '<li><a href="index.php?side='.$value.'" ><h1>'.$key.'</h1></a></li>';
	}

	?>

	</ul>
</nav>   