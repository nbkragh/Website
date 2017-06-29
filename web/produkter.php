<?php

	$kategori = $_GET['side'];


if (!empty($_GET['produkt']) or !empty($_GET['type'] )) {
	$type = $_GET['type'];
	$firma_sql = "SELECT * FROM `produkt` LEFT JOIN `firma` ON `firma_type` = `produkt`.`produkt_type`
	WHERE `produkt`.`produkt_type` = '".$type."'
	LIMIT 1
	";
	echo "<h3";
	$firma_result = $db->query($firma_sql);
	$firma = mysqli_fetch_assoc($firma_result);

	if ($kategori == "Brændeovne") {
		echo " onclick=\"show_info()\"><img src=\"img/site/pilright.png\" width=\"9\" height=\"9\" > ".$firma['firma_navn']."  firma profil";
	}
	else{
		echo ">";
	}
	echo "</h3><h2><a href=\"?side=".$_GET['side']."&kategori=".$kategori."&type=".$type."\">".mb_strtoupper($type, 'UTF-8')."</a></h2>";



?>
<p id="firma_text">
<?php
	echo nl2br($firma['firma_beskrivelse']);
?>
</p>
<?php
	if (isset($_GET['produkt']) && !empty($_GET['produkt'])) {
		$produkt_query = "SELECT * FROM `produkt`
		LEFT JOIN `tilbud` ON `tilbud`.`fk_produkt_id` =`produkt_id` 
		INNER JOIN `billed` ON `produkt_id` = `billed`.`fk_produkt_id`
		WHERE `produkt_id` = '".$_GET['produkt']."' 
		LIMIT 1";
		$produkt_result = $db->query($produkt_query);

		while($show = mysqli_fetch_assoc($produkt_result)){
			
	$imgsize = (isset($_GET['img']) and $_GET['img'] =="small" ) ? "large" : "small" ;
	$img_instr =($imgsize == "large") ? "Klik igen for lille billede" : "Klik for stor billede" ;
?>

<section class="produktinfo">
	<a href="?side=<?php echo $_GET['side'];?>&kategori=<?php echo $kategori;?>&type=<?php echo $type?>&produkt=<?php echo $show['produkt_id'];?>&img=<?php echo $imgsize?>" class="img_box <?php echo $imgsize?>">
		<img src="img/produkter/large/<?php echo $show['billed_filnavn']?>" >
		<p><?php echo $img_instr?></p>
	</a>
	<div class="text_box">
		<h2><?php 
			if ($kategori == "Brændeovne") {
				echo $show['produkt_type']."  ";
			}
		echo $show['produkt_navn'];
?>
		</h2>

		<p><?php echo nl2br($show['produkt_beskrivelse'])?></p>
		</br>
		
			<?php
			if($show['tilbud_pris'] > 0){
	?>          
			<p>
			<span class="normal_pris">Kr <?php echo $show['produkt_pris'] ?> ,- </span>    
			<br>
			<span class="tilbud_pris">Kr  <?php echo $show['tilbud_pris']?> ,- </span> 
			</p>
	<?php
			}else{
	?>
		<p class="alm_pris">Pris: <?php echo  $show['produkt_pris'] ?> ,-</p>
	<?php
			}
	if (!empty($show['produkt_detaljer'])) {
	?>
		<h5>produkt detaljer</h5>

		<p><?php echo nl2br($show['produkt_detaljer'])?></p>
	<?php
	}
	?>
	</div>
	<div class="heighter"></div>

</section>

<?php
		}


	}elseif(isset($_GET['type']) and !empty($_GET['type'])){
		$produkt_query = "SELECT * FROM `produkt`
		LEFT JOIN `tilbud` ON `tilbud`.`fk_produkt_id` =`produkt_id` 
		INNER JOIN `billed` ON `produkt_id` = `billed`.`fk_produkt_id`
		WHERE `produkt_type` = '".$type."'			
		";
		$produkt_result = $db->query($produkt_query);

		while($show = mysqli_fetch_assoc($produkt_result)){	
	?>


	<section class="listeinfo" >
	<a href="?side=<?php echo $kategori;?>&type=<?php echo $type?>&produkt=<?php echo $show['produkt_id'];?>">

		<div class="img_box">
			<img src="img/produkter/small/<?php echo $show['billed_filnavn']?>" >
		</div>
		<div class="text_box">
			<h2>
	<?php 
			if ($kategori == "Brændeovne") {
				echo $show['produkt_type']."  ";
				}
			echo $show['produkt_navn'];
	?>	
			</h2></br>
			<p>
	<?php	
			echo nl2br($show['produkt_beskrivelse']);
	?>
		</p>
		</br>
	<?php
			if($show['tilbud_pris'] > 0){
	?>          
		<p>
			<span class="normal_pris">Kr <?php echo $show['produkt_pris'] ?> ,- </span>    
			<br>
			<span class="tilbud_pris">Kr  <?php echo $show['tilbud_pris']?> ,- </span> 
		</p>
	<?php
			}else{
	?>
		<p class="alm_pris">Pris: <?php echo  $show['produkt_pris'] ?> ,-</p>
	<?php
			}
	?>	

		</div>
		<div class="heighter"></div>
	</a>
	</section>
<?php
		}
	}
}else{
	$produkt_query = "SELECT * FROM `produkt`
	INNER JOIN `billed` ON `produkt_id` = `billed`.`fk_produkt_id`
	WHERE `produkt_kategori` = '".$kategori."'
	GROUP BY `produkt_type`

	";

	$produkt_result = $db->query($produkt_query);
	echo "<h3></h3><h2>".mb_strtoupper($kategori, 'UTF-8')."</h2></hr>";
	echo '<section class="kategorier">';
	while($show = mysqli_fetch_assoc($produkt_result)){
?>
	<a href="?side=<?php echo $_GET['side'];?>&kategori=<?php echo $kategori;?>&type=<?php echo $show['produkt_type']?>" class="chapter">
	<img src="img/produkter/small/<?php echo $show['billed_filnavn']; ?>">
	
	<h3>
		<?php echo $show['produkt_type']; ?>
	</h3>
	</a>
	

<?php
	}
	echo '<div class="heighter"></div></section>';
}
?>
<script type="text/javascript">
function show_info(){
	var firma_text = document.getElementById("firma_text");
	if (firma_text.style.display == "block") {
		firma_text.style.display = "none";
	}else{
		firma_text.style.display = "block";
	}
};
</script>

<!-- <div id="div1" class="someclass">
    <img ... id="image1" name="image1" />
</div>
var d = document.getElementById("div1");
	d.className = d.className + " otherclass";
Then


 -->