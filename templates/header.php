<html>
  <head>
    <title>GCourrier</title>
    <link href="styles.css" rel="stylesheet">
  </head>

  <body>
    <div id="page">
      <div id="top_logo">
        <a href=".">
          <img src="images/banniere2.jpg" alt="GCourrier" />
        </a>
      </div>
      <div id="greeting">Connexion:
        <a href="account.php?id=<?php echo $_SESSION['id']; ?>"><?php echo $_SESSION['login']; ?></a>
	- <a href="logout.php">Quitter</a>
      </div>