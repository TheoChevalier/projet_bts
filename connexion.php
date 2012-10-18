<?php
session_start();
include('includes/connexion.php');
include('includes/fonctions.php');
include('includes/image.php');
connexionbdd();
$nb_succes = 0;
$erreur = 0;
$masquer_formulaire = false;
$demander_password = false;
//On appelle la fonction actualiser_session(); si le cookie pour la connexion auto est présent
if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_password']) || !empty($_GET['id_dest'])) actualiser_session();

/*
 *  Si l'utilisateur est déjà connecté et qu'il ne souhgaite pas se déconnecter, on lui signifie qu'il est déjà et on masque le formulaire
 */
  
if(isset($_SESSION['user_id']) && !isset($_GET['deco']) && $_SERVER['SCRIPT_NAME'] == '/connexion.php')
{
  $titre = "Connexion";
  $nb_succes++;
  $succes[$nb_succes] = 'Vous êtes déjà connecté avec le pseudo <b>'.htmlspecialchars($_SESSION['user_name'], ENT_QUOTES).'</b>.
  <br /><a href="connexion.php?deco"><img src="images/icones/sign_out.png" alt"" align="top" /> Se déconnecter</a>';
  $masquer_formulaire = true;
}

/* 
 * Si il demmande la deconnexion, on supprime la session et les cookies de connexion
 */
 
elseif(isset($_GET['deco']))
{
  $titre = "Deconnexion";
  session_destroy();
  unset($_SESSION['user_id']);
  unset($_SESSION['user_name']);
  $nb_succes++;
  $succes[$nb_succes] = 'Vous avez correctement été déconnecté.';
}
/*
 *  Si il demande la suppression de son compte
 */
