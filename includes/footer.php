</div>
  </div>
    <footer>
        <ul>
        <li class="footer_titre">NOS PRODUITS</li>
        <?php
          $reponse = mysql_query('SELECT * FROM categories ORDER BY cat_nom');
          for($i=0; $i < 8; $i++)
          {
            $cat = mysql_fetch_array($reponse); ?>
            <li><a href="index.php?cat=<?php echo $cat['cat_code'].'">'.utf8_encode($cat['cat_nom']); ?></a></li>
            <?php
          }
        ?>
          <li><a href="index.php?cat">Tous nos produits</a></li>
        </ul>
        <ul>
        <li class="footer_titre">NOS &#201;QUIPES</li>
        <?php
          $reponse = mysql_query('SELECT * FROM equipes ORDER BY equipe_nom');
          for($i=0; $i < 8; $i++)
          {
          $donnees = mysql_fetch_array($reponse);
          ?>
            <li><a href="index.php?page=equipes&amp;equipe_id=<?php echo $donnees['equipe_id']; ?>"><?php echo utf8_encode($donnees['equipe_nom']); ?></a></li>
          <?php
          }?>
          <li><a href="index.php?page=equipes">Toutes nos équipes</a></li>
        </ul>
        <ul>
          <li class="footer_titre">&Agrave; PROPOS DE NOUS</li>
          <li><a href="connexion.php?contact">Nous contacter</a></li>
          <li><a href="connexion.php?aide">Aide à la connexion</a></li>
          <li><a href="http://www.facebook.com" target="_blank"><img src="images/icones/facebook.gif" alt="" /> Notre page Facebook</a></li>
          <br /><br />
          <li><a href="http://www.twitter.com" target="_blank"><img src="images/icones/twitter.gif" alt="" /> Nous suivre sur Twitter</a></li>
        </ul>
        <ul>
          <li class="footer_titre">MENTIONS LÉGALES</li>
          <li><a href="index.php?page=cgv">Cond. Gen. de Vente</a></li>
          <li><a href="index.php?page=cgu">Cond. Gen. d'Utilisation</a></li>
          <li><a href="index.php?page=mentions_legales">Mentions Légales</a></li>
          <p><img src="images/icones/visa_mc.png" alt="VISA / Master Card" /></p>
          <p><a href="http://www.lorraine.eu" alt="" target="_blank"><img src="images/icones/lorraine.png" alt="La région Lorraine" /></a></p>  
        </ul>
     </footer>
       <div class="copyright">Ligue de paintball de Lorraine &copy; 2011. Designed by <a href="http://www.theochevalier.fr" target="_blank">Théo Chevalier</a>.</div>
   
  </div> <!--! end of #container -->
<?php if(isset($_GET['epuise'])) echo'<script>alert("Nous sommes désolés, la quantité disponible pour cet article est inférieure à la quantité que vous demandez.");</script>'; ?>
