<?php
$numero= $_POST['numero'];
header("LOCATION:voirCourrier.php?idCourrierRecherche=".$numero."&type=".$_GET['type']." ");
?>