elseif(isset($_GET['delete']))
{
  //On vérifie que le champ caché du formulaire est présent
  if(isset($_POST['delete_y']) && actualiser_session())
  {
    $titre = "Compte supprimé";
    //Requêtes pour supprimer les entrées dans la bdd
    mysql_query("DELETE FROM commandes, carte, user WHERE commandes.com_user = user.user_id AND carte.com_key = commandes.com_key AND carte.com_paye = '1'");
    mysql_query("DELETE FROM equipes_correspondances WHERE equipes_correspondances.user_id = '".$_SESSION['user_id']."'");
    mysql_query("DELETE FROM carte, user WHERE carte.user_id = user.user_id AND carte_paye = '1'");
    mysql_query("DELETE FROM user WHERE user_id = '".$_SESSION['user_id']."'");
    //Suppresion de la session et des différents cookies éventuels
    session_destroy();
    vider_cookie();
    setcookie("art_panier", false, time() - 3600);
    setcookie("key_panier", false, time() - 3600);
    unset($_COOKIE["art_panier"]);
    unset($_COOKIE["key_panier"]);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    $nb_succes++;
    $succes[$nb_succes] = 'Votre compte et l\'ensemble de vos données ont été supprimés.';
  }
  //Sinon si le champs caché du formulaire pour annuler l'action est présent, redirection à l'accueil
  elseif(isset($_POST['delete_n']))
    header('location:index.php');
  //Sinon il n'est pas connecté ou que aucun des deux champs n'est présent, affichage du message d'erreur
  else
  {
    $message ='Vous devez d\'abord vous connecter pour supprimer votre compte.';
    if(!actualiser_session($message))
    {
      $erreur++;
      $message[$erreur] = 'Vous devez d\'abord vous <a href="connexion.php">connecter</a> pour supprimer votre compte.';
    }
    $titre = "Supprimer le compte";
    $nb_succes++;
    $succes[$nb_succes] = 'Êtes-vous certain de vouloir supprimer votre compte et l\'ensemble de vos données ? Cette action est irréversible.<br/><br/>
    <form enctype="multipart/form-data" action="connexion.php?delete" method="post" ><input type="submit" name="delete_y" value="Oui, supprimez mon compte." /><input type="submit" name="delete_n" value="Pas fou, non? Sortez-moi de là !" /></form>';
    $masquer_formulaire = true;
  }
}
//Si l'action provient du menu d'aide
elseif(isset($_GET['aide']))
{
  //Si cela concerne la réinitialisation du mot de passe
  if(isset($_POST['aide_password']))
  {
    //Si le champs mail est présent
    if(isset($_POST['aide_password_mail']))
    {
      //Si il n'est pas vide
      if($_POST['aide_password_mail'] != "")
      {
        $to = strtolower(htmlspecialchars($_POST["aide_password_mail"], ENT_QUOTES));
        //On vérifie que ce mail est dans la BDD
        $mail = mysql_query("SELECT user_mail FROM user WHERE user_mail = '".$to."'");
        //Si il est dans la BDD
        if(mysql_num_rows($mail) > 0)
        {
          //On génère une clé aléatoire de 16 caractères
          $caracteres = array("a", "b", "c", "d", "e", "f", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
          shuffle($caracteres);
          $user_key = "";
          for($n=0; $n <= 15; $n++)
          {
            $user_key .= $caracteres[$n];
          }
          //On enregistre la clé
          mysql_query("UPDATE user SET user_key_reinit = '".$user_key."' WHERE user_mail = '".$to."'");
          $requete_user= mysql_query("SELECT user_id FROM user WHERE user_mail = '".$to."'");
          $id = mysql_fetch_array($requete_user);
          $id = $id['user_id'];
          //On définit le type d'adresse mail pour utiliser le bon saut de ligne
          if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $to)) $passage_ligne = "\r\n";
          else $passage_ligne = "\n";
          $subject = 'Réinitialisation de votre mot de passe LPL' . $passage_ligne;
          $mail_activation = '<html>
          <head>
          <title>Réinitialisation du mot de passe</title>
          </head>
          <body>
          <p><b>Pour réinitialiser votre mot de passe, merci de cliquer ou de copier dans votre navigateur le lien suivant s\'il n\'est pas cliquable :</b><br />
          <a href="'.ROOTPATH.'/connexion.php?id='.$id.'&key_reinit='.htmlspecialchars($user_key, ENT_QUOTES).'">'.ROOTPATH.'/connexion.php?id='.htmlspecialchars($id, ENT_QUOTES).'&key_reinit='.htmlspecialchars($user_key, ENT_QUOTES).'</a></p>
          <p>Vous serez alors invité à saisir un nouveau mot de passe pour votre compte.</p>
          <p>L\'équipe.</p>
          </font>
          </body>
          </html>';
          //Définition de l'en-tête et envoi du mail
          $headers  = 'MIME-Version: 1.0' . $passage_ligne;
          $headers .= 'Content-type: text/html; charset=utf-8' . $passage_ligne;
          $headers .= 'From: "Ligue de Paintball de Lorraine" <test@theochevalier.fr>' . $passage_ligne;
          mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $mail_activation, $headers);
          $nb_succes++;
          $succes[$nb_succes] = 'Un email vous à été envoyé à l\'adresse '.$to.'. Vérifiez vos mails, puis suivez les instructions du mail pour réinitialiser votre mot de passe.';
          unset($_POST['aide_password']);
        }
        else
        {
          $erreur++;
          $message[$erreur] = 'Cette adresse mail est inconnue. Merci de recommencer.';
        }
      }
      else
      {
        $erreur++;
        $message[$erreur] = 'Vous n\'avez pas saisi votre adresse mail. Merci de recommencer.';
      }
    }
  }
  //Sinon si le nouveau mot de passe est envoyé
  elseif(isset($_POST['aide_password_new']) && isset($_POST['aide_password_new_conf']))
  {
    //Contrôle par expressions régulières si il est correct...
    if(!preg_match("/^.{4,32}$/i", $_POST["aide_password_new"]))
    {
      $erreur++;
      $message[$erreur] = 'Votre mot de passe doit comporter au moins 4 caractères. Tous les caractères sont autorisés.';
    }
    //...et si il à été correctement confirmé
    if($_POST["aide_password_new"] != $_POST["aide_password_new_conf"])
    {
      $erreur++;
      $message[$erreur] = 'Votre mot de passe n\'a pas été correctement confirmé.';
    }
    //Vérification que la clé de réinitialisation et l'identifiant utilisateur sont présents et corrects
    if(isset($_POST['aide_password_key_reinit']) && isset($_POST['aide_password_key_id']))
    {
      $key = strtolower(htmlspecialchars($_POST["aide_password_key_reinit"], ENT_QUOTES));
      $id = strtolower(intval($_POST["aide_password_key_id"]));
      $key_reinit = mysql_query("SELECT user_key_reinit FROM user WHERE user_key_reinit = '".$key."' AND user_id = '".$id."'");
      if(mysql_num_rows($key_reinit) <= 0)
      {
        $erreur++;
        $message[$erreur] = 'La clé de réinitialisation est incorrecte ou à déjà été utilisée.';
      }
    }
    else
    {
      $erreur++;
      $message[$erreur] = 'La clé de réinitialisation est absente.';
    }
    //Si on a aucune erreur, on remplace le mot de passe
    if($erreur == 0)
    {
      mysql_query("UPDATE user SET user_password = '".md5($_POST["aide_password_new"])."' WHERE user_id = '".$id."'");
      $nb_succes++;
      $succes[$nb_succes] = 'Votre mot de passe à été correctement changé, vous pouvez dès à présent vous connecter à votre compte à l\'aide de votre nouveau mot de passe.';
    }
  }
  //Cas si l'utilisateur à envoyé le formulaire de renvoi du mail d'activation
  elseif(isset($_POST['aide_mail']))
  {
    //Si le mail est présent
    if(isset($_POST['aide_mail_mail']))
    {
      //Si il n'est pas vide
      if($_POST['aide_mail_mail'] != "")
      {
        $to = strtolower(htmlspecialchars($_POST["aide_mail_mail"], ENT_QUOTES));
        //On récupère les infos de l'utilisateur à l'aide de son mail
        $requete_user = mysql_query("SELECT user_id, user_key, user_activation FROM user WHERE user_mail = '".$to."'");
        //Si l'utilisateur existe bien
        if(mysql_num_rows($requete_user) > 0)
        {
          $user = mysql_fetch_array($requete_user);
          //Si le compte n'est pas déjà activé
          if($user['user_activation'] == 0)
          {
            //Définition du bon passage de ligne
            if(!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $to)) $passage_ligne = "\r\n";
            else $passage_ligne = "\n";
            $subject = 'Activation de votre compte'.$passage_ligne;
            $mail_activation = '<html>
            <head>
            <title>Activation de votre compte</title>
            </head>
            <body>
            <p><b>Vous avez demandé le renvoi du mail d\'activation de votre compte, merci de cliquer ou de copier dans votre navigateur le lien suivant s\'il n\'est pas cliquable :</b><br />
            <a href="'.ROOTPATH.'/connexion.php?id='.$user['user_id'].'&clef='.$user['user_key'].'">'.ROOTPATH.'/connexion.php?id='.$user['user_id'].'&clef='.$user['user_key'].'</a></p>
            <p>L\'équipe.</p>
            </font>
            </body>
            </html>';
            //Définition des en-têtes et envoi du mail
            $headers  = 'MIME-Version: 1.0'.$passage_ligne;
            $headers .= 'Content-type: text/html; charset=utf-8'.$passage_ligne;
            $headers .= 'From: "La Ligue de Paintball de Lorraine" <test@theochevalier.fr>'.$passage_ligne;
            mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $mail_activation, $headers);
            $nb_succes++;
            $succes[$nb_succes] = 'Un email vous à été envoyé à l\'adresse '.$to.'. Vérifiez vos mails, puis suivez les instructions du mail pour activer votre compte.';
            unset($_POST['aide_mail']);
          }
          else
          {
            $erreur++;
            $message[$erreur] = 'Ce compte est déjà activé. Vous pouvez vous connecter normalement.';
          }
        }
        else
        {
          $erreur++;
          $message[$erreur] = 'Cette adresse mail est inconnue.';
        }
      }
      else
      {
        $erreur++;
        $message[$erreur] = 'Vous n\'avez pas renseigné votre email.';
      }
    }
  }
  //Cas par défaut: affichage du menu d'aide
  else
  {
    $nb_succes++;
    $succes[$nb_succes] = 'DON\'T PANIC! Quel est votre problème ?<br/><br/><form enctype="multipart/form-data" action="connexion.php?aide" method="post" >
    <button type="submit" name="aide_password">J\'ai oublié mon mot de passe ... <img src="images/icones/snif.gif" alt="" /></button>
    <button type="submit" name="aide_mail">Je n\'ai pas reçu le mail d\'activation ... <img src="images/icones/calim.gif" alt="" /></button></form>
    <br/><br/><a href="connexion.php?delete" class="button"><button>Je me suis trompé en créant mon compte, je veux recommencer !</button></a>';
  }
  $masquer_formulaire = true;  
}

