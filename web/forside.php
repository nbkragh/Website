<?php
	$forside_sql = "SELECT * FROM `forside` INNER JOIN `billed` ON `billed_id` = `fk_billed_id`";
	$result = $db->query($forside_sql);
	$forside = mysqli_fetch_assoc($result);
		
	
?>

<section class="forside">
	<h2>Velkommen Til Fireshop ApS</h2>	
	<img src="img/site/<?php echo  $forside['billed_filnavn']?>" width="290" height="215">
	<p><?php echo nl2br($forside['forside_text']);?></p>
</section>
