<?php
$message ="";
$erreur = 0;
if(isset($_POST['TB_equipe_nom']))
{
  if(!preg_match("/^.{1,32}$/i" ,$_POST['TB_equipe_nom']))
  {
    $erreur++;
    $message[$erreur] = 'Le nom de l\'équipe n\'est pas valide.';
  }
  if(!preg_match("/^.{1,1000}$/i" ,$_POST['TA_equipe_desc']))
  {
    $erreur++;
    $message[$erreur] = 'La description de l\'équipe est incorrecte. 1000 caractères maximum!';
  }
  for($i = 1; $i <= $_POST['TB_equipe_nb']; $i++)
  {
    if(!preg_match("^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$^", $_POST["TB_equipe_mail_".$i]))
    {
      $erreur++;
      $message[$erreur] = 'L\'adresse email (N°'.$i.') n\'est pas valide.';
    }
  }
  if($erreur == 0)
  {
    $requete_createur = mysql_query("SELECT * FROM user WHERE user_id = '".$_SESSION['user_id']."'");
    $createur = mysql_fetch_array($requete_createur);
    $equipe_nom = mysql_real_escape_string(utf8_decode($_POST["TB_equipe_nom"]));
    $equipe_desc = mysql_real_escape_string(utf8_decode($_POST["TA_equipe_desc"]));
    mysql_query("INSERT INTO equipes( equipe_nom, equipe_desc, equipe_createur) VALUES ('".$equipe_nom."','".$equipe_desc."', '".$createur['user_id']."')");
    $id = mysql_insert_id();
    for($i = 1; $i <= $_POST['TB_equipe_nb']; $i++)
    {
      $mail = mysql_real_escape_string(utf8_decode($_POST["TB_equipe_mail_".$i]));
      $verif_compte = mysql_query("SELECT * FROM user WHERE user_mail = '".$mail."'");
      if(mysql_fetch_array($verif_compte))
      {
        $donnees = mysql_fetch_array($verif_compte);
        mysql_query("INSERT INTO equipes_correspondances(equipe_id, user_id) VALUES ('".$id."','".$donnees['user_id']."')");
      }
      else
      {
        mysql_query("INSERT INTO equipes_ajouts(equipe_id, user_mail) VALUES ('".$id."','".$mail."')");
        
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail))
        {
          $passage_ligne = "\r\n";
        }
        else
        {
          $passage_ligne = "\n";
        }
        $subject = $createur['user_prenom'].' '.$createur['user_nom'].' vous à ajouté à l\'équipe '.$equipe_nom.'.'.$passage_ligne;
        $mail_activation = '<html>
        <head>
        <title>Ajout dans l\'équipe '.$equipe_nom.'</title>
        </head>
        <body>
        <p><b>L\'utilisater '.$createur['user_prenom'].' '.$createur['user_nom'].' vous à ajouté à l\'équipe '.$equipe_nom.' sur le site de la Ligue de Paintball de Lorraine.
        Pour apparaître dans la liste des joueurs, il vous suffit de créer un compte sur notre site en cliquant ou en copiant dans votre navigateur le lien suivant s\'il n\'est pas cliquable :</b><br />
        <a href="'.ROOTPATH.'/connexion.php?mail='.$mail.'">'.ROOTPATH.'/connexion.php?mail='.$mail.'</a></p>
        <p>L\'équipe.</p>
        </font>
        </body>
        </html>';
        $headers  = 'MIME-Version: 1.0' . $passage_ligne;
        $headers .= 'Content-type: text/html; charset=utf-8' . $passage_ligne;
        $headers .= 'From: "La Ligue de Paintball de Lorraine" <test@theochevalier.fr>' . $passage_ligne;
        mail($mail, '=?UTF-8?B?'.base64_encode($subject).'?=', $mail_activation, $headers);
      }
    }
  }
}
if(isset($_POST['TB_equipe_nb']))
{
  if($_POST['TB_equipe_nb'] < 1 || $_POST['TB_equipe_nb'] > 100)
  {
    $erreur++;
    $message[$erreur] = 'Vous devez entrer un nombre compris entre 1 et 100.';
  }
}
if(isset($_GET['editer_equipe']))
{
  if(isset($_SESSION['user_id']))
  {
    actualiser_session();
    $requete_verif = mysql_query("SELECT * FROM user, equipes WHERE user_id = equipe_createur AND equipe_id = ".$var_equipe_id." AND user_id = ".$_SESSION['user_id']);
    if(mysql_fetch_array($requete_verif))
    {
      ?>
      <div class="equipe_description content"><a href="index.php?page=new_equipe&amp;modifier_equipe=<?=$var_equipe_id?>"><img src="images/icones/arrow_right.png" alt="->" /> Editer cette équipe</a></div>
      <?php
    }
    else
    {
      $erreur++;
      $message[$erreur] = 'Vous n\'êtes pas le créateur de cette équipe, vous ne pouvez donc pas la modifier.';
    }
  }
  else header('location:connexion.php?message=Vous devez vous connecter pour modifier une équipe.');
}
?>
<div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a>
 > <a href="index.php?page=equipes" class="ariane_hover">Equipes</a>
 > <a href="index.php?page=new_equipe" class="ariane_hover">Créer une équipe</a></div>
