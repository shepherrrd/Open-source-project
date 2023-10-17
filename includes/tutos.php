<?php
/*
 * 	Manhali - Free Learning Management System
 *	tutos.php
 *	2011-01-27 12:36
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

$select_statut_comp = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'courses';");
if (mysql_num_rows($select_statut_comp) == 1) {
 	$statut_comp = mysql_result($select_statut_comp,0);
	if ($statut_comp == 1) {
 						
$tuto = intval($_GET['tutorial']);

if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
	$selecttuto = mysql_query("select * from `" . $tblprefix . "tutoriels` where id_tutoriel = $tuto;");
else
	$selecttuto = mysql_query("select * from `" . $tblprefix . "tutoriels` where id_tutoriel = $tuto and publie_tutoriel = '2';");

if (mysql_num_rows($selecttuto) == 1) {
	
	$tutoriel = mysql_fetch_row($selecttuto);

	$id_tutoriel = $tutoriel[0];
	$id_user = $tutoriel[1];

	$titre_tutoriel = html_ent($tutoriel[2]);
	$objectifs_tutoriel = trim($tutoriel[3]);
	$introduction_tutoriel = trim($tutoriel[4]);
	$conclusion_tutoriel = trim($tutoriel[5]);

	$licence_tutoriel = $tutoriel[6];
	$date_creation_tutoriel = $tutoriel[10];
	$date_modification_tutoriel = $tutoriel[11];

	// acces tutoriel
	$acces = $tutoriel[13];
	$acces_valide = 0;
	if ($acces == "*")
		$acces_valide = 1;
	else if ($acces == "0" && isset($_SESSION['log']) && !empty($_SESSION['log']))
		$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
			$acces_valide = 1;
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
		$select_classe = mysql_query("select id_classe from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
    if (mysql_num_rows($select_classe) == 1){
			$id_classe = mysql_result($select_classe,0);
			$tab_classes = explode("-",trim($acces,"-"));
			if (in_array($id_classe,$tab_classes))
				$acces_valide = 1;
		}
	}
	if ($acces_valide == 1){

// select auteur name and email
	$selectauteur = mysql_query("select identifiant_user, email_user, photo_profil from `" . $tblprefix . "users` where id_user = $id_user;");
	if (mysql_num_rows($selectauteur) == 1) {
		
		$auteur = mysql_result($selectauteur,0,0);
		if (!empty($auteur))
			$auteur = html_ent($auteur);
		else $auteur = inconnu;
		
		$mail_auteur = mysql_result($selectauteur,0,1);
		if (!empty($mail_auteur)){
			$mail_auteur = html_ent($mail_auteur);
			$mail_auteur = mail_antispam($mail_auteur,0);
		} else $mail_auteur = "";

		$photo_profil = mysql_result($selectauteur,0,2);
	} else {
		$auteur = inconnu;
		$mail_auteur = "";
		$photo_profil = "";
	}

// select nombre de lectures
	
	$select_nbr_parties = mysql_query("select sum(nombre_lectures) from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_tutoriel = $id_tutoriel;");
	$nombre_lectures = mysql_result($select_nbr_parties,0);
	if (empty($nombre_lectures))
		$nombre_lectures = 0;

// reponses qcm
	$select_reponses_qcm = mysql_query("select sum(total_essais), sum(total_reponses_correctes) from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres`, `" . $tblprefix . "qcm` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and `" . $tblprefix . "chapitres`.id_chapitre = `" . $tblprefix . "qcm`.id_chapitre and id_tutoriel = $id_tutoriel;");
	$total_essais = mysql_result($select_reponses_qcm,0,0);
	$total_reponses_correctes = mysql_result($select_reponses_qcm,0,1);

// ********* rating
	$nombre_votes_tutoriel = $tutoriel[14];
	$rating_tutoriel = $tutoriel[15];
	if ($nombre_votes_tutoriel > 0 && $rating_tutoriel > 0 && ctype_digit($nombre_votes_tutoriel) && ctype_digit($rating_tutoriel))
		$rating_tuto = round($rating_tutoriel / $nombre_votes_tutoriel);
	else $rating_tuto = 0;
	
	if ($rating_tuto < 0) $rating_tuto = 0;
	else if ($rating_tuto > 6) $rating_tuto = 6;

	$can_rate = 0;
if (isset($_SESSION['log']) && !empty($_SESSION['log'])){
	if ($_SESSION['log'] == 1){
		$select_tutos_vote = mysql_query("select tutos_vote from `" . $tblprefix . "users` where id_user = $id_user_session;");
		if (mysql_num_rows($select_tutos_vote) == 1){
			$tutos_vote = mysql_result($select_tutos_vote,0);
			$tab_tutos_vote = explode("-",$tutos_vote);
			if (!in_array($id_tutoriel,$tab_tutos_vote) && $id_user != $id_user_session){
				$can_rate = 1;
				if (!empty($_GET['rating']) && ctype_digit($_GET['rating']) && $_GET['rating'] > 0 && $_GET['rating'] < 7){
					$new_nombre_votes_tutoriel = $nombre_votes_tutoriel+1;
					$new_rating_tutoriel = $rating_tutoriel + $_GET['rating'];
					$update_rating = mysql_query("update `" . $tblprefix . "tutoriels` set nombre_votes_tutoriel = $new_nombre_votes_tutoriel, rating_tutoriel = $new_rating_tutoriel where id_tutoriel = $id_tutoriel;");
					$tutos_vote .= $id_tutoriel."-";
					$update_tutos_vote = mysql_query("update `" . $tblprefix . "users` set tutos_vote = '$tutos_vote' where id_user = $id_user_session;");
					echo "<script type=\"text/javascript\">window.location.href = \"?tutorial=".$id_tutoriel."\";</script>";
				}
			}
		}
	}
	else if ($_SESSION['log'] == 2){
		$select_tutos_vote = mysql_query("select tutos_vote from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
		if (mysql_num_rows($select_tutos_vote) == 1){
			$tutos_vote = mysql_result($select_tutos_vote,0);
			$tab_tutos_vote = explode("-",$tutos_vote);
			if (!in_array($id_tutoriel,$tab_tutos_vote)){
				$can_rate = 1;
				if (!empty($_GET['rating']) && ctype_digit($_GET['rating']) && $_GET['rating'] > 0 && $_GET['rating'] < 7){
					$new_nombre_votes_tutoriel = $nombre_votes_tutoriel+1;
					$new_rating_tutoriel = $rating_tutoriel + $_GET['rating'];
					$update_rating = mysql_query("update `" . $tblprefix . "tutoriels` set nombre_votes_tutoriel = $new_nombre_votes_tutoriel, rating_tutoriel = $new_rating_tutoriel where id_tutoriel = $id_tutoriel;");
					$tutos_vote .= $id_tutoriel."-";
					$update_tutos_vote = mysql_query("update `" . $tblprefix . "apprenants` set tutos_vote = '$tutos_vote' where id_apprenant = $id_user_session;");
					echo "<script type=\"text/javascript\">window.location.href = \"?tutorial=".$id_tutoriel."\";</script>";
				}
			}
		}
	}
}

// affichage		
	echo "<table width=\"100%\" align=\"center\" cellpadding=\"5\" cellspacing=\"5\"><tr><td align=\"center\" width=\"100%\">";

	echo "<div id=\"titre\">".$titre_tutoriel."</div>\n";	
	
	echo "</td></tr><tr><td align=\"center\" width=\"100%\">";

// affichage infos

echo "\n<table border=\"0\" align=\"center\" width=\"100%\" class=\"infos\" cellpadding=\"0\" cellspacing=\"0\"><ul>";
echo "\n<tr>";
echo "\n<td colspan=\"3\" align=\"center\"><div id=\"tuto_infos\">".tuto_infos."</div></td>";
echo "\n</tr>";
echo "\n<tr><td colspan=\"3\" align=\"center\">&nbsp;</td></tr>";
echo "\n<tr>";
echo "\n<td rowspan=\"4\" width=\"20%\" align=\"center\">";
	if (!empty($photo_profil))
		echo "<img border=\"0\" src=\"docs/".$photo_profil."\" alt=\"".$auteur."\" width=\"100\" height=\"100\" />";
echo "</td>";
echo "\n<td width=\"30%\" align=\"left\">";
	if ($afficher_profil == 1)
		echo "<li><b>".auteur." <a href=\"?profiles=".$id_user."\" title=\"".user_profile."\">".$auteur."</a></b></li>";
	else
		echo "<li><b>".auteur." ".$auteur."</b></li>";
echo "</td>";
echo "\n<td width=\"50%\" align=\"left\"><li><b>".cree." ".set_date($dateformat,$date_creation_tutoriel)."</b></li></td>";
echo "\n</tr>";
echo "\n<tr>";
echo "\n<td width=\"30%\" align=\"left\"><li><b>".email." : ".$mail_auteur."</b></li></td>";
echo "\n<td width=\"50%\" align=\"left\"><li><b>".modifie." ".set_date($dateformat,$date_modification_tutoriel)."</b></li></td>";
echo "\n</tr>";
echo "\n<tr>";
echo "\n<td width=\"30%\" align=\"left\"><li><b>".lu." ".$nombre_lectures." ".fois."</b></li></td>";
echo "\n<td width=\"50%\" align=\"left\">";
	if ($total_essais != 0 && $total_reponses_correctes <= $total_essais){
		$reponses_tuto = round(100 * $total_reponses_correctes / $total_essais,2);
		echo "<li><b>".reponses_qcm." ".$reponses_tuto." %</b></li>\n";
	}
echo "</td>";
echo "\n</tr>";
echo "\n<tr>";
echo "\n<td width=\"30%\" align=\"left\"><li><b>".licence." </b><a target=\"_blank\" title=\"".licence_const($licence_tutoriel)."\" href=\"http://creativecommons.org/licenses/".$licence_tutoriel."/3.0/\"><img src=\"images/licenses/".$licence_tutoriel.".png\" border=\"0\" alt=\"".licence_const($licence_tutoriel)."\" /></a></li></td>";
echo "\n<td width=\"50%\" align=\"left\">";
	// ********* rating affichage
	if ($can_rate == 1){
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"styles/rating.js\"></script>";
		echo "<li><div onmouseout=\"old_rating(this, event, ".$rating_tuto.");\" onmouseover=\"init_stars();\"><b>".rating." : </b>";
	}
	else
		echo "<li><div><b>".rating." : </b>";
	for($i=1;$i<=6;$i++){
		if ($i <= $rating_tuto)
			$img_rating = "star_over.gif";
		else
			$img_rating = "star_out.gif";
		echo "<img border=\"0\" id=\"Star".$i."\" src=\"images/others/".$img_rating."\" alt=\"".$i."/6\" title=\"".$i."/6\" width=\"25\" height=\"23\" />";
	}
	echo " <b>(".$nombre_votes_tutoriel." ".votes." : ".$rating_tuto."/6)</b></div></li>\n";
	if ($can_rate == 1)
		echo "<script type=\"text/javascript\">NotationSystem('?tutorial=".$id_tutoriel."');</script>";
	//*********
echo "</td>";
echo "\n</tr>";
echo "\n<tr><td colspan=\"3\" align=\"center\">&nbsp;</td></tr>";
echo "\n</ul></table>";

	echo "</td></tr><tr><td width=\"100%\">";

//objectifs et introduction tuto
	if (!empty($objectifs_tutoriel)) {
		echo "<table width=\"100%\" border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><div id=\"objectifs\">".objectifs_tuto."</div>";
		echo "<div id=\"normal\">".$objectifs_tutoriel."</div></td></tr></table>\n";
	}
	if (!empty($introduction_tutoriel)) {
		echo "<div id=\"normal\">".$introduction_tutoriel."</div>\n";
	}
	
	echo "<hr />";	
	echo "</td></tr><tr><td width=\"100%\">";
	
// parties
	if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
		$selectparties = mysql_query("select * from `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel order by ordre_partie;");
	else
		$selectparties = mysql_query("select * from `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and publie_partie = '1' order by ordre_partie;");

	if (mysql_num_rows($selectparties)> 0) {
		echo "<ol type=\"I\">";
		while($partie = mysql_fetch_row($selectparties)){
			$id_partie = $partie[0];
			
			$titre_partie = html_ent($partie[2]);
			$objectifs_partie = trim($partie[3]);
			$introduction_partie = trim($partie[4]);
			$conclusion_partie = trim($partie[5]);

			echo "<li class=\"partie\"><a class=\"partie\" name=\"".$id_partie."\" href=\"?tutorial=".$id_tutoriel."#".$id_partie."\">".$titre_partie."</a></li><br />\n";

			if (!empty($objectifs_partie)) {
				echo "<table width=\"95%\" border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><div id=\"objectifs\">".objectifs_partie."</div>";
				echo "<div id=\"normal\">".$objectifs_partie."</div></td></tr></table>\n";
			}
			
			if (!empty($introduction_partie)) {
				echo "<br /><div id=\"normal\">".$introduction_partie."</div>\n";
			}
// chapitres

			if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
				$selectchapitres = mysql_query("select * from `" . $tblprefix . "chapitres` where id_partie = $id_partie order by ordre_chapitre;");
			else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2)
				$selectchapitres = mysql_query("select * from `" . $tblprefix . "chapitres` where id_partie = $id_partie and publie_chapitre = '1' and (grade_chapitre = '*' or grade_chapitre = '0' or grade_chapitre like '%-$grade_app_session-%') order by ordre_chapitre;");
			else
				$selectchapitres = mysql_query("select * from `" . $tblprefix . "chapitres` where id_partie = $id_partie and publie_chapitre = '1' and grade_chapitre = '*' order by ordre_chapitre;");

			if (mysql_num_rows($selectchapitres)> 0) {
				echo "<ol>";
				while($chapitre = mysql_fetch_row($selectchapitres)){
					$id_chapitre = $chapitre[0];
					
					$titre_chapitre = html_ent($chapitre[2]);
					$objectifs_chapitre = trim($chapitre[3]);
						
					echo "<li class=\"chapitre\"><a class=\"chapitre\" href=\"?chapter=".$id_chapitre."\">".$titre_chapitre."</a></li>\n";
					
					if (!empty($objectifs_chapitre)) {
						echo "<br /><table width=\"95%\" border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><div id=\"objectifs\">".objectifs_chapitre."</div>";
						echo "<div id=\"normal\">".$objectifs_chapitre."</div></td></tr></table>\n";
					}
	
// blocs		
					echo "<ul>";
					if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
						$selectblocs = mysql_query("select * from `" . $tblprefix . "blocs` where id_chapitre = $id_chapitre order by ordre_bloc;");
					else
						$selectblocs = mysql_query("select * from `" . $tblprefix . "blocs` where id_chapitre = $id_chapitre and publie_bloc = '1' order by ordre_bloc;");

					if (mysql_num_rows($selectblocs)> 0) {
						while($bloc = mysql_fetch_row($selectblocs)) {
							$id_bloc = $bloc[0];
							$titre_bloc = html_ent($bloc[2]);
							echo "<br /><li class=\"bloc\"><a class=\"bloc\" href=\"?chapter=".$id_chapitre."#".$id_bloc."\">".$titre_bloc."</a></li>\n";
						}
					}

// devoir
					
					if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
						$selectdevoir = mysql_query("select id_devoir from `" . $tblprefix . "devoirs` where id_chapitre = $id_chapitre;");
					else
						$selectdevoir = mysql_query("select id_devoir from `" . $tblprefix . "devoirs` where id_chapitre = $id_chapitre and publie_devoir = '1' and date_publie_devoir < ".time()." and date_expire_devoir > ".time().";");

					if (mysql_num_rows($selectdevoir)> 0)
						echo "<br /><li class=\"bloc\"><a class=\"bloc\" href=\"?chapter=".$id_chapitre."#devoir\">".devoir."</a></li>\n";

// qcm
					
					if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
						$selectqcm = mysql_query("select id_qcm from `" . $tblprefix . "qcm` where id_chapitre = $id_chapitre;");
					else 
						$selectqcm = mysql_query("select id_qcm from `" . $tblprefix . "qcm` where id_chapitre = $id_chapitre and publie_qcm = '1';");

					if (mysql_num_rows($selectqcm)> 0)
						echo "<br /><li class=\"bloc\"><a class=\"bloc\" href=\"?chapter=".$id_chapitre."#qcm\">".qcm."</a></li>\n";

					echo "<br /></ul>";
				}
				echo "</ol>";
			} else echo "<br />";
			if (!empty($conclusion_partie)) {
				echo "<div id=\"normal\">".$conclusion_partie."</div><br />\n";
			}
		}
		echo "</ol><hr />";
	}
	if (!empty($conclusion_tutoriel)) {
		echo "<div id=\"normal\">".$conclusion_tutoriel."</div>\n";
	}
	// accéder au cours
	if (!empty($_SESSION['log']) && $_SESSION['log'] == 1)
		$selectchapitre1 = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres`, `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie order by ordre_partie, ordre_chapitre limit 0,1;");
	else if (!empty($_SESSION['log']) && $_SESSION['log'] == 2)
		$selectchapitre1 = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres`, `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and publie_partie = '1' and publie_chapitre = '1' and (grade_chapitre = '*' or grade_chapitre = '0' or grade_chapitre like '%-$grade_app_session-%') order by ordre_partie, ordre_chapitre limit 0,1;");
	else
 		$selectchapitre1 = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres`, `" . $tblprefix . "parties` where id_tutoriel = $id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and publie_partie = '1' and publie_chapitre = '1' and grade_chapitre = '*' order by ordre_partie, ordre_chapitre limit 0,1;");

	if (mysql_num_rows($selectchapitre1) == 1) {
		$id_first_chap = mysql_result($selectchapitre1,0);
		echo "<hr /><div id=\"titre\"><a href=\"?chapter=".$id_first_chap."\">>> ".access_course." <<</a></div>";
	}
	echo "</td></tr></table>";
	
	// ********* commentaires **********
	$type_objet = "t";
	$id_objet = $id_tutoriel;
	$path_objet = "tutorial";
	include_once ("includes/comments.php");
		
 } else echo "\n<div id=\"titre\">".$titre_tutoriel."</a></div><br /><font color=\"red\"><b>".no_access_permission."</b></font>";
} else accueil();
} else accueil();
} else accueil();
?>