/*
 *  Script Réinitialisation de mot de passe et affichage du formulaire pour saisir le nouveau  mdp
 */

elseif(isset($_GET["key_reinit"]) && isset($_GET["id"]))
{
  //Vérification de la clé de réinitialisation et de l'id utilisateur
  if(preg_match("#^[a-f0-9]{16}$#", strtolower($_GET["key_reinit"])) && preg_match("#^[0-9]+$#", $_GET["id"]))
  {
    //Vérification de la correspondance avec la BDD
    $result = mysql_query("SELECT user_id, user_key_reinit FROM user WHERE user_id = '".$_GET["id"]."' AND user_key_reinit = '".strtolower($_GET["key_reinit"])."'");
    //Cas où la requête échoue
    if(!$result)
    {
      $erreur++;
      $message[$erreur] = "Une erreur est survenue lors du changement de mot de passe de votre compte utilisateur.";
    }
    else
    {
      //Redirection forcée si la clé et l'user_id ne correspondent pas dans la BDD
      if(mysql_num_rows($result) == 0)
        header("Location: index.php");
      else
      {
        $row = mysql_fetch_array($result);
        if(!$result)
        {
          $erreur++;
          $message[$erreur] = "Une erreur est survenue lors du changement de mot de passe de votre compte utilisateur.";
        }
        else 
        {
          //L'utilisateur existe, on lui demande le nouveau mot de passe
          $demander_password = true;
          $masquer_formulaire = true;
          $key_reinit= strtolower($_GET["key_reinit"]);
          $key_id= $_GET["id"];
        }
      }
    }
  }
}

/*
 *  Traitement des donnnées envoyées pour la création de compte
 */
 
