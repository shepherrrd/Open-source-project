<?php
/*
 * 	Manhali - Free Learning Management System
 *	calculate_behavior_note.php
 *	2012-02-06 01:01
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
		$select_n_app_behavior = $connect->query("select * from `" . $tblprefix . "apprenants` where active_apprenant = '1';");
		if (mysqli_num_rows($select_n_app_behavior) > 0) {
			$count_apps_stats = 0;
			while($usr_behav = mysqli_fetch_row($select_n_app_behavior)){
			
					$duree_totale = calcule_duree($usr_behav[17]);
					$nbr_of_connexion = $usr_behav[18];
					$total_essais_app = $usr_behav[19];
					$total_correct_app = $usr_behav[20];
					$nbr_pages = $usr_behav[21];
					
					$select_count_comments = $connect->query("select count(id_post) from `" . $tblprefix . "commentaires` where type_user = 'l' and id_user = $usr_behav[0];");
					$nbr_comments = mysqli_result($select_count_comments,0);
					
					$select_count_messages = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where id_emetteur_app = $usr_behav[0];");
					$nbr_messages = mysqli_result($select_count_messages,0);
					
					$select_count_devoirs = $connect->query("select count(id_devoir_rendu) from `" . $tblprefix . "devoirs_rendus` where id_apprenant = $usr_behav[0];");
					$nbr_devoirs = mysqli_result($select_count_devoirs,0);

					// ************* behavior note
					$note_totale = 0;
					$nbr_notes = 0;

					$select_count_apps_same_classe = $connect->query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1' and id_classe = $usr_behav[1];");
					$select_count_apps_active = $connect->query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1';");
					$select_count_apps = $connect->query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where nbr_connexion > 0;");
					if (mysqli_num_rows($select_count_apps_same_classe) > 0 && mysqli_result($select_count_apps_same_classe,0) > 1){
						$count_apps_stats = mysqli_result($select_count_apps_same_classe,0);
						$req_apps_stats = "where nbr_connexion > 0 and active_apprenant = '1' and id_classe = $usr_behav[1]";
						$select_apps_stats = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1' and id_classe = $usr_behav[1];");
					}
					else if (mysqli_num_rows($select_count_apps_active) > 0 && mysqli_result($select_count_apps_active,0) > 1){
						$count_apps_stats = mysqli_result($select_count_apps_active,0);
						$req_apps_stats = "where nbr_connexion > 0 and active_apprenant = '1'";
						$select_apps_stats = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where nbr_connexion > 0 and active_apprenant = '1';");
					}
					else if (mysqli_num_rows($select_count_apps) > 0 && mysqli_result($select_count_apps,0) > 1){
						$count_apps_stats = mysqli_result($select_count_apps,0);
						$req_apps_stats = "where nbr_connexion > 0";
						$select_apps_stats = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where nbr_connexion > 0;");
					}

					if (!empty($count_apps_stats) && isset($req_apps_stats)){
						$count_apps_10 = $count_apps_stats / 10;
						$tab_apps_stats = array();
						while ($one_apps_stats = mysqli_fetch_row($select_apps_stats))
							$tab_apps_stats[] = $one_apps_stats[0];
							$chaine_apps_stats = implode(",",$tab_apps_stats);
						//******* total_duration
						$select_sum_total_duration = $connect->query("select sum(total_duration) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_total_duration) > 0 && mysqli_result($select_sum_total_duration,0) > 0 && mysqli_result($select_sum_total_duration,0) > $count_apps_10){
							$sum_total_duration = mysqli_result($select_sum_total_duration,0);
							$note_total_duration = round(100*$usr_behav[17]/$sum_total_duration,5);
							if ($note_total_duration < 0) $note_total_duration = 0;
							if ($note_total_duration > 100) $note_total_duration = 100;
							$note_totale += $note_total_duration;
							$nbr_notes += 1;
						}
						//******* nbr_connexion
						$select_sum_nbr_connexion = $connect->query("select sum(nbr_connexion) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_nbr_connexion) > 0 && mysqli_result($select_sum_nbr_connexion,0) > 0 && mysqli_result($select_sum_nbr_connexion,0) > $count_apps_10){
							$sum_nbr_connexion = mysqli_result($select_sum_nbr_connexion,0);
							$note_nbr_connexion = round(100*$nbr_of_connexion/$sum_nbr_connexion,5);
							if ($note_nbr_connexion < 0) $note_nbr_connexion = 0;
							if ($note_nbr_connexion > 100) $note_nbr_connexion = 100;
							$note_totale += $note_nbr_connexion;
							$nbr_notes += 1;
						}
						//******* nbr_pages
						$select_sum_nbr_pages = $connect->query("select sum(nbr_pages) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_nbr_pages) > 0 && mysqli_result($select_sum_nbr_pages,0) > 0 && mysqli_result($select_sum_nbr_pages,0) > $count_apps_10){
							$sum_nbr_pages = mysqli_result($select_sum_nbr_pages,0);
							$note_nbr_pages = round(100*$nbr_pages/$sum_nbr_pages,5);
							if ($note_nbr_pages < 0) $note_nbr_pages = 0;
							if ($note_nbr_pages > 100) $note_nbr_pages = 100;
							$note_totale += $note_nbr_pages;
							$nbr_notes += 1;
						}
						//******* total_essais_qcm
						$select_sum_total_essais = $connect->query("select sum(total_essais) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_total_essais) > 0 && mysqli_result($select_sum_total_essais,0) > 0 && mysqli_result($select_sum_total_essais,0) > $count_apps_10){
							$sum_total_essais = mysqli_result($select_sum_total_essais,0);
							$note_total_essais = round(100*$total_essais_app/$sum_total_essais,5);
							if ($note_total_essais < 0) $note_total_essais = 0;
							if ($note_total_essais > 100) $note_total_essais = 100;
							$note_totale += $note_total_essais;
							$nbr_notes += 1;
						}
						//******* total_reponses_correctes
						$select_sum_total_reponses_correctes = $connect->query("select sum(total_reponses_correctes) from `" . $tblprefix . "apprenants` ".$req_apps_stats.";");
						if (mysqli_num_rows($select_sum_total_reponses_correctes) > 0 && mysqli_result($select_sum_total_reponses_correctes,0) > 0 && mysqli_result($select_sum_total_reponses_correctes,0) > $count_apps_10){
							$sum_total_reponses_correctes = mysqli_result($select_sum_total_reponses_correctes,0);
							$note_total_reponses_correctes = round(100*$total_correct_app/$sum_total_reponses_correctes,5);
							if ($note_total_reponses_correctes < 0) $note_total_reponses_correctes = 0;
							if ($note_total_reponses_correctes > 100) $note_total_reponses_correctes = 100;
							$note_totale += $note_total_reponses_correctes;
							$nbr_notes += 1;
						}
						//******* total_commentaires
						$select_count_apps_comments = $connect->query("select count(id_post) from `" . $tblprefix . "commentaires` where type_user = 'l' and id_user in (".$chaine_apps_stats.");");
						if (mysqli_num_rows($select_count_apps_comments) > 0 && mysqli_result($select_count_apps_comments,0) > 0 && mysqli_result($select_count_apps_comments,0) > $count_apps_10){
							$count_apps_comments = mysqli_result($select_count_apps_comments,0);
							$note_apps_comments = round(100*$nbr_comments/$count_apps_comments,5);
							if ($note_apps_comments < 0) $note_apps_comments = 0;
							if ($note_apps_comments > 100) $note_apps_comments = 100;
							$note_totale += $note_apps_comments;
							$nbr_notes += 1;
						}
						//******* total_messages
						$select_count_apps_messages = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where id_emetteur_app in (".$chaine_apps_stats.");");
						if (mysqli_num_rows($select_count_apps_messages) > 0 && mysqli_result($select_count_apps_messages,0) > 0 && mysqli_result($select_count_apps_messages,0) > $count_apps_10){
							$count_apps_messages = mysqli_result($select_count_apps_messages,0);
							$note_apps_messages = round(100*$nbr_messages/$count_apps_messages,5);
							if ($note_apps_messages < 0) $note_apps_messages = 0;
							if ($note_apps_messages > 100) $note_apps_messages = 100;
							$note_totale += $note_apps_messages;
							$nbr_notes += 1;
						}
						//******* total_devoirs
						$select_count_apps_devoirs = $connect->query("select count(id_devoir_rendu) from `" . $tblprefix . "devoirs_rendus` where id_apprenant in (".$chaine_apps_stats.");");
						if (mysqli_num_rows($select_count_apps_devoirs) > 0 && mysqli_result($select_count_apps_devoirs,0) > 0 && mysqli_result($select_count_apps_devoirs,0) > $count_apps_10){
							$count_apps_devoirs = mysqli_result($select_count_apps_devoirs,0);
							$note_apps_devoirs = round(100*$nbr_devoirs/$count_apps_devoirs,5);
							if ($note_apps_devoirs < 0) $note_apps_devoirs = 0;
							if ($note_apps_devoirs > 100) $note_apps_devoirs = 100;
							$note_totale += $note_apps_devoirs;
							$nbr_notes += 1;
						}
					}
					$this_month = date("m",time());
					$this_year = date("Y",time());
					//************ update insert note
					if ($note_totale >= 0 && $nbr_notes > 0){
					 $note_finale_behavior = round($note_totale / $nbr_notes,5);
					 if ($note_finale_behavior >= 0 && $note_finale_behavior <= 100){
   					$select_note_finale_behavior = $connect->query("select id_behavior_note, behavior_note from `" . $tblprefix . "behavior_notes` where id_apprenant = $usr_behav[0] and mois_note = $this_month and annee_note = $this_year;");
      			if (mysqli_num_rows($select_note_finale_behavior) > 0){
   						$id_note_finale_behavior = mysqli_result($select_note_finale_behavior,0,0);
   						$ancien_note_finale_behavior = mysqli_result($select_note_finale_behavior,0,1);
   						if ($ancien_note_finale_behavior != $note_finale_behavior){
   							$update_note_finale_behavior = $connect->query("update `" . $tblprefix . "behavior_notes` set behavior_note = $note_finale_behavior where id_behavior_note = $id_note_finale_behavior;");
   						}
   					}
   					else {
   						$insert_note_finale_behavior = $connect->query("INSERT INTO `" . $tblprefix . "behavior_notes` VALUES (NULL,$this_month,$this_year,$usr_behav[0],$note_finale_behavior);");
   					}
					 }
					} else $note_finale_behavior = 0;
			}
   				//************
			$select_all_apps_notes = $connect->query("select * from `" . $tblprefix . "behavior_notes` where mois_note = $this_month and annee_note = $this_year;");
			if (mysqli_num_rows($select_all_apps_notes) > 0) {
				if ($count_apps_stats > 0){
					$coef_apps = 100 / $count_apps_stats;
					while($app_note_this = mysqli_fetch_row($select_all_apps_notes)){
						$id_this_app_final = $app_note_this[3];
						$note_apps_final = $app_note_this[4];

						$note_for_grade = $note_apps_final / $coef_apps;

      			if ($note_for_grade < 0.5)
   						$grade_behavior_final = "E";
   					else if ($note_for_grade >= 0.5 && $note_for_grade < 1)
   						$grade_behavior_final = "D";
   					else if ($note_for_grade >= 1 && $note_for_grade < 2)
   						$grade_behavior_final = "C";
   					else if ($note_for_grade >= 2 && $note_for_grade < 4)
   						$grade_behavior_final = "B";
   					else if ($note_for_grade >= 4)
   						$grade_behavior_final = "A";
						else $grade_behavior_final = "E";

						$update_grade = $connect->query("update `" . $tblprefix . "apprenants` set grade_apprenant = '$grade_behavior_final' where id_apprenant = $id_this_app_final;");
					}
				}
			}
		}
	}

?>