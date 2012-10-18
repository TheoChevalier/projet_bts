<?php
//Inclusion de ma bibliothèque de fonctions
include('fonctions.php');
//Fonction permettant la connexion à la base de données
connexionbdd();
if($_POST["box"] == "true")
{
  $planifier = 1;
  mysql_query("DELETE FROM planifier WHERE user_id = ".intval($_POST["user_id"]));
  mysql_query("INSERT INTO planifier (user_id) VALUES (".intval($_POST['user_id']).")");
  echo 'document.getElementById("reponse_rep").innerHTML="Votre représentant se déplacera dès que possible.";';
}
else{
 $planifier = 0;
 mysql_query("DELETE FROM planifier WHERE user_id = ".intval($_POST["user_id"]));
  echo 'document.getElementById("reponse_rep").innerHTML="Vous avez annulé la venue de votre représentant.";';
}
 ?>