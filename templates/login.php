  <form method="POST" action="login.php">
    <table style="margin: 30px 0px 20px 0px; margin-left:auto; margin-right:auto;">
      <tr>
        <td>Identifiant: </td>
        <td><input type="text" name="login" /></td>
        <td rowspan="2"><img src="images/password.png" /></td>
      </tr>
      <tr>
        <td>Mot de passe: </td>
        <td><input type="password" name="password" /></td>
      </tr>
    </table>
    <input type="checkbox" name="remember" checked="checked" value="1">
    Se souvenir de moi pendant une semaine (cookies)</input><br />
    <br />
    <input type="submit" value="Connexion">
  </form>
