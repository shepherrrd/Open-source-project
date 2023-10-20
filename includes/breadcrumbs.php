<?php
/*
 * 	Manhali - Free Learning Management System
 *	breadcrumbs.php
 *	2009-04-08 13:02
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

$select_statut_breadcrumps = $connect->query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'breadcrumbs';");
if (mysqli_num_rows($select_statut_breadcrumps) == 1) {
 $statut_breadcrumps = $select_statut_breadcrumps->fetch_row();
 if ($statut_breadcrumps == 1) {
	
	if (isset($_GET['menu']) && ctype_digit($_GET['menu'])){
		$menu = intval($_GET['menu']);
		$selectmenu = $connect->query("select titre_hormenu from `" . $tblprefix . "hormenu` where id_hormenu = $menu and active_hormenu = '1';");
		if (mysqli_num_rows($selectmenu) == 1)
			$titre_menu = $selectmenu->fetch_row()[0];
		else $titre_menu = "inconnu";
		
		$titre_menu = html_ent($titre_menu);
		$titre_menu = readmore($titre_menu,90);
		
		echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
		echo $titre_menu;
	}
	else if (isset($_GET['vermenu']) && ctype_digit($_GET['vermenu'])){
		$vermenu = intval($_GET['vermenu']);
		$selectvermenu = $connect->query("select titre_vermenu from `" . $tblprefix . "vermenu` where id_vermenu = $vermenu and active_vermenu = '1';");
		if (mysqli_num_rows($selectvermenu) == 1)
			$titre_vermenu = $selectvermenu->fetch_row()[0];
		else $titre_vermenu = "inconnu";
		
		$titre_vermenu = html_ent($titre_vermenu);
		$titre_vermenu = readmore($titre_vermenu,90);
		
		echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
		echo $titre_vermenu;
	}
	else if (isset($_GET['tutorial']) && ctype_digit($_GET['tutorial'])) {
		$tuto = intval($_GET['tutorial']);
		$selecttuto = $connect->query("select titre_tutoriel from `" . $tblprefix . "tutoriels` where id_tutoriel = $tuto and publie_tutoriel = '2';");
		if (mysqli_num_rows($selecttuto) == 1)
			$titre_tuto = $selecttuto->fetch_row()[0];
		else $titre_tuto = "inconnu";
		
		$titre_tuto = html_ent($titre_tuto);
		$titre_tuto = readmore($titre_tuto,90);
		
		echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
		echo $titre_tuto;
	}
	else if (isset($_GET['chapter']) && ctype_digit($_GET['chapter'])) {
		$chap = intval($_GET['chapter']);
		$selecttutochap = $connect->query("select `" . $tblprefix . "tutoriels`.id_tutoriel, titre_tutoriel, `" . $tblprefix . "parties`.id_partie, titre_partie, titre_chapitre from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where id_chapitre = $chap and `" . $tblprefix . "chapitres`.id_partie = `" . $tblprefix . "parties`.id_partie and `" . $tblprefix . "parties`.id_tutoriel = `" . $tblprefix . "tutoriels`.id_tutoriel and publie_chapitre = '1';");
		if (mysqli_num_rows($selecttutochap) == 1) {
			$id_tuto = $selecttutochap->fetch_row()[0];
			$titre_tuto = $selecttutochap->fetch_row()[1];
			$id_partie = $selecttutochap->fetch_row()[2];
			$titre_partie = $selecttutochap->fetch_row()[3];
			$titre_chap = $selecttutochap->fetch_row()[4];
		}
		else {
			$id_tuto = 0;
			$titre_tuto = "inconnu";
			$id_partie = 0;
			$titre_partie = "inconnu";
			$titre_chap = "inconnu";
		}
		$titre_tuto = html_ent($titre_tuto);
		$titre_tuto = readmore($titre_tuto,30);
		
		$titre_partie = html_ent($titre_partie);
		$titre_partie = readmore($titre_partie,30);
		
		$titre_chap = html_ent($titre_chap);
		$titre_chap = readmore($titre_chap,30);
		
		echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
		echo "<a class=\"breadcrumbs\" href=\"?tutorial=".$id_tuto."\">".$titre_tuto."</a> > ";
		echo "<a class=\"breadcrumbs\" href=\"?tutorial=".$id_tuto."#".$id_partie."\">".$titre_partie."</a> > ";
		echo $titre_chap;
	}
	else if (isset($_GET['search'])) {
			$select_titre_composant = $connect->query("select titre_composant from `" . $tblprefix . "composants` where nom_composant = 'search';");
			if (mysqli_num_rows($select_titre_composant) == 1)
				$titre_composant = $select_titre_composant->fetch_row()[0];
			else $titre_composant = "inconnu";
			$titre_composant = html_ent($titre_composant);
			$titre_composant = readmore($titre_composant,90);
			echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
			echo $titre_composant;
	}
	else if (isset($_GET['poll'])) {
			$select_titre_composant = $connect->query("select titre_composant from `" . $tblprefix . "composants` where nom_composant = 'poll';");
			if (mysqli_num_rows($select_titre_composant) == 1)
				$titre_composant = $select_titre_composant->fetch_row()[0];
			else $titre_composant = "inconnu";
			$titre_composant = html_ent($titre_composant);
			$titre_composant = readmore($titre_composant,90);
			echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
			echo $titre_composant;
	}
	else if (isset($_GET['register'])) {
			echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
			echo "registration";
	}
	else if (isset($_GET['reset_pass'])) {
			echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
			echo "title_regener";
	}
	else if (isset($_GET['profiles'])) {
			echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
			echo "user_profile";
	}
	else if (isset($_GET['s_profiles'])) {
		echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
		if (!empty($_GET['action']) && $_GET['action'] == "edit")
			echo "modifier_perso";
		else echo "user_profile";
	}
	else if (isset($_GET['s_messages'])) {
			echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
			echo "messagerie";
	}
	else if (isset($_GET['contact'])) {
			$select_titre_composant = $connect->query("select titre_composant from `" . $tblprefix . "composants` where nom_composant = 'contact';");
			if (mysqli_num_rows($select_titre_composant) == 1)
				$titre_composant = $select_titre_composant->fetch_row()[0];
			else $titre_composant = "inconnu";
			$titre_composant = html_ent($titre_composant);
			$titre_composant = readmore($titre_composant,90);
			echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
			echo $titre_composant;
	}
	else if (isset($_GET['article']) && ctype_digit($_GET['article'])) {
		$article = intval($_GET['article']);
		$selectarticle = $connect->query("select titre_article from `" . $tblprefix . "articles` where id_article = $article and publie_article = '1';");
		if (mysqli_num_rows($selectarticle) == 1)
			$titre_article = $selectarticle->fetch_row()[0];
		else $titre_article = "inconnu";
		
		$titre_article = html_ent($titre_article);
		$titre_article = readmore($titre_article,90);
		
		echo "<a class=\"breadcrumbs\" href=\"?\">"."home"."</a> > ";
		echo $titre_article;
	}
	else {
		echo "home";
	}
 }
}
?>