<!DOCTYPE html>
<html>   
    <head>
        <meta charset="utf-8">
      
        <title>FireShop</title>
        <link rel="stylesheet" href="style/style.css">
        <meta name="description" content="">
    </head>
<!DOCTYPE html>
<html>   
    <head>
        <meta charset="utf-8">
      
        <title>FireShop</title>
        <link rel="stylesheet" href="style/style.css">
        <meta name="description" content="">
    </head>
<?php

    require_once('classes.php');
    $db = new Database("fireshop");
    if (!empty($_GET)) {
        $_GET = $db->sanitizer($_GET);
    }
?>

    <body>
    <div id="wrapper">
        <header>
<?php
    $header_i = rand ( 1 , 5 );
?>
            <img src="img/site/headerimage<?php echo $header_i;?>.jpg"/>
            <form action="index.php?side=search" method="get">
                <input type="hidden" name="side" value="soeg" hidden>
                <input type="text" name="search">
                <input type="submit" value="SØG"></br>
                <a href="?side=soeg">Avanceret søg</a>
            </form>
        </header>

<?php
    require_once 'main_nav.php';
    
?>


        <article>
<?php
    if (empty($_GET['side'])) {
        include 'forside.php';
    }else{
        switch ($_GET['side']) {
            case "Brændeovne":
            // ovenstående case udfører det samme som følgende fordi der ikke er noget " ; "
            case "Tilbehør":
                include 'produkter.php';
                break;
            case "kontakt":
                include 'kontakt.php';
                break;
            case "soeg":
                include 'search.php';
            break;
            case "nyheder":
                include 'nyheder.php';
                break;
            default:
                include 'forside.php';
                break;
        }
    }
?>

        </article>
        <aside>
            <h2>Tilbud</h2>
<?php
    $tilbud_sql = "SELECT * FROM `tilbud` INNER JOIN `produkt` ON `produkt_id` = `tilbud`.`fk_produkt_id` INNER JOIN `billed` ON `billed`.`fk_produkt_id` = `produkt_id`
    WHERE `tilbud_status` > 0 AND `tilbud_start` < NOW() < `tilbud_slut` ORDER BY RAND() LIMIT 3";
    $tilbud_result = $db->query($tilbud_sql);

    while($tilbud = mysqli_fetch_assoc($tilbud_result)){
?>
    <div class="tilbud">
        <a href="?side=<?php echo $tilbud['produkt_kategori'];?>&type=<?php echo $tilbud['produkt_type'];?>&produkt=<?php echo $tilbud['produkt_id'];?>">
        <img src="img/produkter/small/<?php echo $tilbud['billed_filnavn']?>">
        <h4><?php echo $tilbud['produkt_navn']?></h4>




<?php
    if($tilbud['tilbud_pris'] > 0){
?>          <p>
                <span class="normal_pris">Kr <?php echo $tilbud['produkt_pris'] ?> ,- </span> 
                     
                <br>
                <span class="tilbud_pris">Kr  <?php echo $tilbud['tilbud_pris']?> ,- </span> 
            </p>
<?php
    }else{
?>
            <p class="alm_pris">Pris: <?php echo  $tilbud['produkt_pris'] ?> ,-</p>
<?php
}
?>       
        </a>
    </div>
<?php       
    }

?>
        </aside>
        <footer>
            <?php 
            $kontakt_query = $db->query("SELECT * FROM `kontakt` LIMIT 1");

            $kontakt = mysqli_fetch_assoc($kontakt_query);
            echo '<h4>'.$kontakt['kontakt_firmanavn'].' - '.$kontakt['kontakt_adresse'].' - '.$kontakt['kontakt_postnr'].' -  '.$kontakt['kontakt_tlf'].' - '.$kontakt['kontakt_email'].'</h4>'
?>
        </footer>
    </div>
    </body>
</html>

<?php
mysqli_close($db->connect);
unset($db);
?>