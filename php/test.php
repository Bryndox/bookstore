<?php
require_once "classNearestNeighbour.php";
require_once "classRecommender.php";

/*
$Neigh=new nearestNeighbour();
$Neigh->getNearestNeighbours(1788036);
*/

$reco=new recommender();
$reco->getRecommendations(1990901);

?>