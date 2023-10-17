<?php
/*
 * 	Manhali - Free Learning Management System
 *	edit_tutorials.php
 *	2009-05-05 10:08
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2" || $grade_user_session == "1")){

	echo "<div id=\"titre\">".gestion_tutoriels."</div>";

	$select_statut_comp = mysql_query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'courses';");
	if (mysql_num_rows($select_statut_comp) == 1) {
 		$statut_comp = mysql_result($select_statut_comp,0);
		if ($statut_comp == 0)
		 echo "<h3><img src=\"../images/icones/warning.png\" /><font color=\"red\">".component_disabled." ".enable_it_now." : </font><a href=\"?inc=components\"\">".gestion_composants."</a></h3>";
	}
	
	include_once ("ckeditor_init.php");
	
	confirmer();

	$max_len = 70;
	$max_len2 = 100;

	if (isset($_GET['l']) && ctype_digit($_GET['l']))
		$page = intval($_GET['l']);
	else $page = 1;
	
	if (isset($_GET['id_tuto']) && ctype_digit($_GET['id_tuto']))
		$id_tuto = intval($_GET['id_tuto']);
	else $id_tuto = 0;

	if (isset($_GET['id_part']) && ctype_digit($_GET['id_part']))
		$id_part = intval($_GET['id_part']);
	else $id_part = 0;

	if (isset($_GET['id_chap']) && ctype_digit($_GET['id_chap']))
		$id_chap = intval($_GET['id_chap']);
	else $id_chap = 0;

	if (isset($_GET['id_bloc']) && ctype_digit($_GET['id_bloc']))
		$id_bloc = intval($_GET['id_bloc']);
	else $id_bloc = 0;

	if (isset($_GET['id_qcm']) && ctype_digit($_GET['id_qcm']))
		$id_qcm = intval($_GET['id_qcm']);
	else $id_qcm = 0;

	if (isset($_GET['id_devoir']) && ctype_digit($_GET['id_devoir']))
		$id_devoir = intval($_GET['id_devoir']);
	else $id_devoir = 0;
	
	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	switch ($do){

		// ****************** create_tuto **************************
    case "create_tuto" : {
    	if (isset($_POST['tuto_titre'])){
    	 $tuto_titre = trim($_POST['tuto_titre']);
    	 if (!empty($tuto_titre)){
    		$tuto_titre = escape_string($tuto_titre);
    		$select_tuto_titre = mysql_query("select id_tutoriel from `" . $tblprefix . "tutoriels` where titre_tutoriel = '$tuto_titre';");
 					if (mysql_num_rows($select_tuto_titre) == 0) {
 						$select_max_order = mysql_query("select max(ordre_tutoriel) from `" . $tblprefix . "tutoriels`;");
 						if (mysql_num_rows($select_max_order) == 1)
 							$ordre_tuto = mysql_result($select_max_order,0) + 1;
 						else $ordre_tuto = 1;
 						$time_insert_tuto = time();
 						$inserttuto = "INSERT INTO `" . $tblprefix . "tutoriels` VALUES (NULL,$id_user_session,'$tuto_titre','','','','by','','1',$ordre_tuto,$time_insert_tuto,$time_insert_tuto,0,'*',0,0);";
	          mysql_query($inserttuto,$connect);
	          if ($this_tuto_insert = mysql_insert_id())
	          	$link = "?inc=edit_tutorials&do=update_tuto&id_tuto=".$this_tuto_insert;
	          else $link = "?inc=edit_tutorials";
	          redirection(tutoriel_cree,$link,3,"tips",1);
 					} else goback(titre_existe,2,"error",1);
    	 } else goback(titre_vide,2,"error",1);
    	}
    	else {
    		goback_button();
    		echo "<form method=\"POST\" action=\"\">";
	    	echo "<p><u><b>" .titre_tutoriel. "</b></u><br /><br /><input name=\"tuto_titre\" type=\"text\" size=\"50\" maxlength=\"100\"></p>";
	    	echo "<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
   		}
    } break;
   	
   	// ****************** update_tuto **************************
    case "update_tuto" : {
    		$select_tuto_complet = mysql_query("select * from `" . $tblprefix . "tutoriels` where id_tutoriel = $id_tuto;");
    		if (mysql_num_rows($select_tuto_complet) == 1) {
    			$tutoriel = mysql_fetch_row($select_tuto_complet);
    			
    			$titre_tuto = html_ent($tutoriel[2]);
    			$objectifs_tuto = html_ent($tutoriel[3]);
    			$introduction_tuto = html_ent($tutoriel[4]);
    			$conclusion_tuto = html_ent($tutoriel[5]);
    			$notes_tuto = html_ent($tutoriel[7]);
    			$acces_tuto = $tutoriel[13];

    				if (!empty($_POST['send'])){
    					$tuto_titre = trim($_POST['tuto_titre']);
    					if (!empty($tuto_titre)){
    						$tuto_titre = escape_string($tuto_titre);
    						$tuto_objectifs = escape_string(trim($_POST['tuto_objectifs']));
    						$tuto_introduction = escape_string(trim($_POST['tuto_introduction']));
    						$tuto_conclusion = escape_string(trim($_POST['tuto_conclusion']));
    						$tuto_licence = escape_string($_POST['tuto_licence']);
    						$tuto_notes = escape_string(trim($_POST['tuto_notes']));
								
								if ($_POST['acces'] == "learner")
    							$tuto_acces = "0";
    						else if ($_POST['acces'] == "classe"){
    							if (!empty($_POST['classes']))
    								$tuto_acces = "-".implode("-",$_POST['classes'])."-";
    							else $tuto_acces = "0";
    						}
    						else $tuto_acces = "*";
    						
    						$select_tuto_titre = mysql_query("select id_tutoriel from `" . $tblprefix . "tutoriels` where titre_tutoriel = '$tuto_titre';");
 								if ((mysql_num_rows($select_tuto_titre) == 0) || (mysql_num_rows($select_tuto_titre) == 1 && mysql_result($select_tuto_titre,0) == $id_tuto)) {
 									$update_tuto = "update `" . $tblprefix . "tutoriels` SET titre_tutoriel = '$tuto_titre', objectifs_tutoriel = '$tuto_objectifs', introduction_tutoriel = '$tuto_introduction', conclusion_tutoriel = '$tuto_conclusion', licence_tutoriel = '$tuto_licence', notes_tutoriel = '$tuto_notes', date_modification_tutoriel = ".time().", acces_tutoriel = '$tuto_acces' where id_tutoriel = $id_tuto;";
 									mysql_query($update_tuto);
 									redirection(tutoriel_modifie,"?inc=edit_tutorials",3,"tips",1);
 								} else goback(titre_existe,2,"error",1);
    					} else goback(titre_vide,2,"error",1);
    				}
    				else {
    					goback_button();
    					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";

    					echo "<form method=\"POST\" action=\"\">";
    					echo "<p><u><b><font color=\"red\">*</font> ".titre_tutoriel."</b></u><br /><br /><input name=\"tuto_titre\" type=\"text\" size=\"50\" maxlength=\"100\" value=\"".$titre_tuto."\"></p>";
							
							echo "<p><u><b><font color=\"red\">*</font> ".acces_cours."</b></u><br /><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"all\" onclick=\"disabled_select('classes',true)\"";
							if ($acces_tuto == "*")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_ouvert."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"learner\" onclick=\"disabled_select('classes',true)\"";
							if ($acces_tuto == "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_apprenants."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"classe\" onclick=\"disabled_select('classes',false)\"";
							if ($acces_tuto != "*" && $acces_tuto != "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_classes." :</b>";
							$tab_classes = explode("-",$acces_tuto);
    					$select_classes = mysql_query("select * from `" . $tblprefix . "classes`;");
					 		if (mysql_num_rows($select_classes) > 0){
					 			echo "<table border=\"0\"><tr><td align=\"center\">";
					 			echo "<table border=\"0\"><tr><td><a href=\"?inc=site_config&do=registration#classe\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=site_config&do=registration#classe\"><b>".ajouter_classe."</b></a></td></tr></table>";
								echo "<select size=\"5\" name=\"classes[]\" id=\"classes\" multiple=\"multiple\">";
    						while($classe = mysql_fetch_row($select_classes)){
    							$id_classe = $classe[0];
    							$nom_classe = html_ent($classe[1]);
    							echo "\n<option value=\"".$id_classe."\"";
    							if (in_array($id_classe,$tab_classes))
    								echo " selected=\"selected\"";
    							echo ">".$nom_classe."</option>";
    						}
								echo "\n</select><br />";
								echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('classes',false)\" />";
								echo "<br />".hold_down_ctrl."</td></tr></table>";
					 		}
					 		else echo aucune_classe;
					 
							echo "<br /><u><b><font color=\"red\">*</font> ".licence."</b></u>";
							foreach ($licence_tab as $licence) {
								echo "\n<br /><br /><input name=\"tuto_licence\" type=\"radio\" ";
								if ($licence == $tutoriel[6]) echo "checked=\"checked\" ";
								echo "value=\"".$licence."\"> <a target=\"_blank\" title=\"".licence_const($licence)."\" href=\"http://creativecommons.org/licenses/".$licence."/3.0/\"><img src=\"../images/licenses/".$licence.".png\" border=\"0\" alt=\"".licence_const($licence)."\" /></a> ".licence_const($licence);
							}
							echo "\n<p align=\"center\"><a target=\"_blank\" href=\"http://creativecommons.org/licenses/\">".liste_licences."</a></p>\n";

    					echo "<p><u><b>" .objectifs_tuto. "</b></u><br />".remarque_update_objectifs."<br /><br /><textarea name=\"tuto_objectifs\" cols=\"100\" rows=\"10\">".$objectifs_tuto."</textarea></p>";
    					echo "<br /><p><u><b>" .introduction_tuto. "</b></u><br /><br /><textarea name=\"tuto_introduction\" cols=\"100\" rows=\"10\">".$introduction_tuto."</textarea></p>";
    					echo "<br /><p><u><b>" .conclusion_tuto. "</b></u><br /><br /><textarea name=\"tuto_conclusion\" cols=\"100\" rows=\"10\">".$conclusion_tuto."</textarea></p>";
    					echo "<br /><p><u><b>" .notes_tuto2. "</b></u><br /><br /><textarea name=\"tuto_notes\" cols=\"100\" rows=\"10\">".$notes_tuto."</textarea></p>";

    					echo "<input type=\"hidden\" name=\"send\" value=\"ok\">";
    					echo "<br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    					ckeditor_replace($language,"tuto_objectifs");
    					ckeditor_replace($language,"tuto_introduction");
    					ckeditor_replace($language,"tuto_conclusion");
    					ckeditor_replace($language,"tuto_notes");
    					if ($acces_tuto == "*" || $acces_tuto == "0")
    						echo "<script type=\"text/javascript\">disabled_select('classes',true);</script>";
    				}
    		} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** delete_tuto **************************
    case "delete_tuto" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    			$select_parties = mysql_query("select id_partie from `" . $tblprefix . "parties` where id_tutoriel = $id_tuto;");
    			if (mysql_num_rows($select_parties) > 0){
    				while($partie = mysql_fetch_row($select_parties)){
    					$select_chapitres = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $partie[0];");
    					if (mysql_num_rows($select_chapitres) > 0){
    						while($chapitre = mysql_fetch_row($select_chapitres)){
    							$delete_bloc = mysql_query("delete from `" . $tblprefix . "blocs` where id_chapitre = $chapitre[0];");
									$delete_qcm = mysql_query("delete from `" . $tblprefix . "qcm` where id_chapitre = $chapitre[0];");
    							$select_devoirs_rendus = mysql_query("select id_devoir from `" . $tblprefix . "devoirs` where id_chapitre = $chapitre[0];");
    							if (mysql_num_rows($select_devoirs_rendus) > 0){
    								while($devoir = mysql_fetch_row($select_devoirs_rendus)){
    									$delete_devoirs_rendus = mysql_query("delete from `" . $tblprefix . "devoirs_rendus` where id_devoir = $devoir[0];");
    									$delete_devoirs_notes = mysql_query("delete from `" . $tblprefix . "devoirs_notes` where id_devoir = $devoir[0];");
    								}
									}
									$delete_devoir = mysql_query("delete from `" . $tblprefix . "devoirs` where id_chapitre = $chapitre[0];");
    						}
							}
							$delete_chapitre = mysql_query("delete from `" . $tblprefix . "chapitres` where id_partie = $partie[0];");					
						}
    			}
    			$delete_partie = mysql_query("delete from `" . $tblprefix . "parties` where id_tutoriel = $id_tuto;");
    			$delete_tuto = mysql_query("delete from `" . $tblprefix . "tutoriels` where id_tutoriel = $id_tuto;");
    	}
    	locationhref_admin("?inc=edit_tutorials");
    } break;


    // ****************** orderup_tuto **********************
    case "orderup_tuto" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_tuto = mysql_query ("select ordre_tutoriel from `" . $tblprefix . "tutoriels` where id_tutoriel = $id_tuto;");
				if (mysql_num_rows($ce_tuto) == 1) {
					$ordre_tuto = mysql_result($ce_tuto,0,0);

    			$tuto_precedent = mysql_query ("select id_tutoriel, ordre_tutoriel from `" . $tblprefix . "tutoriels` where ordre_tutoriel < $ordre_tuto and publie_tutoriel = '2' order by ordre_tutoriel desc;");
					if (mysql_num_rows($tuto_precedent) > 0) {
						$idtuto_precedent = mysql_result($tuto_precedent,0,0);
						$ordretuto_precedent = mysql_result($tuto_precedent,0,1);

						$order_this_tuto = mysql_query("update `" . $tblprefix . "tutoriels` set ordre_tutoriel = $ordretuto_precedent where id_tutoriel = $id_tuto;");
						$order_tuto_precedent = mysql_query("update `" . $tblprefix . "tutoriels` set ordre_tutoriel = $ordre_tuto where id_tutoriel = $idtuto_precedent;");
					}
    		}
    	}
    	locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderdown_tuto **********************
    case "orderdown_tuto" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_tuto = mysql_query ("select ordre_tutoriel from `" . $tblprefix . "tutoriels` where id_tutoriel = $id_tuto;");
				if (mysql_num_rows($ce_tuto) == 1) {
					$ordre_tuto = mysql_result($ce_tuto,0,0);

    			$tuto_suivant = mysql_query ("select id_tutoriel, ordre_tutoriel from `" . $tblprefix . "tutoriels` where ordre_tutoriel > $ordre_tuto and publie_tutoriel = '2' order by ordre_tutoriel;");
					if (mysql_num_rows($tuto_suivant) > 0) {
						$idtuto_suivant = mysql_result($tuto_suivant,0,0);
						$ordretuto_suivant = mysql_result($tuto_suivant,0,1);
							
						$order_this_tuto = mysql_query("update `" . $tblprefix . "tutoriels` set ordre_tutoriel = $ordretuto_suivant where id_tutoriel = $id_tuto;");
						$order_tuto_suivant = mysql_query("update `" . $tblprefix . "tutoriels` set ordre_tutoriel = $ordre_tuto where id_tutoriel = $idtuto_suivant;");
					}
    		}
    	}
    	locationhref_admin("?inc=edit_tutorials");
    } break;
    
    // ****************** publier_tuto *************************
    case "publier_tuto" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$publier_tuto = mysql_query("update `" . $tblprefix . "tutoriels` set publie_tutoriel = '2', id_validateur = $id_user_session where id_tutoriel = $id_tuto;");
    	}
    	locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** depublier_tuto ***********************
    case "depublier_tuto" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$depublier_tuto = mysql_query("update `" . $tblprefix . "tutoriels` set publie_tutoriel = '1' where id_tutoriel = $id_tuto;");
    	}
    	locationhref_admin("?inc=edit_tutorials");
    } break;
    
    // ****************** open_tuto ****************************
    case "open_tuto" : {
    	
		 goback_lien("?inc=edit_tutorials");
     $select_tuto = mysql_query("select titre_tutoriel, notes_tutoriel from `" . $tblprefix . "tutoriels` where id_tutoriel = $id_tuto;");
     if (mysql_num_rows($select_tuto) == 1) {
    	$titre_tuto = mysql_result($select_tuto,0,0);
    	$notes_tuto = mysql_result($select_tuto,0,1);

			$titre_tuto = html_ent($titre_tuto);
			$titre_tuto = readmore($titre_tuto,$max_len2);

			echo "<h2><center>".tutoriel." : <u>".$titre_tuto."</u></center></h2>";
			
 			echo "<table border=\"0\"><tr><td><a href=\"?inc=edit_tutorials&do=create_partie&id_tuto=".$id_tuto."\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=edit_tutorials&do=create_partie&id_tuto=".$id_tuto."\"><b>".creer_partie."</b></a></td></tr></table><br />";

  $select_my_parties = mysql_query("select id_partie, titre_partie, publie_partie, ordre_partie from `" . $tblprefix . "parties` where id_tutoriel = $id_tuto order by ordre_partie;");
	$nbr_trouve = mysql_num_rows($select_my_parties);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}

    	$select_my_parties_limit = mysql_query("select id_partie, titre_partie, publie_partie, ordre_partie from `" . $tblprefix . "parties` where id_tutoriel = $id_tuto order by ordre_partie limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".partie."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				$i_ordre = ($page - 1) * $nbr_resultats + 1;
				while($partie = mysql_fetch_row($select_my_parties_limit)){
					
					$titre_partie = html_ent($partie[1]);
					$titre_partie = readmore($titre_partie,$max_len2);
					
					if ($partie[2] == 1)
						$color = "green";
					else $color = "red";
						
					echo "<tr>\n";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=open_partie&id_part=".$partie[0]."\" title=\"".ouvrir_partie."\"><font color=\"".$color."\"><u><b>".$titre_partie."</b></u></font></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=update_partie&id_part=".$partie[0]."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_partie&id_part=".$partie[0]."&key=".$key."','".confirm_supprimer_partie."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?tutorial=".$id_tuto."#".$partie[0]."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
					$partie_precedent = mysql_query ("select id_partie from `" . $tblprefix . "parties` where ordre_partie < $partie[3] and id_tutoriel = $id_tuto order by ordre_partie desc;");
					if (mysql_num_rows($partie_precedent) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderup_partie&id_part=".$partie[0]."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else
						echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					echo "<b> ".$i_ordre." </b>";
					$i_ordre++;
					$partie_suivant = mysql_query ("select id_partie from `" . $tblprefix . "parties` where ordre_partie > $partie[3] and id_tutoriel = $id_tuto order by ordre_partie;");
					if (mysql_num_rows($partie_suivant) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderdown_partie&id_part=".$partie[0]."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
					echo "</td>";

					if ($partie[2] == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=depublier_partie&id_part=".$partie[0]."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=publier_partie&id_part=".$partie[0]."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."&l=".$page_precedente."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."&l=".$page_precedente."\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."&l=".$i."\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."&l=".$page_suivante."\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."&l=".$page_suivante."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
		
    	} else echo pas_de_partie."<br />";
    	
    	if (!empty($notes_tuto))
    		echo "<br /><u><b>".notes_tuto2."</b></u><br />".$notes_tuto."<hr />";

     } else locationhref_admin("?inc=edit_tutorials");
    } break;
    
    // ****************** create_partie ************************
    case "create_partie" : {
    	if (isset($_POST['partie_titre'])){
    	 $partie_titre = trim($_POST['partie_titre']);
    	 if (!empty($partie_titre)){
    		$partie_titre = escape_string($partie_titre);
    		$select_partie_titre = mysql_query("select id_partie from `" . $tblprefix . "parties` where titre_partie = '$partie_titre' and id_tutoriel= $id_tuto;");
 					if (mysql_num_rows($select_partie_titre) == 0) {
 						$select_max_order = mysql_query("select max(ordre_partie) from `" . $tblprefix . "parties` where id_tutoriel = $id_tuto;");
 						if (mysql_num_rows($select_max_order) == 1)
 							$ordre_partie = mysql_result($select_max_order,0) + 1;
 						else $ordre_partie = 1;
 						$insertpartie = "INSERT INTO `" . $tblprefix . "parties` VALUES (NULL,$id_tuto,'$partie_titre','','','','1',$ordre_partie);";
	          mysql_query($insertpartie,$connect);
	          
	          $date_modification_tuto = time();
	          $update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto where id_tutoriel = $id_tuto;");

	          $select_this_partie = mysql_query("select id_partie from `" . $tblprefix . "parties` where titre_partie = '$partie_titre' and id_tutoriel = $id_tuto;");
	          if (mysql_num_rows($select_this_partie) == 1) {
	          	$id_part = mysql_result($select_this_partie,0);
	          	$link = "?inc=edit_tutorials&do=update_partie&id_part=".$id_part;
						}
	          else $link = "?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto;
	          redirection(partie_cree,$link,3,"tips",1);
 					} else goback(titre_existe,2,"error",1);
    	 } else goback(titre_vide,2,"error",1);
    	}
    	else {
    		goback_button();
    		echo "<form method=\"POST\" action=\"\">";
	    	echo "<p><u><b>" .titre_partie. "</b></u><br /><br /><input name=\"partie_titre\" type=\"text\" size=\"50\" maxlength=\"100\"></p>";
	    	echo "<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
   		}
    } break;

    // ****************** update_partie ************************
    case "update_partie" : {
     	
    		$select_partie_complet = mysql_query("select * from `" . $tblprefix . "parties` where id_partie = $id_part;");
    		if (mysql_num_rows($select_partie_complet) == 1) {
    			$partie = mysql_fetch_row($select_partie_complet);
    			
    			$titre_partie = html_ent($partie[2]);
    			$objectifs_partie = html_ent($partie[3]);
    			$introduction_partie = html_ent($partie[4]);
    			$conclusion_partie = html_ent($partie[5]);
    			
    			$id_tuto = $partie[1];
    			
    				if (!empty($_POST['send'])){
    					$partie_titre = trim($_POST['partie_titre']);
    					if (!empty($partie_titre)){
    						$partie_titre = escape_string($partie_titre);
    						$partie_objectifs = escape_string(trim($_POST['partie_objectifs']));
    						$partie_introduction = escape_string(trim($_POST['partie_introduction']));
    						$partie_conclusion = escape_string(trim($_POST['partie_conclusion']));
    						$select_partie_titre = mysql_query("select id_partie from `" . $tblprefix . "parties` where titre_partie = '$partie_titre' and id_tutoriel = $id_tuto;");
 								if ((mysql_num_rows($select_partie_titre) == 0) || (mysql_num_rows($select_partie_titre) == 1 && mysql_result($select_partie_titre,0) == $id_part)) {
 									$update_partie = "update `" . $tblprefix . "parties` SET titre_partie = '$partie_titre', objectifs_partie = '$partie_objectifs', introduction_partie = '$partie_introduction', conclusion_partie = '$partie_conclusion' where id_partie = $id_part;";
 									mysql_query($update_partie);
 									
	          			$date_modification_tuto = time();
	          			$update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto where id_tutoriel = $id_tuto;");
 									
 									redirection(partie_modifie,"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."",3,"tips",1);
 								} else goback(titre_existe,2,"error",1);
    					} else goback(titre_vide,2,"error",1);
    				}
    				else {
    					goback_button();
    					echo "<form method=\"POST\" action=\"\">";
    					echo "<p><u><b><font color=\"red\">*</font> " .titre_partie. "</b></u><br /><br /><input name=\"partie_titre\" type=\"text\" size=\"50\" maxlength=\"100\" value=\"".$titre_partie."\"></p>";							
    					echo "<br /><p><u><b>" .objectifs_partie. "</b></u><br />".remarque_update_objectifs."<br /><br /><textarea name=\"partie_objectifs\" cols=\"100\" rows=\"10\">".$objectifs_partie."</textarea></p>";
    					echo "<br /><p><u><b>" .introduction_partie. "</b></u><br /><br /><textarea name=\"partie_introduction\" cols=\"100\" rows=\"10\">".$introduction_partie."</textarea></p>";
    					echo "<br /><p><u><b>" .conclusion_partie. "</b></u><br /><br /><textarea name=\"partie_conclusion\" cols=\"100\" rows=\"10\">".$conclusion_partie."</textarea></p>";
  					
    					echo "<input type=\"hidden\" name=\"send\" value=\"ok\">";
    					echo "<br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    					ckeditor_replace($language,"partie_objectifs");
    					ckeditor_replace($language,"partie_introduction");
    					ckeditor_replace($language,"partie_conclusion");
    				}
    		} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** delete_partie ************************
    case "delete_partie" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idtuto = mysql_query("select `" . $tblprefix . "tutoriels`.id_tutoriel from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and id_partie = $id_part;");
    		$id_tutoriel = mysql_result($select_user_idtuto,0,0);
    		$select_chapitres = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_part;");
    		if (mysql_num_rows($select_chapitres) > 0){
    			while($chapitre = mysql_fetch_row($select_chapitres)){
    				$delete_bloc = mysql_query("delete from `" . $tblprefix . "blocs` where id_chapitre = $chapitre[0];");
						$delete_qcm = mysql_query("delete from `" . $tblprefix . "qcm` where id_chapitre = $chapitre[0];");
    				$select_devoirs_rendus = mysql_query("select id_devoir from `" . $tblprefix . "devoirs` where id_chapitre = $chapitre[0];");
    				if (mysql_num_rows($select_devoirs_rendus) > 0){
    					while($devoir = mysql_fetch_row($select_devoirs_rendus)){
    						$delete_devoirs_rendus = mysql_query("delete from `" . $tblprefix . "devoirs_rendus` where id_devoir = $devoir[0];");
    						$delete_devoirs_notes = mysql_query("delete from `" . $tblprefix . "devoirs_notes` where id_devoir = $devoir[0];");
    					}
						}
						$delete_devoir = mysql_query("delete from `" . $tblprefix . "devoirs` where id_chapitre = $chapitre[0];");
    			}
				}
				$delete_chapitre = mysql_query("delete from `" . $tblprefix . "chapitres` where id_partie = $id_part;");					
    		$delete_partie = mysql_query("delete from `" . $tblprefix . "parties` where id_partie = $id_part;");
				
				locationhref_admin("?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tutoriel);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderup_partie **********************
    case "orderup_partie" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$cette_partie = mysql_query ("select id_tutoriel, ordre_partie from `" . $tblprefix . "parties` where id_partie = $id_part;");
				if (mysql_num_rows($cette_partie) == 1) {
					$id_tutoriel = mysql_result($cette_partie,0,0);
					$ordre_partie = mysql_result($cette_partie,0,1);

    			$partie_precedente = mysql_query ("select id_partie, ordre_partie from `" . $tblprefix . "parties` where ordre_partie < $ordre_partie and id_tutoriel = $id_tutoriel order by ordre_partie desc;");
					if (mysql_num_rows($partie_precedente) > 0) {
						$idpartie_precedente = mysql_result($partie_precedente,0,0);
						$ordrepartie_precedente = mysql_result($partie_precedente,0,1);
							
						$order_this_partie = mysql_query("update `" . $tblprefix . "parties` set ordre_partie = $ordrepartie_precedente where id_partie = $id_part;");
						$order_partie_precedente = mysql_query("update `" . $tblprefix . "parties` set ordre_partie = $ordre_partie where id_partie = $idpartie_precedente;");
					}
    			locationhref_admin("?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tutoriel);
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderdown_partie **********************
    case "orderdown_partie" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$cette_partie = mysql_query ("select id_tutoriel, ordre_partie from `" . $tblprefix . "parties` where id_partie = $id_part;");
				if (mysql_num_rows($cette_partie) == 1) {
					$id_tutoriel = mysql_result($cette_partie,0,0);
					$ordre_partie = mysql_result($cette_partie,0,1);
			
    			$partie_suivante = mysql_query ("select id_partie, ordre_partie from `" . $tblprefix . "parties` where ordre_partie > $ordre_partie and id_tutoriel = $id_tutoriel order by ordre_partie;");
					if (mysql_num_rows($partie_suivante) > 0) {
						$idpartie_suivante = mysql_result($partie_suivante,0,0);
						$ordrepartie_suivante = mysql_result($partie_suivante,0,1);
							
						$order_this_partie = mysql_query("update `" . $tblprefix . "parties` set ordre_partie = $ordrepartie_suivante where id_partie = $id_part;");
						$order_partie_suivante = mysql_query("update `" . $tblprefix . "parties` set ordre_partie = $ordre_partie where id_partie = $idpartie_suivante;");
					}
    			locationhref_admin("?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tutoriel);
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;
 
    // ****************** publier_partie ***********************
    case "publier_partie" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idtuto = mysql_query("select `" . $tblprefix . "tutoriels`.id_tutoriel from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and id_partie = $id_part;");
    		$id_tutoriel = mysql_result($select_user_idtuto,0,0);
    		$publier_partie = mysql_query("update `" . $tblprefix . "parties` set publie_partie = '1' where id_partie = $id_part;");
    		locationhref_admin("?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tutoriel);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;
    
    // ****************** depublier_partie *********************
    case "depublier_partie" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idtuto = mysql_query("select `" . $tblprefix . "tutoriels`.id_tutoriel from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and id_partie = $id_part;");
    		$id_tutoriel = mysql_result($select_user_idtuto,0,0);
    		$depublier_partie = mysql_query("update `" . $tblprefix . "parties` set publie_partie = '0' where id_partie = $id_part;");
    		locationhref_admin("?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tutoriel);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** open_partie **************************
    case "open_partie" : {
     if (!empty($_POST['send'])){
    		$select_chapitre_ordre = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_part;");
    		if (mysql_num_rows($select_chapitre_ordre) > 0){
    			while($thechap = mysql_fetch_row($select_chapitre_ordre)){
    				$id_chap_order = $thechap[0];
    				$var_input_order = "order_".$id_chap_order;
						if (isset($_POST[$var_input_order]) && !empty($_POST[$var_input_order])){
							$order_this_chap = intval($_POST[$var_input_order]);
							$order_this_chapitre = mysql_query("update `" . $tblprefix . "chapitres` set ordre_chapitre = $order_this_chap where id_chapitre = $id_chap_order;");
						}
    			}
    		}
    		locationhref_admin("?inc=edit_tutorials&do=open_partie&id_part=".$id_part);
     }
     else {
     $select_tuto = mysql_query("select `" . $tblprefix . "parties`.id_tutoriel, titre_tutoriel, titre_partie from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and id_partie = $id_part;");
     if (mysql_num_rows($select_tuto) == 1) {
    	$id_tuto = mysql_result($select_tuto,0,0);
    	$titre_tuto = html_ent(mysql_result($select_tuto,0,1));
    	$titre_tuto = readmore($titre_tuto,$max_len2);
			$titre_part = html_ent(mysql_result($select_tuto,0,2));
			$titre_part = readmore($titre_part,$max_len2);
    	goback_lien("?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto);

			echo "<b>".tutoriel." : <a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."\">".$titre_tuto."</a></b><br />";
			echo "<h2><center>".partie." : <u>".$titre_part."</u></center></h2>";
						
 			echo "<table border=\"0\"><tr><td><a href=\"?inc=edit_tutorials&do=create_chapitre&id_part=".$id_part."\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=edit_tutorials&do=create_chapitre&id_part=".$id_part."\"><b>".creer_chap."</b></a></td></tr></table><br />";

  $select_my_chapitres = mysql_query("select id_chapitre, titre_chapitre, publie_chapitre, ordre_chapitre, grade_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_part order by ordre_chapitre;");
	$nbr_trouve = mysql_num_rows($select_my_chapitres);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}

    	$select_my_chapitres_limit = mysql_query("select id_chapitre, titre_chapitre, publie_chapitre, ordre_chapitre, grade_chapitre from `" . $tblprefix . "chapitres` where id_partie = $id_part order by ordre_chapitre limit $limit, $nbr_resultats;");
				echo "<form method=\"POST\" action=\"\">";
    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".chapitre."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".access_chapitre."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				while($chapitre = mysql_fetch_row($select_my_chapitres_limit)){
					
					$titre_chapitre = html_ent($chapitre[1]);
					$titre_chapitre = readmore($titre_chapitre,$max_len2);
					
					if ($chapitre[4] == "*")
						$acces_chap = acces_ouvert;
					else if ($chapitre[4] == "0")
						$acces_chap = all_registered_learners;
					else {
						$acces_chap = acces_grades." :<br />";
						$tab_acces_chap = explode("-",trim($chapitre[4],"-"));
						if (!empty($tab_acces_chap[0])){
							$tab_all_grades = array("A","B","C","D","E");
							$tab_rech_count = array();
							foreach ($tab_acces_chap as $un_grade){
								if (in_array($un_grade,$tab_all_grades)){
									$acces_chap .= "<u>".$un_grade."</u>, ";
									$tab_rech_count[] = "'".$un_grade."'";
								}
							}
							$acces_chap = substr($acces_chap,0,-2);
							$chaine_grades_req = implode(",",$tab_rech_count);
							$select_count_apps_grade = mysql_query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where active_apprenant = '1' and grade_apprenant in ($chaine_grades_req);");
							$nbr_apps_grade = mysql_result($select_count_apps_grade,0);
							$acces_chap.= " (".$nbr_apps_grade." ".number_active_learners.")";
    				}
					}

					if ($chapitre[2] == 1)
						$color = "green";
					else $color = "red";
						
					echo "<tr>\n";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$chapitre[0]."\" title=\"".ouvrir_chapitre."\"><font color=\"".$color."\"><u><b>".$titre_chapitre."</b></u></font></a></td>";
					
					echo "\n<td class=\"affichage_table\"><b>".$acces_chap."</b></td>";
					
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=update_chapitre&id_chap=".$chapitre[0]."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
				
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_chapitre&id_chap=".$chapitre[0]."&key=".$key."','".confirm_supprimer_chapitre."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					
					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?chapter=".$chapitre[0]."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";
					
					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
					$chapitre_precedent = mysql_query ("select id_chapitre from `" . $tblprefix . "chapitres` where ordre_chapitre < $chapitre[3] and id_partie = $id_part order by ordre_chapitre desc;");
					if (mysql_num_rows($chapitre_precedent) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderup_chapitre&id_chap=".$chapitre[0]."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else
						echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					
					echo "<b> <input name=\"order_".$chapitre[0]."\" value=\"".$chapitre[3]."\" type=\"text\" size=\"1\" maxlength=\"3\"> </b>";

					$chapitre_suivant = mysql_query ("select id_chapitre from `" . $tblprefix . "chapitres` where ordre_chapitre > $chapitre[3] and id_partie = $id_part order by ordre_chapitre;");
					if (mysql_num_rows($chapitre_suivant) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderdown_chapitre&id_chap=".$chapitre[0]."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
					echo "</td>";
					
					if ($chapitre[2] == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=depublier_chapitre&id_chap=".$chapitre[0]."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=publier_chapitre&id_chap=".$chapitre[0]."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";
				echo "<p align=\"center\"><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .reorder_chapters. "\"></p></form>";
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_partie&id_part=".$id_part."&l=".$page_precedente."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_tutorials&do=open_partie&id_part=".$id_part."&l=".$page_precedente."\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=edit_tutorials&do=open_partie&id_part=".$id_part."&l=".$i."\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_partie&id_part=".$id_part."&l=".$page_suivante."\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_tutorials&do=open_partie&id_part=".$id_part."&l=".$page_suivante."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
		
    	} else echo pas_de_chapitre;
     } else locationhref_admin("?inc=edit_tutorials");
     }
    } break;

    // ****************** create_chapitre **********************
    case "create_chapitre" : {
    	if (isset($_POST['chapitre_titre'])){
    	 	$chapitre_titre = trim($_POST['chapitre_titre']);
    		if (!empty($chapitre_titre)){
    			$chapitre_titre = escape_string($chapitre_titre);
    			$select_chapitre_titre = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where titre_chapitre = '$chapitre_titre' and id_partie = $id_part;");
 					if (mysql_num_rows($select_chapitre_titre) == 0) {
 						$select_max_order = mysql_query("select max(ordre_chapitre) from `" . $tblprefix . "chapitres` where id_partie = $id_part;");
 						if (mysql_num_rows($select_max_order) == 1)
 							$ordre_chapitre = mysql_result($select_max_order,0) + 1;
 						else $ordre_chapitre = 1;
 						
 						$time_insert_chapitre = time();
 						$insertchapitre = "INSERT INTO `" . $tblprefix . "chapitres` VALUES (NULL,$id_part,'$chapitre_titre','',0,'1',$ordre_chapitre,$time_insert_chapitre,$time_insert_chapitre,0,0,'*');";
	          mysql_query($insertchapitre,$connect);

						$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties` where id_partie = $id_part;");
						$id_tuto = mysql_result($select_tuto_id,0,0);
	          $date_modification_tuto = time();
	          $update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto where id_tutoriel = $id_tuto;");
	          
	          $select_this_chapitre = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where titre_chapitre = '$chapitre_titre' and date_creation_chapitre = $time_insert_chapitre;");
	          if (mysql_num_rows($select_this_chapitre) == 1) {
	          	$id_chap = mysql_result($select_this_chapitre,0);
	          	$link = "?inc=edit_tutorials&do=update_chapitre&id_chap=".$id_chap;
						}
	          else $link = "?inc=edit_tutorials&do=open_partie&id_part=".$id_part;
	          redirection(chapitre_cree,$link,3,"tips",1);
 					} else goback(titre_existe,2,"error",1);
    		} else goback(titre_vide,2,"error",1);
    	}
    	else {
    			goback_button();
    			echo "<form method=\"POST\" action=\"\">";
	    		echo "<p><u><b>" .titre_chapitre. "</b></u><br /><br /><input name=\"chapitre_titre\" type=\"text\" size=\"50\" maxlength=\"100\"></p>";
	    		echo "<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
   		}
    } break;

    // ****************** update_chapitre **********************
    case "update_chapitre" : {
     	
    		$select_chapitre_complet = mysql_query("select * from `" . $tblprefix . "chapitres` where id_chapitre = $id_chap;");
    		if (mysql_num_rows($select_chapitre_complet) == 1) {
    			$chapitre = mysql_fetch_row($select_chapitre_complet);
    			
    			$titre_chapitre = html_ent($chapitre[2]);
    			$objectifs_chapitre = html_ent($chapitre[3]);
    			$grade_chap = $chapitre[11];
    			
    			$id_partie = $chapitre[1];

    				if (!empty($_POST['send'])){
    					$chapitre_titre = trim($_POST['chapitre_titre']);
    					if (!empty($chapitre_titre)){
    						$chapitre_titre = escape_string($chapitre_titre);
    						$chapitre_objectifs = escape_string(trim($_POST['chapitre_objectifs']));

								if ($_POST['acces'] == "learner")
    							$chap_acces = "0";
    						else if ($_POST['acces'] == "grade"){
    							if (!empty($_POST['grades']))
    								$chap_acces = "-".implode("-",$_POST['grades'])."-";
    							else $chap_acces = "0";
    						}
    						else $chap_acces = "*";

    						$select_chapitre_titre = mysql_query("select id_chapitre from `" . $tblprefix . "chapitres` where titre_chapitre = '$chapitre_titre' and id_partie = $id_partie;");
 								if ((mysql_num_rows($select_chapitre_titre) == 0) || (mysql_num_rows($select_chapitre_titre) == 1 && mysql_result($select_chapitre_titre,0) == $id_chap)) {
 									$update_chapitre = "update `" . $tblprefix . "chapitres` SET titre_chapitre = '$chapitre_titre', objectifs_chapitre = '$chapitre_objectifs', date_modification_chapitre = ".time().", grade_chapitre = '$chap_acces' where id_chapitre = $id_chap;";
 									mysql_query($update_chapitre);
 									
									$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties` where id_partie = $id_partie;");
									$id_tuto = mysql_result($select_tuto_id,0,0);
	          			$date_modification_tuto = time();
	          			$update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto where id_tutoriel = $id_tuto;");

 									redirection(chapitre_modifie,"?inc=edit_tutorials&do=open_partie&id_part=".$id_partie."",3,"tips",1);
 								} else goback(titre_existe,2,"error",1);
    					} else goback(titre_vide,2,"error",1);
    				}
    				else {
    					goback_button();
    					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";
    					echo "<form method=\"POST\" action=\"\">";
    					echo "<p><u><b><font color=\"red\">*</font> " .titre_chapitre. "</b></u><br /><br /><input name=\"chapitre_titre\" type=\"text\" size=\"50\" maxlength=\"100\" value=\"".$titre_chapitre."\"></p>";							

							echo "<p><u><b><font color=\"red\">*</font> ".access_chapitre."</b></u><br /><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"all\" onclick=\"disabled_select('grades',true)\"";
							if ($grade_chap == "*")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_ouvert."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"learner\" onclick=\"disabled_select('grades',true)\"";
							if ($grade_chap == "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_apprenants."</b><br />";
							echo "\n<input name=\"acces\" type=\"radio\" value=\"grade\" onclick=\"disabled_select('grades',false)\"";
							if ($grade_chap != "*" && $grade_chap != "0")
							 echo " checked=\"checked\"";
							echo " /><b>".acces_grades." :</b>";
							$tab_grades = explode("-",$grade_chap);
					 		echo "<table border=\"0\"><tr><td align=\"center\">".grade."(".number_active_learners.")<br />";
							echo "<select size=\"5\" name=\"grades[]\" id=\"grades\" multiple=\"multiple\">";
							$tab_all_grades = array("A","B","C","D","E");
							foreach ($tab_all_grades as $this_grade){
								$select_count_apps_grade = mysql_query("select count(id_apprenant) from `" . $tblprefix . "apprenants` where active_apprenant = '1' and grade_apprenant = '$this_grade';");
								$nbr_apps_grade = mysql_result($select_count_apps_grade,0);
    						echo "\n<option value=\"".$this_grade."\"";
    						if (in_array($this_grade,$tab_grades))
    							echo " selected=\"selected\"";
    						echo ">".$this_grade." (".$nbr_apps_grade.")</option>";
							}
							echo "\n</select><br />";
							echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('grades',false)\" />";
							echo "<br />".hold_down_ctrl."</td></tr></table>";

    					echo "<br /><p><u><b>" .objectifs_chapitre. "</b></u><br />".remarque_update_objectifs."<br /><br /><textarea name=\"chapitre_objectifs\" cols=\"100\" rows=\"10\">".$objectifs_chapitre."</textarea></p>";

    					echo "<input type=\"hidden\" name=\"send\" value=\"ok\">";
    					echo "<br /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    					ckeditor_replace($language,"chapitre_objectifs");
    					if ($grade_chap == "*" || $grade_chap == "0")
    						echo "<script type=\"text/javascript\">disabled_select('grades',true);</script>";
    				}
    		} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** delete_chapitre **********************
    case "delete_chapitre" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idpartie = mysql_query("select `" . $tblprefix . "parties`.id_partie from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
    		$id_partie = mysql_result($select_user_idpartie,0,0);
				$delete_bloc = mysql_query("delete from `" . $tblprefix . "blocs` where id_chapitre = $id_chap;");
				$delete_qcm = mysql_query("delete from `" . $tblprefix . "qcm` where id_chapitre = $id_chap;");

    		$select_devoirs_rendus = mysql_query("select id_devoir from `" . $tblprefix . "devoirs` where id_chapitre = $id_chap;");
    		if (mysql_num_rows($select_devoirs_rendus) > 0){
    			while($devoir = mysql_fetch_row($select_devoirs_rendus)){
    				$delete_devoirs_rendus = mysql_query("delete from `" . $tblprefix . "devoirs_rendus` where id_devoir = $devoir[0];");
    				$delete_devoirs_notes = mysql_query("delete from `" . $tblprefix . "devoirs_notes` where id_devoir = $devoir[0];");
    			}
				}
				$delete_devoir = mysql_query("delete from `" . $tblprefix . "devoirs` where id_chapitre = $id_chap;");

    		$delete_chapitre = mysql_query("delete from `" . $tblprefix . "chapitres` where id_chapitre = $id_chap;");
				
				locationhref_admin("?inc=edit_tutorials&do=open_partie&id_part=".$id_partie);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderup_chapitre *********************
    case "orderup_chapitre" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_chapitre = mysql_query ("select id_tutoriel, `" . $tblprefix . "parties`.id_partie, ordre_chapitre from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "chapitres`.id_partie = `" . $tblprefix . "parties`.id_partie and id_chapitre = $id_chap;");
				if (mysql_num_rows($ce_chapitre) == 1) {
					$id_tutoriel = mysql_result($ce_chapitre,0,0);
					$id_partie = mysql_result($ce_chapitre,0,1);
					$ordre_chapitre = mysql_result($ce_chapitre,0,2);

    			$chapitre_precedent = mysql_query ("select id_chapitre, ordre_chapitre from `" . $tblprefix . "chapitres` where ordre_chapitre < $ordre_chapitre and id_partie = $id_partie order by ordre_chapitre desc;");
					if (mysql_num_rows($chapitre_precedent) > 0) {
						$idchapitre_precedent = mysql_result($chapitre_precedent,0,0);
						$ordrechapitre_precedent = mysql_result($chapitre_precedent,0,1);
							
						$order_this_chapitre = mysql_query("update `" . $tblprefix . "chapitres` set ordre_chapitre = $ordrechapitre_precedent where id_chapitre = $id_chap;");
						$order_chapitre_precedent = mysql_query("update `" . $tblprefix . "chapitres` set ordre_chapitre = $ordre_chapitre where id_chapitre = $idchapitre_precedent;");
					}
    			locationhref_admin("?inc=edit_tutorials&do=open_partie&id_part=".$id_partie);
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderdown_chapitre *********************
    case "orderdown_chapitre" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$ce_chapitre = mysql_query ("select id_tutoriel, `" . $tblprefix . "parties`.id_partie, ordre_chapitre from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "chapitres`.id_partie = `" . $tblprefix . "parties`.id_partie and id_chapitre = $id_chap;");
				if (mysql_num_rows($ce_chapitre) == 1) {
					$id_tutoriel = mysql_result($ce_chapitre,0,0);
					$id_partie = mysql_result($ce_chapitre,0,1);
					$ordre_chapitre = mysql_result($ce_chapitre,0,2);

    			$chapitre_suivant = mysql_query ("select id_chapitre, ordre_chapitre from `" . $tblprefix . "chapitres` where ordre_chapitre > $ordre_chapitre and id_partie = $id_partie order by ordre_chapitre;");
					if (mysql_num_rows($chapitre_suivant) > 0) {
						$idchapitre_suivant = mysql_result($chapitre_suivant,0,0);
						$ordrechapitre_suivant = mysql_result($chapitre_suivant,0,1);
							
						$order_this_chapitre = mysql_query("update `" . $tblprefix . "chapitres` set ordre_chapitre = $ordrechapitre_suivant where id_chapitre = $id_chap;");
						$order_chapitre_suivant = mysql_query("update `" . $tblprefix . "chapitres` set ordre_chapitre = $ordre_chapitre where id_chapitre = $idchapitre_suivant;");
					}
					locationhref_admin("?inc=edit_tutorials&do=open_partie&id_part=".$id_partie);
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** publier_chapitre *********************
    case "publier_chapitre" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idpartie = mysql_query("select `" . $tblprefix . "parties`.id_partie from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
    		$id_partie = mysql_result($select_user_idpartie,0,0);
    		$publier_chapitre = mysql_query("update `" . $tblprefix . "chapitres` set publie_chapitre = '1' where id_chapitre = $id_chap;");
				locationhref_admin("?inc=edit_tutorials&do=open_partie&id_part=".$id_partie);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** depublier_chapitre *******************
    case "depublier_chapitre" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idpartie = mysql_query("select `" . $tblprefix . "parties`.id_partie from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
    		$id_partie = mysql_result($select_user_idpartie,0,0);
    		$depublier_chapitre = mysql_query("update `" . $tblprefix . "chapitres` set publie_chapitre = '0' where id_chapitre = $id_chap;");
				locationhref_admin("?inc=edit_tutorials&do=open_partie&id_part=".$id_partie);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** open_chapitre ************************
    case "open_chapitre" : {
     $select_tuto = mysql_query("select `" . $tblprefix . "chapitres`.id_partie, `" . $tblprefix . "tutoriels`.id_tutoriel, titre_tutoriel, titre_partie, titre_chapitre from `" . $tblprefix . "tutoriels`, `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "tutoriels`.id_tutoriel = `" . $tblprefix . "parties`.id_tutoriel and `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
     if (mysql_num_rows($select_tuto) == 1) {
    	$id_part = mysql_result($select_tuto,0,0);
    	$id_tuto = mysql_result($select_tuto,0,1);
    	$titre_tuto = html_ent(mysql_result($select_tuto,0,2));
    	$titre_tuto = readmore($titre_tuto,$max_len2);
    	$titre_part = html_ent(mysql_result($select_tuto,0,3));
    	$titre_part = readmore($titre_part,$max_len2);
			$titre_chap = html_ent(mysql_result($select_tuto,0,4));
			$titre_chap = readmore($titre_chap,$max_len2);
			
			goback_lien("?inc=edit_tutorials&do=open_partie&id_part=".$id_part);

			if (isset($_GET['t']) && ctype_digit($_GET['t']))
				$page2 = intval($_GET['t']);
			else $page2 = 1;

			if (isset($_GET['d']) && ctype_digit($_GET['d']))
				$page3 = intval($_GET['d']);
			else $page3 = 1;
			
			echo "<b>".tutoriel." : <a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$id_tuto."\">".$titre_tuto."</a></b><br />";
			echo "<b>".partie." : <a href=\"?inc=edit_tutorials&do=open_partie&id_part=".$id_part."\">".$titre_part."</a></b><br />";
			echo "<h2><center>".chapitre." : <u>".$titre_chap."</u></center></h2>";

			// ***************** blocs ********************
			echo "<hr /><h2><a name=\"blocs\"><font color=\"black\"><u>".blocs."</u></font></a></h2>";
 			echo "<table border=\"0\"><tr><td><a href=\"?inc=edit_tutorials&do=create_bloc&id_chap=".$id_chap."\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=edit_tutorials&do=create_bloc&id_chap=".$id_chap."\"><b>".creer_bloc."</b></a></td></tr></table><br />";

  $select_my_blocs = mysql_query("select id_bloc, titre_bloc, publie_bloc, ordre_bloc from `" . $tblprefix . "blocs` where id_chapitre = $id_chap order by ordre_bloc;");
	$nbr_trouve = mysql_num_rows($select_my_blocs);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}

    	$select_my_blocs_limit = mysql_query("select id_bloc, titre_bloc, publie_bloc, ordre_bloc from `" . $tblprefix . "blocs` where id_chapitre = $id_chap order by ordre_bloc limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".bloc."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				$j_ordre = ($page - 1) * $nbr_resultats + 1;
				while($bloc = mysql_fetch_row($select_my_blocs_limit)){
					
					$titre_bloc = html_ent($bloc[1]);
					$titre_bloc = readmore($titre_bloc,$max_len2);

					if ($bloc[2] == 1)
						$color = "green";
					else $color = "red";
						
					echo "<tr>\n";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$titre_bloc."</b></font></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=update_bloc&id_bloc=".$bloc[0]."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
				
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_bloc&id_bloc=".$bloc[0]."&key=".$key."','".confirm_supprimer_bloc."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					
					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?chapter=".$id_chap."#".$bloc[0]."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";
					
					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
					$bloc_precedent = mysql_query ("select id_bloc from `" . $tblprefix . "blocs` where ordre_bloc < $bloc[3] and id_chapitre = $id_chap order by ordre_bloc desc;");
					if (mysql_num_rows($bloc_precedent) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderup_bloc&id_bloc=".$bloc[0]."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else
						echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					echo "<b> ".$j_ordre." </b>";
					$j_ordre++;
					$bloc_suivant = mysql_query ("select id_bloc from `" . $tblprefix . "blocs` where ordre_bloc > $bloc[3] and id_chapitre = $id_chap order by ordre_bloc;");
					if (mysql_num_rows($bloc_suivant) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderdown_bloc&id_bloc=".$bloc[0]."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
					echo "</td>";
					
					if ($bloc[2] == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=depublier_bloc&id_bloc=".$bloc[0]."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=publier_bloc&id_bloc=".$bloc[0]."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&l=".$page_precedente."&t=".$page2."&d=".$page3."#blocs\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&l=".$page_precedente."&t=".$page2."&d=".$page3."#blocs\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&l=".$i."&t=".$page2."&d=".$page3."#blocs\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&l=".$page_suivante."&t=".$page2."&d=".$page3."#blocs\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&l=".$page_suivante."&t=".$page2."&d=".$page3."#blocs\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
		
    	} else echo pas_de_bloc;

    	// ***************** QCM ********************
    	
			echo "<br /><hr /><h2><a name=\"qcm\"><font color=\"black\"><u>".qcm."</u></font></a></h2>";	
 			echo "<table border=\"0\"><tr><td><a href=\"?inc=edit_tutorials&do=create_qcm&id_chap=".$id_chap."\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=edit_tutorials&do=create_qcm&id_chap=".$id_chap."\"><b>".creer_qcm."</b></a></td></tr></table><br />";

    	$select_my_qcm = mysql_query("select * from `" . $tblprefix . "qcm` where id_chapitre = $id_chap order by ordre_qcm;");
			 $nbr_trouve = mysql_num_rows($select_my_qcm);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page2 <= $page_max && $page2 > 1 && $page_max > 1)
					$limit = ($page2 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page2 = 1;
				}

    	$select_my_qcm_limit = mysql_query("select * from `" . $tblprefix . "qcm` where id_chapitre = $id_chap order by ordre_qcm limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".question."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".reponse_correcte."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				$i_ordre = ($page2 - 1) * $nbr_resultats + 1;
				while($qcm = mysql_fetch_row($select_my_qcm_limit)){

					$titre_qcm = strip_tags($qcm[2]);
					$titre_qcm = readmore($titre_qcm,$max_len);
					
					$reponse_correcte = $qcm[$qcm[9]+2];
					$reponse_correcte = html_ent($reponse_correcte);
					$reponse_correcte = readmore($reponse_correcte,$max_len);
					
					if ($qcm[12] == 1)
						$color = "green";
					else $color = "red";
					
					echo "<tr>\n";
					echo "\n<td class=\"affichage_table\"><font color=\"".$color."\"><b>".$titre_qcm."</b></font></td>";
					
					echo "\n<td class=\"affichage_table\"><b>".$reponse_correcte."</b></font></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=update_qcm&id_qcm=".$qcm[0]."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
				
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_qcm&id_qcm=".$qcm[0]."&key=".$key."','".confirm_supprimer_qcm."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					
					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?chapter=".$id_chap."#qcm\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
					$qcm_precedent = mysql_query ("select id_qcm from `" . $tblprefix . "qcm` where ordre_qcm < $qcm[13] and id_chapitre = $id_chap order by ordre_qcm desc;");
					if (mysql_num_rows($qcm_precedent) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderup_qcm&id_qcm=".$qcm[0]."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else
						echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					echo "<b> ".$i_ordre." </b>";
					$i_ordre++;
					$qcm_suivant = mysql_query ("select id_qcm from `" . $tblprefix . "qcm` where ordre_qcm > $qcm[13] and id_chapitre = $id_chap order by ordre_qcm;");
					if (mysql_num_rows($qcm_suivant) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderdown_qcm&id_qcm=".$qcm[0]."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
					echo "</td>";
					
					if ($qcm[12] == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=depublier_qcm&id_qcm=".$qcm[0]."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=publier_qcm&id_qcm=".$qcm[0]."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page2 - 1;
			$page_suivante = $page2 + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&t=".$page_precedente."&l=".$page."&d=".$page3."#qcm\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&t=".$page_precedente."&l=".$page."&d=".$page3."#qcm\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page2) echo "<a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&t=".$i."&l=".$page."&d=".$page3."#qcm\">";
				echo "<b>".$i."</b>";
				if ($i != $page2) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&t=".$page_suivante."&l=".$page."&d=".$page3."#qcm\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&t=".$page_suivante."&l=".$page."&d=".$page3."#qcm\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
		
   } else echo pas_de_qcm;

    	// ***************** Homework ********************
    	
			echo "<br /><hr /><h2><a name=\"devoir\"><font color=\"black\"><u>".homework_assignments."</u></font></a></h2>";	
 			echo "<table border=\"0\"><tr><td><a href=\"?inc=edit_tutorials&do=create_devoir&id_chap=".$id_chap."\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=edit_tutorials&do=create_devoir&id_chap=".$id_chap."\"><b>".creer_devoir."</b></a></td></tr></table><br />";

    	$select_my_devoir = mysql_query("select * from `" . $tblprefix . "devoirs` where id_chapitre = $id_chap order by ordre_devoir;");
			 $nbr_trouve = mysql_num_rows($select_my_devoir);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page3 <= $page_max && $page3 > 1 && $page_max > 1)
					$limit = ($page3 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page3 = 1;
				}

    	$select_my_devoir_limit = mysql_query("select * from `" . $tblprefix . "devoirs` where id_chapitre = $id_chap order by ordre_devoir limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".titre_devoir."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".acces_devoir."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".date_publication."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".date_expiration."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".jours_restants."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				$d_ordre = ($page3 - 1) * $nbr_resultats + 1;
				while($devoir = mysql_fetch_row($select_my_devoir_limit)){

					$titre_devoir = html_ent($devoir[3]);
					$titre_devoir = readmore($titre_devoir,$max_len);
					
					$date_publication_devoir = set_date($dateformat,$devoir[5]);
					$date_expiration_devoir = set_date($dateformat,$devoir[6]);
							
					if ($devoir[6] > time()){
						if ($devoir[5] <= time()){
							$expiration_devoir = round(($devoir[6] - time()) / 60 / 60 / 24);
							$expiration_chaine = "<font color='green'>".$expiration_devoir."</font>";
						} else $expiration_chaine = "<font color='red'>".not_open_yet."</font>";
					} else $expiration_chaine = "<font color='red'>".expire."</font>";

					if ($devoir[2] == "*")
						$acces_devoir = all_registered_learners;
					else {
						$acces_devoir = classe." : ";
						$tab_acces_devoir = explode("-",trim($devoir[2],"-"));
						if (!empty($tab_acces_devoir[0])){
							$chaine_acces_devoir = implode(",",$tab_acces_devoir);
							$select_classes = mysql_query("select * from `" . $tblprefix . "classes` where id_classe in (".$chaine_acces_devoir.");");
							if (mysql_num_rows($select_classes) > 0){
    						while($classe = mysql_fetch_row($select_classes))
    							$acces_devoir .= "<u>".$classe[1]."</u>, ";
    					}
    				}
					}
					
					if ($devoir[7] == 1)
						$color = "green";
					else $color = "red";
					
					echo "<tr>\n";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=open_devoir&id_devoir=".$devoir[0]."\" title=\"".ouvrir_devoir."\"><font color=\"".$color."\"><u><b>".$titre_devoir."</b></u></font></a></td>";
					echo "\n<td class=\"affichage_table\"><b>".$acces_devoir."</b></td>";
					
					echo "\n<td class=\"affichage_table\"><b>".$date_publication_devoir."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$date_expiration_devoir."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$expiration_chaine."</b></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=update_devoir&id_devoir=".$devoir[0]."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_devoir&id_devoir=".$devoir[0]."&key=".$key."','".confirm_supprimer_devoir."\\n".confirm_supprimer_devoir2."\\n".confirm_supprimer_devoir3."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?chapter=".$id_chap."#devoir\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
					$devoir_precedent = mysql_query ("select id_devoir from `" . $tblprefix . "devoirs` where ordre_devoir < $devoir[8] and id_chapitre = $id_chap order by ordre_devoir desc;");
					if (mysql_num_rows($devoir_precedent) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderup_devoir&id_devoir=".$devoir[0]."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else
						echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					echo "<b> ".$d_ordre." </b>";
					$d_ordre++;
					$devoir_suivant = mysql_query ("select id_devoir from `" . $tblprefix . "devoirs` where ordre_devoir > $devoir[8] and id_chapitre = $id_chap order by ordre_devoir;");
					if (mysql_num_rows($devoir_suivant) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderdown_devoir&id_devoir=".$devoir[0]."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
					echo "</td>";
					
					if ($devoir[7] == 1)
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=depublier_devoir&id_devoir=".$devoir[0]."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";
					else
						echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=publier_devoir&id_devoir=".$devoir[0]."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page3 - 1;
			$page_suivante = $page3 + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&d=".$page_precedente."&l=".$page."&t=".$page2."#devoir\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&d=".$page_precedente."&l=".$page."&t=".$page2."#devoir\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page3) echo "<a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&d=".$i."&l=".$page."&t=".$page2."#devoir\">";
				echo "<b>".$i."</b>";
				if ($i != $page3) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&d=".$page_suivante."&l=".$page."&t=".$page2."#devoir\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."&d=".$page_suivante."&l=".$page."&t=".$page2."#devoir\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
		
    } else echo pas_de_devoir;
    	
    } else locationhref_admin("?inc=edit_tutorials");
   } break;

    // ****************** create_bloc **************************
    case "create_bloc" : {
    	
    	if (isset($_POST['bloc_titre']) && isset($_POST['bloc_contenu'])){
    		$bloc_titre = trim($_POST['bloc_titre']);
    		$bloc_contenu = trim($_POST['bloc_contenu']);
    		if (!empty($bloc_titre) && !empty($bloc_contenu)){

    			$bloc_titre = escape_string($bloc_titre);
    			$bloc_contenu = escape_string($bloc_contenu);
    			
    			$select_bloc_titre = mysql_query("select id_bloc from `" . $tblprefix . "blocs` where titre_bloc = '$bloc_titre' and id_chapitre = $id_chap;");
 					if (mysql_num_rows($select_bloc_titre) == 0) {
 						$select_max_order = mysql_query("select max(ordre_bloc) from `" . $tblprefix . "blocs` where id_chapitre = $id_chap;");
 						if (mysql_num_rows($select_max_order) == 1)
 							$ordre_bloc= mysql_result($select_max_order,0) + 1;
 						else $ordre_bloc = 1;
 						
 						$insertbloc = "INSERT INTO `" . $tblprefix . "blocs` VALUES (NULL,$id_chap,'$bloc_titre','$bloc_contenu','1',$ordre_bloc);";
	          mysql_query($insertbloc,$connect);

						$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
						$id_tuto = mysql_result($select_tuto_id,0,0);
	          $date_modification_tuto_chap = time();
	          $update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto_chap where id_tutoriel = $id_tuto;");
						$update_chap_date_modification = mysql_query("update `" . $tblprefix . "chapitres` set date_modification_chapitre = $date_modification_tuto_chap where id_chapitre = $id_chap;");

	          $select_this_bloc = mysql_query("select id_bloc from `" . $tblprefix . "blocs` where titre_bloc = '$bloc_titre' and contenu_bloc= '$bloc_contenu' and id_chapitre = $id_chap;");
	          if (mysql_num_rows($select_this_bloc) == 1 && $_POST['exit'] == 0) {
	          	$id_bloc = mysql_result($select_this_bloc,0);
	          	$link = "?inc=edit_tutorials&do=update_bloc&id_bloc=".$id_bloc;
	          	locationhref_admin($link);
						}
	          else {
	          	$link = "?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap;
	        		redirection(bloc_cree,$link,3,"tips",1);
	        	}
 					} else goback(titre_existe,2,"error",1);
    		} else goback(titre_contenu_vide,2,"error",1);
    	}
    	else {
    			goback_button();
    			
    			echo "<form id=\"Form_createbloc\" name=\"Form_createbloc\" method=\"POST\" action=\"\">";
    			echo "<p><u><b><font color=\"red\">*</font> " .titre_bloc. "</b></u><br /><br /><input name=\"bloc_titre\" type=\"text\" size=\"50\" maxlength=\"100\"></p>";							
    			echo "<br /><p><u><b><font color=\"red\">*</font> " .contenu_bloc. "</b></u><br /><br /><textarea name=\"bloc_contenu\" cols=\"100\" rows=\"30\"></textarea></p>";
    			echo "<br /><input type=\"hidden\" name=\"exit\" value=\"1\"><input type=\"button\" class=\"button\" value=\"" .btnsave. "\" onClick=\"document.Form_createbloc.exit.value=0;document.Form_createbloc.submit();\">&nbsp;&nbsp;&nbsp;<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
					ckeditor_replace($language,"bloc_contenu");
   		}
    } break;

    // ****************** update_bloc **************************
    case "update_bloc" : {
     	
    		$select_bloc_complet = mysql_query("select * from `" . $tblprefix . "blocs` where id_bloc = $id_bloc;");
    		if (mysql_num_rows($select_bloc_complet) == 1) {
    			$bloc = mysql_fetch_row($select_bloc_complet);
    			
    			$titre_bloc = html_ent($bloc[2]);
					$contenu_bloc = html_ent($bloc[3]);
					$id_chapitre = $bloc[1];
					
    				if (!empty($_POST['send'])){
    					$bloc_titre = trim($_POST['bloc_titre']);
    					$bloc_contenu = trim($_POST['bloc_contenu']);
    					if (!empty($bloc_titre) && !empty($bloc_contenu)){
    						$bloc_titre = escape_string($bloc_titre);
    						$bloc_contenu = escape_string($bloc_contenu);
    						$select_bloc_titre = mysql_query("select id_bloc from `" . $tblprefix . "blocs` where titre_bloc = '$bloc_titre' and id_chapitre = $id_chapitre;");
 								if ((mysql_num_rows($select_bloc_titre) == 0) || (mysql_num_rows($select_bloc_titre) == 1 && mysql_result($select_bloc_titre,0) == $id_bloc)) {
 									$update_bloc = "update `" . $tblprefix . "blocs` SET titre_bloc = '$bloc_titre', contenu_bloc = '$bloc_contenu' where id_bloc = $id_bloc;";
 									mysql_query($update_bloc);
 									
									$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chapitre;");
									$id_tuto = mysql_result($select_tuto_id,0,0);
	          			$date_modification_tuto_chap = time();
	          			$update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto_chap where id_tutoriel = $id_tuto;");
									$update_chap_date_modification = mysql_query("update `" . $tblprefix . "chapitres` set date_modification_chapitre = $date_modification_tuto_chap where id_chapitre = $id_chapitre;");

	          			$select_this_bloc = mysql_query("select id_bloc from `" . $tblprefix . "blocs` where titre_bloc = '$bloc_titre' and contenu_bloc= '$bloc_contenu' and id_chapitre = $id_chapitre;");
	          			if (mysql_num_rows($select_this_bloc) == 1 && $_POST['exit'] == 0) {
	          				$id_bloc = mysql_result($select_this_bloc,0);
	          				$link = "?inc=edit_tutorials&do=update_bloc&id_bloc=".$id_bloc;
	          				locationhref_admin($link);
									}
	          			else redirection(bloc_modifie,"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."",3,"tips",1);
	          			
 								} else goback(titre_existe,2,"error",1);
    					} else goback(titre_contenu_vide,2,"error",1);
    				}
    				else {
    					goback_button();
    					echo "<form id=\"Form_updatebloc\" name=\"Form_updatebloc\" method=\"POST\" action=\"\">";
    					echo "<p><u><b><font color=\"red\">*</font> " .titre_bloc. "</b></u><br /><br /><input name=\"bloc_titre\" type=\"text\" size=\"50\" maxlength=\"100\" value=\"".$titre_bloc."\"></p>";							
    					echo "<br /><p><u><b><font color=\"red\">*</font> " .contenu_bloc. "</b></u><br /><br /><textarea name=\"bloc_contenu\" cols=\"100\" rows=\"30\">".$contenu_bloc."</textarea></p>";
    			
    					echo "<input type=\"hidden\" name=\"send\" value=\"ok\">";
							echo "<br /><input type=\"hidden\" name=\"exit\" value=\"1\"><input type=\"button\" class=\"button\" value=\"" .btnsave. "\" onClick=\"document.Form_updatebloc.exit.value=0;document.Form_updatebloc.submit();\">&nbsp;&nbsp;&nbsp;<input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    					ckeditor_replace($language,"bloc_contenu");
    				}
    		} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** delete_bloc **************************
    case "delete_bloc" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
   			$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "blocs` where id_bloc = $id_bloc;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
				$delete_bloc = mysql_query("delete from `" . $tblprefix . "blocs` where id_bloc = $id_bloc;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderup_bloc *********************
    case "orderup_bloc" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
   			$select_order_idchap = mysql_query("select id_chapitre, ordre_bloc from `" . $tblprefix . "blocs` where id_bloc = $id_bloc;");
				if (mysql_num_rows($select_order_idchap) == 1) {
					$id_chapitre = mysql_result($select_order_idchap,0,0);
					$ordre_bloc = mysql_result($select_order_idchap,0,1);

    			$bloc_precedent = mysql_query ("select id_bloc, ordre_bloc from `" . $tblprefix . "blocs` where ordre_bloc < $ordre_bloc and id_chapitre = $id_chapitre order by ordre_bloc desc;");
					if (mysql_num_rows($bloc_precedent) > 0) {
						$idbloc_precedent = mysql_result($bloc_precedent,0,0);
						$ordrebloc_precedent = mysql_result($bloc_precedent,0,1);
							
						$order_this_bloc = mysql_query("update `" . $tblprefix . "blocs` set ordre_bloc = $ordrebloc_precedent where id_bloc = $id_bloc;");
						$order_bloc_precedent = mysql_query("update `" . $tblprefix . "blocs` set ordre_bloc = $ordre_bloc where id_bloc = $idbloc_precedent;");
					}
					locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre);
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderdown_bloc *********************
    case "orderdown_bloc" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
   			$select_order_idchap = mysql_query("select id_chapitre, ordre_bloc from `" . $tblprefix . "blocs` where id_bloc = $id_bloc;");
				if (mysql_num_rows($select_order_idchap) == 1) {
					$id_chapitre = mysql_result($select_order_idchap,0,0);
					$ordre_bloc = mysql_result($select_order_idchap,0,1);

    			$bloc_suivant = mysql_query ("select id_bloc, ordre_bloc from `" . $tblprefix . "blocs` where ordre_bloc > $ordre_bloc and id_chapitre = $id_chapitre order by ordre_bloc;");
					if (mysql_num_rows($bloc_suivant) > 0) {
						$idbloc_suivant = mysql_result($bloc_suivant,0,0);
						$ordrebloc_suivant = mysql_result($bloc_suivant,0,1);
							
						$order_this_bloc = mysql_query("update `" . $tblprefix . "blocs` set ordre_bloc = $ordrebloc_suivant where id_bloc = $id_bloc;");
						$order_bloc_suivant = mysql_query("update `" . $tblprefix . "blocs` set ordre_bloc = $ordre_bloc where id_bloc = $idbloc_suivant;");
					}
					locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre);
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** publier_bloc *************************
    case "publier_bloc" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
   			$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "blocs` where id_bloc = $id_bloc;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
    		$publier_bloc = mysql_query("update `" . $tblprefix . "blocs` set publie_bloc = '1' where id_bloc = $id_bloc;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;
    
    // ****************** depublier_bloc ***********************
    case "depublier_bloc" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "blocs` where id_bloc = $id_bloc;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
    		$depublier_bloc = mysql_query("update `" . $tblprefix . "blocs` set publie_bloc = '0' where id_bloc = $id_bloc;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre);
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** create_qcm **************************
    case "create_qcm" : {

     		if (!empty($_POST['send'])) {
     		 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 		$_SESSION['random_key'] = $_POST['random'];
     			$text_question = trim($_POST['text_question']);
     			if (!empty($text_question) && !empty($_POST['reponse_correcte'])){
						$reponses_tab = array();
     				$text_question = escape_string($text_question);
     				$reponse1 = escape_string(trim($_POST['reponse1']));
     				$reponse2 = escape_string(trim($_POST['reponse2']));
     				$reponse3 = escape_string(trim($_POST['reponse3']));
     				$reponse4 = escape_string(trim($_POST['reponse4']));
     				$reponse5 = escape_string(trim($_POST['reponse5']));
     				$reponse6 = escape_string(trim($_POST['reponse6']));
     				for ($i=1;$i<7;$i++){
     					$variable_rep = "reponse".$i;
     					if (!empty($$variable_rep))
     						$reponses_tab[] = $$variable_rep;
     				}
     				if (count($reponses_tab)>1) {
     					if (ctype_digit($_POST['reponse_correcte']))
     						$reponse_correcte = intval($_POST['reponse_correcte']);
     					else $reponse_correcte = 1;
     					
     					$variable_rep = "reponse".$reponse_correcte;
     					if (!empty($$variable_rep)){
     				
     						$select_max_order = mysql_query("select max(ordre_qcm) from `" . $tblprefix . "qcm` where id_chapitre = $id_chap;");
 								if (mysql_num_rows($select_max_order) == 1)
 									$ordre_qcm = mysql_result($select_max_order,0) + 1;
 								else $ordre_qcm = 1;

 								$insertqcm = "INSERT INTO `" . $tblprefix . "qcm` VALUES (NULL,$id_chap,'$text_question','$reponse1','$reponse2','$reponse3','$reponse4','$reponse5','$reponse6','$reponse_correcte',0,0,'1',$ordre_qcm);";
	          		mysql_query($insertqcm,$connect);

								$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
								$id_tuto = mysql_result($select_tuto_id,0,0);
	          		$date_modification_tuto_chap = time();
	          		$update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto_chap where id_tutoriel = $id_tuto;");
								$update_chap_date_modification = mysql_query("update `" . $tblprefix . "chapitres` set date_modification_chapitre = $date_modification_tuto_chap where id_chapitre = $id_chap;");

	          		$link = "?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."#qcm";
	          		redirection(qcm_cree,$link,3,"tips",1);
	          	} else goback(reponse_correcte_nonvide,2,"error",1);
	          } else goback(reponse_min_qcm,2,"error",1);
     			} else goback(champ_manq_qcm,2,"error",1);
     		 } else goback(err_data_saved,2,"error",1);
				}
				else {
					goback_button();
					echo "<form method=\"POST\" action=\"\">";
					echo "\n<p><u><b><font color=\"red\">*</font> " .question. " : </b></u><br /><br /><textarea name=\"text_question\" cols=\"100\" rows=\"10\"></textarea><br /></p>";
	      	echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"1\"> " .reponse. " 1 : </b><input name=\"reponse1\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"\"></p>";
	      	echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"2\"> " .reponse. " 2 : </b><input name=\"reponse2\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"\"></p>";
	      	echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"3\"> " .reponse. " 3 : </b><input name=\"reponse3\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"\"></p>";
	      	echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"4\"> " .reponse. " 4 : </b><input name=\"reponse4\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"\"></p>";
	      	echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"5\"> " .reponse. " 5 : </b><input name=\"reponse5\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"\"></p>";
	      	echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"6\"> " .reponse. " 6 : </b><input name=\"reponse6\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"\"></p>";
	      	echo "\n<p><b>- ".remplir_qcm."<br />- ".qcm_selectionner_reponse."</b><br /><br />";
	      	echo "\n<input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form></p>";
					ckeditor_replace($language,"text_question");
				}
    } break;

    // ****************** update_qcm **************************
    case "update_qcm" : {

    		$select_qcm_complet = mysql_query("select * from `" . $tblprefix . "qcm` where id_qcm = $id_qcm;");
    		if (mysql_num_rows($select_qcm_complet) == 1) {
    			$qcm = mysql_fetch_row($select_qcm_complet);
    			
    			$id_chapitre = $qcm[1];
    			$question_qcm = html_ent($qcm[2]);
					$reponse1_qcm = html_ent($qcm[3]);
					$reponse2_qcm = html_ent($qcm[4]);
					$reponse3_qcm = html_ent($qcm[5]);
					$reponse4_qcm = html_ent($qcm[6]);
					$reponse5_qcm = html_ent($qcm[7]);
					$reponse6_qcm = html_ent($qcm[8]);
					$reponse_correcte = $qcm[9];
					function check_qcm($reponse,$reponse_correcte){
						if ($reponse == $reponse_correcte)
							return " checked=\"checked\"";
					}

     			if (!empty($_POST['send'])) {
     			 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 			$_SESSION['random_key'] = $_POST['random'];
     				$text_question = trim($_POST['text_question']);
     				if (!empty($text_question) && !empty($_POST['reponse_correcte'])){
							$reponses_tab = array();
     					$text_question = escape_string($text_question);
     					$reponse1 = escape_string(trim($_POST['reponse1']));
     					$reponse2 = escape_string(trim($_POST['reponse2']));
     					$reponse3 = escape_string(trim($_POST['reponse3']));
     					$reponse4 = escape_string(trim($_POST['reponse4']));
     					$reponse5 = escape_string(trim($_POST['reponse5']));
     					$reponse6 = escape_string(trim($_POST['reponse6']));
     					for ($i=1;$i<7;$i++){
     						$variable_rep = "reponse".$i;
     						if (!empty($$variable_rep))
     							$reponses_tab[] = $$variable_rep;
     					}
     					if (count($reponses_tab)>1) {
     						if (ctype_digit($_POST['reponse_correcte']))
     							$reponse_correcte = intval($_POST['reponse_correcte']);
     						else $reponse_correcte = 1;
     					
     						$variable_rep = "reponse".$reponse_correcte;
     						if (!empty($$variable_rep)){

 									$update_qcm = "update `" . $tblprefix . "qcm` SET question_qcm = '$text_question', reponse1_qcm = '$reponse1', reponse2_qcm = '$reponse2', reponse3_qcm = '$reponse3', reponse4_qcm = '$reponse4', reponse5_qcm = '$reponse5', reponse6_qcm = '$reponse6', reponse_correcte = '$reponse_correcte' where id_qcm = $id_qcm;";
 									mysql_query($update_qcm);
 									
									$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chapitre;");
									$id_tuto = mysql_result($select_tuto_id,0,0);
	          			$date_modification_tuto_chap = time();
	          			$update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto_chap where id_tutoriel = $id_tuto;");
									$update_chap_date_modification = mysql_query("update `" . $tblprefix . "chapitres` set date_modification_chapitre = $date_modification_tuto_chap where id_chapitre = $id_chapitre;");

 									redirection(qcm_modifie,"?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#qcm",3,"tips",1);

	          		} else goback(reponse_correcte_nonvide,2,"error",1);
	          	} else goback(reponse_min_qcm,2,"error",1);
     				} else goback(champ_manq_qcm,2,"error",1);
     			 } else goback(err_data_saved,2,"error",1);
					}
					else {
						goback_button();
						echo "<form method=\"POST\" action=\"\">";
						echo "\n<p><u><b><font color=\"red\">*</font> " .question. " : </b></u><br /><br /><textarea name=\"text_question\" cols=\"100\" rows=\"10\">".$question_qcm."</textarea><br /></p>";
	      		echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"1\"".check_qcm(1,$reponse_correcte)."> " .reponse. " 1 : </b><input name=\"reponse1\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"".$reponse1_qcm."\"></p>";
	      		echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"2\"".check_qcm(2,$reponse_correcte)."> " .reponse. " 2 : </b><input name=\"reponse2\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"".$reponse2_qcm."\"></p>";
	      		echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"3\"".check_qcm(3,$reponse_correcte)."> " .reponse. " 3 : </b><input name=\"reponse3\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"".$reponse3_qcm."\"></p>";
	      		echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"4\"".check_qcm(4,$reponse_correcte)."> " .reponse. " 4 : </b><input name=\"reponse4\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"".$reponse4_qcm."\"></p>";
	      		echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"5\"".check_qcm(5,$reponse_correcte)."> " .reponse. " 5 : </b><input name=\"reponse5\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"".$reponse5_qcm."\"></p>";
	      		echo "\n<p><b><input name=\"reponse_correcte\" type=\"radio\" value=\"6\"".check_qcm(6,$reponse_correcte)."> " .reponse. " 6 : </b><input name=\"reponse6\" type=\"text\" size=\"66\" maxlength=\"200\" value=\"".$reponse6_qcm."\"></p>";
	      		echo "\n<p><b>- ".remplir_qcm."<br />- ".qcm_selectionner_reponse."</b><br /><br />";
	      		echo "\n<input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form></p>";
						ckeditor_replace($language,"text_question");
					}
    		} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** delete_qcm **************************
    case "delete_qcm" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "qcm` where id_qcm = $id_qcm;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
				$delete_qcm = mysql_query("delete from `" . $tblprefix . "qcm` where id_qcm = $id_qcm;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#qcm");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderup_qcm *********************
    case "orderup_qcm" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_order_idchap = mysql_query("select id_chapitre, ordre_qcm from `" . $tblprefix . "qcm` where id_qcm = $id_qcm;");
				if (mysql_num_rows($select_order_idchap) == 1) {
					$id_chapitre = mysql_result($select_order_idchap,0,0);
					$ordre_qcm = mysql_result($select_order_idchap,0,1);

    			$qcm_precedent = mysql_query ("select id_qcm, ordre_qcm from `" . $tblprefix . "qcm` where ordre_qcm < $ordre_qcm and id_chapitre = $id_chapitre order by ordre_qcm desc;");
					if (mysql_num_rows($qcm_precedent) > 0) {
						$idqcm_precedent = mysql_result($qcm_precedent,0,0);
						$ordreqcm_precedent = mysql_result($qcm_precedent,0,1);
							
						$order_this_qcm = mysql_query("update `" . $tblprefix . "qcm` set ordre_qcm = $ordreqcm_precedent where id_qcm = $id_qcm;");
						$order_qcm_precedent = mysql_query("update `" . $tblprefix . "qcm` set ordre_qcm = $ordre_qcm where id_qcm = $idqcm_precedent;");
					}
    			locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#qcm");
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderdown_qcm *********************
    case "orderdown_qcm" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_order_idchap = mysql_query("select id_chapitre, ordre_qcm from `" . $tblprefix . "qcm` where id_qcm = $id_qcm;");
				if (mysql_num_rows($select_order_idchap) == 1) {
					$id_chapitre = mysql_result($select_order_idchap,0,0);
					$ordre_qcm = mysql_result($select_order_idchap,0,1);

    			$qcm_suivant = mysql_query ("select id_qcm, ordre_qcm from `" . $tblprefix . "qcm` where ordre_qcm > $ordre_qcm and id_chapitre = $id_chapitre order by ordre_qcm;");
					if (mysql_num_rows($qcm_suivant) > 0) {
						$idqcm_suivant = mysql_result($qcm_suivant,0,0);
						$ordreqcm_suivant = mysql_result($qcm_suivant,0,1);
							
						$order_this_qcm = mysql_query("update `" . $tblprefix . "qcm` set ordre_qcm = $ordreqcm_suivant where id_qcm = $id_qcm;");
						$order_qcm_suivant = mysql_query("update `" . $tblprefix . "qcm` set ordre_qcm = $ordre_qcm where id_qcm = $idqcm_suivant;");
					}
    			locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#qcm");
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** publier_qcm *************************
    case "publier_qcm" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "qcm` where id_qcm = $id_qcm;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
    		$publier_qcm = mysql_query("update `" . $tblprefix . "qcm` set publie_qcm = '1' where id_qcm = $id_qcm;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#qcm");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;
    
    // ****************** depublier_qcm ***********************
    case "depublier_qcm" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "qcm` where id_qcm = $id_qcm;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
    		$depublier_qcm = mysql_query("update `" . $tblprefix . "qcm` set publie_qcm = '0' where id_qcm = $id_qcm;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#qcm");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** create_devoir **************************
    case "create_devoir" : {

     		if (!empty($_POST['send'])) {
     		 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 		$_SESSION['random_key'] = $_POST['random'];
     			$titre_devoir = trim($_POST['titre_devoir']);
     			$contenu_devoir = trim($_POST['contenu_devoir']);
     			$date_publication = trim($_POST['date_publication']);
     			$duree_publication = trim($_POST['duree_publication']);
     			if (!empty($titre_devoir) && !empty($contenu_devoir) && !empty($date_publication) && !empty($duree_publication)){
     			 $titre_devoir = escape_string($titre_devoir);
     			 $contenu_devoir = escape_string($contenu_devoir);
					 $date_publication = escape_string($date_publication);
					 $duree_publication = escape_string($duree_publication);
						
					 if (is_numeric($duree_publication)){
						$date_publication2 = explode("/",$date_publication);
						if (count($date_publication2) == 3){
						if (isset($language) && $language == "fr"){
							$jj = $date_publication2[0];
							$mm = $date_publication2[1];
						} else {
							$jj = $date_publication2[1];
							$mm = $date_publication2[0];
						}
						$yyyy = $date_publication2[2];

						if ($yyyy < 100){
							if ($yyyy < date("y",time())) $yyyy += 2000;
							else $yyyy += 1900;
						}
						if (ctype_digit($jj) && $jj >= 1 && $jj <= 31 && ctype_digit($mm) && $mm >= 1 && $mm <= 12 && $yyyy >= (date("Y",time()) - 10) && $yyyy <= (date("Y",time()) + 10)){

							$date_publication3 = mktime(date("H",time()), date("i",time()), date("s",time()), $mm, $jj, $yyyy);
							if ($duree_publication > 0)
								$date_expiration = $date_publication3 + (60*60*24*$duree_publication);
							else $date_expiration = $date_publication3;

    					if ($_POST['acces_devoir'] == "classe"){
    						if (!empty($_POST['classes']))
    								$homework_acces = "-".implode("-",$_POST['classes'])."-";
    						else $homework_acces = "*";
    					} else $homework_acces = "*";

     					$select_max_order = mysql_query("select max(ordre_devoir) from `" . $tblprefix . "devoirs` where id_chapitre = $id_chap;");
 							if (mysql_num_rows($select_max_order) == 1)
 								$ordre_devoir = mysql_result($select_max_order,0) + 1;
 							else $ordre_devoir = 1;

 							$insertdevoir = mysql_query("INSERT INTO `" . $tblprefix . "devoirs` VALUES (NULL,$id_chap,'$homework_acces','$titre_devoir','$contenu_devoir',$date_publication3,$date_expiration,'1',$ordre_devoir);");

							$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
							$id_tuto = mysql_result($select_tuto_id,0,0);
	          	$date_modification_tuto_chap = time();
	         		$update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto_chap where id_tutoriel = $id_tuto;");
							$update_chap_date_modification = mysql_query("update `" . $tblprefix . "chapitres` set date_modification_chapitre = $date_modification_tuto_chap where id_chapitre = $id_chap;");

	         		$link = "?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."#devoir";
	         		redirection(devoir_cree,$link,3,"tips",1);
						} else goback(date_publication_invalide,2,"error",1);
						} else goback(date_publication_invalide,2,"error",1);
	         } else goback(duree_publication_numerique,2,"error",1);
     			} else goback(remplir_champs_obligatoires,2,"error",1);
     		 } else goback(err_data_saved,2,"error",1);
				}
				else {
					goback_button();
					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";

					if (isset($language) && $language == "fr"){
						$calendar_path = "mycalendar_fr.js";
						$format_date_calendar = "jj/mm/aaaa";
						$today_date = date("d/m/Y",time());
					}
					else {
						$calendar_path = "mycalendar_us.js";
						$format_date_calendar = "mm/dd/yyyy";
						$today_date = date("m/d/Y",time());
					}
					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/".$calendar_path."\"></script>";
					echo "<form method=\"POST\" name=\"f_add_devoir\" id=\"f_add_devoir\" action=\"\">";

    			echo "\n<p><u><b><font color=\"red\">*</font> " .titre_devoir. "</b></u><br /><br /><input name=\"titre_devoir\" type=\"text\" size=\"50\" maxlength=\"100\"></p>";							

					echo "\n<p><u><b><font color=\"red\">*</font> " .contenu_devoir. " : </b></u><br /><br /><textarea name=\"contenu_devoir\" cols=\"100\" rows=\"20\"></textarea><br /></p>";

					echo "<p><u><b><font color='red'>*</font> ".date_publication." : </b></u> (".$format_date_calendar.") : <br /><input name=\"date_publication\" id=\"date_publication\" type=\"text\" maxlength=\"10\" size=\"10\" value=\"".$today_date."\">";
					echo "\n<script type=\"text/javascript\">new tcal({'formname':'f_add_devoir','controlname':'date_publication'});</script></p>";
					
					echo "<p><u><b><font color='red'>*</font> ".duree_publication." : </b></u><br /><input type=\"text\" name=\"duree_publication\" id=\"duree_publication\" maxlength=\"3\" size=\"3\" value=\"\"> <b><font color='red'>".jours."</font></b></p>";

					echo "<p><u><b><font color=\"red\">*</font> ".acces_devoir."</b></u><br />";

					echo "\n<input name=\"acces_devoir\" type=\"radio\" value=\"all\" checked=\"checked\" onclick=\"disabled_select('classes',true)\" /><b>".acces_apprenants."</b>";
					
					echo "\n<br /><input name=\"acces_devoir\" type=\"radio\" value=\"classe\" onclick=\"disabled_select('classes',false)\" /><b>".acces_classes." :</b>";
					
					$select_classes = mysql_query("select * from `" . $tblprefix . "classes`;");
					if (mysql_num_rows($select_classes) > 0){
					 	echo "<table border=\"0\"><tr><td align=\"center\">";
						echo "<select size=\"5\" name=\"classes[]\" id=\"classes\" multiple=\"multiple\">";
    				while($classe = mysql_fetch_row($select_classes)){
    					$id_classe = $classe[0];
    					$nom_classe = html_ent($classe[1]);
    					echo "\n<option value=\"".$id_classe."\">".$nom_classe."</option>";
    				}
						echo "\n</select><br />";
						echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('classes',false)\" />";
						echo "<br />".hold_down_ctrl."</td></tr></table>";
					} else echo aucune_classe;
					
					echo "<script type=\"text/javascript\">disabled_select('classes',true);</script>";
	      	echo "\n</p><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form></p>";
					ckeditor_replace($language,"contenu_devoir");
				}
    } break;

    // ****************** update_devoir **************************
    case "update_devoir" : {

    	$select_devoir_complet = mysql_query("select * from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
    	if (mysql_num_rows($select_devoir_complet) == 1) {
    		$devoir = mysql_fetch_row($select_devoir_complet);
 				$id_chap = $devoir[1];
 				
     		if (!empty($_POST['send'])) {
     		 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 		$_SESSION['random_key'] = $_POST['random'];
     			$titre_devoir = trim($_POST['titre_devoir']);
     			$contenu_devoir = trim($_POST['contenu_devoir']);
     			$date_publication = trim($_POST['date_publication']);
     			$duree_publication = trim($_POST['duree_publication']);
     			if (!empty($titre_devoir) && !empty($contenu_devoir) && !empty($date_publication) && !empty($duree_publication)){
     			 $titre_devoir = escape_string($titre_devoir);
     			 $contenu_devoir = escape_string($contenu_devoir);
					 $date_publication = escape_string($date_publication);
					 $duree_publication = escape_string($duree_publication);
						
					 if (is_numeric($duree_publication)){
						$date_publication2 = explode("/",$date_publication);
						if (count($date_publication2) == 3){
						if (isset($language) && $language == "fr"){
							$jj = $date_publication2[0];
							$mm = $date_publication2[1];
						} else {
							$jj = $date_publication2[1];
							$mm = $date_publication2[0];
						}
						$yyyy = $date_publication2[2];

						if ($yyyy < 100){
							if ($yyyy < date("y",time())) $yyyy += 2000;
							else $yyyy += 1900;
						}
						if (ctype_digit($jj) && $jj >= 1 && $jj <= 31 && ctype_digit($mm) && $mm >= 1 && $mm <= 12 && $yyyy >= (date("Y",time()) - 10) && $yyyy <= (date("Y",time()) + 10)){

							$date_publication3 = mktime(date("H",time()), date("i",time()), date("s",time()), $mm, $jj, $yyyy);
							if ($duree_publication > 0)
								$date_expiration = $date_publication3 + (60*60*24*$duree_publication);
							else $date_expiration = $date_publication3;

    					if ($_POST['acces_devoir'] == "classe"){
    						if (!empty($_POST['classes']))
    								$homework_acces = "-".implode("-",$_POST['classes'])."-";
    						else $homework_acces = "*";
    					} else $homework_acces = "*";

 							$update_devoir = mysql_query("update `" . $tblprefix . "devoirs` SET acces_devoir = '$homework_acces', titre_devoir = '$titre_devoir', contenu_devoir = '$contenu_devoir', date_publie_devoir = $date_publication3, date_expire_devoir = $date_expiration where id_devoir = $id_devoir;");

							$select_tuto_id= mysql_query("select id_tutoriel from `" . $tblprefix . "parties`, `" . $tblprefix . "chapitres` where `" . $tblprefix . "parties`.id_partie = `" . $tblprefix . "chapitres`.id_partie and id_chapitre = $id_chap;");
							$id_tuto = mysql_result($select_tuto_id,0,0);
	          	$date_modification_tuto_chap = time();
	         		$update_tuto_date_modification = mysql_query("update `" . $tblprefix . "tutoriels` set date_modification_tutoriel = $date_modification_tuto_chap where id_tutoriel = $id_tuto;");
							$update_chap_date_modification = mysql_query("update `" . $tblprefix . "chapitres` set date_modification_chapitre = $date_modification_tuto_chap where id_chapitre = $id_chap;");

	         		$link = "?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chap."#devoir";
	         		redirection(devoir_modifie,$link,3,"tips",1);
						} else goback(date_publication_invalide,2,"error",1);
						} else goback(date_publication_invalide,2,"error",1);
	         } else goback(duree_publication_numerique,2,"error",1);
     			} else goback(remplir_champs_obligatoires,2,"error",1);
     		 } else goback(err_data_saved,2,"error",1);
				}
				else {
					goback_button();
					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";

					if (isset($language) && $language == "fr"){
						$calendar_path = "mycalendar_fr.js";
						$format_date_calendar = "jj/mm/aaaa";
						$devoir_date = date("d/m/Y",$devoir[5]);
					}
					else {
						$calendar_path = "mycalendar_us.js";
						$format_date_calendar = "mm/dd/yyyy";
						$devoir_date = date("m/d/Y",$devoir[5]);
					}
					echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/".$calendar_path."\"></script>";
					echo "<form method=\"POST\" name=\"f_add_devoir\" id=\"f_add_devoir\" action=\"\">";

    			echo "\n<p><u><b><font color=\"red\">*</font> " .titre_devoir. "</b></u><br /><br /><input name=\"titre_devoir\" type=\"text\" value=\"".html_ent($devoir[3])."\" size=\"50\" maxlength=\"100\"></p>";							

					echo "\n<p><u><b><font color=\"red\">*</font> " .contenu_devoir. " : </b></u><br /><br /><textarea name=\"contenu_devoir\" cols=\"100\" rows=\"20\">".html_ent($devoir[4])."</textarea><br /></p>";

					echo "<p><u><b><font color='red'>*</font> ".date_publication." : </b></u> (".$format_date_calendar.") : <br /><input name=\"date_publication\" id=\"date_publication\" type=\"text\" maxlength=\"10\" size=\"10\" value=\"".$devoir_date."\">";
					echo "\n<script type=\"text/javascript\">new tcal({'formname':'f_add_devoir','controlname':'date_publication'});</script></p>";
					
					if ($devoir[6] > $devoir[5])
						$devoir_expiration = round(($devoir[6] - $devoir[5]) / 60 / 60 / 24);
					else $devoir_expiration = 0;

					echo "<p><u><b><font color='red'>*</font> ".duree_publication." : </b></u><br /><input type=\"text\" name=\"duree_publication\" id=\"duree_publication\" maxlength=\"3\" size=\"3\" value=\"".$devoir_expiration."\"> <b><font color='red'>".jours."</font></b></p>";

					echo "<p><u><b><font color=\"red\">*</font> ".acces_devoir."</b></u><br />";

					echo "\n<input name=\"acces_devoir\" type=\"radio\" value=\"all\" onclick=\"disabled_select('classes',true)\"";
					if ($devoir[2] == "*")
						echo " checked=\"checked\"";
					echo " /><b>".acces_apprenants."</b>";

					echo "\n<br /><input name=\"acces_devoir\" type=\"radio\" value=\"classe\" onclick=\"disabled_select('classes',false)\"";
					if ($devoir[2] != "*")
						echo " checked=\"checked\"";
					echo " /><b>".acces_classes." :</b>";
					
					$tab_acces_devoir = explode("-",$devoir[2]);
					$select_classes = mysql_query("select * from `" . $tblprefix . "classes`;");
					if (mysql_num_rows($select_classes) > 0){
					 	echo "<table border=\"0\"><tr><td align=\"center\">";
						echo "<select size=\"5\" name=\"classes[]\" id=\"classes\" multiple=\"multiple\">";
    				while($classe = mysql_fetch_row($select_classes)){
    					$id_classe = $classe[0];
    					$nom_classe = html_ent($classe[1]);
    					echo "\n<option value=\"".$id_classe."\"";
    					if (in_array($id_classe,$tab_acces_devoir))
    						echo " selected=\"selected\"";
    					echo ">".$nom_classe."</option>";
    				}
						echo "\n</select><br />";
						echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('classes',false)\" />";
						echo "<br />".hold_down_ctrl."</td></tr></table>";
					} else echo aucune_classe;
					
					if ($devoir[2] == "*")
						echo "<script type=\"text/javascript\">disabled_select('classes',true);</script>";
						
	      	echo "\n</p><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form></p>";
					ckeditor_replace($language,"contenu_devoir");
				}
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** delete_devoir **************************
    case "delete_devoir" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
				$select_devoirs_rendus = mysql_query("select lien_file from `" . $tblprefix . "devoirs_rendus` where id_devoir = $id_devoir;");
				if (mysql_num_rows($select_devoirs_rendus) > 0){
    			while($lien_file = mysql_fetch_row($select_devoirs_rendus))
    				@unlink("../docs/".$lien_file[0]);
    		}
    		$select_titre_devoir = mysql_query("select titre_devoir from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
				if (mysql_num_rows($select_titre_devoir) == 1){
					$titre_devoir = mysql_result($select_titre_devoir,0);
					$lien_zip = $id_devoir."_".special_chars($titre_devoir).".zip";
					@unlink("../docs/".$lien_zip);
					$csv_notes_devoir_titre = $id_devoir."_".special_chars($titre_devoir)."_marks.csv";
					@unlink("../docs/".$csv_notes_devoir_titre);
				}
				$delete_devoirs_rendus = mysql_query("delete from `" . $tblprefix . "devoirs_rendus` where id_devoir = $id_devoir;");
				$delete_devoirs_notes = mysql_query("delete from `" . $tblprefix . "devoirs_notes` where id_devoir = $id_devoir;");
				$delete_devoir = mysql_query("delete from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#devoir");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderup_devoir *********************
    case "orderup_devoir" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_ordre_idchap = mysql_query("select id_chapitre, ordre_devoir from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
				if (mysql_num_rows($select_ordre_idchap) == 1) {
					$id_chapitre = mysql_result($select_ordre_idchap,0,0);
					$ordre_devoir = mysql_result($select_ordre_idchap,0,1);

    			$devoir_precedent = mysql_query ("select id_devoir, ordre_devoir from `" . $tblprefix . "devoirs` where ordre_devoir < $ordre_devoir and id_chapitre = $id_chapitre order by ordre_devoir desc;");
					if (mysql_num_rows($devoir_precedent) > 0) {
						$iddevoir_precedent = mysql_result($devoir_precedent,0,0);
						$ordredevoir_precedent = mysql_result($devoir_precedent,0,1);
							
						$order_this_devoir = mysql_query("update `" . $tblprefix . "devoirs` set ordre_devoir = $ordredevoir_precedent where id_devoir = $id_devoir;");
						$order_devoir_precedent = mysql_query("update `" . $tblprefix . "devoirs` set ordre_devoir = $ordre_devoir where id_devoir = $iddevoir_precedent;");
					}
    			locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#devoir");
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** orderdown_devoir *********************
    case "orderdown_devoir" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_ordre_idchap = mysql_query("select id_chapitre, ordre_devoir from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
				if (mysql_num_rows($select_ordre_idchap) == 1) {
					$id_chapitre = mysql_result($select_ordre_idchap,0,0);
					$ordre_devoir = mysql_result($select_ordre_idchap,0,1);

    			$devoir_suivant = mysql_query ("select id_devoir, ordre_devoir from `" . $tblprefix . "devoirs` where ordre_devoir > $ordre_devoir and id_chapitre = $id_chapitre order by ordre_devoir;");
					if (mysql_num_rows($devoir_suivant) > 0) {
						$iddevoir_suivant = mysql_result($devoir_suivant,0,0);
						$ordredevoir_suivant = mysql_result($devoir_suivant,0,1);
							
						$order_this_devoir = mysql_query("update `" . $tblprefix . "devoirs` set ordre_devoir = $ordredevoir_suivant where id_devoir = $id_devoir;");
						$order_devoir_suivant = mysql_query("update `" . $tblprefix . "devoirs` set ordre_devoir = $ordre_devoir where id_devoir = $iddevoir_suivant;");
					}
    			locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#devoir");
    		} else locationhref_admin("?inc=edit_tutorials");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

    // ****************** publier_devoir *************************
    case "publier_devoir" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
    		$publier_devoir = mysql_query("update `" . $tblprefix . "devoirs` set publie_devoir = '1' where id_devoir = $id_devoir;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#devoir");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;
    
    // ****************** depublier_devoir ***********************
    case "depublier_devoir" : {
    	if (isset($_GET['key']) && $_GET['key'] == $key){
    		$select_user_idchap = mysql_query("select id_chapitre from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
    		$id_chapitre = mysql_result($select_user_idchap,0,0);
    		$depublier_devoir = mysql_query("update `" . $tblprefix . "devoirs` set publie_devoir = '0' where id_devoir = $id_devoir;");
				locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#devoir");
    	} else locationhref_admin("?inc=edit_tutorials");
    } break;

  // ****************** delete_devoir_rendu **************************
  case "delete_devoir_rendu" : {
  	if (isset($_GET['key']) && $_GET['key'] == $key){
  		if (isset($_GET['id_devoir_rendu']) && ctype_digit($_GET['id_devoir_rendu']))
				$id_devoir_rendu = intval($_GET['id_devoir_rendu']);
			else $id_devoir_rendu = 0;

			$select_devoir_rendu = mysql_query("select id_devoir,lien_file from `" . $tblprefix . "devoirs_rendus` where id_devoir_rendu  = $id_devoir_rendu;");
			if (mysql_num_rows($select_devoir_rendu) == 1){
				
				$id_devoir = html_ent(mysql_result($select_devoir_rendu,0,0));
		  	$lien_fichier = html_ent(mysql_result($select_devoir_rendu,0,1));

		  	$delete_file = mysql_query("delete from `" . $tblprefix . "devoirs_rendus` where id_devoir_rendu = $id_devoir_rendu;");
		  	@unlink("../docs/".$lien_fichier);
    		locationhref_admin("?inc=edit_tutorials&do=open_devoir&id_devoir=".$id_devoir);
    	} else locationhref_admin("?inc=edit_tutorials");
		} else locationhref_admin("?inc=edit_tutorials");
  } break;
  
    // ****************** open_devoir **************************
    case "open_devoir" : {
    	if (!empty($_POST['send'])){
    		$select_apps = mysql_query("select id_apprenant from `" . $tblprefix . "apprenants`;");
    		if (mysql_num_rows($select_apps) > 0){
    			while($app_note = mysql_fetch_row($select_apps)){
    				$id_app = $app_note[0];
    				$var_app_note = "note_".$id_app;
						if (isset($_POST[$var_app_note]) && !empty($_POST[$var_app_note])){
							$note_this_app = floatval($_POST[$var_app_note]);
							if ($note_this_app > 20 || $note_this_app < 0) $note_this_app = 0;
   						$select_this_note_devoir = mysql_query("select id_devoir_note from `" . $tblprefix . "devoirs_notes` where id_devoir = $id_devoir and id_apprenant = $id_app;");
      				if (mysql_num_rows($select_this_note_devoir) == 1){
   							$id_devoir_note = mysql_result($select_this_note_devoir,0);
   							$update_note = mysql_query("update `" . $tblprefix . "devoirs_notes` set note_devoir = $note_this_app where id_devoir_note = $id_devoir_note;");
   						}
   						else {
   							$insert_note = mysql_query("INSERT INTO `" . $tblprefix . "devoirs_notes` VALUES (NULL,$id_devoir,$id_app,$note_this_app);");
   						}
						}
    			}
    			redirection(devoir_modifie,"?inc=edit_tutorials&do=open_devoir&id_devoir=".$id_devoir,3,"tips",1);
    		}
    	}
    	else {
    	$select_devoir = mysql_query("select * from `" . $tblprefix . "devoirs` where id_devoir = $id_devoir;");
    	if (mysql_num_rows($select_devoir) == 1){
    		if($devoir = mysql_fetch_row($select_devoir)){
    			goback_lien("?inc=edit_tutorials&do=open_chapitre&id_chap=".$devoir[1]);
					$titre_devoir = html_ent($devoir[3]);
					$date_publication_devoir = set_date($dateformat,$devoir[5]);
					$date_expiration_devoir = set_date($dateformat,$devoir[6]);
					
					if ($devoir[6] > $devoir[5])
						$duree_devoir = round(($devoir[6] - $devoir[5]) / 60 / 60 / 24);
					else
						$duree_devoir = 0;

					if ($devoir[6] > time()){
						if ($devoir[5] < time()){
							$expiration_devoir = round(($devoir[6] - time()) / 60 / 60 / 24);
							$expiration_chaine = "<font color='green'>".$expiration_devoir." ".jours."</font>";
						} else $expiration_chaine = "<font color='red'>".not_open_yet."</font>";
					} else $expiration_chaine = "<font color='red'>".expire."</font>";
					
					if ($devoir[2] == "*")
						$acces_devoir = acces_apprenants;
					else {
						$acces_devoir = acces_classes." : ";
						$tab_acces_devoir = explode("-",trim($devoir[2],"-"));
						if (!empty($tab_acces_devoir[0])){
							$chaine_acces_devoir = implode(",",$tab_acces_devoir);
							$select_classes = mysql_query("select * from `" . $tblprefix . "classes` where id_classe in (".$chaine_acces_devoir.");");
							if (mysql_num_rows($select_classes) > 0){
    						while($classe = mysql_fetch_row($select_classes))
    							$acces_devoir .= "<u>".$classe[1]."</u>, ";
    					}
    				}
					}
					echo "<ul>";
					echo "<li><b>".titre_devoir." : </b>".$titre_devoir."</li>";
					echo "<li><b>".date_publication." : </b>".$date_publication_devoir."</li>";
					echo "<li><b>".date_expiration." : </b>".$date_expiration_devoir."</li>";
					echo "<li><b>".duree_publication." : </b>".$duree_devoir." ".jours."</li>";
					echo "<li><b>".jours_restants." : </b>".$expiration_chaine."</li>";
					echo "<li><b>".acces_devoir." : </b>".$acces_devoir."</li>";
					echo "<hr />";

//*********** Telecharger devoir pour tous

					@ini_set('memory_limit', -1);
					if (!extension_loaded('zip'))
						@dl('zip.so');

					if ($devoir[2] == "*"){
						$apps_rendus = array();
						echo "<form method=\"POST\" action=\"\">";
						echo "<li><u><b>".homework_assignments." : </b></u></li><br />";

						$csv_notes_devoir_titre = $id_devoir."_".special_chars($titre_devoir)."_marks.csv";
						$csv_notes_devoir_file = fopen("../docs/".$csv_notes_devoir_titre,"w");
    				$csv_notes_devoir1  = "\"".identifiant."\";\"".mark."\";";
						$csv_notes_devoir1 = mb_convert_encoding($csv_notes_devoir1, 'ISO-8859-1', 'UTF-8');
						fwrite($csv_notes_devoir_file,$csv_notes_devoir1."\r\n");

						$select_devoir_rendu = mysql_query("select identifiant_apprenant,lien_file,date_file, `" . $tblprefix . "devoirs_rendus`.id_apprenant, id_devoir_rendu from `" . $tblprefix . "devoirs_rendus`,`" . $tblprefix . "apprenants` where `" . $tblprefix . "devoirs_rendus`.id_apprenant = `" . $tblprefix . "apprenants`.id_apprenant and id_devoir = $id_devoir;");
    				if (mysql_num_rows($select_devoir_rendu) > 0){
							echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
							echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
							echo "\n<td class=\"affichage_table\"><b>".devoir."</b></td>";
							echo "\n<td class=\"affichage_table\"><b>".date_ajout."</b></td>";
							echo "\n<td class=\"affichage_table\"><b>".mark."</b></td>";
							echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
							echo "</tr>";
							$files_zip = array();
    					while($devoir_rendu = mysql_fetch_row($select_devoir_rendu)){
    						$identifiant_apprenant = html_ent($devoir_rendu[0]);
    						$lien_file = html_ent($devoir_rendu[1]);
   							$date_file = set_date($dateformat,$devoir_rendu[2]);
   							$id_app = $devoir_rendu[3];
   							
   								$select_note_devoir = mysql_query("select note_devoir from `" . $tblprefix . "devoirs_notes` where id_devoir = $id_devoir and id_apprenant = $id_app;");
    	  					if (mysql_num_rows($select_note_devoir) == 1)
    								$note_devoir = mysql_result($select_note_devoir,0);
    							else $note_devoir = "00.00";

    							$csv_notes_devoir2  = "\"".$identifiant_apprenant."\";\"".$note_devoir."\";";
									$csv_notes_devoir2 = mb_convert_encoding($csv_notes_devoir2, 'ISO-8859-1', 'UTF-8');
									fwrite($csv_notes_devoir_file,$csv_notes_devoir2."\r\n");
					
    							echo "<tr>\n";
    							echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_app."\" title=\"".learner_profile."\"><b>".$identifiant_apprenant."</b></a></td>";
    							if (file_exists("../docs/".$lien_file)){
    								echo "\n<td class=\"affichage_table\"><b><a href=\"../includes/download.php?f=".$lien_file."\">".download."</a></b></td>";
    							  $files_zip[] = "../docs/".$lien_file;
   									$apps_rendus[] = $id_app;
   								} else echo "\n<td class=\"affichage_table\"><b><font color=\"red\">".introuvable."</font></b></td>";
    							echo "\n<td class=\"affichage_table\"><b>".$date_file."</b></td>";
    							echo "\n<td class=\"affichage_table\"><b><input name=\"note_".$id_app."\" value=\"".$note_devoir."\" type=\"text\" size=\"2\" maxlength=\"5\"> /20</b></td>";
    							echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_devoir_rendu&id_devoir_rendu=".$devoir_rendu[4]."&key=".$key."','".confirm_supprimer_file."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
    							echo "</tr>\n";
   						}
   						echo "\n</table>";
							if (count($files_zip) > 0){
    						require("ziplib.php");
								$zip = new zipfile();
   							$i = 0;
    						while (count($files_zip) > $i) {
        					$fo = fopen($files_zip[$i],'r');
        					$contenu = fread($fo, filesize($files_zip[$i]));
        					fclose($fo);
        					$zip->addfile($contenu, $files_zip[$i]);
        					$i++;
    						}
    						$archive = $zip->file();
    						$lien_zip = $id_devoir."_".special_chars($titre_devoir).".zip";
    						$open = fopen("../docs/".$lien_zip,"wb");
    						fwrite($open, $archive);
    						fclose($open);
    						echo "<p align=\"center\"><a href=\"../includes/download.php?f=".$lien_zip."\"><b>".download_homework_zip."</b></a></p>";
    					} else echo "<br />";
   					} else echo aucun;
   					if (count($apps_rendus) > 0){
    						$chaine_apps_req = implode(",", $apps_rendus);
    						$select_apps_non_rendu = mysql_query("select id_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants` where active_apprenant = '1' and id_apprenant NOT IN (".$chaine_apps_req.");");
    				} else $select_apps_non_rendu = mysql_query("select id_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants` where active_apprenant = '1';");
    						
    						echo "<li><u><b>".learners_not_uploaded_homework." : </b></u></li><br />";
    						if (mysql_num_rows($select_apps_non_rendu) > 0){
									echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
									echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
									echo "\n<td class=\"affichage_table\"><b>".mark."</b></td>";
									echo "</tr>";
    							while($apps_non_rendu = mysql_fetch_row($select_apps_non_rendu)){
    								$id_app = $apps_non_rendu[0];
    								$identifiant_apprenant = html_ent($apps_non_rendu[1]);
   								
   									$select_note_devoir = mysql_query("select note_devoir from `" . $tblprefix . "devoirs_notes` where id_devoir = $id_devoir and id_apprenant = $id_app;");
    	  						if (mysql_num_rows($select_note_devoir) == 1)
    									$note_devoir = mysql_result($select_note_devoir,0);
    								else $note_devoir = "00.00";

    								$csv_notes_devoir2  = "\"".$identifiant_apprenant."\";\"".$note_devoir."\";";
										$csv_notes_devoir2 = mb_convert_encoding($csv_notes_devoir2, 'ISO-8859-1', 'UTF-8');
										fwrite($csv_notes_devoir_file,$csv_notes_devoir2."\r\n");
									
    								echo "<tr>\n";
    								echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_app."\" title=\"".learner_profile."\"><b>".$identifiant_apprenant."</b></a></td>";
    								echo "\n<td class=\"affichage_table\"><b><input name=\"note_".$id_app."\" value=\"".$note_devoir."\" type=\"text\" size=\"2\" maxlength=\"5\"> /20</b></td>";
    								echo "</tr>\n";
    							}
    							echo "\n</table>";
    						} else echo none;
    					echo "<hr />";
    					echo "<p align=\"center\"><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .save_marks. "\"></p></form>";
						
						fclose ($csv_notes_devoir_file);
						if (file_exists("../docs/".$csv_notes_devoir_titre))
							echo "<hr /><p align=\"center\"><a href=\"../includes/download.php?f=".$csv_notes_devoir_titre."\"><b>".download_homework_csv."</b></a></p>";
					}

//*********** Telecharger devoir par classe

					else {
					 require("ziplib.php");
					 $tab_acces_devoir = explode("-",trim($devoir[2],"-"));
    			 if (!empty($tab_acces_devoir[0])){
    			 	echo "<form method=\"POST\" action=\"\">";
						foreach ($tab_acces_devoir as $classe_devoir){
							
    					$select_classe = mysql_query("select classe from `" . $tblprefix . "classes` where id_classe = $classe_devoir;");
    	  			if (mysql_num_rows($select_classe) == 1)
    						$classe_apprenant = html_ent(mysql_result($select_classe,0));
    					else $classe_apprenant = "";
							$apps_rendus = array();
							
							echo "<h3><u>".$classe_apprenant." : </u></h3>";
							echo "<li><u><b>".homework_assignments." : </b></u></li><br />";

							$csv_notes_devoir_titre = $id_devoir."_".special_chars($classe_apprenant)."_".special_chars($titre_devoir)."_marks.csv";
							$csv_notes_devoir_file = fopen("../docs/".$csv_notes_devoir_titre,"w");
    					$csv_notes_devoir1  = "\"".identifiant."\";\"".mark."\";";
							$csv_notes_devoir1 = mb_convert_encoding($csv_notes_devoir1, 'ISO-8859-1', 'UTF-8');
							fwrite($csv_notes_devoir_file,$csv_notes_devoir1."\r\n");
						
							$select_devoir_rendu = mysql_query("select identifiant_apprenant,lien_file,date_file, `" . $tblprefix . "devoirs_rendus`.id_apprenant from `" . $tblprefix . "devoirs_rendus`,`" . $tblprefix . "apprenants` where `" . $tblprefix . "devoirs_rendus`.id_apprenant = `" . $tblprefix . "apprenants`.id_apprenant and `" . $tblprefix . "apprenants`.id_classe = ".$classe_devoir." and id_devoir = $id_devoir;");
    					if (mysql_num_rows($select_devoir_rendu) > 0){
								echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
								echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
								echo "\n<td class=\"affichage_table\"><b>".devoir."</b></td>";
								echo "\n<td class=\"affichage_table\"><b>".date_ajout."</b></td>";
								echo "\n<td class=\"affichage_table\"><b>".mark."</b></td>";
								echo "</tr>";
    						$files_zip = array();
    						
    						while($devoir_rendu = mysql_fetch_row($select_devoir_rendu)){
    							$identifiant_apprenant = html_ent($devoir_rendu[0]);
    							$lien_file = html_ent($devoir_rendu[1]);
    							$date_file = set_date($dateformat,$devoir_rendu[2]);
    							$id_app = $devoir_rendu[3];
    							if (file_exists("../docs/".$lien_file)){
   									$select_note_devoir = mysql_query("select note_devoir from `" . $tblprefix . "devoirs_notes` where id_devoir = $id_devoir and id_apprenant = $id_app;");
    	  						if (mysql_num_rows($select_note_devoir) == 1)
    									$note_devoir = mysql_result($select_note_devoir,0);
    								else $note_devoir = "00.00";

    								$csv_notes_devoir2  = "\"".$identifiant_apprenant."\";\"".$note_devoir."\";";
										$csv_notes_devoir2 = mb_convert_encoding($csv_notes_devoir2, 'ISO-8859-1', 'UTF-8');
										fwrite($csv_notes_devoir_file,$csv_notes_devoir2."\r\n");
									
    								echo "<tr>\n";
    								echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_app."\" title=\"".learner_profile."\"><b>".$identifiant_apprenant."</b></a></td>";
    								echo "\n<td class=\"affichage_table\"><b><a href=\"../includes/download.php?f=".$lien_file."\">".download."</a></b></td>";
    								echo "\n<td class=\"affichage_table\"><b>".$date_file."</b></td>";
    								echo "\n<td class=\"affichage_table\"><b><input name=\"note_".$id_app."\" value=\"".$note_devoir."\" type=\"text\" size=\"2\" maxlength=\"5\"> /20</b></td>";
    								echo "</tr>\n";
    								$files_zip[] = "../docs/".$lien_file;
    								$apps_rendus[] = $id_app;
    							}
    						}
    						echo "\n</table>";
    						if (count($files_zip) > 0){
									$zip = new zipfile();
   								$i = 0;
    							while (count($files_zip) > $i) {
        						$fo = fopen($files_zip[$i],'r');
        						$contenu = fread($fo, filesize($files_zip[$i]));
        						fclose($fo);
        						$zip->addfile($contenu, $files_zip[$i]);
        						$i++;
    							}
    							$archive = $zip->file();
    							$lien_zip = $id_devoir."_".special_chars($classe_apprenant)."_".special_chars($titre_devoir).".zip";
    							$open = fopen("../docs/".$lien_zip,"wb");
    							fwrite($open, $archive);
    							fclose($open);
    							echo "<p align=\"center\"><a href=\"../includes/download.php?f=".$lien_zip."\"><b>".download_homework_zip." : ".$classe_apprenant."</b></a></p>";
    						}
    					} else echo aucun;
    					if (count($apps_rendus) > 0){
    						$chaine_apps_req = implode(",", $apps_rendus);
    						$select_apps_non_rendu = mysql_query("select id_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants` where id_classe = $classe_devoir and active_apprenant = '1' and id_apprenant NOT IN (".$chaine_apps_req.");");
    					} else $select_apps_non_rendu = mysql_query("select id_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants` where id_classe = $classe_devoir and active_apprenant = '1';");
    						
    						echo "<li><u><b>".learners_not_uploaded_homework." : </b></u></li><br />";
    						if (mysql_num_rows($select_apps_non_rendu) > 0){
									echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
									echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
									echo "\n<td class=\"affichage_table\"><b>".mark."</b></td>";
									echo "</tr>";
    							while($apps_non_rendu = mysql_fetch_row($select_apps_non_rendu)){
    								$id_app = $apps_non_rendu[0];
    								$identifiant_apprenant = html_ent($apps_non_rendu[1]);

   									$select_note_devoir = mysql_query("select note_devoir from `" . $tblprefix . "devoirs_notes` where id_devoir = $id_devoir and id_apprenant = $id_app;");
    	  						if (mysql_num_rows($select_note_devoir) == 1)
    									$note_devoir = mysql_result($select_note_devoir,0);
    								else $note_devoir = "00.00";

    								$csv_notes_devoir2  = "\"".$identifiant_apprenant."\";\"".$note_devoir."\";";
										$csv_notes_devoir2 = mb_convert_encoding($csv_notes_devoir2, 'ISO-8859-1', 'UTF-8');
										fwrite($csv_notes_devoir_file,$csv_notes_devoir2."\r\n");
										
    								echo "<tr>\n";
    								echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_app."\" title=\"".learner_profile."\"><b>".$identifiant_apprenant."</b></a></td>";
    								echo "\n<td class=\"affichage_table\"><b><input name=\"note_".$id_app."\" value=\"".$note_devoir."\" type=\"text\" size=\"2\" maxlength=\"5\"> /20</b></td>";
    								echo "</tr>\n";
    							}
    							echo "\n</table>";
    						} else echo none;
    					
    					fclose ($csv_notes_devoir_file);
							if (file_exists("../docs/".$csv_notes_devoir_titre))
								echo "<p align=\"center\"><a href=\"../includes/download.php?f=".$csv_notes_devoir_titre."\"><b>".download_homework_csv." : ".$classe_apprenant."</b></a></p>";
							echo "<hr />";
    				}
    				echo "<p align=\"center\"><input type=\"hidden\" name=\"send\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"" .save_marks. "\"></p></form>";
    			 }
    			}
    			echo "</ul>";
				}	else locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#devoir");
			} else locationhref_admin("?inc=edit_tutorials&do=open_chapitre&id_chap=".$id_chapitre."#devoir");
		 }
    } break;
    
    // ****************** liste_tutos **************************
    default : {
    	echo "<table border=\"0\"><tr><td><a href=\"?inc=edit_tutorials&do=create_tuto\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td><td><a href=\"?inc=edit_tutorials&do=create_tuto\"><b>".creer_tuto."</b></a></td></tr></table>";

	if (isset($_GET['t']) && ctype_digit($_GET['t']))
		$page2 = intval($_GET['t']);
	else $page2 = 1;

    // En attente de validation
    	echo "<hr /><a name=\"en_attente\"><b><u>- ".tutoriels_attente_validation." : </u></b></a><br /><br />";

  $select_tutos1 = mysql_query("select * from `" . $tblprefix . "tutoriels` where publie_tutoriel = '1' order by ordre_tutoriel;");
	$nbr_trouve = mysql_num_rows($select_tutos1);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}
    	$select_tutos1_limit = mysql_query("select * from `" . $tblprefix . "tutoriels` where publie_tutoriel = '1' order by ordre_tutoriel limit $limit, $nbr_resultats;");
    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".tutoriel."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".acces_cours."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";

				while($tutoriel = mysql_fetch_row($select_tutos1_limit)){
					
					$titre_tutoriel = html_ent($tutoriel[2]);
					$titre_tutoriel = readmore($titre_tutoriel,$max_len);

					if ($tutoriel[13] == "*")
						$acces_tuto = acces_ouvert;
					else if ($tutoriel[13] == "0")
						$acces_tuto = all_registered_learners;
					else {
						$acces_tuto = classe." : ";
						$tab_acces_tuto = explode("-",trim($tutoriel[13],"-"));
						if (!empty($tab_acces_tuto[0])){
							$chaine_acces_tuto = implode(",",$tab_acces_tuto);
							$select_classes = mysql_query("select * from `" . $tblprefix . "classes` where id_classe in (".$chaine_acces_tuto.");");
							if (mysql_num_rows($select_classes) > 0){
    						while($classe = mysql_fetch_row($select_classes))
    							$acces_tuto .= "<u>".$classe[1]."</u>, ";
    					}
    				}
					}

					if ($tutoriel[1] == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$tutoriel[0]."\" title=\"".ouvrir_tuto."\"><b>".$titre_tutoriel."</b></a></td>";

					echo "\n<td class=\"affichage_table\"><b>".$acces_tuto."</b></td>";

    			$select_auteur = mysql_query("select identifiant_user from `" . $tblprefix . "users` where id_user = $tutoriel[1];");
    			if (mysql_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysql_result($select_auteur,0));
    			else $auteur = inconnu;
    			$auteur = wordwrap($auteur,15,"<br />",true);
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$tutoriel[1]."\" title=\"".user_profile."\"><b>".$auteur."</b></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=update_tuto&id_tuto=".$tutoriel[0]."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_tuto&id_tuto=".$tutoriel[0]."&key=".$key."','".confirm_supprimer_tuto."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?tutorial=".$tutoriel[0]."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=publier_tuto&id_tuto=".$tutoriel[0]."&key=".$key."\" title=\"".publier_element."\"><b>".publier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_tutorials&l=".$page_precedente."&t=".$page2."#en_attente\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_tutorials&l=".$page_precedente."&t=".$page2."#en_attente\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=edit_tutorials&l=".$i."&t=".$page2."#en_attente\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_tutorials&l=".$page_suivante."&t=".$page2."#en_attente\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_tutorials&l=".$page_suivante."&t=".$page2."#en_attente\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
    	} else echo aucun_tutoriel_non_valide."<br />";

    // validés
    	echo "<br /><hr /><a name=\"valides\"><b><u>- ".tutoriels_valides." : </u></b></a><br /><br />";

    	$select_tutos2 = mysql_query("select * from `" . $tblprefix . "tutoriels` where publie_tutoriel = '2' order by ordre_tutoriel;");
			 $nbr_trouve = mysql_num_rows($select_tutos2);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page2 <= $page_max && $page2 > 1 && $page_max > 1)
					$limit = ($page2 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page2 = 1;
				}

    	$select_tutos2_limit = mysql_query("select * from `" . $tblprefix . "tutoriels` where publie_tutoriel = '2' order by ordre_tutoriel limit $limit, $nbr_resultats;");

    		echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".tutoriel."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".acces_cours."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".cree_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".valide_par."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".editer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".previsualiser."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ordonner."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".action."</b></td>";
				echo "</tr>";
				
				$i_ordre = ($page - 1) * $nbr_resultats + 1;
				while($tutoriel = mysql_fetch_row($select_tutos2_limit)){
					
					$titre_tutoriel = html_ent($tutoriel[2]);
					$titre_tutoriel = readmore($titre_tutoriel,$max_len);

					if ($tutoriel[13] == "*")
						$acces_tuto = acces_ouvert;
					else if ($tutoriel[13] == "0")
						$acces_tuto = all_registered_learners;
					else {
						$acces_tuto = classe." : ";
						$tab_acces_tuto = explode("-",trim($tutoriel[13],"-"));
						if (!empty($tab_acces_tuto[0])){
							$chaine_acces_tuto = implode(",",$tab_acces_tuto);
							$select_classes = mysql_query("select * from `" . $tblprefix . "classes` where id_classe in (".$chaine_acces_tuto.");");
							if (mysql_num_rows($select_classes) > 0){
    						while($classe = mysql_fetch_row($select_classes))
    							$acces_tuto .= "<u>".$classe[1]."</u>, ";
    					}
    				}
					}
					
					if ($tutoriel[1] == $id_user_session)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
					
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=open_tuto&id_tuto=".$tutoriel[0]."\" title=\"".ouvrir_tuto."\"><b>".$titre_tutoriel."</b></a></td>";
					
					echo "\n<td class=\"affichage_table\"><b>".$acces_tuto."</b></td>";
					
    			$select_auteur = mysql_query("select identifiant_user from `" . $tblprefix . "users` where id_user = $tutoriel[1];");
    			if (mysql_num_rows($select_auteur) == 1)
    				$auteur = html_ent(mysql_result($select_auteur,0));
    			else $auteur = inconnu;
    			$auteur = wordwrap($auteur,15,"<br />",true);
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$tutoriel[1]."\" title=\"".user_profile."\"><b>".$auteur."</b></a></td>";

    			$select_validateur = mysql_query("select identifiant_user from `" . $tblprefix . "users` where id_user = $tutoriel[12];");
    			if (mysql_num_rows($select_validateur) == 1)
    				$validateur = html_ent(mysql_result($select_validateur,0));
    			else $validateur = inconnu;
    			$validateur = wordwrap($validateur,15,"<br />",true);
					echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$tutoriel[12]."\" title=\"".user_profile."\"><b>".$validateur."</b></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=update_tuto&id_tuto=".$tutoriel[0]."\" title=\"".editer."\"><img border=\"0\" src=\"../images/others/edit.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=edit_tutorials&do=delete_tuto&id_tuto=".$tutoriel[0]."&key=".$key."','".confirm_supprimer_tuto."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a target=\"_blank\" href=\"../?tutorial=".$tutoriel[0]."\" title=\"".previsualiser."\"><img border=\"0\" src=\"../images/others/view.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\" nowrap=\"nowrap\">";
					$tuto_precedent = mysql_query ("select id_tutoriel from `" . $tblprefix . "tutoriels` where ordre_tutoriel < $tutoriel[9] and publie_tutoriel = '2' order by ordre_tutoriel desc;");
					if (mysql_num_rows($tuto_precedent) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderup_tuto&id_tuto=".$tutoriel[0]."&key=".$key."\" title=\"".deplacer_haut."\"><img border=\"0\" src=\"../images/others/up.png\" width=\"15\" height=\"15\" /></a>";
					else
						echo "<img border=\"0\" src=\"../images/others/up2.png\" width=\"15\" height=\"15\" />";
					echo "<b> ".$i_ordre." </b>";
					$i_ordre++;
					$tuto_suivant = mysql_query ("select id_tutoriel from `" . $tblprefix . "tutoriels` where ordre_tutoriel > $tutoriel[9] and publie_tutoriel = '2' order by ordre_tutoriel;");
					if (mysql_num_rows($tuto_suivant) > 0)
						echo "<a href=\"?inc=edit_tutorials&do=orderdown_tuto&id_tuto=".$tutoriel[0]."&key=".$key."\" title=\"".deplacer_bas."\"><img border=\"0\" src=\"../images/others/down.png\" width=\"15\" height=\"15\" /></a>";
					else echo "<img border=\"0\" src=\"../images/others/down2.png\" width=\"15\" height=\"15\" />";
					echo "</td>";

					echo "\n<td class=\"affichage_table\"><a href=\"?inc=edit_tutorials&do=depublier_tuto&id_tuto=".$tutoriel[0]."&key=".$key."\" title=\"".depublier_element."\"><b>".depublier."</b></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page2 - 1;
			$page_suivante = $page2 + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=edit_tutorials&t=".$page_precedente."&l=".$page."#valides\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=edit_tutorials&t=".$page_precedente."&l=".$page."#valides\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page2) echo "<a href=\"?inc=edit_tutorials&t=".$i."&l=".$page."#valides\">";
				echo "<b>".$i."</b>";
				if ($i != $page2) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=edit_tutorials&t=".$page_suivante."&l=".$page."#valides\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=edit_tutorials&t=".$page_suivante."&l=".$page."#valides\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
    	} else echo aucun_tutoriel_valide."<br />";
    }
  }
} else echo restricted_access;

?>