elseif((isset($_POST['TB_user_mail']) && !empty($_POST['TB_user_mail'])) || isset($_POST['TB_user_mail_1']) && !empty($_POST['TB_user_mail_1']))
{
  $masquer_formulaire = false;
  $titre = "Inscription";
  if(isset($_POST['creer_compte']))
  {
    if(isset($_POST['TB_user_mail']))
    {
      //-----Vérification des données reçues à l'aide d'expressions régulières-----
      if(isset($_POST["CB_CGU"]) && $_POST["CB_CGU"] != "1")
      {
        $erreur++;
        $message[$erreur] = 'Vous devez prendre connaissance et accepter les conditions générales.';
      }
      
      if(!preg_match("/^.{4,30}$/i", $_POST["TB_user_name"]))
      {
        $erreur++;
        $message[$erreur] = 'Votre nom d\'utilisateur doit comporter entre 4 et 30 caractères. L\'utilisation de l\'underscore &laquo; _ &raquo; et du tiret &laquo; - &raquo; est autorisée.';
      }
      if(!preg_match("/^.{4,32}$/i", $_POST["TB_user_password"]))
      {
        $erreur++;
        $message[$erreur] = 'Votre mot de passe doit comporter au moins 4 caractères. Tous les caractères sont autorisés.';
      }
      if($_POST["TB_user_password"] != $_POST["TB_Confirmation_user_password"])
      {
        $erreur++;
        $message[$erreur] = 'Votre mot de passe n\'a pas été correctement confirmé.';
      }
      if(!preg_match("^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]{2,}[.][a-zA-Z]{2,4}$^", $_POST["TB_user_mail"]))
      {
        $erreur++;
        $message[$erreur] = 'Votre adresse mail n\'est pas valide.';
      }
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
        $message[$erreur]  = 'Votre jour de naissance n\'a pas correctement été rempli.';
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
      if(isset($_POST['TB_user_tel']) && !empty($_POST['TB_user_tel']))
      {
        if(!preg_match("#^0[1-9]([-. ]?[0-9]{2}){4}$#" ,$_POST['TB_user_tel']))
        {
          $erreur++;
          $message[$erreur] = 'Votre numéro de téléphone n\'est pas valide.';
        }
      }
      //Si aucune erreur sur les champs, on vérifie à l'aide de la BDD
      if($erreur == 0)
      {
        //-----Vérification unicité mail et pseudo-----
        $nom_form_minuscule = strtolower($_POST["TB_user_name"]);
        $email_form_minuscule = strtolower($_POST["TB_user_mail"]);
        $result = mysql_query("SELECT user_name, user_mail FROM user WHERE user_name = '".$_POST["TB_user_name"]."' OR user_mail = '".$email_form_minuscule."' OR user_name = '".$nom_form_minuscule."'");          
        //Si on a au moins une occurence, le nom ou le mail sont déjà utilisés.
        if(mysql_num_rows($result) > 0)
        {
          //On vérifie si il s'agit du mail ou du nom
          while($row = mysql_fetch_array($result))
          {
            $nom_bd_minuscule = strtolower($row["user_name"]);
            if($nom_form_minuscule == $nom_bd_minuscule)
            {
              $erreur++;
              $message[$erreur] = 'Le nom d\'utilisateur '.$_POST["TB_user_name"].' est déjà utilisé.';
            }
            elseif($email_form_minuscule == $row["user_mail"])
            {
              $erreur++;
              $message[$erreur] = 'L\'adresse e-mail '.$_POST["TB_user_mail"].' est déjà utilisée.';
            }
          }
        }
        else
        {
          //-----Génération clé puis enregistrement dans la bdd-----
          $caracteres = array("a", "b", "c", "d", "e", "f", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
          shuffle($caracteres);
          $user_key = "";
          for($n=0; $n <= 15; $n++)
          {
            $user_key .= $caracteres[$n];
          }
          $date = date("Y-m-d");
          if(intval($_POST['CB_DdN_Jour']) < 10) $_POST['CB_DdN_Jour'] = '0'.$_POST['CB_DdN_Jour'];
          if(intval($_POST['CB_DdN_Mois']) < 10) $_POST['CB_DdN_Mois'] = '0'.$_POST['CB_DdN_Mois'];
          $DdN = $_POST['CB_DdN_Annee'].'-'.$_POST['CB_DdN_Mois'].'-'.$_POST['CB_DdN_Jour'];
          $result = mysql_query("INSERT INTO user(
              user_name
            , user_password
            , user_mail
            , user_date
            , user_key
            , user_titre
            , user_nom
            , user_prenom
            , user_datenaiss
            , user_adresse
            , user_cp
            , user_ville
            )
            VALUES(
              '" . mysql_real_escape_string(utf8_decode($_POST["TB_user_name"])) . "'
            , '" . mysql_real_escape_string(strtolower(md5(utf8_decode($_POST["TB_user_password"])))) . "'
            , '" . $email_form_minuscule . "'
            , '" . $date . "'
            , '" . $user_key . "'
            , '" . mysql_real_escape_string(utf8_decode($_POST["TB_user_titre"])) . "'
            , '" . mysql_real_escape_string(utf8_decode($_POST["TB_user_nom"])) . "'
            , '" . mysql_real_escape_string(utf8_decode($_POST["TB_user_prenom"])) . "'
            , '" . mysql_real_escape_string(utf8_decode($DdN)) . "'
            , '" . mysql_real_escape_string(utf8_decode($_POST["TB_user_adresse"])) . "'
            , '" . mysql_real_escape_string(utf8_decode($_POST["TB_user_cp"])) . "'
            , '" . mysql_real_escape_string(utf8_decode($_POST["TB_user_ville"])) . "'
            )
            ");
          $id = mysql_insert_id();
          //Vérification si cette adresse mail est en attente d'affectation à une équipe
          $requete_equipe = mysql_query("SELECT * FROM equipes_ajouts WHERE user_mail = '".$email_form_minuscule."'");
          if($requete_equipe)
          {
            $equipe = mysql_fetch_array($requete_equipe);
            //Affectation de cet utilisateur à l'équipe pour laquelle il était en attente
            mysql_query("INSERT INTO equipes_correspondances(equipe_id, user_id), VALUES(".$equipe['equipe_id'].", '".$id."')");
            //Suppression de l'entrée qui mémorisait son affectation à cette équipe tant qu'i n'avait pas de compte
            mysql_query("DELETE FROM equipes_ajouts WHERE equipe_id = ".$equipe['equipe_id']." AND user_mail = '".$email_form_minuscule."'");
            $requete_equipe2 = mysql_query("SELECT * FROM equipes WHERE equipe_id = ".$equipe['equipe_id']);
            $equipe2 = mysql_fetch_array($requete_equipe2);
            $nb_succes++;
            $succes[$nb_succes] = 'Vous avez été ajouté à l\'équipe '.$equipe2['equipe_id'].'. Vous pouvez modifier ces paramètres sur votre profil.';
          }
          //Enregistrement du numéro de téléphone si il est défini
          if(isset($_POST['TB_user_tel']) && !empty($_POST['TB_user_tel']))
          {
            mysql_query("UPDATE user SET user_tel='".mysql_real_escape_string(intval($_POST["TB_user_tel"]))."' WHERE user_mail='".mysql_real_escape_string(utf8_decode($_POST['TB_user_mail']))."' ");
          }
          //Si la requête échoue
          if(!$result)
          {
            $erreur++;
            $message[$erreur] = 'Erreur d\'accès à la base de données lors de la création du compte utilisateur';
          }
          else
          {
            //-----Mail d'activation----
            $to = $email_form_minuscule;
            $pseudo = $_POST["TB_user_name"];
            $passe = $_POST["TB_user_password"];
            if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $to))
              $passage_ligne = "\r\n";
            else
              $passage_ligne = "\n";
            $subject = 'Activation de votre compte' . $passage_ligne;
            $mail_activation = '<html>
            <head>
            <title>Confirmation de l\'adresse e-mail</title>
            </head>
            <body>
            <p><b>Pour valider votre compte, merci de cliquer ou de copier dans votre navigateur le lien suivant s\'il n\'est pas cliquable :</b><br />
            <a href="'.ROOTPATH.'/connexion.php?id='.htmlspecialchars($id, ENT_QUOTES).'&clef='.htmlspecialchars($user_key, ENT_QUOTES).'">'.ROOTPATH.'/connexion.php?id='.htmlspecialchars($id, ENT_QUOTES).'&clef='.htmlspecialchars($user_key, ENT_QUOTES).'</a></p>
            <p>L\'équipe.</p>
            </font>
            </body>
            </html>';
            $headers  = 'MIME-Version: 1.0' . $passage_ligne;
            $headers .= 'Content-type: text/html; charset=utf-8' . $passage_ligne;
            $headers .= 'From: "La Ligue de Paintball de Lorraine" <test@theochevalier.fr>' . $passage_ligne;
            //mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $mail_activation, $headers);
            
            //-----Notification webmaster-----
            $pour = 'test@theochevalier.fr';
            $sujet = 'Nouveau compte '.$pseudo.' (N '.$id.')';
            $texte = 'Le compte '.$pseudo.' à été créé avec l\'adresse mail '.$to.' sur '.ROOTPATH.'. Ceci est un message automatique uniquement destiné au webmaster.';
            $headers  = 'MIME-Version: 1.0' . $passage_ligne;
            $headers .= 'Content-type: text/html; charset=utf-8' . $passage_ligne;
            mail($pour, '=?UTF-8?B?'.base64_encode($sujet).'?=', $texte, $headers);
            
            $nb_succes++;
            $succes[$nb_succes] = 'Votre compte utilisateur a correctement été créé.
            <br />Un email vient de vous être envoyé à l\'adresse <b>' . $to . '</b> afin de l\'activer.';
            $masquer_formulaire = true;
            
            //-----Traitement si image envoyée-----
            if(!empty($_FILES['fichier']['name']))
            {
              
              $nom_file = strtolower($_FILES['fichier']['name']);
              $extention = '.'. str_replace('.','',strstr($nom_file, '.'));
              $fichier = $id.$extention;
              $result = mysql_query("SELECT user_id, user_name FROM user WHERE user_mail = '".utf8_decode($_POST['TB_user_mail'])."'");
              $row = mysql_fetch_array($result);
              $chemin = 'images/upload/'.$fichier;
              if(move_uploaded_file($_FILES['fichier']['tmp_name'],$chemin))
              {
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
                  mysql_query("UPDATE user SET user_avatar='".$fichier."' WHERE user_mail='".mysql_real_escape_string(utf8_decode($_POST['TB_user_mail']))."' ");
                  $nb_succes++;
                  $succes[$nb_succes]= 'Cette image est dès à présent votre avatar sur le site.
                  <br /><div class="equipe"><img src="'.$chemin.'" alt="" /></div>';
                }
              }
              else
              {
                $erreur++;
                $message[$erreur] = 'Erreur lors de l\'envoi de l\'image.<br />'. $_FILES['fichier']['error'];
              }
            }
            unset($_POST['TB_user_mail']);
          }
        }
      }
    }
    else
    {
      $erreur++;
      $message[$erreur] = 'Veuillez indiquer votre adresse mail.';
    }
  }
}


