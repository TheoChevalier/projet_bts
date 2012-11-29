<?php
//On vérifie que l'utilisateur soit connecté
actualiser_session("Vous devez vous connecter pour accéder aux coordonnées des membres.");
//Si l'id utilisateur est défini dans l'url (Partie profil)
if(isset($_GET['user_id']) && !empty($_GET['user_id']))
{
  $user_id = intval($_GET['user_id']);
  //On récupère toutes les données de cet utilisateur
  $requete_profil = mysql_query("SELECT * FROM user WHERE user_id = '".$user_id."'");
  $profil = mysql_fetch_array($requete_profil);
  //On récupère les équipes auxquelles cet utilisateur appartient
  $requete_equipes = mysql_query("SELECT equipe_nom, equipes.equipe_id FROM equipes, equipes_correspondances, user 
  WHERE user.user_id = '".$user_id."'
  AND equipes_correspondances.equipe_id = equipes.equipe_id
  AND equipes_correspondances.user_id = user.user_id 
  ORDER BY equipe_nom");
  //Si l'utilisateur existe
  if(!empty($profil))
  {
    //Si on demande à consulter le profil de l'utilisateur actuellement connnecté
    if($_SESSION['user_id'] == $_GET['user_id'])
    {
      //Si l'utilisateur veut modifier son profil
      if(isset($_GET['modifier']))
      {
        //On masque le formulaire
        $masquer_formulaire = false;
        $erreur = 0;
        $message = "";
        //Si les données de modification du profil ont été envoyées
        if(isset($_POST['modifier_donnees']))
        {
          //On vérifie tous les champs
          if(!preg_match("/^.{1,32}$/i" ,$_POST['TB_user_prenom']))
          {
            $erreur++;
            $message[$erreur] = 'Votre prénom n\'est pas valide.';
          }
          if(!preg_match("/^.{1,32}$/i" ,$_POST['TB_user_nom']))
          {
            $erreur++;
            $message[$erreur] = 'Votre nom n\'est pas valide.';
          }
          if(!isset($_POST['CB_DdN_Jour']) || $_POST['CB_DdN_Jour'] == "--" || $_POST['CB_DdN_Jour'] == "")
          {
            $erreur++;
            $message[$erreur] = 'Votre jour de naissance n\'a pas correctement été rempli.';
          }
          if(!isset($_POST['CB_DdN_Mois']) || $_POST['CB_DdN_Mois'] == "--" || $_POST['CB_DdN_Mois'] == "")
          {
            $erreur++;
            $message[$erreur] = 'Votre mois de naissance n\'a pas correctement été rempli.';
          }
          if(!isset($_POST['CB_DdN_Annee']) || $_POST['CB_DdN_Annee'] == "--" || $_POST['CB_DdN_Annee'] == "")
          {
            $erreur++;
            $message[$erreur] = 'Votre année de naissance n\'a pas correctement été remplie.';
          }
          if(!preg_match("/^.{5,150}$/i" ,$_POST['TB_user_adresse']))
          {
            $erreur++;
            $message[$erreur] = 'Votre adresse n\'est pas valide.';
          }
          if(!preg_match("/^.{4,7}$/i" ,$_POST['TB_user_cp']))
          {
            $erreur++;
            $message[$erreur] = 'Votre code postal n\'est pas valide.';
          }
          if(!preg_match("/^.{2,150}$/i" ,$_POST['TB_user_ville']))
          {
            $erreur++;
            $message[$erreur] = 'Votre ville n\'est pas valide.';
          }
          //On vérifie les champs optionnels
          if(isset($_POST['TB_user_tel']) && !empty($_POST['TB_user_tel']))
          {
            if(!preg_match("#^0[1-9]([-. ]?[0-9]{2}){4}$#" ,$_POST['TB_user_tel']))
            {
              $erreur++;
              $message[$erreur] = 'Votre numéro de téléphone n\'est pas valide.';
            }
          }
          //Si un nouveau mot de passe à été défini, on vérifie qu'il correspond aux critères et à sa confirmation
          if(isset($_POST["TB_user_password"]) && !empty($_POST["TB_user_password"]) && $_POST["TB_user_password"] != "")
          {
            if(!preg_match("/^.{4,32}$/i", $_POST["TB_user_password"]))
            {
              $erreur++;
              $message[$erreur] = 'Votre mot de passe doit comporter au moins 4 caractères. Tous les caractères sont autorisés.';
            }
            if(!isset($_POST["TB_user_password"]) || $_POST["TB_user_password"] != $_POST["TB_Confirmation_user_password"])
            {
              $erreur++;
              $message[$erreur] = 'Votre mot de passe n\'a pas été correctement confirmé.';
            }
          }
          //On vérifie que le mot de passe du compte actuel est présent
          if(!isset($_POST["TB_verif_password"]) || empty($_POST["TB_verif_password"]))
          {
            $erreur++;
            $message[$erreur] = 'Vous n\'avez pas saisi votre mot de passe actuel.';
          }
          else
          {
            $requete_password = mysql_query("SELECT user_password FROM user WHERE user_id = '".$_SESSION['user_id']."'");
            $password = mysql_fetch_array($requete_password);
            if(md5($_POST["TB_verif_password"]) != $password['user_password'])
            {
              $erreur++;
              $message[$erreur] = 'Votre mot de passe actuel est incorrect.';
            }
          }
          //Si une image est envoyée, on la vérifie, l'upload, la redimentionne, et met à jour la table.
          if(!empty($_FILES['fichier']['name']))
          {
            include('includes/image.php');
            //On récupère les informations du fichier (nom, extention, taille...)
            $nom_file = strtolower($_FILES['fichier']['name']);
            $extention = '.'. str_replace('.','',strstr($nom_file, '.'));
            $fichier = $_SESSION['user_id'].$extention;
            //On récupère les informations e l'utilisateur
            $result = mysql_query("SELECT user_id, user_name FROM user WHERE user_id = ".$_SESSION['user_id']);
            $row = mysql_fetch_array($result);
            $chemin = 'images/upload/'.$fichier;
            //Si l'upload et l'enregistrement sur le serveur se passe bien
            if(move_uploaded_file($_FILES['fichier']['tmp_name'],$chemin))
            {
              //Si on arrive à avoir les dimentions à l'aide de cette fonction, c'est forcément une image
              if(!getimagesize($chemin))
              {
                $erreur++;
                $message[$erreur] = "Le format de l'image envoyée n'est pas pris en charge.";
              }
              else
              {
                $x = 170;
                $y = 170;
                $size = getimagesize($chemin);
                if($size[0] >= $size[1]) $y = ($size[1] * 170)/$size[0];
                else $x = ($size[0] * 170)/$size[1];
                //Utilisation des fonctions de la bibliothèque image.php
                $avatar = new Image($chemin);
                $avatar->resize_to($x, $y);
                $avatar->save_as($chemin);
              }
            }
            else
            {
              $erreur++;
              $message[$erreur] = 'Erreur lors de l\'envoi de l\'image.<br />'. $_FILES['fichier']['error'];
            }
          }
          //Si il n'y a pas d'erreur
          if($erreur == 0)
          {
            //Si le jour de naissance est compris entre 1 et 9, on lui ajoute un 0 devant
            if(intval($_POST['CB_DdN_Jour']) < 10) $_POST['CB_DdN_Jour'] = '0'.$_POST['CB_DdN_Jour'];
            //Si le mois de naissance est compris entre 1 et 9, on lui ajoute un 0 devant
            if(intval($_POST['CB_DdN_Mois']) < 10) $_POST['CB_DdN_Mois'] = '0'.$_POST['CB_DdN_Mois'];
            //On met la date au format SQL date
            $DdN = $_POST['CB_DdN_Annee'].'-'.$_POST['CB_DdN_Mois'].'-'.$_POST['CB_DdN_Jour'];
            //On met à jour les informations de l'utilisateur
            mysql_query("UPDATE user SET user_prenom='".mysql_real_escape_string(utf8_decode($_POST['TB_user_prenom']))."',
            user_nom='".mysql_real_escape_string(utf8_decode($_POST['TB_user_nom']))."',
            user_titre='".intval($_POST['TB_user_titre'])."',
            user_adresse='".mysql_real_escape_string(utf8_decode($_POST['TB_user_adresse']))."',
            user_cp='".mysql_real_escape_string(utf8_decode($_POST['TB_user_cp']))."',
            user_datenaiss='".$DdN."',
            user_ville='".mysql_real_escape_string(utf8_decode($_POST['TB_user_ville']))."'
            WHERE user_id='".intval($_SESSION['user_id'])."' ");
            //Si le téléphone (optionnel) est envoyé, on l'ajoute à l'utilisateur
            if(isset($_POST['TB_user_tel']) && !empty($_POST['TB_user_tel']))
            {
              mysql_query("UPDATE user SET user_tel='".mysql_real_escape_string(intval($_POST["TB_user_tel"]))."' WHERE user_id='".intval($_SESSION['user_id'])."' ");
            }
            //Si il y a une image envoyée (qui a été traitée, on passe avatar à la valeur de l'id , afin de correspondre avec le nom de l'image.jpg
            if(!empty($_FILES['fichier']['name'])) mysql_query("UPDATE user SET user_avatar = '".$fichier."' WHERE user_id=".$_SESSION['user_id']);
            if(isset($_POST["TB_user_password"]) && !empty($_POST["TB_user_password"]) && $_POST["TB_user_password"] != "") mysql_query("UPDATE user SET user_password = '".md5($_POST['TB_user_password'])."' WHERE user_id='".$_SESSION['user_id']."' ");
            //On parcours toutes les équipes de l'utilisateur
            while($equipes = mysql_fetch_array($requete_equipes))
            {
              //Si le numéro de cette équipe à été communiqué, on supprime la relation de l'utilisateur à cette équipe.
              if(isset($_POST[$equipes['equipe_id']]))
              {
                mysql_query("DELETE FROM equipes_correspondances WHERE equipe_id = '".$equipes['equipe_id']."' AND user_id = '".$_SESSION['user_id']."'");
              }
            }
            // On re-exécute la requête pour récupérer les informations mises à jour
            $requete_profil = mysql_query("SELECT * FROM user WHERE user_id = '".$user_id."'");
            $profil = mysql_fetch_array($requete_profil);
          }
        }
        //On re-récupère toutes les données de l'utilisateur (Pour le cas où les données sont modifiées, vu que la mise à jour de la table se fait juste au dessus)
        
        //$profil = mysql_fetch_array($requete_profil);
        //Définition du tableau des mois pour l'affichage pour la date de naissance
        $mois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
        ?>
        <script>
        function checkPassword(password1, password2) {
          if (password1.value != password2.value) {
            password2.setCustomValidity('La confirmation du mot de passe est incorrecte.');
          } else {
            password2.setCustomValidity('');
          }
        }
        </script>
        <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=profil&amp;user_id=<?php echo $user_id; ?>" class="ariane_hover">Profil de <?php echo utf8_encode($profil['user_name']);?></a>
        > <a href="index.php?page=profil&amp;user_id=<?php echo $user_id; ?>&modifier" class="ariane_hover">Modifier le profil</a></div>
        <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Modifiez vos informations</h1>
        <div class="equipe">
          <img src="images/upload/<?php echo $profil['user_avatar']; ?>" alt="" />
          <!--<script type="text/javascript" src="js/dragAndDrop.js"></script>
          <p class="box">
            <input type="file" id="fichier" onchange="handleFile(this.file)"/>
            <svg xmlns="http://www.w3.org/2000/svg" width="300" height="150" viewBox="-10 -10 410 210" id="dropbox">
                <path d="M 0 0 L 400 0 L 400 140 L 260 140 L 200 190 L 140 140 L 0 140 Z"/>
                <text x="50%" y="35%">Déposez une image</text>
            </svg>
    
            <div id="preview"></div>
            <button onclick="sendFiles()">Send all files</button>
          </p>-->
        </div>
        <div class="equipe_description">
        <h2>Modifiez votre profil</h2>
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
          <form enctype="multipart/form-data" method="post" action="index.php?page=profil&amp;user_id=<?php echo $_SESSION['user_id']; ?>&amp;modifier" name="modifier" id="modifier">
          <div class="champs">
            <fieldset><legend>Supprimez votre appartenance à une ou plusieurs équipes</legend>
            <p><?php
            $equipe = "";
            $nb=0;
            $requete_equipes = mysql_query("SELECT equipe_nom, equipes.equipe_id FROM equipes, equipes_correspondances, user 
            WHERE equipes_correspondances.equipe_id = equipes.equipe_id
            AND user.user_id = '".$user_id."'
            AND equipes_correspondances.user_id = user.user_id 
            ORDER BY equipe_nom");
            while($equipes = mysql_fetch_array($requete_equipes))
            {
              $nb++;
              echo '<p><input type="checkbox" name="'.$equipes['equipe_id'].'" id="'.$equipes['equipe_id'].'" value="'.$equipes['equipe_id'].'" /> <label for="'.$equipes['equipe_id'].'">'.$equipes['equipe_nom'].'</label></p>';
            } 
            if($nb == 0) echo '<div class="info_fieldset">Vous ne faites parti d\'aucune équipe pour l\'instant...</div>';
            ?>
            </p>
            </fieldset>
          </div>
          <div class="champs">
            <fieldset><legend>Vos coordonnées</legend>
            <p>
              <label>Civilité</label>
              <input type="radio" name="TB_user_titre" value="1" <?php if($profil['user_titre'] == 1) echo 'checked="checked"'; ?> required=""/>M.
              <input type="radio" name="TB_user_titre" value="2" <?php if($profil['user_titre'] == 2) echo 'checked="checked"'; ?> />Mme.
              <input type="radio" name="TB_user_titre" value="3" <?php if($profil['user_titre'] == 3) echo 'checked="checked"'; ?> />Mlle.
            </p>
            <p>
              <label for="TB_user_prenom">Prénom</label>
              <input type="text" id="TB_user_prenom" autocomplete="off" value="<?php echo utf8_encode($profil['user_prenom']); ?>" name="TB_user_prenom" required=""/>
            </p>
            <p>
              <label for="TB_user_nom">Nom</label>
              <input type="text" id="TB_user_nom" autocomplete="off" value="<?php echo utf8_encode($profil['user_nom']); ?>" name="TB_user_nom" required=""/>
            </p>
            <p>
              <select name="CB_DdN_Jour" id="CB_DdN_Jour" class="combobox" required="">
              <?php
              //On récupère la date de naissance et on place jour, mois et année dans des variables séparées.
              list($DBannee, $DBmois, $DBjour) = preg_split('[-.]', $profil['user_datenaiss']);
              
              for($i=0; $i <= 31;$i++)
              {
                echo '<option value="';
                if($i != 0) echo $i;
                echo '"'; if($DBjour == $i) echo 'selected="selected"';
                echo '>';
                if($i != 0) echo $i;
                echo'</option>';
              } ?>
              </select>
              <select name="CB_DdN_Mois" id="CB_DdN_Mois" class="combobox">
              <?php
              for($i=0; $i <= 12;$i++)
              {
                echo '<option value="';
                if($i != 0) echo $i;
                echo '"'; if($DBmois == $i) echo 'selected="selected"';
                echo '>';
                if($i != 0) echo $mois[$i];
                echo'</option>';
              } ?>
              </select>
              <select name="CB_DdN_Annee" id="CB_DdN_Annee" class="combobox">
              <?php
              for($i=0; $i <= 101;$i++)
              {
                $annee = date("Y")-$i+1;
                echo $annee;
                echo '<option value="';
                if($i != 0) echo $annee;
                echo '"'; if($DBannee == $annee) echo 'selected="selected"';
                echo '>';
                if($i != 0) echo $annee;
                echo'</option>';
              } ?>
              </select>
              <label for="CB_DdN_Jour">Date de naissance</label>
              </p>
            <p>
              <label for="TB_user_adresse">Adresse</label>
              <input type="text" id="TB_user_adresse" autocomplete="off" value="<?php echo utf8_encode($profil['user_adresse']); ?>" name="TB_user_adresse" required=""/>
            </p>
            <p>
              <label for="TB_user_cp">Code Postal</label>
              <input type="text" id="TB_user_cp" autocomplete="off" value="<?php echo utf8_encode($profil['user_cp']); ?>" name="TB_user_cp" required=""/>
            </p>
            <p>
              <label for="TB_user_ville">Ville</label>
              <input type="text" id="TB_user_ville" autocomplete="off" value="<?php echo utf8_encode($profil['user_ville']); ?>" name="TB_user_ville" required=""/>
            </p>
            <p>
              <label for="TB_user_tel">Téléphone (optionnel)</label>
              <input type="text" id="TB_user_tel" autocomplete="off" value="<?php if($profil['user_tel'] != "") echo utf8_encode('0'.$profil['user_tel']); ?>" name="TB_user_tel" />
            </p>
            </fieldset>
          </div>
          <div class="champs">
            <fieldset><legend>Informations relatives à votre compte</legend>
            <p>
              <label for="TB_user_password">Nouveau mot de passe</label>
              <input type="password" id="TB_user_password" autocomplete="off" name="TB_user_password" />
            </p>
            <p>
              <label for="TB_Confirmation_user_password">Confirmation</label>
              <input type="password" id="TB_Confirmation_user_password" autocomplete="off" name="TB_Confirmation_user_password" 
              onfocus="checkPassword(document.getElementById('TB_user_password'), this);" oninput="checkPassword(document.getElementById('TB_user_password'), this);" />
            </p>
            <p>
              <label for="fichier">Avatar</label>
              <input type="file" name="fichier" id="fichier" />
            </p>
            </fieldset>
          </div>
          <div class="champs">
          <fieldset><legend>Entrez votre mot de passe, puis cliquez sur envoyer</legend>
            <p>
              <label for="TB_verif_password">Mot de passe</label>
              <input type="password" id="TB_verif_password" autocomplete="off" name="TB_verif_password" required=""/>
            </p>
            <button class="button submit" type="submit" name="modifier_donnees" >Envoyer</button>
          </fieldset>
          </div>
          </form>
        </div>
        </div>
        <div class="clear"></div>
        <?php
      }
      else
      { ?>
        <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=profil&amp;user_id=<?php echo $user_id; ?>" class="ariane_hover">Profil de <?php echo utf8_encode($profil['user_name']);?></a></div>
        <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Profil de <?php echo utf8_encode($profil['user_name']);?></h1>
        <div class="equipe">
          <img src="images/upload/<?php echo $profil['user_avatar']; ?>" alt="" />
        </div>
        <div class="equipe_description">
        <a href="index.php?page=profil&user_id=<?php echo utf8_encode($profil['user_id']); ?>&modifier"><button class="button">Modifier mon profil</button></a>
        <a href="connexion.php?deco" ><button class="button">Déconnexion</button></a>
        <a href="connexion.php?delete"><button class="button">Supprimer mon profil</button></a>
        <a href="index.php?page=commandes"><button class="button">Mes commandes</button></a>
        <p><?php if($profil['user_titre'] == 1) echo 'M.'; elseif($profil['user_titre'] == 2) echo 'Mme.'; else echo 'Mlle.'; 
        echo utf8_encode(' '.$profil['user_prenom'].' '.$profil['user_nom']);?></p>
        <p>Vos équipe(s): <?php
        while($equipes = mysql_fetch_array($requete_equipes))
        {
          echo '<p>&#8226; <a href="index.php?page=equipes&amp;equipe_id='.$equipes['equipe_id'].'" class="lien">'.$equipes['equipe_nom'].'</a></p>';
        } ?>
        </p><?php
        echo '<p>Inscrit depuis le '.formater_date($profil['user_date']).'.</p>';
        $age = age($profil['user_datenaiss']);
        echo 'Vous avez '.$age.' ans.';
        $rang = $profil['user_rang'];
        echo '<p>Votre compte est ';
        switch($rang)
        {
          case 1: echo "banni.";
          break;
          case 2: echo "activé.";
          break;
          case 3: echo "celui d'un administrateur.";
            echo '<p><a href="index.php?page=profil&administrer"><button class="button">Gérer les membres</button></a></p>';
          break;
        }
        echo '</p>';
        ?>
        </div>
        <div class="clear"></div>
        <?php
      }
    }
    else
    { ?>
      <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=profil&amp;user_id=<?php echo $user_id; ?>" class="ariane_hover">Profil de <?php echo utf8_encode($profil['user_name']);?></a></div>
      <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Profil de <?php echo utf8_encode($profil['user_name']);?></h1>
        <div class="equipe">
          <img src="images/upload/<?php echo $profil['user_avatar']; ?>" alt="" />
        </div>
        <div class="equipe_description">
        <p><?php if($profil['user_titre'] == 1) echo 'Mr.'; elseif($profil['user_titre'] == 2) echo 'Mme.'; else echo 'Mlle.'; 
        echo utf8_encode(' '.$profil['user_prenom'].' '.$profil['user_nom']);?></p>
        <p>Équipe(s): <?php
        while($equipes = mysql_fetch_array($requete_equipes))
        {
          echo '<p>&#8226; <a href="index.php?page=equipes&amp;equipe_id='.$equipes['equipe_id'].'" class="lien">'.$equipes['equipe_nom'].'</a></p>';
        } ?>
        </p><?php
        echo '<p>Inscrit depuis le '.formater_date($profil['user_date']).'.</p>';
        $age = age($profil['user_datenaiss']);
        echo 'Il a '.$age.' ans.';
        $rang = $profil['user_rang'];
        echo '<p>Ce compte est ';
        switch($rang)
        {
          case 1: echo "banni.";
          break;
          case 2: echo "activé.";
          break;
          case 3: echo "celui d'un administrateur du site.";
          break;
        }
        echo '</p>';
        ?>
        <p><a href="connexion.php?id_dest=<?php echo utf8_encode($profil['user_id']); ?>" class="page_hover">Envoyer un message</a></p>
        </div>
        <div class="clear"></div>
    <?php
    }
  }
  else echo "<h2>Désolé, mais le profil demandé n'existe pas ...</h2>";
}


//#####  Partie gestion des membres pour les administrateurs, uniquement #####



elseif(isset($_GET['administrer']) && isset($_SESSION['level']) && $_SESSION['level'] == 3)
{
  //Si on reçoit le formulaire, on supprime les comptes. Puis on affiche la liste dans tous les cas.
  if(isset($_POST["nb_comptes"]))
  {
    for($i=0; $i < intval($_POST["nb_comptes"]);$i++)
    {
      if(isset($_POST[$i]))
      {
        mysql_query("DELETE FROM user WHERE user_id = ".intval($_POST[$i]));
      }
    }
  }
  //Si un numéro de page est présent dans l'url, on affichera cette page, sinon on affiche la page 1 par défaut
  if(isset($_GET['p'])) $page = intval($_GET['p']);
  else $page = 1;
  //Si un nombre de comptes par page est défini, on initialise le cookie pour stocker ce nombre, et on défini la variable pour la requête sql
  if(isset($_POST['nb_art']) && !empty($_POST['nb_art']))
  {
    $nb_items_par_page = intval($_POST['nb_art']);
    setcookie('nb_art', $nb_items_par_page, time()+7*24*3600);
  }
  //Sinon on utilise le cookie, ou sinon la valeur par défaut
  elseif(isset($_COOKIE['nb_art'])) $nb_items_par_page = intval($_COOKIE['nb_art']);
  else $nb_items_par_page = 10;
  ?>
<div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=profil&amp;user_id=<?php echo $_SESSION['user_id']; ?>" class="ariane_hover">Mon profil</a>
 > <a href="index.php?page=profil&amp;administrer" class="ariane_hover">Gérer les comptes</a></div>
<h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Gérer les comptes utilisateurs</h1>
  <div class="classement">
    <form class="nb_p" method="post" action="">
    <label for="nb_art">Comptes par page: </label>
    <select name="nb_art" id="nb_art" class="combobox" onChange='this.form.submit();'>
      <option value="1" <?php if ($nb_items_par_page == 1) echo 'selected="selected"' ; ?>>1</option>
      <option value="10" <?php if ($nb_items_par_page == 10) echo 'selected="selected"' ; ?>>10</option>
      <option value="30" <?php if ($nb_items_par_page == 30) echo 'selected="selected"' ; ?>>30</option>
      <option value="50" <?php if ($nb_items_par_page == 50) echo 'selected="selected"' ; ?>>50</option>
      <option value="100" <?php if ($nb_items_par_page == 100) echo 'selected="selected"' ; ?>>100</option>
    </select>
    </form>
    <div class="clear"></div>
  </div>
  <?php

  //Nombre total d'enregistrements répondant à la requête
  $requete=mysql_query("SELECT COUNT(*) AS nb_comptes FROM user ORDER BY user_id");
  $nb_total=mysql_fetch_array($requete);
  $total_items = $nb_total['nb_comptes'];
  //On calcule le nombre de pages nécessaires
  $nb_pages = ceil($total_items / $nb_items_par_page);
  //On défini le numéro du premier article à afficher en fonction du numéro de la page et du nombre d'articles à afficher par page
  $premier_item = ($page - 1) * $nb_items_par_page;
  $requete=mysql_query("SELECT user_id, user_name, user_nom, user_prenom, user_mail FROM user ORDER BY user_id ASC LIMIT ".$premier_item.",".$nb_items_par_page);
  $num=mysql_num_rows($requete);
  //On affiche le résultat
    echo "<b>".$total_items."</b> utilisateur";
    if ($total_items > 1) echo "s"; ?>
    <div class="pages"><?php
    //On affiche les liens de pages, en affichant spécialement la page actuelle
    for($i = 1 ; $i <= $nb_pages ; $i++)
    {
      echo '<a href="index.php?page=profil&amp;administrer&amp;p='.$i.'">';
      echo '<div class=';
      if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
      echo $i.'</div></a>';
    }
    ?>
    </div>
    <form method="post" action="">
  <table class="panier">
  <tr class="panier_top">
    <th>Suppr.</th>
    <th>ID</th>
    <th>Pseudo</th>
    <th>Prénom</th>
    <th>Nom</th>
    <th>Mail</th>
  </tr>
    <?php
    $i =0;
    while($user = mysql_fetch_array($requete))
    { ?>
        <tr>
          <td><input type="checkbox" name="<?php echo $i; $i++; ?>" value="<?php echo $user['user_id']; ?>" /></td>
          <td><?php echo $user['user_id']; ?></td>
          <td class="panier_name"><?php echo utf8_encode($user['user_name']); ?></td>
          <td><?php echo utf8_encode($user['user_prenom']); ?></td>
          <td><?php echo utf8_encode($user['user_nom']); ?></td>
          <td><?php echo utf8_encode($user['user_mail']); ?></td>
        </tr><?php
    }
  ?>
  </table>
  <input type="hidden" name="nb_comptes" value="<?php echo $i; ?>" />
  <input type="submit" value="Valider" />
  </form>
<?php
} ?>
