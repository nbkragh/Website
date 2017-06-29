<!DOCTYPE html>
<html>   
    <head>
        <meta charset="utf-8">
      
        <title>FireShop Admin</title>
        <link rel="stylesheet" href="../style/admin_style.css">
        <meta name="description" content="">
    </head>
<?php
    require_once('../classes.php');
    $db = new Database("fireshop");
    if (!empty($_GET)) {
        $_GET = $db->sanitizer($_GET);
    }
?>

    <body>
    <div id="wrapper">
<?php
    require 'login.php';
?>
        <header>
        
        </header>

<?php
    require_once 'main_nav.php';
?>

        <article>
<?php
    switch (TRUE) {
        case isset($_GET['side']) && $_GET['side'] == "brandeovne":
        // ovenstående case udfører det samme som følgende
        case isset($_GET['side']) && $_GET['side'] == "tilbehor":
            include 'produkter.php';
            break;
        case isset($_GET['side']) && $_GET['side'] == "tilbud":
            include 'tilbud.php';
            break;
        case isset($_GET['side']) && $_GET['side'] == "nyheder":
            include 'nyheder.php';
            break;
        case isset($_GET['side']) && $_GET['side'] == "forside":
            include 'forside.php';
            break;
        case isset($_GET['side']) && $_GET['side'] == "kontakt":
            include 'kontakt.php';
            break;
        case isset($_GET['side']) && $_GET['side'] == "login":
            include 'login.php';
            break;
        default:
            include 'forside.php';
            break;
    }
?>
        </article>

    </div>
    </body>
</html>

<?php
mysqli_close($db->connect);
unset($db);

?>