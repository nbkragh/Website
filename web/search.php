
<?php
    require_once('classes.php');
    $db = new Database("fireshop");
    $searched = $_GET;
    unset($searched['side']);
	if(isset($searched['pris']) and filter_var($searched['pris'], FILTER_VALIDATE_INT ) == FALSE){
		$db->save_message("Prisen skal være et tal");
		
	}
?>


<form action="index.php" method="get" class="searcher">
	<input type="hidden" name="side" value="soeg">
	<label>kategori:</label>
	<select  id="select_kat" name="kategori" onchange="changer(this.value)">
		<option value="">-- vælg --</option>
		<option value="Brændeovne" <?php $selected = (in_array("Brændeovne", $searched)) ? "selected" : "" ; echo $selected;?>>Brændeovne</option>
		<option value="Tilbehør" <?php $selected = (in_array("Tilbehør", $searched)) ? "selected" : "" ; echo $selected;?>>Tilbehør</option>
	</select></br>

<label for="type" id="label_type">Mærke:</label>
		<select name="type" id="option_type" >
		</select>
</br>
<label for="sog_pris">Pris:</label>
<input type="number" id="sog_pris" name="pris" min="0" max="999999" value="<?php $sog_pris = (!empty($searched['pris'])) ? $searched['pris'] : "" ; echo $sog_pris;?>"></br>
<label for="sog_ord">søgeord:</label>
<input type="text" name="search" id="sog_ord" value="<?php $soge_ord = (!empty($searched['search'])) ? $searched['search'] : "" ; echo $soge_ord;?>"></br>
	<input type="submit" value="Søg">
</form>
<?php

$sog_sql =" SELECT * FROM `produkt` LEFT JOIN `tilbud` ON `tilbud`.`fk_produkt_id` =`produkt_id`  INNER JOIN `billed` ON `produkt_id` = `billed`.`fk_produkt_id` WHERE 1=1 ";


if(!empty($searched['search']) or !empty($searched['pris']) or !empty($searched['type']) or !empty($searched['kategori']) ){

	if(!empty($searched['pris'])){
		$sog_sql .= " AND (`produkt_pris` <'".$searched['pris']."' OR `tilbud_pris` <'".$searched['pris']."' )";
	}

	$searchword = " `produkt_navn` LIKE '%".$searched['search']."%'";

	$search_type = " OR (`produkt_type` LIKE '%".$searched['search']."%' AND `produkt_kategori` = 'Brændeovne')";

	$search_kategori = " OR `produkt_kategori` LIKE '%".$searched['search']."%'";


	if(!empty($searched['kategori'])){
		$search_brand = " AND `produkt_kategori` ='".$searched['kategori']."' ";
		$sog_sql .= $search_brand;
		$search_kategori ="";
	}

	if(!empty($searched['type'])){
		$search_type = " AND `produkt_type` = '".$searched['type']."'";
		$sog_sql .= $search_type;
		$search_type ="";
	}

	if (!empty($searched['search'])) {
		$sog_sql .= " AND (  ";
		$sog_sql .= $searchword.$search_type.$search_kategori." )  ";
	}
	
	$sog_result = $db->query($sog_sql);
	if (mysqli_num_rows($sog_result) <1 ) {
		echo '<p style="font-style: italic; text-align:center;margin-top:50px;">Der er desværre ikke nogen emner, der matcher dine søgekriterier.<p>';
	} else {

	
	while($show = mysqli_fetch_assoc($sog_result)){
?>
	<section class="listeinfo" >
	<a href="?side=<?php echo $show['produkt_kategori'];?>&type=<?php echo $show['produkt_type']?>&produkt=<?php echo $show['produkt_id'];?>">

		<div class="img_box">
			<img src="img/produkter/small/<?php echo $show['billed_filnavn']?>" >
		</div>
		<div class="text_box">
			<h2>
	<?php 
			if ($show['produkt_kategori'] == "Brændeovne") {
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

	
}

?>
<script type="text/javascript">
	function changer(selected){

		if( selected =="Brændeovne" ){
			document.getElementById("option_type").disabled = false;
			document.getElementById("label_type").innerHTML = "Mærke :";
			// document.getElementById("label_marke").style.display = "inline-block";
			// document.getElementById("option_marke").style.display = "inline-block";
			document.getElementById("option_type").innerHTML ='<option value="">-- vælg --</option><option value="Varde"  >Varde</option><option value="Jydepejsen"  >Jydepejsen</option><option value="Wiking" >Wiking</option><option value="TermaTech" >TermaTech</option><option value="Morsø" >Morsø</option>';
			// document.getElementById("option_type").style.display = "none";
			// document.getElementById("option_type").innerHTML = "";	
		
		};
		if(selected =="Tilbehør"){
			document.getElementById("option_type").disabled = false;
			document.getElementById("label_type").innerHTML = "Type :";
			// document.getElementById("label_marke").style.display = "none";
			// document.getElementById("option_marke").style.display = "none";
			// document.getElementById("option_marke").innerHTML = "";
			// document.getElementById("option_type").style.display = "inline-block";
			document.getElementById("option_type").innerHTML ='<option value="">-- vælg --</option><option value="Brændespande" >Brændespande</option><option value="Pejsesæt" >Pejsesæt</option><option value="Optænding" >Optænding</option><option value="Vedligeholdelse" >Vedligeholdelse</option>';
			
		};
		if(selected ==""){
			document.getElementById("option_type").disabled = true;
			// document.getElementById("option_marke").style.display = "none";
			// document.getElementById("label_type").style.display = "none";
			// document.getElementById("label_marke").style.display = "none";
			// document.getElementById("option_type").style.display = "none";
			// document.getElementById("option_marke").innerHTML = "";
			document.getElementById("option_type").innerHTML = "";
		};

	};
	changer(document.getElementById("select_kat").value);
	

</script>