<nav>
	<ul>

	<?php
	$_GET = $db->sanitizer($_GET);

	$hovedmenu = array(
		"BRÆNDEOVNE" => "brandeovne",
		"TILBEHØR" =>	"tilbehor",
		"TILBUD" => "tilbud",
		"NYHEDER" => "nyheder",
		"FORSIDE"=> "forside",
		"KONTAKT" => "kontakt",
		
		"LOGUD" => "login&access=logout"
		);

	foreach ($hovedmenu as $key => $value) {
	$local = '';
	if ((!empty($_GET['side']) and $_GET['side'] == $value) or (empty($_GET['side']) and $value == "forside")) {
		$local = 'style="background-color: black;color:white"';
		}
		echo '<li '.$local.'><a href="index.php?side='.$value.'" ><h1 '.$local.'>'.$key.'</h1></a></li>';
		}
	?>
	</ul>
</nav>  