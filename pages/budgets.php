<?php 
$connexion_autorisee = false;
if (isset($_POST['pseudo']) && !empty($_POST['pseudo']) && isset($_POST['mdp']) && !empty($_POST['mdp'])) {
  $comptes = array(
  "user" => "password",
  "user2" => "password2");
  foreach ($comptes as $user => $password) {
    if ($user == $_POST['pseudo'] && $password == $_POST['mdp'])
      $connexion_autorisee = true;
  }
}
if ($connexion_autorisee != true) {
?>
<div id="auth_statique">

  Vous devez vous connecter à votre compte utilisateur pour accéder à cette page.
    <form name="connexion" method="post" action="">
    <p>
      <input name="pseudo" type="text" autocomplete="off" id="pseudo" placeholder="Nom d'utilisateur" required=""/>
    </p>
    <p>
      <input name="mdp" type="password" autocomplete="off" id="mdp" placeholder="Mot de passe" required=""/>
    </p>
    <p>
      <input type="submit" value="Connexion" />
    </p>
    </form>
</div>
 <?php
 }
 else {
 ?>
Budgets annuels - protection par mot de passe stocké dans le code.
Cette partie est "sécurisée"... Ou pas.
<?php } ?>