/*
 * ---------------- DEBUT SCRIPT CONNEXION -----------------------------------
 */
elseif(!empty($_POST['pseudo']) && !empty($_POST['mdp']))
{
  $titre = "Connexion";
  $result = mysql_query("SELECT user_id, user_name, user_password, user_mail, user_activation, user_rang FROM user
    WHERE user_name = '".mysql_real_escape_string(utf8_decode($_POST['pseudo']))."'
    OR user_name = '".mysql_real_escape_string(strtolower(utf8_decode($_POST['pseudo'])))."'
    OR user_mail = '".mysql_real_escape_string(utf8_decode($_POST['pseudo']))."'");
  $row = mysql_fetch_array($result);
  $nom_db_tmp = strtolower(utf8_decode($row["user_name"]));
  $mail_db_tmp = strtolower(utf8_decode($row["user_mail"]));
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
      if(md5(utf8_decode($_POST['mdp'])) == $row["user_password"])
      {
        //Vérification que le compte soit bien activé
        if($row["user_activation"] != 0)
        {
          //Vérification que le compte ne soit pas banni
          if($row["user_rang"] > 0)
          {
            //Toutes les données ont été vérifiées: création de la session
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['user_password'] = $row['user_password'];
            $_SESSION['level'] = $row['user_rang'];
            unset($_SESSION['connexion_pseudo']);
            unset($_SESSION['rep_id']);
            unset($_SESSION['rep_name']);
            unset($_SESSION['rep_password']);
            $masquer_formulaire = true;
            
            //Enregistrement du log
            // Ouverture du fichier, on place le pointeur à la fin pour garder le contenu.
            $file = fopen("log.txt", "a+");
            // On calcule la date et l'heure actuelle
            $date = date("j F Y, à H:i:s", time());
            //Ce qu'on doit enregistrer...
            $texte = "Le ".$date." / User ID: ".$row['user_id']." / User name: ".$row['user_name']."\r\n";
            //On écrit dans le fichier
            fwrite($file, $texte);
            //On le ferme
            fclose($file);
            
            //Si l'utilisateur à coché "se souvenir de moi", on créé des cookies pour la connexion auto
            if(isset($_POST['cookie']) && $_POST['cookie'] == 'on')
            {
              setcookie('user_id', $row['user_id'], time()+365*24*3600);
              setcookie('user_password', $row['user_password'], time()+365*24*3600);
            }
            $nb_succes++;
            $succes[$nb_succes] = 'Vous êtes désormais connecté avec le pseudo <b>'.htmlspecialchars($_SESSION['user_name'], ENT_QUOTES).'</b>.';
          }
          else
          {
            $erreur++;
            $message[$erreur] = 'Le compte '.$_POST['pseudo'].' a été banni par un administrateur.
            <br />Vous ne pouvez plus vous connecter au site durant une durée indéterminée. En cas de bannissement abusif, <a href="mailto:test@theochevalier.fr">contactez le webmaster.</a>';
          }
        }
        else
        {
          $erreur++;
          $message[$erreur] = '<p>Le compte '.$_POST['pseudo'].' n\'a pas été activé. Merci de vérifier les mails de l\'adresse '.$row["user_mail"].' afin d\'activer votre compte.
          <br />Si vous n\'avez rien reçu, utilisez le <a href="connexion.php?aide">formulaire de réactivation</a>
          </p>Vous désirez reprendre l\'inscription depuis le début? <a href="connexion.php?delete">Effacez le compte '.$_POST['pseudo'].'</a>';
        }
      }
      else
      {
        $_SESSION['connexion_pseudo'] = $_POST['pseudo'];
        $erreur++;
        $message[$erreur] = 'Votre mot de passe est incorrect. Merci de réessayer.';
      }
    }
    else
    {
      $erreur++;
      $message[$erreur] = 'Le pseudo <b>'.htmlspecialchars($_POST['pseudo'], ENT_QUOTES).'</b> n\'existe pas, merci de rééssayer. (Attention aux majuscules !)';
    }
  }
}

/*
 *  Script Activation 
 */
 
elseif(isset($_GET["clef"]) && isset($_GET["id"]))
{
  $titre = "Activation";
  //Vérification de la clé d'activation et de l'id du compte à activer
  if(preg_match("#^[a-f0-9]{16}$#", strtolower($_GET["clef"])) && preg_match("#^[0-9]+$#", $_GET["id"]))
  {
    //Vérification que ce compte existe
    $result = mysql_query("SELECT user_id, user_activation, user_key FROM user WHERE user_id = '" . $_GET["id"] . "' AND user_key = '".strtolower($_GET["clef"])."'");
    if(!$result)
    {
      $erreur++;
      $message[$erreur] = "Une erreur est survenue lors de l'activation de votre compte utilisateur.";
    }
    else
    {
      //Redirection forcée si la clé et l'user_id ne correspondent pas dans la BDD (compte inexistant)
      if(mysql_num_rows($result) == 0)
        header("Location: index.php");
      else
      {
        $row = mysql_fetch_array($result);
        //Vérification que le compte ne soit pas encore activé
        if($row["user_activation"] != 0)
        {
          $erreur++;
          $message[$erreur] = "Votre compte utilisateur a déjà été activé.";
        }
        else
        {
          //Activation du compte
          $result = mysql_query("UPDATE user SET user_activation = '1' WHERE user_id = '".intval($_GET["id"])."' AND user_key = '".strtolower($_GET["clef"])."'");
          if(!$result)
          {
            $erreur++;
            $message[$erreur] = "Une erreur est survenue lors de l'activation de votre compte utilisateur.";
          }
          else
          {
            $nb_succes++;
            $succes[$nb_succes] = "Votre compte utilisateur a correctement été activé.";
          }
        }
      }
    }
  }
}

/*
 *  Script envoi de message
 */
 
