<?php
$nb_art = 0;
$total= 0;
//Cas où le panier à été vérifié, et le paiement fait, ou demandé
if(isset($_COOKIE['art_panier']) && isset($_GET['payer']))
{
  $masquer_formulaire = false;
  //Si les coordonnées banquaires ont déjà étées renseignées
  if(isset($_POST['payer_carte']))
  {
    $user_id = intval($_SESSION['user_id']);
    $nb_erreurs = 0;
    $erreurs = "";
    //Vérification des coordonées banquaires
    if(strlen($_POST['payer_carte']) != 16 && is_numeric($_POST['payer_carte']))
    {
      $nb_erreurs++;
      $erreurs .= '<p>&#8226; Le code de la carte est incorrect.</p>';
    }
    if(strlen(intval($_POST['payer_securite'])) != 3)
    {
      $nb_erreurs++;
      $erreurs .= '<p>&#8226; Le code de sécurité est incorrect.</p>';
    }
    if(intval($_POST['payer_expire']) <= 0 || intval($_POST['payer_expire']) > 12 ||  intval($_POST['payer_annee']) < 11 ||  intval($_POST['payer_annee']) > 99)
    {
      $nb_erreurs++;
      $erreurs .= '<p>&#8226; La date d\'expiration de la carte est incorrecte.</p>';
    }
    //Si tout est bon, on rempli la table carte
    if($nb_erreurs == 0)
    {
      //Rajouter com_payee à 1.
      $carte_expire = '20'.intval($_POST['payer_annee']).'-'.intval($_POST['payer_expire']).'-01';
      $result = mysql_query("INSERT INTO carte(user_id, carte_num, carte_secu, carte_expire)
      VALUES( '" . mysql_real_escape_string(utf8_decode($user_id)) . "'
      , '" . mysql_real_escape_string(utf8_decode($_POST['payer_carte'])) . "'
      , '" . mysql_real_escape_string(utf8_decode($_POST['payer_securite'])) . "'
      , '" . mysql_real_escape_string(utf8_decode($carte_expire)) . "'
      )");
      //Suppression des cookies
      setcookie("key_panier", false, time() - 3600);
      setcookie("art_panier", false, time() - 3600);
      unset($_COOKIE['key_panier']);
      unset($_COOKIE['art_panier']);
      $masquer_formulaire = true;
    }
  }
  //Si les coordonées banquaires ont déjà été renseignées, on masque ce formulaire
  if($masquer_formulaire == false)
  {  ?>
    <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=panier" class="ariane_hover">Mon panier</a> > 
    <a href="index.php?page=panier&verification_panier" class="ariane_hover">Vérification du panier</a> > 
    <a href="index.php?page=panier&valider" class="ariane_hover">Vérifier l'adresse</a> > 
    <a href="index.php?page=panier&modifier" class="ariane_hover">Modifier l'adresse</a> > 
    <a href="index.php?page=panier&payer" class="ariane_hover">Régler la commande</a></div>
    <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Entrez vos coordonées bancaires - Étape 3/3</h1>

    <div class="equipe_description content">
    <div class="form_message">
    Entrez vos informations bancaires afin de procéder au règlement de la commande:
    <?php if(isset($erreurs) && $erreurs != "") echo $erreurs; ?>
    <form method="post" action="index.php?page=panier&amp;payer" name="payer" id="payer">
    <p><label for="payer_carte">Numéro de carte</label> <input type="text" maxlength="16" name="payer_carte" id="payer_carte" <?php if(isset($_POST['payer_carte'])) echo 'value="'.htmlspecialchars($_POST['payer_carte']).'"'; ?> /></p>
    <p><label for="payer_securite">Code de sécurité</label> <input type="text" maxlength="3" name="payer_securite" id="payer_securite" <?php if(isset($_POST['payer_securite'])) echo 'value="'.htmlspecialchars($_POST['payer_securite']).'"'; ?>/></p>
    <p><label for="payer_expire">Date d'expiration</label> <input type="text" maxlength="2" name="payer_expire" id="payer_expire" size="1" <?php if(isset($_POST['payer_expire'])) echo 'value="'.htmlspecialchars($_POST['payer_expire']).'"'; ?> /> 
    / <input type="text" maxlength="2" name="payer_annee" id="payer_annee" size="1" <?php if(isset($_POST['payer_annee'])) echo 'value="'.htmlspecialchars($_POST['payer_annee']).'"'; ?> /></p>
    <button class="submit" type="submit" >Envoyer</button>
    </form>
    </div>
    </div>
<?php
  }
  else echo 'Merci de votre confiance, votre commande va être traitée dans les meilleurs délais. Vous serez tenu au courant de l\'avancement de votre commande par e-mail.
  <br />Si vous avez la moindre question, n\'hésitez pas à <a href="connexion.php?contact">nous contacter.</a>';
}
//Si la validation est demandée
elseif(isset($_COOKIE['art_panier']) && isset($_GET['valider']))
{
  $user_id = intval($_SESSION['user_id']);
  //On récupère toutes les données de la table user pour l'utilisateur connecté
  $requete_profil = mysql_query("SELECT * FROM user WHERE user_id = '".$user_id."'");
  $profil = mysql_fetch_array($requete_profil);
  ?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=panier" class="ariane_hover">Mon panier</a> > 
  <a href="index.php?page=panier&verification_panier" class="ariane_hover">Vérification du panier</a> > 
  <a href="index.php?page=panier&valider" class="ariane_hover">Vérifier l'adresse</a></div>
  <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Vérifiez que les données de livraison sont correctes  - Étape 2/3</h1>
  
  <div class="equipe_description content">
    <p><?php if($profil['user_titre'] == 1) echo 'Mr.'; elseif($profil['user_titre'] == 2) echo 'Mme.'; else echo 'Mlle.'; 
    echo utf8_encode(' '.$profil['user_prenom'].' '.$profil['user_nom']);?></p>
    <p><?php echo utf8_encode($profil['user_adresse']); ?></p>
    <p><?php echo utf8_encode($profil['user_cp'].' '.$profil['user_ville']); ?></p>
    <?php if(!empty($profil['user_tel'])) echo '<p>Tel : 0'.$profil['user_tel'].'</p>'; ?>
  </div>
  <a href="index.php?page=panier&amp;modifier" class="button"><button><img src="images/icones/modifier.png" alt="" /> Modifier les informations</button></a>
  <a href="index.php?page=panier&amp;payer" class="button"><button><img src="images/icones/arrow_right.png" alt="" /> Régler la commande</button></a>
<?php  
}
//Si lors de la vérification du panier l'utilisateur demande la modification
elseif(isset($_COOKIE['art_panier']) && isset($_GET['modifier']))
{
  $masquer_formulaire = false;
  $erreur = 0;
  $message = "";
  //Si les données modifiées ont été envoyées par le formulaire
  if(isset($_POST['modifier']))
  {
    //Vérification à l'aide des expressions régulières
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
    //Si aucune erreur
    if($erreur == 0)
    {
      //Mise à jour de l'utilisateur avec les nouvelles données
      mysql_query("UPDATE user SET user_prenom='".mysql_real_escape_string(utf8_decode($_POST['TB_user_prenom']))."',
      user_nom='".mysql_real_escape_string(utf8_decode($_POST['TB_user_nom']))."',
      user_adresse='".mysql_real_escape_string(utf8_decode($_POST['TB_user_adresse']))."',
      user_cp='".mysql_real_escape_string(utf8_decode($_POST['TB_user_cp']))."',
      user_ville='".mysql_real_escape_string(utf8_decode($_POST['TB_user_ville']))."'
      WHERE user_id='".intval($_SESSION['user_id'])."' ");
      //On gère le cas où le numéro de téléphone n'et pas renseigné, car il est optionnel
      if(isset($_POST['TB_user_tel']) && !empty($_POST['TB_user_tel']))
      {
        mysql_query("UPDATE user SET user_tel='".mysql_real_escape_string(intval($_POST["TB_user_tel"]))."' WHERE user_id='".intval($_SESSION['user_id'])."' ");
      }
      //Vu qu'on a reçu les données, on masque le formulaire de modification des données
      $masquer_formulaire = true;
    }
  }
  //Si on a pas demandé le masquage du formulaire, on l'affiche, sinon on renvoie à la validation du panier
  if($masquer_formulaire == false)
  {
    $user_id = intval($_SESSION['user_id']);
    //On récupère toutes les données de l'utilisateur
    $requete_profil = mysql_query("SELECT * FROM user WHERE user_id = '".$user_id."'");
    $profil = mysql_fetch_array($requete_profil);
    if($profil['user_tel'] != "") $tel = "0".utf8_encode($profil['user_tel']);
    else $tel = "";
?>
    <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=panier" class="ariane_hover">Mon panier</a> > 
    <a href="index.php?page=panier&verification_panier" class="ariane_hover">Vérification du panier</a> > 
    <a href="index.php?page=panier&valider" class="ariane_hover">Vérifier l'adresse</a> > 
    <a href="index.php?page=panier&modifier" class="ariane_hover">Modifier l'adresse</a></div>
    <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Modifiez les informations de livraison - Étape 2-a/3</h1>
    <div class="equipe_description content">
      <div class="form_message">
      <?php
      if($erreur != 0)
      {
        echo '<p>Il y a '.$erreur.' erreur(s):</p>';
        for($i = 1; $i <= $erreur; $i++)
        {
          echo '&#8226; '.$message[$i].'<br />';
        }
      } ?>
        <form method="post" action="index.php?page=panier&amp;modifier" name="modifier" id="modifier">
          <div class="champs">
            <p>
              <label>Civilité</label>
              <input type="radio" name="TB_user_titre" value="1" <?php if($profil['user_titre'] == 1) echo 'checked="checked"'; ?> />Mr.
              <input type="radio" name="TB_user_titre" value="2" <?php if($profil['user_titre'] == 2) echo 'checked="checked"'; ?> />Mme.
              <input type="radio" name="TB_user_titre" value="3" <?php if($profil['user_titre'] == 3) echo 'checked="checked"'; ?> />Mlle.
            </p>
            <p>
              <label for="TB_user_prenom">Prénom</label>
              <input type="text" id="TB_user_prenom" autocomplete="off" value="<?php echo utf8_encode($profil['user_prenom']); ?>" name="TB_user_prenom" />
            </p>
            <p>
              <label for="TB_user_nom">Nom</label>
              <input type="text" id="TB_user_nom" autocomplete="off" value="<?php echo utf8_encode($profil['user_nom']); ?>" name="TB_user_nom" />
            </p>
            <p>
              <label for="TB_user_adresse">Adresse</label>
              <input type="text" id="TB_user_adresse" autocomplete="off" value="<?php echo utf8_encode($profil['user_adresse']); ?>" name="TB_user_adresse" />
            </p>
            <p>
              <label for="TB_user_cp">Code Postal</label>
              <input type="text" id="TB_user_cp" autocomplete="off" value="<?php echo utf8_encode($profil['user_cp']); ?>" name="TB_user_cp" />
            </p>
            <p>
              <label for="TB_user_ville">Ville</label>
              <input type="text" id="TB_user_ville" autocomplete="off" value="<?php echo utf8_encode($profil['user_ville']); ?>" name="TB_user_ville" />
            </p>
            <p>
              <label for="TB_user_tel">Téléphone (optionnel)</label>
              <input type="text" id="TB_user_tel" autocomplete="off" value="<?php echo $tel; ?>" name="TB_user_tel" />
            </p>
            <p>
              <button class="submit" type="submit" name="modifier" >Envoyer</button>
            </p>
          </div>
        </form>
      </div>
    </div>
    <div class="clear"></div>
<?php
  }
else header('location:index.php?page=panier&valider');
}
//Vérification du panier
elseif(isset($_GET['verification_panier']) && isset($_COOKIE['art_panier']))
{
?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=panier" class="ariane_hover">Mon panier</a> > 
  <a href="index.php?page=panier&verification_panier" class="ariane_hover">Vérification du panier</a></div>
  <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Confirmez votre commande - Étape 1/3</h1>
  <?php if(isset($_POST['verification_ok']))
  { ?>  
  <img src="images/icones/ok.png" alt="" align="middle" /> <b>Vous avez commandé les produits suivants.</b>
  <br />Cliquez sur "Vérifier mes informations" pour choisir votre adresse de livraison.
  <?php }else{ ?>
  Vérifiez les produits sélectionnés, ainsi que leur quantité.
  S'il y a des erreurs, cliquez sur le bouton "Modifier mon panier", sinon vous pouvez cliquer sur "Valider mon panier".
  <?php } ?>
  <table class="panier">
  <tr class="panier_top">
    <th></th>
    <th>Nom</th>
    <th>Ref.</th>
    <th>Dispo.</th>
    <th>Prix unitaire</th>
    <th>Quantité commandée</th>
    <th>Total</th>
  </tr>
  <form action="index.php?page=panier&amp;verification_panier" method="post">
<?php
  //On parcours le cookie du panier pour l'afficher
  $tableau_art_panier = unserialize($_COOKIE['art_panier']);
  foreach($tableau_art_panier as $art_id => $art_qte)
  {
    //On récupère les informations du produit dans la bdd
    $requete_art = mysql_query('SELECT * FROM articles WHERE art_id ='.intval($art_id));
    $art = mysql_fetch_array($requete_art);  
    //Si le stock est supérieur à la quantité commandée, et qu'il est supérieur à 0
    if($art['art_stock'] >= $art_qte && $art['art_stock'] > 0 && $art_qte > 0)
    { ?>
      <tr class="panier_ligne">
        <td class="panier_img"><img src="articles/<?php echo $art['art_id']; ?>.jpg" alt="" /></td>
        <td class="panier_name"><?php echo utf8_encode($art['art_name']); ?></td>
        <td class="panier_id">id<?php echo $art['art_id']; ?></td>
        <td class="panier_stock"><img src="images/icones/check.png" alt="" /></td>
        <td class="panier_prix"><?php echo str_replace(".", ",", $art['art_prix']); ?> &#128;</td>
        <td class="panier_qte"><?php echo $art_qte; ?></td>
        <td class="panier_sous_total">+ <?php $sous_total = $art['art_prix'] * $art_qte;
        echo str_replace(".", ",", $sous_total).' &#128;'; $total = $total + $sous_total;?></td>
      </tr><?php
    }
  }?>
  <tr><td colspan="7" class="panier_total">TOTAL: <?php echo str_replace(".", ",", $total).' &#128;'; ?> TTC</td></tr>
  </table>
<?php
  //Enregistrement BDD si verification_ok reçu
  if(isset($_POST['verification_ok']))
  {
    //Si on a bien la clé panier
    if(isset($_COOKIE['key_panier']))
    {
      //Vérification que la clé panier n'a jamais servi à enregistrer de commande, et à bien été distribuée.
      $key_panier = $_COOKIE['key_panier'];
      $requete_key = mysql_query("SELECT key_panier FROM key_panier WHERE key_panier = '".$key_panier."' AND key_used = '0'");
      //Si la clé panier à déjà été enregistrée auparavent et jamais utilisée
      if($requete_key)
      {
        //Pour chaque articles, je vérifie le stock
        foreach($tableau_art_panier as $art_id => $art_qte)
        {
          $requete_art = mysql_query('SELECT * FROM articles WHERE art_id ='.intval($art_id));
          $art = mysql_fetch_array($requete_art);
          //Si le stock est ok, on enregistre la commande pour cet article
          if($art['art_stock'] >= $art_qte && $art['art_stock'] > 0 && $art_qte > 0)
          {
            //Enregistrement de la commande
            mysql_query("INSERT INTO commandes(com_user, com_art, com_qte, com_key)
            VALUES( '".$_SESSION['user_id']."', '".$art['art_id']."', '".$art_qte."', '".$_COOKIE['key_panier']."')");
            //Mise à jour du stock
            $requete_article = mysql_query("SELECT art_stock FROM articles WHERE art_id = '".$art['art_id']."'");
            $article = mysql_fetch_array($requete_article);
            $new_qte = intval($article['art_stock']) - $art_qte;
            mysql_query("UPDATE articles SET art_stock ='".$new_qte."' WHERE art_id = '".$art['art_id']."'");
          }
        }
        //Révocation de la clé panier utilisée
        mysql_query("UPDATE key_panier SET key_used = '1' WHERE key_panier = '".$key_panier."'");
        //Afficher le bouton pour envoyer sur la page vérifier les informations
        ?></form>
        <a href="index.php?page=panier&amp;valider" class="button"><button><img src="images/icones/arrow_right.png" alt="" /> Vérifier mes informations</button></a>
<?php
      }
      else
      {
        //Cette clé n'a jamais été enregistrée dans la BDD
        echo '<div class="erreurs">clé invalide (non-distribuée)</div>';
      }
    }
    else
    {
       //Afficher erreur: clé absente.
       echo '<div class="erreurs">La clé de vérification du panier est absente. Videz votre panier et recommencez.</div>';
    }
  }
  else
  { ?>
  <button name="verification_ok" type="submit"><img src="images/icones/arrow_right.png" alt="" /> Valider mon panier</button>
  </form>
  <a href="index.php?page=panier" class="button"><button><img src="images/icones/modifier.png" alt="" /> Modifier mon panier</button></a>
<?php
  }
}
//Cas où le cookie est defini, et qu'il y a au moins un article présent.
elseif(isset($_COOKIE['art_panier']) && $_COOKIE['art_panier']!= "a:0:{}")
{
?>
<div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=panier" class="ariane_hover">Mon panier</a></div>
<h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Mon panier</h1>
<table class="panier">
<tr class="panier_top">
  <th></th>
  <th>Nom</th>
  <th>Ref.</th>
  <th>Dispo.</th>
  <th>Prix unitaire</th>
  <th>Quantité commandée</th>
  <th>Total</th>
</tr>
<?php
  $tableau_art_panier = unserialize($_COOKIE['art_panier']);
  //Si le tableau temporaire à été défini en début d'index.php(défini pour le premier rechargement de page vu que le cookie se trouve sur le poste de l'utilisateur)
  if(isset($tableau_temp)) $tableau = $tableau_temp; else $tableau = $tableau_art_panier;
  //Pour chaque item du panier, récupérer ses infos dans la BDD
  foreach($tableau as $art_id => $art_qte)
  {
    $nb_art = $nb_art + $art_qte;
    $requete_art = mysql_query('SELECT * FROM articles WHERE art_id ='.intval($art_id));
    $art = mysql_fetch_array($requete_art);
    ?>
    <tr class="panier_ligne">
      <td class="panier_img"><img src="articles/<?php echo $art['art_id']; ?>.jpg" alt="" /></td>
      <td class="panier_name"><a href="index.php?id=<?php echo $art['art_id']; ?>"><?php echo utf8_encode($art['art_name']); ?></a></td>
      <td class="panier_id">id<?php echo $art['art_id']; ?></td>
      <td class="panier_stock"><?php if($art['art_stock'] > 0)echo '<img src="images/icones/check.png" alt="" />';
      else echo '<img src="images/icones/cross.png" alt="" />'; ?></td>
      <td class="panier_prix"><?php echo str_replace(".", ",", $art['art_prix']); ?> &#128;</td>
      <td class="panier_qte">
        <form method="post" action="index.php?page=panier">
        <input type="hidden" name="art_id" value="<?php echo $art['art_id']; ?>" />
        <input type="text" size="1" name="art_qte" value="<?php echo $art_qte; ?>" />
        <button type="submit"><img src="images/icones/update.png" alt="" /></button>
        </form>
        <form method="post" action="index.php?page=panier">
        <input type="hidden" name="delete_art" value="<?php echo $art['art_id']; ?>"/>
        <button type="submit"><img src="images/icones/trash.gif" alt="" /></button>
        </form>
      </td>
      <td class="panier_sous_total">+ <?php $sous_total = $art['art_prix'] * $art_qte;
      echo str_replace(".", ",", $sous_total).' &#128;'; $total = $total + $sous_total;?></td>
    </tr>
    <?php
  }
?>
<tr><td colspan="7" class="panier_total">TOTAL: <?php echo str_replace(".", ",", $total).' &#128;'; ?> TTC</td></tr>
</table>
<form method="post" action="index.php?page=panier">
<button type="submit" name="delete_panier" value="Vider le panier"><img src="images/icones/trash.gif" alt="" /> Vider le panier</button>
</form>
<a href="index.php?page=panier&amp;verification_panier" class="button"><button><img src="images/icones/panier_2.png" alt="" /> Vérifier mon panier</button></a>

<?php
}
//Si le cookie n'est pas défini, ou qu'il ne contien aucun article, on affiche 'panier vide'
else
{
?>
<h2>Votre panier est vide ! :O</h2>
<a href="index.php?cat" class="ariane_hover">Parcourrez les différentes catégories</a>pour faire vos achats.
<?php
}
?>
