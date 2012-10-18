<section class="contenu_central">
<!--Diaporama CSS3 des informations importantes -->
  <article id="diaporama">
    <div id="wrapper">
      <div id="wrap1">
        <div id="wrap2">
          <div id="wrap3">
            <div id="wrap4">
              <div id="background">
                <ul>
                  <li><a href="#wrap1"><span>#1</span></a></li>
                  <li><a href="#wrap2"><span>#2</span></a></li>
                  <li><a href="#wrap3"><span>#3</span></a></li>
                  <li><a href="#wrap4"><span>#4</span></a></li>
                </ul>
                <div id="fleche"></div>
                <div id="textes">
                  <a href="index.php?page=calendrier"><p><strong>Compétition</strong>Consultez les classements, tenez-vous au courant des prochains tournois !</p></a>
                  <a href="index.php?page=equipes"><p><strong>Équipes</strong>Découvrez la composition des équipes licenciées</p></a>
                  <a href="index.php?cat=1"><p><strong>Promotions</strong>Les billes sont en promotion ce mois-ci: profitez-en !</p></a>
                  <a href="index.php?cat=4"><p><strong>Marqueurs</strong>Découvrez vite nos plus beaux marqueurs!</p></a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
      
    </div>
  </article>
  <div class="clear"></div>
  <article class="accueil_texte">
    <!--<h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Bienvenue !</h1>-->
    <h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Bienvenue !</h1>
    <p><b>Ce site est un projet d'étudiants en BTS SIO à Carcassonne et n'est en aucun cas un réel site de ligue de paintball.
    <br />Nous ne vendons strictement rien, et les informations présentes sur ce site sont entièrement fausses, merci de votre compréhension.</b>
    </p>
    <h4>TODO LIST</h4>
    <p>&#8226; <img src="images/icones/check.png" alt="" /> Ajouter l'enregistrement (optionnel?) de la date de naissance à l'inscription -> Ajouter les modifications nécessaires au profil, à la modification de profil.</p>
    <p>&#8226; <img src="images/icones/check.png" alt="" /> Terminer l'implémentation de la signature numérique du panier. (Bug lors de l'enregistrement BDD - Etapes encore floues)</p>
    <p>&#8226; <img src="images/icones/check.png" alt="" />Implémenter la fonction de recherche dans les articles.</p>
    <p>&#8226; Améliorer la présentation des news. (+ meilleur agencement de la page d'accueil?)</p>
    <p>&#8226; <img src="images/icones/check.png" alt="" />Améliorer la réécriture de l'url lors du choix des options de classement des articles.</p>
    <p>&#8226; Implémenter le calendrier !</p>
    <p>&#8226; Terminer le drag'n'drop HTML5 des images.</p>
    <p>&#8226; Implémenter le forum !</p>
    <p>&#8226; <img src="images/icones/check.png" alt="" />Appliquer le style des boutons du panier, et des boutons-liens</p>
    <p>&#8226; <img src="images/icones/check.png" alt="" />Améliorer la lisibilité des liens du footer</p>
    <p>&#8226; Créer un formulaire pour créer une équipe et y ajouter des joueurs (adresses mail) possédant déjà un compte (lien automatique) ou non (envoi d'invitation d'inscription)</p>
    <p>&#8226; <img src="images/icones/check.png" alt="" />Permettre de supprimer un compte (activé ou non)</p>
    <p>&#8226; <img src="images/icones/check.png" alt="" />Créer formulaire de réinitialisation de mot de passe, et de renvoi de mail d'activation. (Proposer la suppression de compte pour repartir "à neuf")</p>
    <p>&#8226; Envoyer un fichier .pdf en pièce jointe au mail de confirmation de la commande, qui contient la facture, avec tous les produits.</p>
    <h4>Fonctions déjà implémentées:</h4>
    <p>&#8226; La création de comptes utilisateurs, (vérification des données, enregistrement bdd, envoi de mail pour activation, activation)</p>
    <p>&#8226; La connexion au compte utilisateur (vérification pseudo/mdp, envoi de $_SESSION) et la déconnexion.</p>
    <p>&#8226; L'affichage de produits d'une catégorie, avec cookie mémorisant le nombre d'articles affichés par page (affichage dynamique en pages), récupération des données produits dans la bdd, tri par prix, catégories.</p>
    <p>&#8226; Gestion des équipes (affichage des équipes, puis du profil de l'équipe avec la liste des joueurs)</p>
    <p>&#8226; Envoi de message via un formulaire à un joueur d'une équipe. (En étant connecté à un compte)</p>
    <p>&#8226; Affichage des résultats sur la page d'accueil, avec mise en évidence du vainqueur et du perdant, plus affichage des scores.</p>
    <p>&#8226; Affichage des news: apperçu à l'accueil, présentation sur plusieurs pages sur la page des news.</p>
    <p>&#8226; Présentation sous forme de slides CSS3 des informations importantes (Promos, résultats...) à l'accueil.</p>
    <p>&#8226; Page d'erreur 404 originale.</p>
    <p>&#8226; Ajout de produits, avec choix de quantité dans le panier (cookie id/nb produits), vérification de la quantité commandée en stock, signature numérique du panier pour éviter les ajouts multiples.</p>
    <p>&#8226; Fonctions vider panier, et valider le panier (Vérification des données de livraison, paiement en ligne non-sécurisé pour cette version factice.).</p>
    <p>&#8226; Page de contact de la LPL via un formulaire.</p>
  </article>