elseif(isset($_POST['envoyer_message']))
{
  //Vérification que l'utilisateur est connecté
  actualiser_session();
  $titre = "Envoyer un message";
  //Vérification que les donnnées nécessaires sont présentes
  if(isset($_POST["em_dest"]) && !empty($_POST["em_dest"]) && isset($_POST["em_message"]) && !empty($_POST["em_message"]))
  {
    $passage_ligne = "\n";
    //Récupération des coordonnées du dstinataire
    $requete_dest = mysql_query("SELECT user_id, user_name, user_nom, user_prenom, user_mail FROM user WHERE user_id ='".intval($_POST["em_dest"])."'");
    $dest = mysql_fetch_array($requete_dest);
    //Récupération des coordonnées de l'expéditeur
    $requete_expe = mysql_query("SELECT user_id, user_name, user_nom, user_prenom, user_mail FROM user WHERE user_id ='".intval($_SESSION['user_id'])."'");
    $expe = mysql_fetch_array($requete_expe);
    $to = $dest['user_mail'];
    //Sujet et corps du mail
    $subject = 'Message de '.$expe['user_name'].' ('.$expe['user_prenom'].' '.$expe['user_nom'].') à partir de '.ROOTPATH.$passage_ligne;
    $mail = '<html>
    <head>
    <title>Message de '.$expe['user_name'].' ('.$expe['user_prenom'].' '.$expe['user_nom'].')</title>
    </head>
    <body>
    '.stripslashes(htmlspecialchars($_POST["em_message"], ENT_QUOTES)).'
    </body>
    </html>' . $passage_ligne;
    //Headers du mail
    $headers  = 'MIME-Version: 1.0' . $passage_ligne;
    $headers .= 'Content-type: text/html; charset=utf-8' . $passage_ligne;
    $headers .= 'From: "Ligue de Paintball de Lorraine" <test@theochevalier.fr>' . $passage_ligne;
    $headers .= 'Reply-To: "'.$expe['user_name'].'"<'.$expe['user_mail'].'>' . $passage_ligne;
    //Envoi du mail
    mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $mail, $headers);
    $nb_succes++;
    $succes[$nb_succes] = 'Votre message a été envoyé à '.$expe['user_name'].' ('.$expe['user_prenom'].' '.$expe['user_nom'].')';
    $masquer_formulaire = true;
  }
}

/*
 * Script contact
 */
 
elseif(isset($_POST["contacter_nom"]) && !empty($_POST["contacter_nom"]) && isset($_POST["contacter_email"]) && !empty($_POST["contacter_email"]) && isset($_POST["contacter_message"]) && !empty($_POST["contacter_message"]))
{
  $passage_ligne = "\n";
  $to = 'test@theochevalier.fr';
  $subject = 'Message de '.htmlspecialchars($_POST["contacter_nom"], ENT_QUOTES).' à partir de www.paintball-lorraine.theochevalier.fr' . $passage_ligne;
  $mail = '<html>
  <head>
  <title>Message de '.htmlspecialchars($_POST["contacter_nom"], ENT_QUOTES).'</title>
  </head>
  <body>
  '.stripslashes(htmlspecialchars($_POST["contacter_message"], ENT_QUOTES)).'
  </body>
  </html>' . $passage_ligne;

  $headers  = 'MIME-Version: 1.0' . $passage_ligne;
  $headers .= 'Content-type: text/html; charset=utf-8' . $passage_ligne;
  $headers .= 'From: "Ligue de Paintball de Lorraine" <test@theochevalier.fr>' . $passage_ligne;
  $headers .= 'Reply-To: "'.stripslashes(htmlspecialchars($_POST["contacter_nom"], ENT_QUOTES)).'"<'.stripslashes(htmlspecialchars($_POST["contacter_email"], ENT_QUOTES)).'>' . $passage_ligne;
  mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $mail, $headers);
  
  $nb_succes++;
  $succes[$nb_succes] = "Votre message nous a correctement été transmis.";
  $masquer_formulaire = true;
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
<div id="authentification">
<?php

/*
 *  Affichage du formulaire complet d'inscription, une fois que l'utilisateur à indiqué son mail
 */
 
