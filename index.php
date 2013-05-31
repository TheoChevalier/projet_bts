<?php
session_start();
  include("includes/connexion.php");
//Inclusion de ma bibliothèque de fonctions
include('includes/fonctions.php');
//Fonction permettant la connexion à la base de données
connexionbdd();
$nb_art=0;
$tableau_art_panier = array();
//Contrôle de la session utilisateur lors de l'accès aux pages de validation du panier
if(isset($_GET['page']) && $_GET['page'] == "panier" && ($_SERVER['REQUEST_URI'] != "/index.php?page=panier" && $_SERVER['REQUEST_URI'] != "/ppe/index.php?page=panier"))
  actualiser_session("Avant de valider votre panier, connectez-vous à votre compte, ou créez-en un:");
  
//Contrôle de la session utilisateur lors de l'accès à la page d'historique des commandes
if(isset($_GET['page']) && $_GET['page'] == "commandes")
  actualiser_session("Avant de consulter votre historique des commandes, connectez-vous à votre compte, ou créez-en un:");

//Contrôle de la session représentant pour l'accès aux pages planning et commander
if(isset($_GET['page']) && ($_GET['page'] == "commander" || $_GET['page'] == "planning"))
  actualiser_session_representant("Vous devez vous connecter à un compte de représentant pour accéder à cette page.");
  