</section>
<aside class="menu_droite">
  <!--Dernières news, récupérées dans la BDD-->
  <div class="news_container">
  <?php
    //Récupération des 4 dernières news
    $requete_news = mysql_query("SELECT * FROM news ORDER BY news_date DESC LIMIT 4") or die("Impossible d'afficher les news.");
    //Passage du format date/horaire en Français
    setlocale(LC_TIME, 'fr_FR');
    //Affichage des news
    while($news = mysql_fetch_array($requete_news))
    { ?>
    <div class="news_hover"><a href="<?php echo 'index.php?page=news&amp;news_id='.$news['news_id']; ?>">
    <div class="news"><h2 class="news_titre"><?php echo utf8_encode($news['news_titre']); ?></h2>
      <div class="news_text">
      <?php echo utf8_encode(substr($news['news_desc'], 0, 100)); if(substr($news['news_desc'], 0, 100) != $news['news_desc']) echo '(...)<br /><span class="afficher_suite">Afficher la suite</span>'; ?>
      </div>
      <div class="news_date"><?php
      //Extraction et mise en forme de la date à partir de JJ-MM-AAAA vers JJ Mois Année
      $date_annee = substr($news['news_date'], 0, 4);
      $date_mois = substr($news['news_date'], 8, 2);
      $date_jour = substr($news['news_date'], 5, 2);
      $timestamp = mktime( 0, 0, 0, $date_jour , $date_mois , $date_annee);
      $date = strftime( "%d %B %Y" , $timestamp);
      echo 'Posté le '.$date; ?>.</div>
    </div></a></div>

    <?php } ?>
  </div>
  <div class="n_news_pages">
  <a href="index.php?page=news"><div class="page_hover">Voir toutes les news</div></a>
  </div>
  <div class="resultats_container">
    <h2>Résultats des matchs</h2>
    <?php
    $requete_matchs = mysql_query("SELECT * FROM resultats ORDER BY resultats_date DESC LIMIT 5") or die("Impossible d'afficher les résultats.");
    //Passage du format date/horaire en Français
    setlocale(LC_TIME, 'fr_FR');
    //Affichage des résultats des matchs
    while($matchs = mysql_fetch_array($requete_matchs))
    { 
    $requete_equipe_1 = mysql_query("SELECT  equipe_id, equipe_nom FROM equipes WHERE equipe_id ='".$matchs['resultats_equipe_1']."'") or die("Impossible d'afficher le nom.");
    $requete_equipe_2 = mysql_query("SELECT  equipe_id, equipe_nom FROM equipes WHERE equipe_id ='".$matchs['resultats_equipe_2']."'") or die("Impossible d'afficher le nom.");
    $equipe_1 = mysql_fetch_array($requete_equipe_1);
    $equipe_2 = mysql_fetch_array($requete_equipe_2);
    ?>
    

    <h2 class="resultats_titre"><?php echo utf8_encode($equipe_1['equipe_nom']); ?><br /><span class="resultats_vs">Vs.</span><br /><?php echo utf8_encode($equipe_2['equipe_nom']); ?></h2>
      <div class="resultats_texte">
        <?php if($matchs['resultats_morts_1'] < $matchs['resultats_morts_2']){ 
        ?><a href="index.php?page=equipes&amp;equipe_id=<?php echo $equipe_1['equipe_id']; ?>">
        <div class="resultats_gagnant">
          <?php echo utf8_encode($equipe_1['equipe_nom']); ?><div class="resultats_morts">(<?php echo $matchs['resultats_morts_1']; ?> <img src="images/icones/mort.gif" alt="" />)</div>
        </div>
        </a>
        <a href="index.php?page=equipes&amp;equipe_id=<?php echo $equipe_2['equipe_id']; ?>">
        <div class="resultats_perdant">
          <?php echo utf8_encode($equipe_2['equipe_nom']); ?><div class="resultats_morts">(<?php echo $matchs['resultats_morts_2']; ?> <img src="images/icones/mort.gif" alt="" />)</div>
        </div>
        </a>
        <?php }elseif($matchs['resultats_morts_1'] > $matchs['resultats_morts_2']){
        ?><a href="index.php?page=equipes&amp;equipe_id=<?php echo $equipe_1['equipe_id']; ?>">
        <div class="resultats_perdant">
          <?php echo utf8_encode($equipe_1['equipe_nom']); ?><div class="resultats_morts">(<?php echo $matchs['resultats_morts_1']; ?> <img src="images/icones/mort.gif" alt="" />)</div>
        </div>
        </a>
        <a href="index.php?page=equipes&amp;equipe_id=<?php echo $equipe_2['equipe_id']; ?>">
        <div class="resultats_gagnant">
          <?php echo utf8_encode($equipe_2['equipe_nom']); ?><div class="resultats_morts">(<?php echo $matchs['resultats_morts_2']; ?> <img src="images/icones/mort.gif" alt="" />)</div>
        </div>
        </a>
        <?php }else{
        ?><a href="index.php?page=equipes&amp;equipe_id=<?php echo $equipe_1['equipe_id']; ?>">
        <div class="resultats_egalite">
          <?php echo utf8_encode($equipe_1['equipe_nom']); ?><div class="resultats_morts">(<?php echo $matchs['resultats_morts_1']; ?> <img src="images/icones/mort.gif" alt="" />)</div>
        </div>
        </a>
        <a href="index.php?page=equipes&amp;equipe_id=<?php echo $equipe_1['equipe_id']; ?>">
        <div class="resultats_egalite">
          <?php echo utf8_encode($equipe_2['equipe_nom']); ?><div class="resultats_morts">(<?php echo $matchs['resultats_morts_2']; ?> <img src="images/icones/mort.gif" alt="" />)</div>
        </div>
        </a>
        <?php } 
        ?><div class="news_date"><?php
        //Extraction et mise en forme de la date à partir de JJ-MM-AAAA vers JJ Mois Année
        $date_annee = substr($matchs['resultats_date'], 0, 4);
        $date_mois = substr($matchs['resultats_date'], 8, 2);
        $date_jour = substr($matchs['resultats_date'], 5, 2);
        $timestamp = mktime( 0, 0, 0, $date_jour , $date_mois , $date_annee);
        $date = strftime( "%d %B %Y" , $timestamp);
        echo 'Match du '.$date; ?>.</div>
      </div>
    <?php } ?>
  </div>
</aside>
<div class="clear"></div>