if((isset($_POST['renvoyer_form_creer_compte']) && $_POST['TB_user_mail_1'] != 'Votre adresse mail') && $masquer_formulaire != true || isset($_POST['TB_user_mail']) )
{?>
<script>
function checkPassword(password1, password2) {
  if (password1.value != password2.value) {
    password2.setCustomValidity('La confirmation du mot de passe est incorrecte.');
  } else {
    password2.setCustomValidity('');
  }
}
</script>
<div class="titre_authentification">Créer un compte</div>
<div id="div_tab_container">
  <div id="se_connecter">
    <div class="titre_authentification">Informations du compte</div>
      <form enctype="multipart/form-data" action="connexion.php" method="post" id="upload_box">
    <div class="champs">
      <p>
        <label for="TB_user_mail">Adresse email <span class="champ_obligatoire">(*)</span></label>
        <input type="email" id="TB_user_mail" tabindex="40" autocomplete="off" 
        value="<?php if(isset($_POST['TB_user_mail_1'])) echo htmlspecialchars(strtolower($_POST['TB_user_mail_1'])); elseif(isset($_POST['TB_user_mail'])) echo htmlspecialchars(strtolower($_POST['TB_user_mail'])); ?>"
        name="TB_user_mail" placeholder="john.doe@site.org" required="" />
      </p>
      <p>
        <label for="TB_user_name">Pseudo <span class="champ_obligatoire">(*)</span></label>
        <input type="text" id="TB_user_name" autocomplete="off" name="TB_user_name" value="<?php if(isset($_POST['TB_user_name'])) echo htmlspecialchars($_POST['TB_user_name']); ?>"
        tabindex="10" maxlength="20" placeholder="JohnDoe" required=""/>
      </p>
      <p>
        <label for="TB_user_password">Mot de passe <span class="champ_obligatoire">(*)</span></label>
        <input type="password" id="TB_user_password" autocomplete="off" value="<?php if(isset($_POST['TB_user_password'])) echo htmlspecialchars($_POST['TB_user_password']); ?>"
        tabindex="20" name="TB_user_password" placeholder="*********" required=""/>
      </p>
      <p>
        <label for="TB_Confirmation_user_password">Confirmation <span class="champ_obligatoire">(*)</span></label>
        <input type="password" id="TB_Confirmation_user_password" autocomplete="off" tabindex="30" name="TB_Confirmation_user_password" 
        onfocus="checkPassword(document.getElementById('TB_user_password'), this);" oninput="checkPassword(document.getElementById('TB_user_password'), this);" required=""/>
      </p>
      <p>
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
      </p>
    </div>
  </div>
  <div id="creer_compte">
  <div class="titre_authentification">Informations livraison</div>
  <div class="champs">
    <p>
    <label><span class="champ_obligatoire">(*)</span> Civilité : </label>
      <input type="radio" name="TB_user_titre" value="1" <?php if(isset($_POST['TB_user_titre']) && $_POST['TB_user_titre'] == 1) echo 'checked="checked"'; ?> />M.
      <input type="radio" name="TB_user_titre" value="2" <?php if(isset($_POST['TB_user_titre']) && $_POST['TB_user_titre'] == 2) echo 'checked="checked"'; ?> />Mme.
      <input type="radio" name="TB_user_titre" value="3" <?php if(isset($_POST['TB_user_titre']) && $_POST['TB_user_titre'] == 3) echo 'checked="checked"'; ?> />Mlle.
    </p>
    <p>
      <input type="text" id="TB_user_prenom" autocomplete="off" value="<?php if(isset($_POST['TB_user_prenom'])) echo htmlspecialchars($_POST['TB_user_prenom']); ?>" name="TB_user_prenom" placeholder="John" required="" />
      <label for="TB_user_prenom"><span class="champ_obligatoire">(*)</span> Prénom</label>
    </p>
    <p>
      <input type="text" id="TB_user_nom" autocomplete="off" value="<?php if(isset($_POST['TB_user_nom'])) echo htmlspecialchars($_POST['TB_user_nom']); ?>" name="TB_user_nom" placeholder="Doe" required=""/>
      <label for="TB_user_nom"><span class="champ_obligatoire">(*)</span> Nom</label>
    </p>
    <p>
      <div class="largeur_champs">
      <select name="CB_DdN_Jour" id="CB_DdN_Jour" class="combobox" required="">
      <?php
      //Définition des numéros de jours ( 1 à 31) 
      for($i=0; $i <= 31;$i++)
      {
        echo '<option value="';
        if($i == 0) echo ""; else echo $i;
        echo '"'; if(isset($_POST['CB_DdN_Jour']) && $_POST['CB_DdN_Jour'] == $i) echo 'selected="selected"';
        echo '>';
        if($i == 0) echo ""; else echo $i;
        echo'</option>';
      } ?>
      </select>
      <select name="CB_DdN_Mois" id="CB_DdN_Mois" class="combobox" required="">
      <?php
      $mois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
      //Définition des numéros de mois
      for($i=0; $i <= 12;$i++)
      {
        echo '<option value="';
        if($i == 0) echo ""; else echo $i;
        echo '"'; if(isset($_POST['CB_DdN_Mois']) && $_POST['CB_DdN_Mois'] == $i) echo 'selected="selected"';
        echo '>';
        if($i == 0) echo ""; else echo $mois[$i];
        echo'</option>';
      } ?>
      </select>
      <select name="CB_DdN_Annee" id="CB_DdN_Annee" class="combobox" required="">
      <?php
      //Définition des 100 dernières années à partir de l'année actuelle
      for($i=0; $i <= 101;$i++)
      {
        $annee = date("Y")-$i+1;
        echo $annee;
        echo '<option value="';
        if($i == 0) echo ""; else echo $annee;
        echo '"'; if(isset($_POST['CB_DdN_Annee']) && $_POST['CB_DdN_Annee'] == $annee) echo 'selected="selected"';
        echo '>';
        if($i == 0) echo ""; else echo $annee;
        echo'</option>';
      } ?>
      </select>
      <div class="clear"></div>
      </div>
      <label for="CB_DdN_Jour"><span class="champ_obligatoire">(*)</span> Date de naissance</label>
    </p>
    <p>
      <input type="text" id="TB_user_adresse" autocomplete="off" value="<?php if(isset($_POST['TB_user_adresse'])) echo htmlspecialchars($_POST['TB_user_adresse']); ?>" name="TB_user_adresse" placeholder="221B Barker Street" required=""/>
      <label for="TB_user_adresse"><span class="champ_obligatoire">(*)</span> Adresse</label>
    </p>
    <p>
      <input type="text" id="TB_user_cp" autocomplete="off" value="<?php if(isset($_POST['TB_user_cp'])) echo htmlspecialchars($_POST['TB_user_cp']); ?>" name="TB_user_cp" placeholder="31560" required=""/>
      <label for="TB_user_cp"><span class="champ_obligatoire">(*)</span> Code Postal</label>
    </p>
    <p>
      <input type="text" id="TB_user_ville" autocomplete="off" value="<?php if(isset($_POST['TB_user_ville'])) echo htmlspecialchars($_POST['TB_user_ville']); ?>" name="TB_user_ville" placeholder="Toulouse" required=""/>
      <label for="TB_user_ville"><span class="champ_obligatoire">(*)</span> Ville</label>
    </p>
    <p>
      <input type="text" id="TB_user_tel" autocomplete="off" value="<?php if(isset($_POST['TB_user_tel'])) echo htmlspecialchars($_POST['TB_user_tel']); ?>" name="TB_user_tel" placeholder="06 11 22 33 44" />
      <label for="TB_user_tel">Téléphone</label>
    </p>
  </div>
  </div>
</div>
    <p>
      <input type="checkbox" id="CB_CGU" autocomplete="off" name="CB_CGU" value="1" required=""/>
      <label for="CB_CGU">J'accepte et certifie avoir pris connaîssance des <a href="index.php?page=cgu" target="_blank">Conditions Générales.</a></label>
    </p>
    <p>
      <label><span class="champ_obligatoire">(*)</span>: champ obligatoire</label>
    </p>
    <p>
      <input type="submit" name="creer_compte" value="Créer le compte" />
    </p>
    
</form>
  </div>
<?php
}

/*
 *  affichage du formulaire d'envoi de message à un autre utilisateur si l'id de l'utilisateur cible est transmis
 */
 
