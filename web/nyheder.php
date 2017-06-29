

<h3></h3><h2>KONTAKT</h2>

<?php
$nyheder_sql = "SELECT * FROM `nyhed` ORDER BY `nyhed_dato` DESC";
$result = $db->query($nyheder_sql);
while ($nyheder = mysqli_fetch_assoc($result)) {
	
// print_r($nyheder);
?>
<section class="nyheder">
	<h2><?php echo $nyheder['nyhed_overskrift'];?></h2>
	<p><?php echo substr($nyheder['nyhed_dato'], 8, 2).".".substr($nyheder['nyhed_dato'], 5, 2).".".substr($nyheder['nyhed_dato'], 0, 4) ;?></p>
</br>
	<p><?php echo nl2br($nyheder['nyhed_tekst']);?></p>
</section>
<?php
}
?>
