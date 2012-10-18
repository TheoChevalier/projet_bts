<?php
///On récupère toutes les données de l'article dont l'id se trouve dans l'URL
$tab_code = explode('F', $_GET['id']);
$art_id = intval($tab_code[0]);
if(isset($_POST['art_id'])) $fourn_form = explode('F', $_POST['art_id']);
if(isset($tab_code[1])) $code_fourn = intval($tab_code[1]);
elseif(isset($fourn_form[1])) $code_fourn = intval($fourn_form[1]);
else $code_fourn = 0;
$requete_art = mysql_query('SELECT * FROM articles WHERE art_id ='.$art_id);
$art = mysql_fetch_array($requete_art);
?>
<div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?cat" class="ariane_hover">Boutique</a> > 
<?php
//Récupération des catégories de l'article par jointure entre les tables cat_correspondances et categories
$requete_cat = mysql_query('SELECT cat_nom, cat_correspondances.cat_code FROM categories, cat_correspondances 
    WHERE cat_correspondances.art_code = "'.$art_id.'" AND cat_correspondances.cat_code = categories.cat_code');
    $affichage_categories ="";
//Tant qu'on a des résultats, on affiche les liens des catégories dans le fil d'ariane
while($cat = mysql_fetch_array($requete_cat))
{
  echo '<a href="index.php?cat='.$cat['cat_code'].'" class="ariane_hover">'.utf8_encode($cat['cat_nom']).'</a> > ';
  $affichage_categories .='<a href="index.php?cat='.$cat['cat_code'].'" class="ariane_hover">'.utf8_encode($cat['cat_nom']).'</a> ';
}
 ?><a href="index.php?id=<?php echo $art['art_id']; ?>" class="ariane_hover"><?php echo utf8_encode($art['art_name']); ?></a></div>
<h1 class="ribbon shadow"><span class="ribbon_shadow"></span><?php echo utf8_encode($art['art_name']); ?></h1>
  <div class="classement">
    <div class="options_classement">
    <form method="get" action="index.php">
    <input type="hidden" name="cat" />
    <input type="text" name="rechercher" id="rechercher" value="Rechercher..." class="rechercher" OnFocus="if(this.value=='Rechercher...'){this.style.color='#000'; this.value='';}"
    OnBlur="if(this.value==''){this.style.color='#aaa'; this.value='Rechercher...';}" />
    <a href="<?php echo "index.php?cat&amp;rechercher="; ?>" onClick="this.href=this.href+document.getElementById('rechercher').value;"><div class="rechercher_loupe" ><img src="images/icones/art_loupe.png" alt="" /></div></a>
    </form>
    </div>
    <div class="clear"></div>
  </div>
<div class="art_det">
  <div class="art_det_img"><img src="articles/<?php echo $art['art_id']; ?>.jpg" alt="" /></div>
  <div class="equipe_description">
    <div class="art_det_name"><?php echo utf8_encode($art['art_name']); ?></div>
    <div class="art_det_desc"><?php echo utf8_encode($art['art_desc']); ?></div>

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
  function envoi_fourn(f)
  {
    //Instanciation de la requête XHR
    creerRequete();
    //f est le formulaire qui contient la combobox, passé en paramètres
    var code = f.elements["art_id"];
    //url du script à qui envoyer
    var url = 'includes/art_ajax.php';
    //Numéro de la ligne sélectionnée dans la CB
    var index = code.selectedIndex;
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
    var data = "code="+escape(code.options[index].value); 
    //On envoie ces données
    requete.send(data);
  }
  </script>
    <form method="post" action="index.php?id=<?php if(isset($_POST['art_id']))echo $_POST['art_id']; else echo $art['art_id'];?>#ajout">
      <select id="art_id" name="art_id" size="1" onchange="envoi_fourn(this.form);">
      <?php
      //Récupération des fournisseurs de cet articles, et création de la Combobox
      $requete_fournisseurs = mysql_query("SELECT fournisseurs.code_fourn, fournisseurs.nom_fourn
      FROM fournisseurs, fournir, articles
      WHERE articles.art_id = ".$art['art_id']."
      AND articles.art_id = fournir.art_id
      AND fournir.code_fourn = fournisseurs.code_fourn
      ORDER BY fournisseurs.nom_fourn");
      while($fournisseurs = mysql_fetch_array($requete_fournisseurs))
      {
        if($code_fourn == 0 && !isset($fourn1))$fourn1 = $fournisseurs['code_fourn'];
        echo '<option value="'.$art_id.'F'.$fournisseurs['code_fourn'].'"';
        if($code_fourn == $fournisseurs['code_fourn'])echo ' selected="selected"';
        echo '>'.utf8_encode($fournisseurs['nom_fourn']).'</option>';
      }
      //Requete pour afficher les détails en fonction du fournisseur par défaut dans la liste si aucun fournisseur
      //n'est présent dans le code produit, sinon utiliser le fournisseur du code produit
      if($code_fourn != 0)$fourn = $code_fourn;
      else $fourn = $fourn1;
      $requete_infos = mysql_query("SELECT art_prix, art_qte, art_delai
      FROM fournisseurs, fournir
      WHERE art_id = ".$art_id."
      AND fournisseurs.code_fourn = ".$fourn."
      AND fournir.code_fourn = fournisseurs.code_fourn");
      $infos = mysql_fetch_array($requete_infos);
      ?>
      </select>
      <div class="ajouter_panier"><span id="prix"><?php echo str_replace(".", ",", $infos['art_prix']); ?></span> &#128; &#160; X &#160; <input type="text" value="1" size="1" name="art_qte" />
      <button type="submit" class="button" name="ajout_panier" id="ajout_panier"><img src="images/icones/trolley.png" alt="" /> Ajouter au panier</button>
      </div>
    </form>
    <div class="art_det_stock"><p>Disponibilité : <img id="dispo" src="images/icones/<?php if($infos['art_qte'] > 0)echo 'check.png';
    else echo 'cross.png'; ?>" alt="" /> (<span id="stock"><?php echo $infos['art_qte']; ?></span> article(s) en stock)</p></div>
    <div><p>Délai de livraison: <span id="delai"><?php if($infos['art_qte'] <= 0) echo $infos['art_delai']; else echo "2"; ?></span> jours.</p></div>
    <div class="art_det_cat"><p>Catégorie(s) : <?php echo $affichage_categories; ?></p></div>
  </div>
</div>
<div class="clear"></div>
