<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="fr"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="fr"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="fr"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="fr"> <!--<![endif]-->
<head>
  <title><?php if(isset($titre)) echo $titre.' | LPL'; else echo "LPL | Ligue de Paintball de Lorraine"; ?></title>
  <meta charset="utf-8">
  <meta name="author" content="Théo Chevalier, Solène Bitsch"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="description" content="Site de la ligue de paintball de Lorraine">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="image_src" href="images/logo_big.jpg" />
  <?php
  require_once 'CssCrush/CssCrush.php';
  $global_css = CssCrush::file('/ppe/css/style.css'); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $global_css; ?>" />
    <?php if(isset($_SESSION["rep_id"])) echo '<link rel="stylesheet" type="text/css" href="css/rep.css" />';
    elseif(isset($_SESSION['level']) && $_SESSION['level'] == 3)  echo '<link rel="stylesheet" type="text/css" href="css/admin.css" />';
    ?>
  <!--Instruction conditionnelle pour charger les hacks CSS seulement pour IE 8 ou inférieur-->
  <!--[if lte IE 8]><link rel="stylesheet" href="css/iestyle.css" /><![endif]-->
  <script src="js/libs/modernizr-2.0.6.min.js"></script>
</head>