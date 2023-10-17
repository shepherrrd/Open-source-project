<?php
/*
 * 	Manhali - Free Learning Management System
 *	statistics.php
 *	2009-04-19 13:34
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1){

	$max_len = 40;

	echo "<div id=\"titre\">".statistiques."</div><br />";

	echo "<table width=\"100%\" cellpadding=\"10\"  align=\"center\" style=\"border: 1px solid #000000;\">";
	echo "\n<tr><td style=\"border: 1px solid #000000;\" rowspan=\"4\" width=\"50%\" valign=\"top\">";
	
	// ************** tutos *******************
	echo "<h3><u>".tutoriels."</u></h3><ul>";
	
  // **************
	$select_count_tutos = mysql_query("select count(id_tutoriel) from `" . $tblprefix . "tutoriels`;");
	$nbr_tutos = mysql_result($select_count_tutos,0);
	echo "<li><h4>".$nbr_tutos." ".tutoriels2;
	if ($nbr_tutos > 0) {
		echo " ".including." : </h4><ul type=\"circle\">";

		$select_count_tutos_types = mysql_query("select publie_tutoriel, count(id_tutoriel) from `" . $tblprefix . "tutoriels` group by publie_tutoriel;");
		while($tutoriel = mysql_fetch_row($select_count_tutos_types)){
			echo "<li>".$tutoriel[1]." ";
			if ($tutoriel[0] == 2) echo tutoriels_valid;
			else if ($tutoriel[0] == 1) echo tutoriels_attente;
			else echo tutoriels_creation;
			echo "</li>";
		}
		echo "</ul>";
	} else echo "</h4>";
	echo "</li>";

  // **************
	echo "<br /><li><h4>".tutos_plus_visites." : ";

	$select_tutos_visites = mysql_query("select `" . $tblprefix . "tutoriels`.id_tutoriel, titre_tutoriel, sum(nombre_lectures) as nbr_lect from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie group by `" . $tblprefix . "tutoriels`.id_tutoriel order by nbr_lect desc limit 0,3;");
	if (mysql_num_rows($select_tutos_visites) > 0){
		echo "</h4><ol>";
		while($tutoriel_visite = mysql_fetch_row($select_tutos_visites)){
			$titre_tuto = html_ent($tutoriel_visite[1]);
			$titre_tuto = readmore($titre_tuto,$max_len);

			echo "<li><a target=\"_blank\" href=\"../?tutorial=".$tutoriel_visite[0]."\" title=\"".previsualiser."\"><b>".$titre_tuto."</b></a>";
			echo " : ".$tutoriel_visite[2]." ".lecture."</li>";
		}
		echo "</ol>";
	} else echo aucun."</h4>";
	echo "</li>";

  // **************
	echo "<br /><li><h4>".chapitres_plus_visites." : ";

	$select_chapitres_visites = mysql_query("select id_chapitre, titre_chapitre, sum(nombre_lectures) as nbr_lect from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie group by id_chapitre order by nbr_lect desc limit 0,3;");
	if (mysql_num_rows($select_chapitres_visites) > 0){
		echo "</h4><ol>";
		while($chapitre_visite = mysql_fetch_row($select_chapitres_visites)){
			$titre_chap = html_ent($chapitre_visite[1]);
			$titre_chap = readmore($titre_chap,$max_len);

			echo "<li><a target=\"_blank\" href=\"../?chapter=".$chapitre_visite[0]."\" title=\"".previsualiser."\"><b>".$titre_chap."</b></a>";
			echo " : ".$chapitre_visite[2]." ".lecture."</li>";
		}
		echo "</ol>";
	} else echo aucun."</h4>";
	echo "</li>";

  // **************
	echo "<br /><li><h4>".qcm." : </h4><ul type=\"circle\">";
	$sum_qcm = mysql_query("select sum(total_reponses_correctes), sum(total_essais) from `" . $tblprefix . "qcm`;");
	$total_reponses_correctes = mysql_result($sum_qcm,0,0);
	$total_essais = mysql_result($sum_qcm,0,1);
	if ($total_essais != 0 && $total_reponses_correctes <= $total_essais)
		$reponses_correctes = round(100 * $total_reponses_correctes / $total_essais,2);
	else {
		$reponses_correctes = 0;
		$total_essais = 0;
	}
	echo "<li>".$total_essais." ".essais_qcm." ".$reponses_correctes." % ".reponses_sont_correctes."</li>";
	echo "</ul></li>";
	
  // **************
	echo "<br /><li><h4>".meilleur_qcm." : ";

	$select_meilleur_qcm = mysql_query("select id_chapitre, sum(total_reponses_correctes), sum(total_essais), sum(total_reponses_correctes)/sum(total_essais) as pourcentage from `" . $tblprefix . "qcm` where total_essais != 0 group by id_chapitre order by pourcentage desc, id_chapitre limit 0,3;");
	if (mysql_num_rows($select_meilleur_qcm) > 0){
		echo "</h4><ol>";
		while($chapitre_meilleur = mysql_fetch_row($select_meilleur_qcm)){
			$id_chapitre_meilleur = $chapitre_meilleur[0];
			$sum_reponses_correctes = $chapitre_meilleur[1];
			$sum_total_essais = $chapitre_meilleur[2];

			$meilleur_qcm = round(100 * $sum_reponses_correctes / $sum_total_essais,2);

			$select_titre_chapitre = mysql_query("select titre_chapitre from `" . $tblprefix . "chapitres` where id_chapitre = $id_chapitre_meilleur;");
			$titre_chapitre_meilleur = html_ent(mysql_result($select_titre_chapitre,0));
			$titre_chapitre_meilleur = readmore($titre_chapitre_meilleur,$max_len);

			echo "<li><a target=\"_blank\" href=\"../?chapter=".$id_chapitre_meilleur."\" title=\"".previsualiser."\"><b>".$titre_chapitre_meilleur."</b></a>";
			echo " : ".$meilleur_qcm." %</li>";
		}
		echo "</ol>";

	} else echo aucun."</h4>";
	echo "</li>";

  // **************
	echo "<br /><li><h4>".pire_qcm." : ";

	$select_pire_qcm = mysql_query("select id_chapitre, sum(total_reponses_correctes), sum(total_essais), sum(total_reponses_correctes)/sum(total_essais) as pourcentage from `" . $tblprefix . "qcm` where total_essais != 0 group by id_chapitre order by pourcentage, id_chapitre desc limit 0,3;");
	if (mysql_num_rows($select_pire_qcm) > 0){
		echo "</h4><ol>";
		while($chapitre_pire = mysql_fetch_row($select_pire_qcm)){
			$id_chapitre_pire = $chapitre_pire[0];
			$sum_reponses_correctes = $chapitre_pire[1];
			$sum_total_essais = $chapitre_pire[2];
	
			$pire_qcm = round(100 * $sum_reponses_correctes / $sum_total_essais,2);
	
			$select_titre_chapitre = mysql_query("select titre_chapitre from `" . $tblprefix . "chapitres` where id_chapitre = $id_chapitre_pire;");
			$titre_chapitre_pire = html_ent(mysql_result($select_titre_chapitre,0));
			$titre_chapitre_pire = readmore($titre_chapitre_pire,$max_len);

			echo "<li><a target=\"_blank\" href=\"../?chapter=".$id_chapitre_pire."\" title=\"".previsualiser."\"><b>".$titre_chapitre_pire."</b></a>";
			echo " : ".$pire_qcm." %</li>";
		}
		echo "</ol>";
	} else echo aucun."</h4>";
	echo "</li>";

  // **************
  echo "</ul>";
	echo "\n</td><td style=\"border: 1px solid #000000;\" width=\"50%\" valign=\"top\">";
	
	// ************** users *******************
	echo "<h3><u>".users." : </u></h3><ul>";

  // **************
	$select_count_users = mysql_query("select count(id_user) from `" . $tblprefix . "users`;");
	$nbr_users = mysql_result($select_count_users,0);
	echo "<li><h4>".$nbr_users." ".users;
	if ($nbr_users > 0) {
		echo " ".including." : </h4>";
		
		$select_count_users_actives = mysql_query("select count(id_user) from `" . $tblprefix . "users` where active_user = '1';");
		$nbr_users_actives = mysql_result($select_count_users_actives,0);
		echo "<h4>- ".$nbr_users_actives." ".users_enabled.".</h4>";
		
		$select_count_users_connected = mysql_query("select id_user, identifiant_user, grade_user from `" . $tblprefix . "users` where connected_now = '1' order by grade_user desc;");
		$nb_connected = mysql_num_rows($select_count_users_connected);
		echo "<h4>- ".$nb_connected." ".users_connected." : </h4>";
		if ($nb_connected > 0){
			while($user = mysql_fetch_row($select_count_users_connected)){
				echo "<b><a href=\"../?profiles=".$user[0]."\" title=\"".user_profile."\">".$user[1]."</a> (";
				echo $grade_tab[$user[2]];
				echo ")</b>, ";
			}
		}
	} else echo "</h4>";
	echo "</li></ul>";

  // **************

	echo "\n</td></tr><tr><td style=\"border: 1px solid #000000;\" width=\"50%\" valign=\"top\">";

	// ************** apprenants *******************
	echo "<h3><u>".learners." : </u></h3><ul>";

  // **************
	$select_count_learners = mysql_query("select count(id_apprenant) from `" . $tblprefix . "apprenants`;");
	$nbr_learners = mysql_result($select_count_learners,0);
	echo "<li><h4>".$nbr_learners." ".learners;
	if ($nbr_learners > 0) {
		echo " ".including." : </h4>";
		
		$select_count_learners_actives = mysql_query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where active_apprenant = '1';");
		$nbr_learners_actives = mysql_result($select_count_learners_actives,0);
		echo "<h4>- ".$nbr_learners_actives." ".learners_enabled.".</h4>";
		
		$select_count_learners_connected = mysql_query("select id_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants` where connected_now_apprenant = '1';");
		$nb_learners_connected = mysql_num_rows($select_count_learners_connected);
		echo "<h4>- ".$nb_learners_connected." ".learners_connected." : </h4>";
		if ($nb_learners_connected > 0){
			while($learner = mysql_fetch_row($select_count_learners_connected)){
				echo "<b><a href=\"../?s_profiles=".$learner[0]."\" title=\"".learner_profile."\">".$learner[1]."</a></b>, ";
			}
		}
	} else echo "</h4>";
	echo "</li></ul>";

  // **************

	echo "\n</td></tr><tr><td style=\"border: 1px solid #000000;\" width=\"50%\" valign=\"top\">";
	
	// ************** articles *******************
	echo "<h3><u>".articles." : </u></h3><ul>";
	
  // **************
	$select_count_articles = mysql_query("select count(id_article) from `" . $tblprefix . "articles`;");
	$nbr_articles = mysql_result($select_count_articles,0);
	echo "<li><h4>".$nbr_articles." ".articles;
	if ($nbr_articles > 0) {
		echo " ".including." : </h4><ul type=\"circle\">";

		$select_count_articles_types = mysql_query("select publie_article, count(id_article) from `" . $tblprefix . "articles` group by publie_article;");
		while($article = mysql_fetch_row($select_count_articles_types)){
			echo "<li>".$article[1]." ";
			if ($article[0] == 1) echo articles_valid;
			else echo articles_attente;
			echo "</li>";
		}
		echo "</ul>";
	} else echo "</h4>";
	echo "</li></ul>";

  // **************

	echo "\n</td></tr><tr><td style=\"border: 1px solid #000000;\" width=\"50%\" valign=\"top\">";
	
	// ************** sondage *******************
	echo "<h3><u>".poll." : </u></h3><ul>";

  // **************
	$select_count_polls = mysql_query("select count(id_question) from `" . $tblprefix . "sondage_questions` where id_conjoint = 0;");
	$nbr_polls = mysql_result($select_count_polls,0);
	echo "<li><h4>".$nbr_polls." ".polls;
	if ($nbr_polls > 0) {

		$select_active_sondage = mysql_query("select * from `" . $tblprefix . "sondage_questions` where active_question = '1' and id_conjoint = 0;");
		if ($poll = mysql_fetch_row($select_active_sondage)){
			
			echo " ".including." 1 ".enabled_polls." : </h4><ul type=\"circle\">";
			
			$select_other_question = mysql_query("select * from `" . $tblprefix . "sondage_questions` where id_conjoint = $poll[0];");
			if (mysql_num_rows($select_other_question) > 0){
				$poll2 = mysql_fetch_row($select_other_question);
				echo "<li><a href=\"?inc=poll_manager&do=view_poll&id_poll=".$poll2[0]."\" title=\"".previsualiser."\"><b>".$poll[2]."</b></a></li>";
				echo "<li><a href=\"?inc=poll_manager&do=view_poll&id_poll=".$poll2[0]."\" title=\"".previsualiser."\"><b>".$poll2[2]."</b></a></li>";
			}
			else
				echo "<li><a href=\"?inc=poll_manager&do=view_poll&id_poll=".$poll[0]."\" title=\"".previsualiser."\"><b>".$poll[2]."</b></a></li>";
			
			echo "</ul>";		
		} else echo " ".including." 0 ".enabled_polls.".</h4>";

	} else echo "</h4>";
	echo "</li></ul>";

	// **************

	echo "</td></tr></table>";
} else echo restricted_access;

?>