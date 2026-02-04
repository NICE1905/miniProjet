
<?php
$dossier_public ="http://localhost/groupeum/mini/public/";
include_once "includes/header.php";
include_once "includes/navbar.php";
include_once "includes/sidebar.php";
require_once "traitements/requets.php";

$page = isset($_GET["page"]) ? $_GET["page"] : "accueil";
if(file_exists("pages/$page.php")){
    include_once "pages/$page.php";
}else{
    include_once "pages/erreur404.php";
}
include_once "includes/footer.php";
?>
