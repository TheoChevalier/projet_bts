<?php
require_once('streamer.php');  
$ft = new File_Streamer();  
$ft->setDestination('../images/upload/');  
$ft->receive();  