<?php
actualiser_session("Pas de bol, il faut un compte Admin... U MAD?");
if(isset($_SESSION['level']) && $_SESSION['level'] == 3)
{
?>
<h1 class="ribbon shadow"><span class="ribbon_shadow"></span>Petit Quizz</h1>
<script language="JavaScript">
<!-- Début du code JavaScript
var n=9;
function effacer(form)
{
  for (var i=1; i<n; i++) form[i].value="";
}
var res=new Array(n+1);
function score(form)
{
<?php $j = 1; ?>
  var somme=0;
  if (form[<?=$j?>].value==2)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==1)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==3)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==1)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==1)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==2)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==3)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==3)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  if (form[<?=$j?>].value==2)
  {
    somme++;
    res[<?=$j?>]="juste";
  }
  else res[<?=$j?>]="faux";
  <?php $j++; ?>
  return somme;
}
function verif(form)
{
  var i=1;
  while ((i<n) && (form[i].value!="")) 
    i++;
  return ((i==n) && (form[n].value!=""));
}
function corriger(form)
{
  var j=1;
  var somme=0;
  var ch = '<html><head><title>Quiz</title>  <?php
  require_once "CssCrush/CssCrush.php";
  $global_css = CssCrush::file("/ppe/css/style.css"); ?>
    <link rel="stylesheet" type="text/css" href="<?=$global_css?>" /></head><body><div id="corrige"><h2>Corrigé</h2>';
  somme=score(form);
  while (res[j] != null)
  {
    if(res[j] == "juste") res[j] = '<img src="images/icones/check.png" alt="juste" align="middle" />';
    else res[j] = '<img src="images/icones/cross.png" alt="faux" align="middle" />';
    ch=ch+'Question '+j+': '+res[j]+'<br/>';
    j++;
  }
  ch=ch+"<p>Votre score est de "+somme+"/"+n+"</p>";
  var note = 1+5*somme/n;
  note = note.toFixed(2);
  ch=ch+"<p>Votre note est "+note+"</p>";
  ch=ch+"</div></body></html>"
  return(ch)
} 
function ouvrir(form)
{
  var haut = 400;
  var larg = 220;
  var options = "toolbar=no,location=no,directories=no,status=no," + "menubar=no,scrollbars=yes,resizable=yes,copyhistory=yes," + "width=" + larg + ",height=" + haut;
  var maFenetre=window.open("","Quizz",options);
  maFenetre.document.open();
  var corps = corriger(form);
  maFenetre.document.write(corps);
  maFenetre.document.close();
}
function process(form)
{
  if (verif(form)) ouvrir(form);
  else alert("\nFormulaire incomplet");
}
// Fin du code JavaScript -->

</script>
<?php $i = 1; $p = 1;?>
<form name="test_form" id="quiz">
  <table cellspacing="2" cellpadding="1" border="1">
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>Quelle est la différence entre le Paintball et l'Airsoft?</p>
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">Aucune, seul le nom diffère.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">Les munitions et le type d'arme n'est pas le même.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">L'Airsoft est un style de Paintball qui se joue en lévitation.</label><br/><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>D'où vient le Paintball ?</p>
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">Le paintball était autrefois une méthode de marquage pour le bétail.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">Le paintball est inspiré par Call Of Duty : Modern Warfare.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">Le paintball vient du pôle nord, c'est une technique inuit afin de repérer les ours polaires.</label><br/><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>De quoi est composée de base une équipe de Paintball ?</p>
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">Un gardien du drapeau, un casque rouge (capitaine), les casques verts (défensifs), les casques bleus (offensifs).</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">Les ailiers défensifs, les quarterbacks, les runningbacks et les arrières défensifs.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">Les couvreurs, les avants, le milieu.</label><br/><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>Comment s'appelle le système de verouillage de la détente du lanceur ?</p>
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">Verrou de pontet.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">Verrou du ponay.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">Verrou du Lelou.</label><br/><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>Quelles sont les munitions utilisées dans le Paintball ?</p>
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">Des billes de colorant alimentaire faites de gélatine qui donnent la diarhée si on les ingère.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">Des billes dures de 6mmm de calibre et biodégradables.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">Des mini-ballons en latex remplis de peinture.</label><br/><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>Quel est le/les gaz communément utilisé(s) au Paintball ?</p>
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">De la nitro et du Gaz lacrymogène.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">De l'air comprimé et du CO2.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">Un gaz naturel à base d'extrait de putois.</label><br/><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>A quoi ressemble un terrain de Paintball?</p>
      </td>
      <td>
        <p><input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>"><img src="images/quizz/piscine.jpg" alt="proposition 1" align="middle" /></label></p><?php $p++; ?>
        <p><input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>"><img src="images/quizz/basket.jpg" alt="proposition 2" align="middle" /></label></p><?php $p++; ?>
        <p><input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>"><img src="images/quizz/paintplan.jpg" alt="proposition 3" align="middle" /></label></p><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>Kesskecé ?</p><img src="images/quizz/paintforest.jpg" alt="proposition 2" align="middle" />
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">Un camp de bûcherons.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">Une exploitation forestière.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">Un terrain de Paintball.</label><br/><?php $p++; ?>
      </td>
    </tr>
    <?php $i++; ?>
    <tr>
      <td>
        <span class="titre"><img src="images/icones/help.png" alt="?" /> Question <?=$i?></span><p>Kesskecé 2.0 ?</p><img src="images/quizz/mickey_mouse.jpg" alt="proposition 2" align="middle" />
      </td>
      <td>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=1" /><label for="prop<?=$p?>">Un tetris géant.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=2" /><label for="prop<?=$p?>">Un terrain de jeu Mickey Mouse.</label><br/><?php $p++; ?>
        <input type="radio" id="prop<?=$p?>" value="" name="form<?=$i?>" onclick="this.form[<?=$i?>].value=3" /><label for="prop<?=$p?>">Un terrain de Paintball.</label><br/><?php $p++; ?>
      </td>
    </tr>
  </table>
  <p>
    <input type="button" name="corriger" value="Corriger" onclick="process(this.form)" />
    <input type="button" name="tout_effacer" value="Remettre à zéro" onclick="reset(this.form); effacer(this.form);" />
  </p>
</form>
<?php }else echo "Pas de bol, il faut un compte Admin... U MAD?"; ?>