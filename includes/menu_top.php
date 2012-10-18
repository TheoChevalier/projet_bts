    <div id="menu_top">
      <a href="index.php"><div class="item"><img src="images/icones/home.png" alt="" />Accueil</div></a>
      <a href="index.php?page=<?php if(isset($_SESSION['rep_name'])) echo "commander"; else echo "panier"; ?>">
      <div class="item"><img src="images/icones/panier.png" alt="" />
      <?php if(isset($_SESSION['rep_name'])) echo "Panier client"; else echo "Mon panier"; ?> (<?php echo $nb_art; ?>)</div></a><?php
      if(isset($_SESSION['user_name'])) {
      echo'<a href="index.php?page=profil&amp;user_id='.htmlspecialchars($_SESSION['user_id'], ENT_QUOTES).'"><div class="item"><img src="images/icones/compte.png" alt="" />'.htmlspecialchars($_SESSION['user_name'], ENT_QUOTES).'</div></a>';
      }elseif(isset($_SESSION['rep_id'])){ ?>
      <a href="connexion_rep.php?deco"><div class="item"><img src="images/icones/connexion.png" alt="" />Déconnexion</div></a>
      <?php }
      else{ ?><a href="connexion.php"><div class="item"><img src="images/icones/connexion.png" alt="" />Connexion</div></a><?php } ?>
      <div class="clear"></div>
    </div>
    <div id="ajout">
      <div id="ajout_fleche"></div>
      <div id="ajout_texte">
      <a href="#close"><div id="ajout_close"></div></a>
      L'article a bien été ajouté à votre panier.
      </div>
    </div>
    