elseif(!empty($_GET['id_dest']))
{
  $requete_expe = mysql_query("SELECT user_id, user_name, user_nom, user_prenom, user_mail FROM user WHERE user_id ='".intval($_SESSION['user_id'])."'");
  $expe = mysql_fetch_array($requete_expe);
  ?>
  <div class="titre_authentification">Envoyer un message</div>
  <div class="form_message">
    <form method="post" action="connexion.php" enctype="application/x-www-form-urlencoded" name="em_formulaire" id="em_formulaire">
    <div><label for="em_nom">Votre nom:</label> <input type="text" name="em_nom" id="em_nom" readonly="true" value="<?php echo utf8_encode($expe['user_prenom'].' '.$expe['user_nom']); ?>" placeholder="John Doe" required=""/></div>
    <div><label for="em_email">Votre email:</label> <input type="email" name="em_email" id="em_email" readonly="true" value="<?php echo $expe['user_mail']; ?>" placeholder="john.doe@site.org" required="" /></div>
    <div><label for="em_message">Message:</label> <textarea id="em_message" name="em_message" rows="4" cols="80" placeholder="Votre message." required=""></textarea></div>
    <input type="hidden" name="em_dest" id="em_dest" value="<?php echo $_GET['id_dest']; ?>" />
    <button class="submit" name="envoyer_message" type="submit" >Envoyer</button>
    </form>
  </div><?php
}

/*
 *  Affichage du formulaire de contact
 */
 
elseif(isset($_GET['contact']))
{ ?>
  <div class="form_message">
    <div class="titre_authentification">Nous contacter</div>
    <div class="form_message">
    <p>Remplissez le formulaire suivant afin de nous envoyer un message, nous vous répondrons dans les meilleurs délais.</p><br />
        <form method="post" action="connexion.php" enctype="application/x-www-form-urlencoded" onSubmit="return check();" name="contacter_formulaire" id="contacter_formulaire">
      <div><label for="contacter_nom">Nom:</label> <input type="text" name="contacter_nom" id="contacter_nom" placeholder="John Doe" required=""/></div>
      <div><label for="contacter_email">Email:</label> <input type="email" name="contacter_email" id="contacter_email" placeholder="john.doe@site.org" required="" /></div>
      <div><label for="contacter_message">Message:</label> <textarea id="contacter_message" name="contacter_message" rows="4" cols="80" placeholder="Votre message." required="" ></textarea></div>
      <input type="hidden" name="verif" id="verif" value="1" />
      <button class="submit" type="submit" >Envoyer</button>
      </form>
    </div>
  </div>
<?php
}

/*
 *  Sinon si le champs caché 'aide_mail' est présent, on affiche le formulaire permettant de renseigner le mail auquel envoyer le mail d'activation
 */
 
elseif(isset($_POST['aide_mail']))
{ ?>
  <form enctype="multipart/form-data" action="connexion.php?aide" method="post">
  <h2>Renvoi du mail d'activation</h2>
  <label for="aide_mail_mail">Adresse mail avec laquelle vous vous êtes inscrit(e) : </label><input type="email" name="aide_mail_mail" id="aide_mail_mail" placeholder="john.doe@site.org" required=""/>
  <input type="hidden" name="aide_mail" />
  <input type="submit" value="Envoyer" />
  </form>
  <?php $masquer_formulaire = true;
}

/*
 *  Sinon si le champs caché 'aide_password' est présent, on affiche le formulaire permettant de renseigner le mail pour lequel on doit réinitialiser le mdp
 */
 
elseif(isset($_POST['aide_password']))
{ ?>
  <form enctype="multipart/form-data" action="connexion.php?aide" method="post">
  <h2>Réinitialisation du mot de passe (Etape 1/2)</h2>
  <label for="aide_password_mail">Adresse mail avec laquelle vous vous êtes inscrit(e) : </label><input type="email" name="aide_password_mail" id="aide_password_mail" placeholder="john.doe@site.org"  required=""/>
  <input type="hidden" name="aide_password" />
  <input type="submit" value="Envoyer" />
  </form>
  <?php $masquer_formulaire = true;
}

/*
 *  Si le booléen demander_password est passé à true (Après la vérification de la clé de réinitialisation et de l'id transmis par le lien que l'utilisateur à obtenu dans le mail)
 */
 
elseif($demander_password == true)
{ ?><div class="form_message">
  <form enctype="multipart/form-data" action="connexion.php?aide" method="post">
  <h2>Réinitialisation du mot de passe (Etape 2/2)</h2>
  <p>Saisissez votre nouveau mot de passe</p>
  <p>
    <label for="aide_password_new">Mot de passe : </label><input type="password" autocomplete="off" name="aide_password_new" id="aide_password_new" required=""/>
  </p>
  <p>
    <label for="aide_password_new_conf">Confirmation : </label><input type="password" autocomplete="off" name="aide_password_new_conf" id="aide_password_new_conf"
    onfocus="checkPassword(document.getElementById('aide_password_new'), this);" oninput="checkPassword(document.getElementById('aide_password_new'), this);" required="" />
  </p>
  <input type="hidden" name="aide_password_key_reinit" value="<?php echo $key_reinit; ?>"/>
  <input type="hidden" name="aide_password_key_id" value="<?php echo $key_id; ?>"/>
  <input type="submit" value="Envoyer" />
  </form>
  </div>
<?php
}

/*
 *  Si aucune erreur empêchant l'inscription ou la connexion (Déjà connecté, compte non activé, banni ...), afficher les formulaires
 */

elseif($masquer_formulaire == false) { ?>
  <div id="se_connecter">
  <div class="titre_authentification">Se connecter</div>
    <form name="connexion" method="post" action="connexion.php">
    <p>
      <input name="pseudo" type="text" autocomplete="off" id="pseudo"  placeholder="Pseudo / Email" required="" >
    </p>
    <p>
      <input name="mdp" type="password" autocomplete="off" id="mdp"  placeholder="Mot de passe" required="" >
    </p>
    <p>
      <label for="cookie">Se souvenir de moi </label><input type="checkbox" name="cookie" id="cookie"/>
    </p>
    <p>
      <input type="submit" value="Connexion" />
    </p>

    <p id="aide_connexion">
      <a href="connexion.php?aide"><img src="images/icones/help.png" alt="" /><b> Aide à la connexion.</b></a><br />
      <a href="connexion_rep.php"><img src="images/icones/help.png" alt="" /><b> Connexion représentant</b></a>
    </p>
    </form>
  </div>
  
  <div id="creer_compte">
  <div class="titre_authentification">Créer un compte</div>
    <form enctype="multipart/form-data" action="connexion.php" method="post">
<div class="champs">
  <p>
    <input type="email" id="TB_user_mail_1" tabindex="40" autocomplete="off" name="TB_user_mail_1" placeholder="Votre adresse mail" required="" />
  </p>
  <p>
    <input type="submit" name="renvoyer_form_creer_compte" value="Créer le compte" />
  </p>
</div>
</form>
  </div>
  <div class="clear"></div>
  <?php } ?>
</div>
</body>
</html>