<h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Créer une équipe</h1>
<div class="equipe_description content">
<h2>Entrez les informations relatives à la création d'une équipe</h2>
<div class="form_message">
<div class="form_erreur">
  <?php
  if($erreur != 0)
  {
    echo '<p>Il y a '.$erreur.' erreur(s):</p>';
    for($i = 1; $i <= $erreur; $i++)
    {
      echo '&#8226; '.$message[$i].'<br />';
    }
  } ?>
</div>
  <form enctype="multipart/form-data" method="post" action="index.php?page=new_equipe">
  <div class="champs">
    <?php if(isset($_POST['TB_equipe_nb']) && $erreur == 0){ ?>
    <fieldset><legend>Informations générales</legend>
    <p>
      <label for="TB_equipe_nom">Nom de l'équipe</label>
      <input type="text" id="TB_equipe_nom" autocomplete="off" value="<?php if(isset($_POST['TB_equipe_nom'])) echo $_POST['TB_equipe_nom']; ?>" name="TB_equipe_nom" />
    </p>
    <p>
      <label for="TA_equipe_desc">Desciption de l'équipe</label>
      <textarea id="TA_equipe_desc" name="TA_equipe_desc"><?php if(isset($_POST['TA_equipe_desc'])) echo $_POST['TA_equipe_desc']; ?></textarea>
    </p>
    </fieldset>
    <fieldset><legend>Options</legend>
    <p>
      <label for="upload_box">Logo de l'équipe</label>
      <div id="upload_box">
      <script type="text/javascript" src="js/createUpload.js"></script>
      <script type="text/javascript">
        (function() {
        var button = uploadButton.create({
          text: 'Parcourir',
          name: 'fichier',
          className: 'upload_button',
          hoverClassName: 'upload_button_hover',
          disabledClassName: 'upload_button_disabled',
          uploadWhenChanged: false,
        });
        button = document.getElementById('upload_box').appendChild(button);
        document.getElementsByTagName('body')[0].appendChild(a);
        })();
      </script>
      </div>
    </p>
    </fieldset>
    <fieldset><legend>Renseignez les emails des différents co-équipiers</legend>
    <p>
    <?php for($i = 1; $i <= $_POST['TB_equipe_nb']; $i++)
    {
      ?>
      <p>
        <label for="TB_equipe_email_<?=$i?>">Adresse email (<?=$i?>)</label>
        <input type="text" id="TB_equipe_email_<?=$i?>" autocomplete="off" value="<?php if(isset($_POST['TB_equipe_email_'.$i])) echo $_POST['TB_equipe_email_'.$i]; ?>" name="TB_equipe_email_<?=$i?>" />
      </p>
      <?php
    } ?>
    </fieldset>
    <p>
      <input type="submit" value="Envoyer" />
    </p>
    <?php }else{ ?>
    <fieldset><legend>Entrez le nombre de co-équipiers de l'équipe</legend>
    <form method="post" action="index.php?page=new_equipe" name="equipe_nb">
    <p>
      <label for="TB_equipe_nb">Nombre de co-équipiers</label>
      <input type="text" id="TB_equipe_nb" size="1" class="equipe_nb" autocomplete="off" value="<?php if(isset($_POST['TB_equipe_nb'])) echo $_POST['TB_equipe_nb']; ?>" name="TB_equipe_nb" />
    </p>
    </form>
    </fieldset>
    <p>
      <input type="submit" value="Envoyer" />
    </p>
    <?php } ?>
    
  </div>
  </form>
</div>
</div>
<div class="clear"></div>