<?php
//Affichage d'une news en particulier
if(isset($_GET['news_id']))
{
  //Récupération des données de la news dans la BDD
  $news_id = $_GET['news_id'];
  $requete_news = mysql_query("SELECT * FROM news WHERE news_id ='".$news_id."'") or die("Impossible d'afficher la news.");
  $news = mysql_fetch_array($requete_news);
  //Passage du format date/horaire en Français
  setlocale(LC_TIME, 'fr_FR');
  ?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=news" class="ariane_hover">News</a> > <a href="index.php?page=news&amp;news_id=<?php echo $news_id; ?>" class="ariane_hover"><?php echo utf8_encode($news['news_titre']); ?></a></div>
    <h1 class="ribbon shadow"><span class="ribbon_shadow"></span><?php echo utf8_encode($news['news_titre']); ?></h1>
  <div class="news_details">
    <div class="news">
      <div class="news_text">
      <div class="news_date"><?php 
      //Extraction et mise en forme de la date à partir de JJ-MM-AAAA vers JJ Mois Année
      $date_annee = substr($news['news_date'], 0, 4);
      $date_mois = substr($news['news_date'], 8, 2);
      $date_jour = substr($news['news_date'], 5, 2);
      $timestamp = mktime( 0, 0, 0, $date_jour , $date_mois , $date_annee);
      $date = strftime( "%d %B %Y" , $timestamp);
      echo 'Le '.$date; ?>.</div>
      <?php echo utf8_encode($news['news_desc']); ?>
      </div>
    </div>
  </div>
  <div class="clear"></div><?php
}
else
{
  //Affichage de toutes les news, par pages de 4 news
  if(isset($_GET['p'])) $page = intval($_GET['p']); else $page = 1;
  //Comptage du total des news
  $retour = mysql_query('SELECT COUNT(*) AS nb_news FROM news');
  $donnees = mysql_fetch_array($retour);
  $total_items = $donnees['nb_news'];
  $nb_items_par_page = 4;
  //Calcul du nombre de pages nécessaires
  $nb_pages  = ceil($total_items / $nb_items_par_page);
  ?>
  <div class="ariane"><a href="index.php" class="ariane_hover">Accueil</a> > <a href="index.php?page=news" class="ariane_hover">News</a></div>
  <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Toutes les news</h1>
   <div class="pages"><?php
  //Affichage de tous les liens de pages
  for($i = 1 ; $i <= $nb_pages ; $i++)
  {
    echo '<a href="index.php?page=news&amp;p='.$i.'"><div class=';
    if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
    echo $i.'</div></a>';
  }
  ?>
  </div>
  <div class="news_details">
<?php
  //Si un numéro de page est transmis, on affiche cette page, sinon on affiche la page 1
  if(isset($_GET['p'])) $page = intval($_GET['p']);
  else $page = 1;
  //définition du numéro du premier message de la page en fonction du numéro de la page et du nombre de messages par page
  $premier_item = ($page - 1) * $nb_items_par_page;
  $requete_derniere_news = mysql_query("SELECT * FROM news");
  $derniere_news = mysql_num_rows($requete_derniere_news);
  //Récuparation des news pour cette page uniquement
  $requete_news = mysql_query('SELECT * FROM news ORDER BY news_date DESC LIMIT '.$premier_item.', '.$nb_items_par_page) or die("Impossible d'afficher les news.");
  setlocale(LC_TIME, 'fr_FR');
  //on les affiche
  while($news = mysql_fetch_array($requete_news))
  { ?>
      <div class="news_hover">
      <a href="index.php?page=news&amp;news_id=<?php echo $news['news_id']; ?>">
      <div class="news"><h2 class="news_titre"><?php echo utf8_encode($news['news_titre']); ?></h2>
        <div class="news_text">
        <div class="news_date"><?php 
        $date_annee = substr($news['news_date'], 0, 4);
        $date_mois = substr($news['news_date'], 8, 2);
        $date_jour = substr($news['news_date'], 5, 2);
        $timestamp = mktime( 0, 0, 0, $date_jour , $date_mois , $date_annee);
        $date = strftime( "%d %B %Y" , $timestamp);
        echo 'Le '.$date; ?>.</div>
        <?php echo utf8_encode(substr($news['news_desc'], 0, 300)); if(substr($news['news_desc'], 0, 300) != $news['news_desc']) echo '(...)<br /><span class="afficher_suite">Afficher la suite</span>'; ?>
        </div>
      </div>
      </div>
      </a>
  <?php } ?>

</div>
<div class="clear"></div>
  <div class="pages"><?php
  //De nouveau les liens de pages
  for($i = 1 ; $i <= $nb_pages ; $i++)
  {
    echo '<a href="index.php?page=news&amp;p='.$i.'"><div class=';
    if($page == $i) echo '"page_actuelle">'; else echo '"page_hover">';
    echo $i.'</div></a>';
  }
  ?>
  </div>

<?php } ?>