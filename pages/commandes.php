<?php 
$user_id = intval($_SESSION['user_id']);
$nb_erreurs = 0;
$requete_nom = mysql_query("SELECT user_prenom, user_nom FROM user WHERE user_id = ".$user_id);
$nom = mysql_fetch_array($requete_nom);
$masquer_formulaire = false;
//Si les coordonnées banquaires ont déjà étées renseignées
if(isset($_GET['com_id']))
{

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
    //Si tout est bon, on rempli la table carte et on procède au paiement
    if($nb_erreurs == 0)
    {
      //On récupère le client de cette commande et on le compare au client connecté
      $requete_commande = mysql_query("SELECT user_id, carte_id FROM commandes WHERE com_id = '".mysql_real_escape_string($_GET['com_id'])."'");
      $commande = mysql_fetch_array($requete_commande);
      if($commande['user_id'] == $_SESSION['user_id'])
      {
        $carte_expire = '20'.intval($_POST['payer_annee']).'-'.intval($_POST['payer_expire']).'-01';
        //Si on a déjà une carte enregistrée pour cette commande
        if($commande['carte_id'] != 0)
        {
          //Rajouter com_payee à 1.
          mysql_query("UPDATE carte SET carte_num = ".mysql_real_escape_string($_POST['payer_carte']).", carte_secu = ".intval($_POST['payer_securite']).", carte_expire = '".$carte_expire."'
          WHERE carte_id = ".$commande['carte_id']);
          $carte_id = $commande['carte_id'];
        }
        else
        {
          mysql_query("INSERT INTO carte(
              user_id
            , carte_num
            , carte_secu
            , carte_expire
            )
            VALUES(
            ".intval($commande['user_id'])."
            , '".mysql_real_escape_string($_POST['payer_carte'])."'
            , ".intval($_POST['payer_securite'])."
            , '".mysql_real_escape_string($carte_expire)."'
            )
            ");
            $carte_id = mysql_insert_id();
        }
        mysql_query("UPDATE commandes SET com_payee = '1', carte_id = ".$carte_id." WHERE com_id = '".mysql_real_escape_string($_GET['com_id'])."'");
        //Suppression des cookies
        //setcookie("key_panier", false, time() - 3600);
        //setcookie("art_panier", false, time() - 3600);
        //setcookie('remise', false, time() - 3600);
        //setcookie('user_id', false, time() - 3600);
        unset($_COOKIE['key_panier']);
        unset($_COOKIE['art_panier']);
        unset($_COOKIE['remise']);
        unset($_COOKIE['user_id']);
        $masquer_formulaire = true;
      }else echo "Mauvais client";
    }
  
  //Si les coordonées banquaires ont déjà été renseignées, on masque ce formulaire
  if($masquer_formulaire == false)
  {  ?>
    <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=commandes" class="ariane_hover">Mes commandes</a> > 
    <a href="index.php?page=commandes&payer" class="ariane_hover">Régler la commande</a></div>
    <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Règlez votre commande</h1>

    <div class="equipe_description content">
    <div class="form_message">
    Entrez vos informations bancaires afin de procéder au règlement de la commande N°<?php echo $_GET['com_id'];
    $requete_carte = mysql_query("SELECT commandes.com_id, commandes.com_montant, user.user_id
    FROM commandes, user
    WHERE commandes.com_id = '".mysql_real_escape_string($_GET['com_id'])."'
    AND user.user_id = commandes.user_id");
    $carte = mysql_fetch_array($requete_carte); ?>
    <?php if(isset($erreurs) && $erreurs != "") echo $erreurs; ?>
    <form method="post" action="index.php?page=commandes&amp;payer&amp;com_id=<?php echo $_GET['com_id']; ?>" name="payer" id="payer">
    <p><label for="payer_carte">Numéro de carte</label> <input type="text" maxlength="16" name="payer_carte" id="payer_carte" /></p>
    <p><label for="payer_securite">Code de sécurité</label> <input type="text" maxlength="3" name="payer_securite" id="payer_securite" /></p>
    <p><label for="payer_expire">Date d'expiration</label> <input type="text" maxlength="2" name="payer_expire" id="payer_expire" size="1" />
    / <input type="text" maxlength="2" name="payer_annee" id="payer_annee" size="1" /></p>
    Montant débité: <?php
    echo str_replace(".", ",", number_format($carte['com_montant'],2))." &#128";
    ?>
    <button type="submit" >Envoyer</button>
    </form>
    </div>
    </div>
<?php
  }
  else{?>
    <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=commandes" class="ariane_hover">Mes commandes</a> > 
    <a href="index.php?page=commandes&payer" class="ariane_hover">Régler la commande</a></div>
    <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Commande règlée</h1>
    La commande a bien été règlée, votre compte va être débité. Merci de votre achat!
  <?php }
}else{
?>
<div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=commandes" class="ariane_hover">Commandes de <?php echo utf8_encode($nom['user_prenom']." ".$nom['user_nom']);?></a></div>
<h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Mes commandes</h1>
<script type="text/javascript">
  //Requête pour initialiser l'objet XHR en fonction du navigateur
  function creerRequete() {
    try {
    requete = new XMLHttpRequest();
    }
    catch (microsoft) {
      try {
      requete = new ActiveXObject('Msxml2.XMLHTTP');
      } catch(autremicrosoft) {
        try {
        requete = new ActiveXObject('Microsoft.XMLHTTP');
        } catch(echec) {
        requete = null;
        }
      }
    }
    if(requete == null) {
    alert('Votre navigateur ne supporte pas les requêtes XHR...');
    }
  }
  //Fonction pour envoyer l'ordre et recevoir les familles
  function envoi_box(f)
  {
    //Instanciation de la requête XHR
    creerRequete();
    //f est le formulaire qui contient la checkbox, passé en paramètre
    var box = f.elements["representant"];
    var user = f.elements["user_id"];
    //url du script à qui envoyer
    var url = 'includes/rep_ajax.php';
    //On initialise la connexion, et on définit le mode d'envoi (POST)
    requete.open('POST', url, true);
    //On regarde quand la connexion change d'état (reception d'une réponse)
    requete.onreadystatechange = function anonymous() {
    //Si l'état de la connexion est 4 (réponse reçue)
    if(this.readyState == 4) { 
    //On exécute les instruction JS contenues dans la réponse (voir slam1_ajax.php)
    eval(this.responseText);
    }
    };
    //Définition du type de contenu qui sera envoyé (en-tête)
    requete.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //Création de la requête à envoyer au script
    var data = "box="+box.checked+"&user_id="+user.value; 
    //On envoie ces données
    requete.send(data);
  }
  </script>
<form method="post" action="" id="form_rep">
<p>
<?php
  $requete_rendez_vous = mysql_query("SELECT user_id FROM planifier WHERE user_id = ".$user_id);
  $rendez_vous = mysql_num_rows($requete_rendez_vous);
?>
<input id="representant" name="representant" type="checkbox" <?php if($rendez_vous > 0) echo 'checked="checked"'; ?> onchange="envoi_box(this.form);" /> <label for="representant">Je souhaite le passage du réprésentant de mon département</label>
<input type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
</p>
<p><span id="reponse_rep"></span>
</form>
  Commandes non payées:
  <table class="panier">
  <tr class="panier_top">
    <th>N°commande</th>
    <th>Date cmd</th>
    <th></th>
    <th>Nom</th>
    <th>Ref.</th>
    <th>Fourn.</th>
    <th>Dispo.</th>
    <th>Prix unit.</th>
    <th>Quantité commandée</th>
    <th></th>
  </tr>
  <?php
  $requete_commandes1 = mysql_query("SELECT taux_remise, commandes.com_id, com_date, com_montant
  FROM commandes, user
  WHERE user.user_id = ".$user_id."
  AND commandes.com_payee = '0'
  AND user.user_id = commandes.user_id");
  while($com_np = mysql_fetch_array($requete_commandes1))
  {
   ?>
    <tr class="panier_ligne commande_ligne">
      <td class="panier_id"><?php echo $com_np['com_id']; ?></td>
      <td class="panier_id"><?php echo $com_np['com_date']; ?></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td class="panier_sous_total commande_montant"><a href="index.php?page=commandes&amp;com_id=<?php echo $com_np['com_id']; ?>" ><button>Payer</button></a></td>
    </tr><?php
    $requete_art_com_np = mysql_query("SELECT articles.art_id, art_name, art_prix, commander.com_qte, fournisseurs.code_fourn, fournisseurs.nom_fourn
    FROM articles, commander, commandes, fournir, fournisseurs
    WHERE commandes.com_id = '".$com_np['com_id']."'
    AND commandes.com_id = commander.com_id
    AND commander.art_id = articles.art_id
    AND commander.code_fourn = fournisseurs.code_fourn
    AND articles.art_id = fournir.art_id
    AND fournir.code_fourn = fournisseurs.code_fourn");

    while($art_com_np = mysql_fetch_array($requete_art_com_np))
    { ?>
        <tr>
          <td></td>
          <td></td>
          <td class="panier_img"><img src="articles/<?php echo $art_com_np['art_id']; ?>.jpg" alt="" /></td>
          <td class="panier_name"><?php echo utf8_encode($art_com_np['art_name']); ?></td>
          <td class="panier_id"><a href="index.php?id=<?php echo $art_com_np['art_id'].'F'.$art_com_np['code_fourn']; ?>">
          id<?php echo $art_com_np['art_id'].'F'.$art_com_np['code_fourn']; ?></a></td>
          <td class="panier_id"><?php echo $art_com_np['nom_fourn']; ?></td>
          <td class="panier_stock"><img src="images/icones/check.png" alt="" /></td>
          <td class="panier_prix"><?php echo str_replace(".", ",", $art_com_np['art_prix']); ?> &#128;</td>
          <td class="panier_qte"><?php echo $art_com_np['com_qte']; ?></td>
          <td></td>
          <td></td>
        </tr><?php
    }
  }?>
  </table>
  Commandes payées:
  <table class="panier">
  <tr class="panier_top">
    <th>N°cmd</th>
    <th>Date cmd</th>
    <th></th>
    <th>Nom</th>
    <th>Ref.</th>
    <th>Fourn.</th>
    <th>Qté commandée</th>
    <th>Prix payé</th>
  </tr>
<?php
  $requete_commandes2 = mysql_query("SELECT taux_remise, commandes.com_id, com_date, com_montant
  FROM commandes, user
  WHERE user.user_id = ".$user_id."
  AND commandes.com_payee = '1'
  AND user.user_id = commandes.user_id
  ORDER BY com_date");
  while($com_p = mysql_fetch_array($requete_commandes2))
  {
   ?>
    <tr class="panier_ligne commande_ligne">
      <td class="panier_id"><?php echo $com_p['com_id']; ?></td>
      <td class="panier_id"><?php echo $com_p['com_date']; ?></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td class="panier_sous_total commande_montant"><?php echo str_replace(".", ",", $com_p['com_montant']).' &#128;'; ?></td>
    </tr><?php
    $requete_art_com = mysql_query("SELECT articles.art_id, art_name, commander.com_qte, fournisseurs.code_fourn, fournisseurs.nom_fourn
    FROM articles, commander, commandes, fournisseurs
    WHERE commandes.com_id = '".$com_p['com_id']."'
    AND commandes.com_id = commander.com_id
    AND commander.art_id = articles.art_id
    AND commander.code_fourn = fournisseurs.code_fourn
    ORDER BY com_date");
    while($art_com = mysql_fetch_array($requete_art_com))
    {
      $art_qte = $art_com['com_qte'];
   ?>
        <tr class="panier_ligne">
          <td></td>
          <td></td>
          <td class="panier_img"><img src="articles/<?php echo $art_com['art_id']; ?>.jpg" alt="" /></td>
          <td class="panier_name"><a href="index.php?id=<?php echo $art_com['art_id'].'F'.$art_com['code_fourn']; ?>"><?php echo utf8_encode($art_com['art_name']); ?></a></td>
          <td class="panier_id">id<?php echo $art_com['art_id'].'F'.$art_com['code_fourn']; ?></td>
          <td class="panier_id"><?php echo $art_com['nom_fourn']; ?></td>
          <td class="panier_qte"><?php echo $art_qte; ?></td>
          <td class="panier_sous_total"></td>
        </tr><?php
    }
  }?>
  </table>
<?php
} ?>