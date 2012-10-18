<?php

if(isset($_GET['equipe_id']))
{
  $var_equipe_id = mysql_real_escape_string($_GET['equipe_id']);
  $reponse_equipe = mysql_query('SELECT * FROM equipes WHERE equipe_id = "'.$var_equipe_id.'"');
  $var_equipe = mysql_fetch_array($reponse_equipe);?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=equipes" class="ariane_hover">Nos équipes</a> > <a href="index.php?page=equipes&amp;equipe_id=<?php echo $var_equipe_id; ?>" class="ariane_hover"><?php echo utf8_encode($var_equipe['equipe_nom']); ?></a></div>
  <?php
  $reponse = mysql_query('SELECT user.user_id, user.user_nom, user.user_prenom, user.user_avatar, equipes.equipe_id, equipes.equipe_nom FROM user, equipes, equipes_correspondances
  WHERE equipes_correspondances.equipe_id ="'.$var_equipe_id.'" AND equipes_correspondances.equipe_id = equipes.equipe_id AND equipes_correspondances.user_id = user.user_id ORDER BY user.user_prenom');
  ?>
    <h1 class="ribbon shadow"><span class="ribbon_shadow"></span><?php echo utf8_encode($var_equipe['equipe_nom']); ?></h1>
    <div class="equipe">
      <img src="articles/equipes/<?php if($var_equipe['equipe_avatar'] == 0)echo "0"; else echo $var_equipe_id; ?>.jpg" alt="" />
    </div>
    <div class="equipe_description"><?php echo utf8_encode($var_equipe['equipe_desc']); ?></div>
    <div class="equipe_joueurs">
    <h2>Membres</h2>
    <?php
    while($donnees = mysql_fetch_array($reponse))
    {?>
      <div class="fiche_joueur">
        <div class="fiche_joueur_photo" ><img src="images/upload/<?php echo $donnees['user_avatar'];?>" alt="" /></div>
        <div class="fiche_joueur_texte">
        <a href="index.php?page=profil&amp;user_id=<?php echo utf8_encode($donnees['user_id']); ?>" class="lien"><?php echo utf8_encode($donnees['user_prenom'].' '.$donnees['user_nom']);?></a>
        &#149; <a href="connexion.php?id_dest=<?php echo utf8_encode($donnees['user_id']); ?>" class="ariane_hover">Envoyer un message</a></div>
      <div class="clear"></div>
      </div>
   <?php } ?>
    </div>
    <div class="clear"></div>
  <?php
  if(isset($_SESSION['user_id']))
  {
    actualiser_session();
    $requete_verif = mysql_query("SELECT * FROM user, equipes WHERE equipe_id = ".$var_equipe_id." AND user_id = ".$_SESSION['user_id']." AND user_id = equipe_createur");
    if(mysql_fetch_array($requete_verif))
    {
      ?>
      <div class="equipe_description content"><a href="index.php?page=new_equipe&amp;modifier_equipe=<?=$var_equipe_id?>"><img src="images/icones/arrow_right.png" alt="->" /> Editer cette équipe</a></div>
      <?php
    }
  }
}else
{ ?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=equipes" class="ariane_hover">Nos équipes</a></div>
  <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Nos équipes</h1>
  <?php
  $reponse = mysql_query('SELECT * FROM equipes ORDER BY equipe_nom');
  while($donnees = mysql_fetch_array($reponse))
  {
    ?><a href="index.php?page=equipes&amp;equipe_id=<?php echo $donnees['equipe_id']; ?>">
    <div class="cat" style="background: url('articles/equipes/<?php if($donnees['equipe_avatar'] == 0)echo "0"; else echo $donnees['equipe_id']; ?>.jpg') no-repeat;">  
    <div class="cat_name"><?php echo utf8_encode($donnees['equipe_nom']); ?></div>
    </div></a>
  <?php
  }
  if(isset($_SESSION['user_id']))
  {
    actualiser_session();
    ?>
    <div class="clear"></div>
    <div class="equipe_description content"><a href="index.php?page=new_equipe"><img src="images/icones/arrow_right.png" alt="->" /> Créer une équipe</a></div>
    <?php
    }else{?>
    <div class="clear"></div>
    <div class="equipe_description content"><img src="images/icones/attention.png" alt="!" /> Vous devez être <a href="connexion.php">connecté</a> pour créer une équipe.</div>
<?php } 
}?>