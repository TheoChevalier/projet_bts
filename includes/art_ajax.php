<?php
//Inclusion de ma bibliothèque de fonctions
include('fonctions.php');
//Fonction permettant la connexion à la base de données
connexionbdd();
$tab_code = explode('F', $_POST['code']);
$art_id = intval($tab_code[0]);
$code_fourn = intval($tab_code[1]);
$requete_infos = mysql_query("SELECT art_prix, art_qte, art_delai
FROM fournisseurs, fournir
WHERE art_id = ".$art_id."
AND fournisseurs.code_fourn = ".$code_fourn."
AND fournir.code_fourn = fournisseurs.code_fourn");
$infos = mysql_fetch_array($requete_infos);
$infos['art_prix'] = str_replace(".", ",", $infos['art_prix']);
echo 'document.getElementById("prix").innerHTML="'.$infos['art_prix'].'";';
echo 'document.getElementById("stock").innerHTML="'.$infos['art_qte'].'";';
if($infos['art_qte'] > 0) echo 'document.getElementById("delai").innerHTML="2";document.getElementById("dispo").src="images/icones/check.png";';
else echo 'document.getElementById("delai").innerHTML="'.$infos['art_delai'].'"; document.getElementById("dispo").src="images/icones/cross.png";';
 ?>