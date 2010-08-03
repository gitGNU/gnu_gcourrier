<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title>GCourrier</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <script src="javascripts/prototype.js" type="text/javascript"></script>
    <script src="javascripts/scriptaculous.js" type="text/javascript"></script>
  </head>

  <body>
    <div class="page">
      <div id="top_logo">
        <a href=".">
          <img src="images/banniere2.jpg" alt="GCourrier" />
        </a>
      </div>
      <div id="greeting">Connexion:
        <a href="account.php?id=<?php echo $_SESSION['id']; ?>"><?php echo $_SESSION['login']; ?></a>
	(service <?php
	 require_once('functions/db.php');
	 $result = db_execute('SELECT libelle FROM service WHERE id=?', array($_SESSION['idService']));
	 $row = mysql_fetch_array($result);
	 echo $row['libelle'];
	 ?>)
	 - <a href="logout.php">Quitter</a>
      </div>

<?php
require_once('functions/status.php');
while (($msg = status_shift()) !== null)
  echo "<div class='status'>$msg</div>";
