<?php
if(isset($_SESSION['level']) && $_SESSION['level'] == 3)
{
	mysql_query("CALL calc_reduc(@res);");
    $reponse = mysql_fetch_array(mysql_query("SELECT @res;"));
    $format = number_format($reponse["@res"], 2, '.', '');
    mysql_query("CALL stared();");
    echo " Somme de l’ensemble des remises : ". $format." €";
  
} else { echo "Connexion admin nécessaire.";}
?>