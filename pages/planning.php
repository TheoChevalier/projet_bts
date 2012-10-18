<?php
if(isset($_POST['nb_visite']))
{
  for($i=1;$i <= intval($_POST['nb_visite']);$i++)
  {
    if(isset($_POST['visite'.$i]))
    {
      mysql_query("UPDATE rendez_vous SET visite = '1' WHERE user_id = ".$_POST['visite'.$i]);
    }
  }
}
?>
<div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=planning" class="ariane_hover">Planning</a></div>
<h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Planning journalier</h1>
<p>Clients à visiter aujourd'hui:</p>
  <?php
  //Requête pour sélectionner tous les clients présents dans la table planifier et dont les deux premiers chiffres du code postal correspondent à l’un du/des département(s) dont le reprsésentant s’occupe.
  $requete=mysql_query("SELECT user.user_id, user_name, user_nom, user_prenom, user_mail, user_cp, user_ville, user_adresse, user_tel, user_avatar, creneau, jour, visite
  FROM user, rendez_vous
  WHERE jour = '".date('Y-m-d')."'
  AND rendez_vous.user_id = user.user_id
  AND LEFT(user_cp,2) IN (SELECT code_depart FROM departement, representant WHERE code_rep = rep_id AND code_rep = ".$_SESSION['rep_id'].")
  ORDER BY creneau");
  //On affiche le résultat
?>
  <form method="post" action="" class="commande_montant" >
  <table class="panier">
  <tr class="panier_top">
    <th>Visité</th>
    <th>Créneau</th>
    <th>ID</th>
    <th></th>
    <th>Pseudo</th>
    <th>Prénom</th>
    <th>Nom</th>
    <th>Adresse</th>
    <th>Mail</th>
    <th>Telephone</th>
  </tr>
    <?php
    $i=0;
    while($user = mysql_fetch_array($requete))
    {
      $i++; ?>
        <tr <?php if($user['visite'] == 1) echo 'class="visite"'; ?> >
          <td><input name="visite<?php echo $i; ?>" type="checkbox" value="<?php echo $user['user_id']; ?>" <?php if($user['visite'] == 1) echo 'checked="checked" disabled="disabled"'; ?> /></td>
        <?php 
          switch($user['creneau'])
          {
            case "1":
              echo '<td>8h - 9h</td>';
            break;
            case "2":
              echo '<td>9h - 10h</td>';
            break;
            case "3":
              echo '<td>10h - 11h</td>';
            break;
            case "4":
              echo '<td>11h - 12h</td>';
            break;
            case "5":
              echo '<td>13h - 14h</td>';
            break;
            case "6":
              echo '<td>14h - 15h</td>';
            break;
            case "7":
              echo '<td>15h - 16h</td>';
            break;
            case "8":
              echo '<td>16h - 17h</td>';
            break;
            case "9":
              echo '<td>17h - 18h</td>';
            break;
          }
          ?></td>
          <td><?php echo $user['user_id']; ?></td>
          <td><div class="fiche_joueur_photo" ><img src="images/upload/<?php echo $user['user_avatar'];?>" alt="plop" /></div></td>
          <td class="panier_name"><?php echo utf8_encode($user['user_name']); ?></td>
          <td><?php echo utf8_encode($user['user_prenom']); ?></td>
          <td><?php echo utf8_encode($user['user_nom']); ?></td>
          <td><?php echo utf8_encode($user['user_adresse'].'<br/>'.$user['user_cp'].' '.$user['user_ville']); ?></td>
          <td><?php echo utf8_encode($user['user_mail']); ?></td>
          <td><?php echo utf8_encode('0'.$user['user_tel']); ?></td>
        </tr><?php
    }
  ?>
  </table>
  <input type="hidden" name="nb_visite" value="<?php echo $i; ?>" />
  <button type="submit">Valider</button>
  </form>