<?php
session_start();
include('includes/fonctions.php');
connexionbdd();
$nb_succes = 0;
$erreur = 0;
$masquer_formulaire = false;
$demander_password = false;
// ---------------- DEBUT SCRIPT CONNEXION -----------------------------------
if(!empty($_POST['pseudo']) && !empty($_POST['mdp']) && $_POST['pseudo'] != "Pseudo / Email")
{
  $titre = "Connexion";
  $result = mysql_query("SELECT rep_id, rep_name, rep_password, rep_mail FROM representant
              WHERE rep_name = '".mysql_real_escape_string(utf8_decode($_POST['pseudo']))."'
              OR rep_name = '".mysql_real_escape_string(strtolower(utf8_decode($_POST['pseudo'])))."'
              OR rep_mail = '".mysql_real_escape_string(utf8_decode($_POST['pseudo']))."'");
  $row = mysql_fetch_array($result);
  $nom_db_tmp = strtolower(utf8_decode($row["rep_name"]));
  $mail_db_tmp = strtolower(utf8_decode($row["rep_mail"]));
  $nom_post_tmp = strtolower(utf8_decode($_POST['pseudo']));
  if(!$result)
  {
    $erreur++;
    $message[$erreur] = "Erreur de la base de données.";
  }
  else
  {
    //Vérification des données, le cas échéant, affichage du message d'erreur correspondant
    if(($nom_post_tmp == $nom_db_tmp) || ($nom_post_tmp == $mail_db_tmp))
    {
      //Vérification de l'exactitude du mot de passe renseigné
      if(md5(utf8_decode($_POST['mdp'])) == $row["rep_password"])
      {
        //Toutes les données ont été vérifiées: création de la session
        $_SESSION['rep_id'] = $row['rep_id'];
        $_SESSION['rep_name'] = $row['rep_name'];
        $_SESSION['rep_password'] = $row['rep_password'];
        unset($_SESSION['user_name']);
        unset($_SESSION['user_id']);
        unset($_SESSION['user_password']);
        unset($_SESSION['level']);
        $masquer_formulaire = true;
        $nb_succes++;
        $succes[$nb_succes] = 'Vous êtes désormais connecté en tant que représentant avec le compte <b>'.htmlspecialchars($_SESSION['rep_name'], ENT_QUOTES).'</b>.';
      }
      else
      {
        $erreur++;
        $message[$erreur] = 'Votre mot de passe est incorrect. Merci de réessayer.';
      }
    }
    else
    {
      $erreur++;
      $message[$erreur] = 'Le compte <b>'.htmlspecialchars($_POST['pseudo'], ENT_QUOTES).'</b> n\'existe pas, merci de rééssayer. (Attention aux majuscules !)';
    }
  }
}
//Si il demmande la deconnexion, on supprime la session et les cookies de connexion
elseif(isset($_GET['deco']))
{
  $titre = "Deconnexion";
  session_destroy();
  unset($_SESSION['rep_id']);
  unset($_SESSION['rep_name']);
  $nb_succes++;
  $succes[$nb_succes] = 'Vous avez correctement été déconnecté.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
include("includes/header.php");
?>
<style>
body{
  background: none;
  background-size: 100% 100%;
  background-attachment: fixed;
  background-color: #111;
  background-image: -moz-radial-gradient(50% 50%, ellipse cover, rgba(255,255,255,0.3) 15%,rgba(255,255,255,0) 100%);
  background-image: -webkit-radial-gradient(50% 50%, ellipse cover, rgba(255,255,255,0.3) 15%,rgba(255,255,255,0) 100%);
  background-image: -o-radial-gradient(50% 50%, ellipse cover, rgba(255,255,255,0.3) 15%,rgba(255,255,255,0) 100%);
  background-image: -ms-radial-gradient(50% 50%, ellipse cover, rgba(255,255,255,0.3) 15%,rgba(255,255,255,0) 100%);
  background-image: radial-gradient(50% 50%, ellipse cover, rgba(255,255,255,0.3) 15%,rgba(255,255,255,0) 100%);}
</style>
<body>
<div id="container">
  <?php
  //Récupération du nombre d'articles stockés dans le cookie utilisateur, pour l'affichage dans le menu
  $nb_art=0;
  $tableau_art_panier = array();
  if(isset($_COOKIE['art_panier']))
  {
    $tableau_art_panier = unserialize($_COOKIE['art_panier']);
    foreach($tableau_art_panier as $art_id => $art_qte) $nb_art = $nb_art + $art_qte;
  }
  include("includes/menu_top.php");
  ?>
  <div class="clear"></div>
  <div class="annonce_page">
    <?php
    //Affichage du tableau des succès, puis du tableau des erreurs, si le nombre d'erreurs (ou succes) est positif
    if($nb_succes != 0)
    {
      echo '<p>Informations:</p>';
      for($j = 1; $j <= $nb_succes; $j++)
      {
        echo '&#8226; '.$succes[$j].'<br />';
      }
    }
    if($erreur != 0)
    {
      echo '<div class="erreurs"><p>Il y a '.$erreur.' erreur(s):</p>';
      for($j = 1; $j <= $erreur; $j++)
      {
        echo '&#8226; '.$message[$j].'<br />';
      }
      echo '</div>';
    }
    if(isset($_GET['message'])) echo htmlspecialchars($_GET['message']);
    ?>
  </div>
</div>
<?php if(!$masquer_formulaire){ ?>
<div id="authentification">
<center><!--Hoooouu, c'est tout sale une balise <center>-->
  <div class="titre_authentification">Se connecter en tant que représentant</div>
    <form name="connexion" method="post" action="connexion_rep.php">
    <p>
      <input name="pseudo" type="text" autocomplete="off" id="pseudo" placeholder="Pseudo / Email" required=""/>
    </p>
    <p>
      <input name="mdp" type="password" autocomplete="off" id="mdp" placeholder="Mot de passe" required=""/>
    </p>
    <p>
      <input type="submit" value="Connexion" />
    </p>

    <p id="aide_connexion">
      <a href="connexion.php?aide"><img src="images/icones/help.png" alt="" /><b> Aide à la connexion.</b></a><br />
      <a href="connexion.php"><img src="images/icones/help.png" alt="" /><b> Connexion client</b></a>
    </p>
    </form>
</div>
</center>
<?php } ?>
</body>
</html>