//Supprime le cookie des articles si delete_panier est envoyé en POST
if(isset($_POST['delete_panier']))
{
  setcookie("art_panier", false, time() - 3600);
  setcookie("key_panier", false, time() - 3600);
  unset($_COOKIE["art_panier"]);
  unset($_COOKIE["key_panier"]);
}
//Supprime un article si delete_art est envoyé en POST
if(isset($_POST['delete_art']))
{
  $tableau_temp = array();
  $tableau_art_panier = unserialize($_COOKIE['art_panier']);
  foreach($tableau_art_panier as $id => $qte)
  {
    if($id != intval($_POST['delete_art'])) $tableau_temp[$id] = $qte;
  }
  foreach($tableau_temp as $id => $qte) $nb_art = $nb_art + $qte;
  setcookie('art_panier', serialize($tableau_temp), time()+7*24*3600);
}
//Met à jour le cookie du panier si la quantité d'un article vient d'être modifiée
elseif(isset($_POST['art_qte']) && isset($_POST['art_id']))
{
  $tableau_temp = array();
  $nb_art = 0;
  $art_qte = intval($_POST['art_qte']);
  $art_code = $_POST['art_id'];
  //Cas où le panier à déjà été rempli précédement
  if(isset($_COOKIE['art_panier']))
  {
    $tableau_temp = unserialize($_COOKIE['art_panier']);
    //Cas où l'article modifié est déjà dans le panier
    if(isset($tableau_temp[$art_code]))
    {
      foreach($tableau_temp as $id => $qte)
      {
        if($id == $art_code)
        {
          $tableau_temp[$id] = $qte + $art_qte;
          $nb_art = $nb_art + $qte + $art_qte;
        }
        else $nb_art = $nb_art + $qte;
      }
    }
    else
    {
      //L'article ne se trouve pas déjà dans le panier
      $tableau_temp[$art_code] = $art_qte;
      foreach($tableau_temp as $id => $qte) $nb_art = $nb_art + $art_qte;
    }
  }
  else
  {
    //Création de l'article dans un panier "neuf"
    $tableau_temp[$art_code] = $art_qte;
    $nb_art = $art_qte;
  }
  //Création du cookie contenant le tableau des articles créé juste avant
  setcookie('art_panier', serialize($tableau_temp), time()+7*24*3600);
  //Si la clé unique du cookie est déjà présente, on l'actualise, sinon on en créé une nouvelle
  if(isset($_COOKIE['key_panier'])) setcookie('key_panier', $_COOKIE['key_panier'], time()+7*24*3600);
  else
  {
    //Génération d'une clé unique pour le second cookie: key_panier
    //Et vérification de l'unicité de la clé avant de créer le cookie
    $caracteres = array("a", "b", "c", "d", "e", "f", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    do{
      shuffle($caracteres);
      $key_panier = "";
      for($n=0; $n <= 15; $n++)
      {
        $key_panier .= $caracteres[$n];
      }
      $requete_key = mysql_query("SELECT key_panier FROM key_panier WHERE key_panier = '".$key_panier."'");
      $nb_key = mysql_fetch_row($requete_key);
    }while($nb_key != 0);
    mysql_query("INSERT INTO key_panier (key_panier) VALUES ('".$key_panier."')");
    setcookie('key_panier', $key_panier, time()+7*24*3600);
  }
}
else
{
  //On calcule la quantité d'articles dans le cas ou rien n'est modifié
  if(isset($_COOKIE['art_panier']))
  {    
    $tableau_temp = unserialize($_COOKIE['art_panier']);
    foreach($tableau_temp as $id => $qte)
    {
      $nb_art = $nb_art + $qte;
    }
  }
  else $nb_art = 0;
}
//Inclusion du header (meta tags, title, links ...)
include('includes/header.php');
 ?>
<body>
<!-- Message d'avertissement du caractère factice du site
<div id="avertissement">Attention, ce site est un site factice<br />créé par des étudiants.</div>-->
<div id="container">
<header>
  <!-- Inclusion du menu vert affichant le lien vers l'accueil, le panier et le lien vers la page de connexion-->
  <?php include("includes/menu_top.php"); ?>
  <a href="index.php">
  <div id="texte_logo">Ligue de Paintball de Lorraine
  </div></a>
  <div class="clear"></div>
</header>
  <div id="main">
<header>
<!--Menu général du site-->
  <div id="onglets_menu">
  <ul id="onglets_generaux">
    
    <?php
    $req_menu = mysql_query("SELECT * FROM menu");
     while($menu = mysql_fetch_array($req_menu)) { ?>
      <li class="onglet_general"><a class="lien_general <?php if ($_SERVER['REQUEST_URI'] == '/index.php?'.$menu['lien']) echo 'current';?>" href="index.php?<?php echo $menu['lien']; ?>"><?php echo $menu['nom']; ?></a><?php
      if ($menu['lien'] == 'cat') { ?>
        <div class="sous_menu_afficher">
        <ul class="sous_menu_onglets_liste">
          <?php $reponse = mysql_query('SELECT * FROM categories ORDER BY cat_nom');
            while($cat = mysql_fetch_array($reponse))
            {
              echo '<li class="sous_menu_onglet"><a href="index.php?cat='.$cat['cat_code'].'">'.utf8_encode($cat['cat_nom']).'</a></li>';
            } ?>
        </ul>
      </div><?php
      }
      ?></li><?php
    }
    ?>
    <li class="onglet_general"><a class="lien_general" href="index.php?page=equipes">&#201;quipes</a>
      <div class="sous_menu_afficher">
        <ul class="sous_menu_onglets_liste">
          <?php
          $reponse = mysql_query('SELECT * FROM equipes ORDER BY equipe_nom');
          while($donnees = mysql_fetch_array($reponse))
          {?>
            <li class="sous_menu_onglet"><a href="index.php?page=equipes&amp;equipe_id=<?php echo $donnees['equipe_id']; ?>"><?php echo utf8_encode($donnees['equipe_nom']); ?></a></li>
          <?php
          }?>
        </ul>
      </div>
    </li>
    <li class="onglet_general"><a class="lien_general" href="forum/index.php">Forum</a></li>
    <li class="onglet_general"><a class="lien_general" href="index.php?page=jeu">Le jeu</a></li>
    <li class="onglet_general"><a class="lien_general" href="index.php?page=infos">Quizz</a></li>
    <li class="onglet_general"><a class="lien_general" href="index.php?page=planning">Représentant</a>
      <div class="sous_menu_afficher">
        <ul class="sous_menu_onglets_liste">
          <li class="sous_menu_onglet"><a href="index.php?page=planning">Planning</a></li>
          <li class="sous_menu_onglet"><a href="index.php?page=budgets">Budgets</a></li>
        </ul>
      </div>
    </li>
  </ul>
  </div>
</header>
<!-- Corps de la page-->
  <div id="contenu">
<?php
while($reponse = mysql_fetch_array(mysql_query("CALL new_routine(2, @res);"))) {
  echo $reponse["@res"];
}

  if(isset($_GET['id']) && !empty($_GET['id']))
  {
    include("pages/art_details.php");
  }
  elseif(isset($_GET['cat']))
  {
    include("pages/art_all.php");
  }
  elseif(isset($_GET['page']) && !empty($_GET['page']))
  {
    include("pages/".$_GET['page'].".php");
  }
  else{
    include("pages/accueil.php");
  }
include("includes/footer.php");
?>
<!--Envoi de statistiques vers Google stats-->
<script>
  var _gaq=[['_setAccount','UA-10787732-5'],['_trackPageview']];
  (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
  g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
  s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<!-- Affiche l'instalation de google chrome frame pour internet explorer jusqu'à la version 8 a fin de rendre compatible le site-->
<!--[if lt IE 9 ]>
  <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
  <script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
<![endif]-->

</body>
</html>
