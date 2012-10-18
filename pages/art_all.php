<?php
//Si un numéro de page est présent dans l'url, on affichera cette page, sinon on affiche la page 1 par défaut
if(isset($_GET['p'])) $page = intval($_GET['p']);
else $page = 1;
//Si un nombre d'articles par page est défini, on initialise le cookie pour stocker ce nombre, et on défini la variable pour la requête sql
if(isset($_POST['nb_art']) && !empty($_POST['nb_art']))
{
  $nb_items_par_page = intval($_POST['nb_art']);
  setcookie('nb_art', $nb_items_par_page, time()+7*24*3600);
}
//Sinon on utilise la valeur par défaut
elseif(isset($_COOKIE['nb_art'])) $nb_items_par_page = intval($_COOKIE['nb_art']);
else $nb_items_par_page = 10;
//Si une recherche à été lancée, on affiche les résultats
if(isset($_GET['rechercher']))
{ ?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?cat" class="ariane_hover">Boutique</a> >
  <?php echo ' <a href="'.$_SERVER['REQUEST_URI'].'" class="ariane_hover">Recherche: '.htmlspecialchars($_GET['rechercher'], ENT_QUOTES).'</a>'; ?></div>
  <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Résultats de la recherche de "<?php echo htmlspecialchars($_GET['rechercher'], ENT_QUOTES); ?>"</h1>
  <div class="classement">
    <form class="nb_p" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <label for="nb_art">Articles par page: </label>
    <select name="nb_art" id="nb_art" class="combobox" onChange='this.form.submit();'>
      <option value="1" <?php if ($nb_items_par_page == 1) echo 'selected="selected"' ; ?>>1</option>
      <option value="10" <?php if ($nb_items_par_page == 10) echo 'selected="selected"' ; ?>>10</option>
      <option value="30" <?php if ($nb_items_par_page == 30) echo 'selected="selected"' ; ?>>30</option>
      <option value="50" <?php if ($nb_items_par_page == 50) echo 'selected="selected"' ; ?>>50</option>
      <option value="100" <?php if ($nb_items_par_page == 100) echo 'selected="selected"' ; ?>>100</option>
    </select>
    </form>
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
  <?php
  $mot = htmlspecialchars($_GET['rechercher'], ENT_QUOTES);  
  $mots = explode(" ",$mot);
  $r_mot = $mot.strtolower(htmlspecialchars($_GET['rechercher'], ENT_QUOTES));
  $nombre_mots = count($mots);
  $j=1;
  $texte='Articles contenant <b>"'.$mots[0].'"</b>';
  $phrase="'%".$mots[0]."%'";
  while($j < $nombre_mots)
  {
    $phrase.=" OR art_name LIKE '%".$mots[$j]."%'";
    $texte.=' ou <b>"'.$mots[$j].'"</b>';
    $j++;
  }
  //Nombre total d'enregistrements répondant à la requête
  $requete=mysql_query("SELECT COUNT(*) AS nb_articles FROM articles WHERE art_name LIKE ".$phrase." ORDER BY art_name");
  $nb_total=mysql_fetch_array($requete);
  $total_items = $nb_total['nb_articles'];
  //On calcule le nombre de pages nécessaires
  $nb_pages  = ceil($total_items / $nb_items_par_page);
  //On défini le numéro du premier article à afficher en fonction du numéro de la page et du nombre d'articles à afficher par page
  $premier_item = ($page - 1) * $nb_items_par_page;
  //Si le classement par prix est présent dans l'url, on classera les résultats de la requête par art_prix, sinon par défaut, par art_name
  if(isset($_GET['c_prix'])) $orderby="art_prix";
  else $orderby ="art_name";
  $requete=mysql_query("SELECT * FROM articles WHERE art_name LIKE ".$phrase." ORDER BY ".$orderby." ASC LIMIT ".$premier_item.",".$nb_items_par_page);
  $num=mysql_num_rows($requete);
  //Définition des messages d'erreur
  if ($num==0 && $mot != "Rechercher...") {echo "Désolé, aucun article ne contient <b>".$mot."</b>...";}
  else if ($mot=="" || $mot=="Rechercher...")   {echo "Vous n'avez rien entré dans le champ de recherche.";}
  else if (strlen($mot)<2) {echo "Vous devez saisir au moins 2 caractères.";}  
  //On affiche le résultat
  else 
  {
    echo "<b>".$total_items."</b> article";
    if ($total_items > 1) {echo "s";}
    echo "<br>".$texte;?>
    <div class="pages"><?php
    //On affiche les liens de pages, en affichant spécialement la page actuelle
    for($i = 1 ; $i <= $nb_pages ; $i++)
    {
      echo '<a href="index.php?cat&amp;rechercher='.htmlspecialchars($_GET['rechercher'], ENT_QUOTES).'&amp;p='.$i.'">';
      echo '<div class=';
      if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
      echo $i.'</div></a>';
    }
    ?>
    </div><?php
    //Tant que on a des articles, on les affiche
    while($resultats = mysql_fetch_array($requete))
    {
      ?><a href="index.php?id=<?php echo $resultats['art_id']; ?>">
      <div class="art_hover"><div class="art" style="background-image: url('articles/<?php echo $resultats['art_id']; ?>.jpg');">
      <div class="art_loupe"></div>
      <div class="art_name"><?php echo utf8_encode($resultats['art_name']); ?></div>
      <div class="art_prix"><?php echo str_replace(".", ",", $resultats['art_prix']); ?> &#128;</div>
      </div></a></div>
    <?php
    }
    ?>
    <div class="clear"></div>
    <div class="pages"><?php
    //Et on re-affiche les liens de pages
    for($i = 1 ; $i <= $nb_pages ; $i++)
    {
      echo '<a href="index.php?cat&amp;rechercher='.htmlspecialchars($_GET['rechercher'], ENT_QUOTES).'&amp;p='.$i.'">';
      echo '<div class=';
      if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
      echo $i.'</div></a>';
    } ?>
    </div><?php
  }
}
//Si une catégorie est définie, on affiche cette catégorie
elseif(!empty($_GET['cat']))
{
  //on commence par récupérer le nombre total d'articles pour cette catégorie
  $retour = mysql_query('SELECT COUNT(*) AS nb_articles FROM cat_correspondances WHERE cat_code ="'.mysql_real_escape_string($_GET['cat']).'"');
  $donnees = mysql_fetch_array($retour);
  //ce nombre d'articles sera le total des messages pour l'ensemble des pages
  $total_items = $donnees['nb_articles'];
  //On calcule le nombre de pages nécessaires
  $nb_pages  = ceil($total_items / $nb_items_par_page);
  $cat_id_requete = intval($_GET['cat']);
  //On récupère le nom de la catégorie choisie
  $requete_cat = mysql_query('SELECT cat_nom, cat_code FROM categories WHERE cat_code = "'.$cat_id_requete.'"');
  $cat = mysql_fetch_array($requete_cat);
  ?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?cat" class="ariane_hover">Boutique</a> >
  <?php echo ' <a href="index.php?cat='.$cat['cat_code'].'" class="ariane_hover">'.utf8_encode($cat['cat_nom']).'</a>'; ?></div>
  <h1 class="ribbon shadow"><span class="ribbon_shadow"></span><?php echo utf8_encode($cat['cat_nom']);?></h1>
  <div class="classement">
    <form class="nb_p" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    <label for="nb_art">Articles par page: </label>
    <select name="nb_art" id="nb_art" class="combobox" onChange='this.form.submit();'>
      <option value="1" <?php if ($nb_items_par_page == 1) echo 'selected="selected"' ; ?>>1</option>
      <option value="10" <?php if ($nb_items_par_page == 10) echo 'selected="selected"' ; ?>>10</option>
      <option value="30" <?php if ($nb_items_par_page == 30) echo 'selected="selected"' ; ?>>30</option>
      <option value="50" <?php if ($nb_items_par_page == 50) echo 'selected="selected"' ; ?>>50</option>
      <option value="100" <?php if ($nb_items_par_page == 100) echo 'selected="selected"' ; ?>>100</option>
    </select>
    </form>
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
  <div class="pages"><?php
  //On affiche les liens de pages, en affichant spécialement la page actuelle
  for($i = 1 ; $i <= $nb_pages ; $i++)
  {
    echo '<a href="index.php?cat='.$_GET['cat'].'&amp;p='.$i.'">';
    echo '<div class=';
    if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
    echo $i.'</div></a>';
  }
  ?>
  </div><?php
  //On défini le numéro du premier article à afficher en fonction du numéro de la page et du nombre d'articles à afficher par page
  $premier_item = ($page - 1) * $nb_items_par_page;
  //Si le classement par catégories est présent dans l'url, on classera les résultats de la requête par categories.cat_code
  if(isset($_GET['c_cat'])) $orderby="categories.cat_code";
  //Sinon si le classement par prix est présent dans l'url, on classera les résultats de la requête par art_prix
  elseif(isset($_GET['c_prix'])) $orderby="art_prix";
  else $orderby ="art_name";
    $var_cat = mysql_real_escape_string($_GET['cat']);
    $reponse = mysql_query('SELECT art_id, art_name FROM articles, categories, cat_correspondances 
    WHERE categories.cat_code ="'.$var_cat.'" AND categories.cat_code = cat_correspondances.cat_code AND articles.art_id = cat_correspondances.art_code
    ORDER BY '.$orderby.' ASC LIMIT '.$premier_item.', '.$nb_items_par_page);
  //Tant que on a des articles, on les affiche
  while($donnees = mysql_fetch_array($reponse))
  {
    ?><a href="index.php?id=<?php echo $donnees['art_id']; ?>">
    <div class="art_hover"><div class="art" style="background-image: url('articles/<?php echo $donnees['art_id']; ?>.jpg');">
    <div class="art_loupe"></div>
    <div class="art_name"><?php echo utf8_encode($donnees['art_name']); ?></div>
    <?php $requete_prix = mysql_query('SELECT max(art_prix) as max, min(art_prix) as min FROM articles, fournir
    WHERE articles.art_id='.$donnees['art_id'].' AND articles.art_id = fournir.art_id');
    $prix = mysql_fetch_array($requete_prix);
    ?>
    <div class="art_prix"><?php echo 'De '.str_replace(".", ",", number_format($prix['min'], 2)).' &#128; à '.str_replace(".", ",", number_format($prix['max'],2)); ?> &#128;</div>
    </div></a></div>
  <?php
  }
  //Si on a aucun article on affiche un message
  if($total_items == 0)
  {
    echo "Désolé, il n'y a aucun article dans cette catégorie...";
  } ?>
  <div class="clear"></div>
  <div class="pages"><?php
  //Et on re-affcihe les liens de pages
  for($i = 1 ; $i <= $nb_pages ; $i++)
  {
    echo '<a href="index.php?cat='.$_GET['cat'].'&amp;p='.$i.'">';
    echo '<div class=';
    if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
    echo $i.'</div></a>';
  } ?>
  </div>
  <?php
}
//Si rien n'est présent dans l'url on affiche par défaut toutes les catégories
else
{
  //on commence par récupérer le nombre total de catégories
  $retour = mysql_query('SELECT COUNT(*) AS nb_categories FROM categories');
  $donnees = mysql_fetch_array($retour);
  //ce nombre de catégories sera le total des messages pour l'ensemble des pages de catégories
  $total_items = $donnees['nb_categories'];
  //On calcule le nombre de pages nécessaires
  $nb_pages  = ceil($total_items / $nb_items_par_page);
  $premier_item = ($page - 1) * $nb_items_par_page;
  $reponse = mysql_query('SELECT * FROM categories ORDER BY cat_nom ASC LIMIT '.$premier_item.','.$nb_items_par_page);
?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?cat" class="ariane_hover">Boutique</a></div>
  <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Tous nos produits</h1>
  <div class="classement">
  <form class="nb_p" method="post" action="index.php?cat">
  <label for="nb_art">Articles par page: </label>
  <select name="nb_art" id="nb_art" class="combobox" onChange='this.form.submit();'>
    <option value="1" <?php if ($nb_items_par_page == 1) echo 'selected="selected"' ; ?>>1</option>
    <option value="10" <?php if ($nb_items_par_page == 10) echo 'selected="selected"' ; ?>>10</option>
    <option value="30" <?php if ($nb_items_par_page == 30) echo 'selected="selected"' ; ?>>30</option>
    <option value="50" <?php if ($nb_items_par_page == 50) echo 'selected="selected"' ; ?>>50</option>
    <option value="100" <?php if ($nb_items_par_page == 100) echo 'selected="selected"' ; ?>>100</option>
  </select>
  </form>
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
  <div class="pages"><?php
  //On affiche les liens de pages, en affichant spécialement la page actuelle
  for($i = 1 ; $i <= $nb_pages ; $i++)
  {
    echo '<a href="index.php?cat&amp;p='.$i.'">';
    echo '<div class=';
    if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
    echo $i.'</div></a>';
  }
  ?>
  </div>
  <?php
  while($donnees = mysql_fetch_array($reponse))
  {
    ?><div class="art_hover"><a href="index.php?cat=<?php echo $donnees['cat_code']; ?>">
    <?php if($donnees['cat_avatar'] == 0){ ?>
      <div class="cat image_defaut" style="background-image: url('articles/categories/0.png');">  
    <?php } else{ ?>
      <div class="cat" style="background-image: url('articles/categories/<?php echo $donnees['cat_code']; ?>.jpg');">
    <?php } ?>
    <div class="cat_name"><?php echo utf8_encode($donnees['cat_nom']); ?></div>
    </div></a></div>
  <?php
  }?>
  <div class="clear"></div>
  <div class="pages"><?php
  //On affiche les liens de pages, en affichant spécialement la page actuelle
  for($i = 1 ; $i <= $nb_pages ; $i++)
  {
    echo '<a href="index.php?cat&amp;p='.$i.'">';
    echo '<div class=';
    if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
    echo $i.'</div></a>';
  }
  ?>
  </div>
<?php
}
?>