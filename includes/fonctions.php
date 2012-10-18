<?php

function connexionbdd()
{
  mysql_connect(NOM_SERVEUR, LOGIN, MOT_DE_PASSE);
  mysql_select_db(NOM_BD);
}

//Fonction actualisant la session de l'utilisateur pour vérifier son identité
function actualiser_session($message_page = "")
{
  $message_page = "?message=".$message_page;
  //Si la session est déjà présente
  if(isset($_SESSION['user_id']) && intval($_SESSION['user_id']) != 0)
  {
    //On récupère les infos de l'utilisateur
    $result = mysql_query("SELECT user_id, user_name, user_password, user_activation, user_rang FROM user WHERE user_id = '".intval($_SESSION['user_id'])."'");
    $row = mysql_fetch_array($result);
    /*//Stockage de la version minuscule du login de la bdd
    $nom_db_tmp = strtolower($row["user_name"]);
    //Stockage de la version minuscule du login de la session
    $nom_session_tmp = strtolower($_SESSION["user_name"]);*/
    if(!$result)
    {
      $message = "?message=Erreur de la base de données.";
      vider_cookie();
      session_destroy();
      if($message_page != "?message=") $message = $message_page;
      header('location:connexion.php'.$message);
      return false;
    }
    else
    {
      //Si les deux logins correspondent
      if($_SESSION["user_name"] == $row["user_name"])
      {
        //Si les mots de passe correspondent
        if($_SESSION["user_password"] == $row["user_password"])
        {
          //Si le compte ets activé
          if($row["user_activation"] != 0)
          {
            //Si le compte n'est pas banni
            if($row["user_rang"] > 0)
            {
              //On re-créé les sessions
              $_SESSION['user_id'] = $row['user_id'];
              $_SESSION['user_name'] = $row['user_name'];
              $_SESSION['user_password'] = $row['user_password'];
              $_SESSION['level'] = $row['user_rang'];
              return true;
            }
            else
            {
              //L'utilisateur est banni
              $message = '?message=Le compte '.$row["user_name"].' a été banni par l\'administrateur.
              <br />Vous ne pouvez plus vous connecter au site durant une durée indeterminée. En cas de bannissement involontaire, <a href="mailto:teamforfun@free.fr">contactez l\'administrateur.</a>';
              $afficher_formulaire = false;
              vider_cookie();
              session_destroy();
              if($message_page != "?message=") $message = $message_page;
              header('location:connexion.php'.$message);
              return false;
            }
          }
          else
          {
            //Le compte n'est pas activé
            $message = '?message=Le compte '.$row["user_name"].' n\'a pas été activé. Merci de vérifier vos mails afin d\'activer votre compte.
            <br />Si vous n\'avez rien reçu, remplissez le formulaire ci-dessous:';
            $afficher_reactivation = true;
            vider_cookie();
            session_destroy();
            if($message_page != "?message=") $message = $message_page;
            header('location:connexion.php'.$message);
            return false;
          }
        }
        else
        {
          //Le mot de passe stocké dans la session est incorrect
          $message = '?message=Le mot de passe de votre session est incorrect, vous devez vous reconnecter.';
          vider_cookie();
          session_destroy();
          if($message_page != "?message=") $message = $message_page;
          header('location:connexion.php'.$message);
          return false;
        }
      }
      else
      {
        //Le login stocké dans la session est incorrect
        $message = '?message=Le nom d\'utilisateur de votre session est incorrect, vous devez vous reconnecter.';
        vider_cookie();
        session_destroy();
        if($message_page != "?message=") $message = $message_page;
        header('location:connexion.php'.$message);
        return false;
      }
    }
  }
  else
  {
    //Si le cookie de session est présent
    if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_password']))
    {
      //Si l'id est bien un nombre et qu'il est différent de 0
      if(intval($_COOKIE['user_id']) != 0)
      {
        //On récupère les informations de l'utilisateur
        $result = mysql_query("SELECT user_id, user_name, user_password, user_activation, user_rang FROM user WHERE user_id = '".intval($_COOKIE['user_id'])."'");
        $row = mysql_fetch_array($result);
        if(!$result)
        {
          $message = "?message=Erreur de connexion";
          vider_cookie();
          session_destroy();
          if($message_page != "?message=") $message = $message_page;
          header('location:connexion.php'.$message);
          return false;
        }
        else
        {
          //Si le mot de passe stocké dans le cookie correspond à celui de le bdd
          if($_COOKIE["user_password"] == $row["user_password"])
          {
            //Si le compte est activé
            if($row["user_activation"] != 0)
            {
              //Si le compte n'est pas banni
              if($row["user_rang"] > 0)
              {
                //On re-créé les sessions
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_name'] = $row['user_name'];
                $_SESSION['user_password'] = $row['user_password'];
                $_SESSION['level'] = $row['user_rang'];
                return true;
              }
              else
              {
                //Si le compte à été banni
                $message = '?message=Le compte '.$row["user_name"].' a été banni par l\'administrateur.
                <br />Vous ne pouvez plus vous connecter au site durant une durée indeterminée. En cas de bannissement involontaire, <a href="mailto:teamforfun@free.fr">contactez l\'administrateur.</a>';
                $afficher_formulaire = false;
                vider_cookie();
                session_destroy();
                if($message_page != "?message=") $message = $message_page;
                header('location:connexion.php'.$message);
                return false;
              }
            }
            else
            {
              //Si le compte n'a pas été activé
              $message = '?message=Le compte '.$row["user_name"].' n\'a pas été activé. Merci de vérifier vos emails afin d\'activer votre compte.
              <br />Si vous n\'avez rien reçu, remplissez le formulaire ci-dessous:';
              $afficher_reactivation = true;
              vider_cookie();
              session_destroy();
              if($message_page != "?message=") $message = $message_page;
              header('location:connexion.php'.$message);
              return false;
            }
          }
          else
          {
            //Si le mot de passe stocké dans la session est incorrect
            $message = '?message=Le mot de passe de votre session est incorrect, vous devez vous reconnecter.';
            vider_cookie();
            session_destroy();
            if($message_page != "?message=") $message = $message_page;
            header('location:connexion.php'.$message);
            return false;
          }

        }
      }
      else
      {
        //Si l'id stocké dans la session est incorrect
        vider_cookie();
        session_destroy();
        if($message_page != "?message=") $message = $message_page;
        header('location:connexion.php'.$message);
        return false;
      }
    }
    else
    {
      //Si le cookie n'est pas présent et que l'utilisateur est connecté
      if(isset($_SESSION['user_id'])) unset($_SESSION['user_id']);
      vider_cookie();
      session_destroy();
      if($message_page != "?message=") $message = $message_page;
      header('location:connexion.php'.$message);
      return false;
    }
  }
  session_destroy();
  if($message_page != "?message=") $message = $message_page;
  header('location:connexion.php'.$message);
  return false;
}
//Fonction pour effacer les cookies de sessions
function vider_cookie()
{
  setcookie('user_id', '', time()-3600);
  setcookie('user_password', '', time()-3600);
}
//Fonction pour retourner l'âge à partir de la date dans la base de données
function age($naiss){
  list($annee, $mois, $jour) = preg_split('[-.]', $naiss);
  $today['mois'] = date('n');
  $today['jour'] = date('j');
  $today['annee'] = date('Y');
  $annees = $today['annee'] - $annee;
  if($today['mois'] <= $mois)
  {
    if($mois == $today['mois'])
    {
      if($jour > $today['jour']) $annees--;
    }
    else $annees--;
  }
  return $annees;
}
//Fonction pour formater une date à partir de la date dans la base de données
function formater_date($date_bdd)
{
  $date_annee = substr($date_bdd, 0, 4);
  $date_mois = substr($date_bdd, 8, 2);
  $date_jour = substr($date_bdd, 5, 2);
  $timestamp = mktime( 0, 0, 0, $date_jour , $date_mois , $date_annee);
  $date = strftime( "%d %B %Y" , $timestamp);
  return $date;
}
//Fonction actualisant la session de l'utilisateur pour vérifier son identité
function actualiser_session_representant($message_page = "")
{
  $message_page = "?message=".$message_page;
  //Si la session est déjà présente
  if(isset($_SESSION['rep_id']) && intval($_SESSION['rep_id']) != 0)
  {
    //On récupère les infos de l'utilisateur
    $result = mysql_query("SELECT rep_id, rep_name, rep_password FROM representant WHERE rep_id = '".intval($_SESSION['rep_id'])."'");
    $row = mysql_fetch_array($result);
    //Stockage de la version minuscule du login de la bdd
    $nom_db_tmp = strtolower($row["rep_name"]);
    //Stockage de la version minuscule du login de la session
    $nom_session_tmp = strtolower($_SESSION["rep_name"]);
    if(!$result)
    {
      $message = "?message=Erreur de la base de données.";
      session_destroy();
      if($message_page != "?message=") $message = $message_page;
      header('location:connexion_rep.php'.$message);
      return false;
    }
    else
    {
      //Si les deux logins correspondent
      if($_SESSION["rep_name"] == $row["rep_name"])
      {
        //Si les mots de passe correspondent
        if($_SESSION["rep_password"] == $row["rep_password"])
        {
          //On re-créé les sessions
          $_SESSION['rep_id'] = $row['rep_id'];
          $_SESSION['rep_name'] = $row['rep_name'];
          $_SESSION['rep_password'] = $row['rep_password'];
          return true;
        }
        else
        {
          //Le mot de passe stocké dans la session est incorrect
          $message = '?message=Le mot de passe de votre session est incorrect, vous devez vous reconnecter.';
          session_destroy();
          if($message_page != "?message=") $message = $message_page;
          header('location:connexion_rep.php'.$message);
          return false;
        }
      }
      else
      {
        //Le login stocké dans la session est incorrect
        $message = '?message=Le nom d\'utilisateur de votre session est incorrect, vous devez vous reconnecter.';
        session_destroy();
        if($message_page != "?message=") $message = $message_page;
        header('location:connexion_rep.php'.$message);
        return false;
      }
    }
  }
  session_destroy();
  if($message_page != "?message=") $message = $message_page;
  header('location:connexion_rep.php'.$message);
  return false;
}
?>
