<?php
/*
 * 	Manhali - Free Learning Management System
 *	articles_inc.php
 *	2011-11-26 12:51
 * 	Author: El Haddioui Ismail <ismail.elhaddioui@gmail.com>
 * 	Copyright (C) 2009-2014  El Haddioui Ismail. All rights reserved
 * 	License : GNU/GPL v3

This file is part of Manhali

Manhali is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Manhali is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Manhali.  If not, see <http://www.gnu.org/licenses/>.

*/

defined("access_const") or die( 'Restricted access' );
 $article = intval($_GET['article']);
 echo "<script>console.log('article');</script>";
 
 if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
 		$selectarticle = $connect->query("select * from `" . $tblprefix . "articles` where id_article = $article;");
 else
 		$selectarticle = $connect->query("select * from `" . $tblprefix . "articles` where id_article = $article and publie_article = '1';");

 if (mysqli_num_rows($selectarticle) == 1) {
		
			// acces article
	$acces = mysqli_result($selectarticle,0,12);
	$acces_valide = 0;
	if ($acces == "*")
		$acces_valide = 1;
	else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
		$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
		$select_classe = $connect->query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
    if (mysqli_num_rows($select_classe) == 1){
			$id_classe = mysqli_result($select_classe,0);
			$tab_classes = explode("-",trim($acces,"-"));
			if (in_array($id_classe,$tab_classes))
				$acces_valide = 1;
		}
	}

		$id_user = mysqli_result($selectarticle,0,1);
		$titre_article = mysqli_result($selectarticle,0,3);
		$titre_article = html_ent($titre_article);

	if ($acces_valide == 1){

		$contenu_article = mysqli_result($selectarticle,0,4);

		$date_creation_article = mysqli_result($selectarticle,0,10);
		$date_modification_article = mysqli_result($selectarticle,0,11);
		
		$date_creation_article = set_date($dateformat,$date_creation_article);
		$date_modification_article = set_date($dateformat,$date_modification_article);
		
		$selectauteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $id_user;");
		if (mysqli_num_rows($selectauteur) == 1) {
			$auteur = html_ent(mysqli_result($selectauteur,0));
		} else $auteur = inconnu;

		echo "<div id=\"titre_article\">".$titre_article."</div>";
		
		if ($afficher_profil == 1)
			echo "\n<div id=\"write_by\">".write_by." <a href=\"?profiles=".$id_user."\" title=\"".user_profile."\">".$auteur."</a>, ";
		else
			echo "\n<div id=\"write_by\">".write_by." ".$auteur.", ";

		echo $date_creation_article." | ".modifie." ".$date_modification_article;
		echo "</div><br />";

		echo $contenu_article."<br />";
		
		// ********* commentaires **********
		$type_objet = "a";
		$id_objet = $article;
		$path_objet = "article";
		include_once ("includes/comments.php");

	} else echo "\n<div id=\"titre_article\">".$titre_article."</a></div><font color=\"red\"><b>".no_access_permission."</b></font>";
 } else accueil();
 
?>