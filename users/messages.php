<?php
/*
 * 	Manhali - Free Learning Management System
 *	messages.php
 *	2009-05-14 23:43
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

	echo "<div id=\"titre\">".messagerie."</div><br />";
	
	confirmer();
	
	$max_len = 100;
	$max_len2 = 30;
	
	if (isset($_GET['id_msg']) && ctype_digit($_GET['id_msg']))
		$id_msg = intval($_GET['id_msg']);
	else $id_msg = 0;

	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";

	// need classe
	$select_demande_classe = $connect->query("select demander_classe from `" . $tblprefix . "site_infos`;");
	if (mysqli_num_rows($select_demande_classe) == 1) {
		$select_classes = $connect->query("select * from `" . $tblprefix . "classes`;");
		if (mysqli_num_rows($select_classes) > 0 && mysqli_result($select_demande_classe,0) == 1)
			$need_classe = 1;
		else $need_classe = 0;
	} else $need_classe = 0;
					
	switch ($do){

	// ****************** open_msg **************************
		case "open_msg" : {
			
			goback_button();

			if ($grade_user_session == "3" || $grade_user_session == "2")
				$select_un_msg = $connect->query("select * from `" . $tblprefix . "messages` where id_message = $id_msg and (id_destinataires like '%-$id_user_session-%' or (id_destinataires = '*' and id_destinataires_app = '*') or id_emetteur = $id_user_session);");
			else
				$select_un_msg = $connect->query("select * from `" . $tblprefix . "messages` where id_message = $id_msg and (id_destinataires like '%-$id_user_session-%' or id_emetteur = $id_user_session);");

			if (mysqli_num_rows($select_un_msg) == 1) {
				
				$id_emetteur = mysqli_result($select_un_msg,0,1);
				$id_emetteur_app = mysqli_result($select_un_msg,0,2);
				$mail_msg = html_ent(mysqli_result($select_un_msg,0,4));
				$titre_msg = html_ent(mysqli_result($select_un_msg,0,7));
				$contenu_msg = bbcode_br(html_ent(mysqli_result($select_un_msg,0,8)));
				$contenu_msg = preg_replace("#\[\#\](.+)\[/\#\]#i", "<div id=\"fwd\">$1</div>", $contenu_msg);
				$contenu_msg = preg_replace("#\[\#\]|\[/\#\]#", " ", $contenu_msg);
				$lu_message = mysqli_result($select_un_msg,0,9);
				$ladate = mysqli_result($select_un_msg,0,11);
				$destinataires = html_ent(mysqli_result($select_un_msg,0,12));
				$destinataires_app = html_ent(mysqli_result($select_un_msg,0,13));
				
				echo "<table border=\"0\"><tr>";
				
				if($id_emetteur == 0 && $id_emetteur_app == 0){
					$emetteur = html_ent(mysqli_result($select_un_msg,0,3));
					echo "<td align=\"center\"><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/icones/delete.png\" width=\"50\" height=\"50\" /></a><br /><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><b>".supprimer."</b></a></td>";
					echo "</tr></table><br />";
				}
				else {
					
					if ($id_emetteur != $id_user_session)
						echo "<td align=\"center\"><a href=\"?inc=messages&do=reply&id_msg=".$id_msg."\"><img border=\"0\" src=\"../images/others/reply.png\" width=\"50\" height=\"50\" /></a><br /><a href=\"?inc=messages&do=reply&id_msg=".$id_msg."\"><b>".reply."</b></a> - </td>";
					
					echo "<td align=\"center\"><a href=\"?inc=messages&do=forward&id_msg=".$id_msg."\"><img border=\"0\" src=\"../images/others/forward.png\" width=\"50\" height=\"50\" /></a><br /><a href=\"?inc=messages&do=forward&id_msg=".$id_msg."\"><b>".forward."</b></a> - </td>";
					
					if ($id_emetteur != $id_user_session)
						echo "<td align=\"center\"><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/icones/delete.png\" width=\"50\" height=\"50\" /></a><br /><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><b>".supprimer."</b></a></td>";
					else
						echo "<td align=\"center\"><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_outbox_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/icones/delete.png\" width=\"50\" height=\"50\" /></a><br /><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_outbox_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><b>".supprimer."</b></a></td>";

					echo "</tr></table><br />";
					
					if ($id_emetteur != 0){
   					$select_emetteur = $connect->query("select nom_user, identifiant_user from `" . $tblprefix . "users` where id_user = $id_emetteur;");
   					if (mysqli_num_rows($select_emetteur) == 1){
    					$nom_user = html_ent(mysqli_result($select_emetteur,0,0));
    					$identifiant_user = html_ent(mysqli_result($select_emetteur,0,1));
    					$emetteur = $identifiant_user;
    					if (!empty($nom_user))
    						$emetteur .= " (".$nom_user.")";
   					}
   					else $emetteur = inconnu;
   				}
   				else if ($id_emetteur_app != 0){
   					$select_emetteur_app = $connect->query("select nom_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $id_emetteur_app;");
   					if (mysqli_num_rows($select_emetteur_app) == 1){
    					$nom_apprenant = html_ent(mysqli_result($select_emetteur_app,0,0));
    					$identifiant_apprenant = html_ent(mysqli_result($select_emetteur_app,0,1));
    					$emetteur = $identifiant_apprenant;
    					if (!empty($nom_apprenant))
    						$emetteur .= " (".$nom_apprenant.")";
   					}
   					else $emetteur = inconnu;
   				}
				}
				
				if (strpos($lu_message,"-".$id_user_session."-") === false){
					$lu_message .= $id_user_session."-";
					$update_lu_message = $connect->query("update `" . $tblprefix . "messages` set lu_message = '$lu_message' where id_message = $id_msg;");
				}

				$date_msg = set_date($dateformat,$ladate);
    			
				//  *** message *** 

				if($id_emetteur == 0 && $id_emetteur_app == 0)
					echo "<b>".from." :</b> ".$emetteur."<br /><br />";
				else if ($id_emetteur != 0)
					echo "<b>".from." :</b> <a href=\"../?profiles=".$id_emetteur."\" title=\"".user_profile."\">".$emetteur."</a><br /><br />";
				else if ($id_emetteur_app != 0)
					echo "<b>".from." :</b> <a href=\"../?s_profiles=".$id_emetteur."\" title=\"".learner_profile."\">".$emetteur."</a><br /><br />";
					
				echo "<b>".to." : </b><ul>";
				
				$select_users = $connect->query("select id_user, identifiant_user, grade_user from `" . $tblprefix . "users`;");
    		if (mysqli_num_rows($select_users) > 0){
    			echo "<li><b>".users." : </b>";
    			while($user = mysql_fetch_row($select_users)){
    				$id_user = $user[0];
    				$identifiant_user = html_ent($user[1]);
    				$identifiant_user = readmore($identifiant_user,$max_len2);
						$grade_user = $user[2];
    				if (strpos($destinataires,"-".$id_user."-")!== false || ($destinataires == "*" && $destinataires_app == "*" && ($grade_user == "3" || $grade_user == "2")))
							echo "<a href=\"../?profiles=".$id_user."\" title=\"".user_profile."\">".$identifiant_user."</a>, ";
    			}
    			echo "</li>";
    		}
    		$select_learners = $connect->query("select id_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants`;");
    		if (mysqli_num_rows($select_learners) > 0){
    			echo "<li><b>".learners." : </b>";
    			while($apprenant = mysql_fetch_row($select_learners)){
    				$id_apprenant = $apprenant[0];
    				$identifiant_apprenant = html_ent($apprenant[1]);
    				$identifiant_apprenant = readmore($identifiant_apprenant,$max_len2);
    				if (strpos($destinataires_app,"-".$id_apprenant."-")!== false)
							echo "<a href=\"../?s_profiles=".$id_apprenant."\" title=\"".learner_profile."\">".$identifiant_apprenant."</a>, ";
    			}
    			echo "</li>";
    		}
    		echo "</ul>";
				if (!empty($mail_msg))
					echo "<b>".email." :</b> ".$mail_msg."<br /><br />";
				
				echo "<b>".ladate." :</b> ".$date_msg."<br /><br />";
				echo "<b>".titre_msg." :</b> ".$titre_msg."<br /><br />";
				echo "<b>".msg." :</b><br />".$contenu_msg."<br /><br />";

				//affichage suivant precedent
				if ($id_emetteur == $id_user_session){
						$msg_precedent = $connect->query ("select id_message from `" . $tblprefix . "messages` where date_message > $ladate and id_emetteur = $id_user_session order by date_message;");
						$msg_suivant   = $connect->query ("select id_message from `" . $tblprefix . "messages` where date_message < $ladate and id_emetteur = $id_user_session order by date_message desc;");
				}
				else{
					if ($grade_user_session == "3" || $grade_user_session == "2"){
						$msg_precedent = $connect->query ("select id_message from `" . $tblprefix . "messages` where date_message > $ladate and (id_destinataires like '%-$id_user_session-%' or (id_destinataires = '*' and id_destinataires_app = '*')) order by date_message;");
						$msg_suivant   = $connect->query ("select id_message from `" . $tblprefix . "messages` where date_message < $ladate and (id_destinataires like '%-$id_user_session-%' or (id_destinataires = '*' and id_destinataires_app = '*')) order by date_message desc;");
					}
					else {
						$msg_precedent = $connect->query ("select id_message from `" . $tblprefix . "messages` where date_message > $ladate and id_destinataires like '%-$id_user_session-%' order by date_message;");
						$msg_suivant   = $connect->query ("select id_message from `" . $tblprefix . "messages` where date_message < $ladate and id_destinataires like '%-$id_user_session-%' order by date_message desc;");
					}
				}
				echo "<table width=\"50%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr><td align=\"center\" width=\"50%\" style=\"border: 1px solid #000000;\"><b>";

				if (mysqli_num_rows($msg_precedent) > 0){
					$id_msg_precedent = mysqli_result($msg_precedent,0);
					echo "<table border=\"0\"><tr><td><a href=\"?inc=messages&do=open_msg&id_msg=".$id_msg_precedent."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=messages&do=open_msg&id_msg=".$id_msg_precedent."\"><b>".msg_precedent."</b></a></td></tr></table>";
				} else
					echo "<table border=\"0\"><tr><td><img border=\"0\" src=\"../images/others/precedent2.png\" width=\"32\" height=\"32\" /></td><td><b>".msg_precedent."</b></td></tr></table>";
				
				echo "</b></td><td align=\"center\" width=\"50%\" style=\"border: 1px solid #000000;\"><b>";

				if (mysqli_num_rows($msg_suivant) > 0){
					$id_msg_suivant = mysqli_result($msg_suivant,0);
					echo "<table border=\"0\"><tr><td><a href=\"?inc=messages&do=open_msg&id_msg=".$id_msg_suivant."\"><b>".msg_suivant."</b></a></td><td><a href=\"?inc=messages&do=open_msg&id_msg=".$id_msg_suivant."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td></tr></table>";
				} else
					echo "<table border=\"0\"><tr><td><b>".msg_suivant."</b></td><td><img border=\"0\" src=\"../images/others/suivant2.png\" width=\"32\" height=\"32\" /></td></tr></table>";
				echo "</b></td></tr></table>";

			} else locationhref_admin("?inc=messages");
		} break;

	// ****************** delete_msg **************************
		case "delete_msg" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_dest_msg = $connect->query("select id_destinataires, id_destinataires_app from `" . $tblprefix . "messages` where id_message = $id_msg;");
				if (mysqli_num_rows($select_dest_msg) == 1) {
					$id_destinataires = mysqli_result($select_dest_msg,0,0);
					$id_destinataires_app = mysqli_result($select_dest_msg,0,1);
					if ($id_destinataires == "*" && $id_destinataires_app == "*" && ($grade_user_session == "3" || $grade_user_session == "2"))
						$delete_msg = $connect->query("delete from `" . $tblprefix . "messages` where id_message = $id_msg;");
					else if (strpos($id_destinataires,"-".$id_user_session."-") !== false){
						$tab_dest = explode("-",$id_destinataires);
						unset($tab_dest[array_search($id_user_session, $tab_dest)]);
						$id_destinataires2 = implode("-",$tab_dest);
						$update_dest_message = $connect->query("update `" . $tblprefix . "messages` set id_destinataires = '$id_destinataires2' where id_message = $id_msg;");
					}
				}
			}
			locationhref_admin("?inc=messages");
		} break;

	// ****************** delete_outbox_msg **************************
		case "delete_outbox_msg" : {
			if (isset($_GET['key']) && $_GET['key'] == $key){
				$select_outbox_msg = $connect->query("select id_emetteur from `" . $tblprefix . "messages` where id_message = $id_msg;");
				if (mysqli_num_rows($select_outbox_msg) == 1 && mysqli_result($select_outbox_msg,0) == $id_user_session)
						$update_outbox_msg = $connect->query("update `" . $tblprefix . "messages` set deleted_from_outbox = '1' where id_message = $id_msg;");
			}
			locationhref_admin("?inc=messages&do=outbox");
		} break;
		
	// ****************** new_msg **************************
		case "new_msg" : {
			echo "<div id=\"titre\">".new_msg."</div><br />";
			if (isset($_POST['titre_msg']) && isset($_POST['contenu_msg'])){
    	 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 	$_SESSION['random_key'] = $_POST['random'];
				$titre_msg = escape_string(trim($_POST['titre_msg']));
				$contenu_msg = escape_string(trim($_POST['contenu_msg']));
				if (!empty($titre_msg) && !empty($contenu_msg)){
					
				  if (isset($_POST['destinataires']) && !empty($_POST['destinataires']))
				  	$chaine_users = "-".implode("-",$_POST['destinataires'])."-";
				  else $chaine_users = "-";
				  
				  $dest_apprenants = array();
				  if ($need_classe == 1){
				  	if (isset($_POST['classes_app']) && !empty($_POST['classes_app'])){
				  		foreach ($_POST['classes_app'] as $elem_classe){
				  			$select_app_classe = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where id_classe = $elem_classe;");
								if (mysqli_num_rows($select_app_classe) > 0){
									while($app_classe = mysql_fetch_row($select_app_classe)){
										if (!in_array($app_classe[0],$dest_apprenants))
											$dest_apprenants[] = $app_classe[0];
									}
								}
				  		}
				  	}
					}
				  if (isset($_POST['destinataires_app']) && !empty($_POST['destinataires_app'])){
				  	foreach ($_POST['destinataires_app'] as $elem_app){
				  		if (!in_array($elem_app,$dest_apprenants))
							$dest_apprenants[] = $elem_app;
						}
				  }
				  if (isset($dest_apprenants) && count($dest_apprenants) > 0)
				  	$chaine_apps = "-".implode("-",$dest_apprenants)."-";
				  else $chaine_apps = "-";

    			if ($chaine_users != "-" || $chaine_apps != "-"){
    				$insertmessage = "INSERT INTO `" . $tblprefix . "messages` VALUES (NULL,$id_user_session,0,'','','$chaine_users','$chaine_apps','".$titre_msg."','".$contenu_msg."','-','-',".time().",'$chaine_users','$chaine_apps','0');";
	          $connect->query($insertmessage,$connect);
	          redirection(message_succes,"?inc=messages",3,"tips",1);
    			} else goback(choisir_distinataire,2,"error",1);
				} else goback(tous_champs,2,"error",1);
			 } else goback(err_data_saved,2,"error",1);
			}
			else{
				echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";
				goback_button();
				if (!empty($_GET['touser']))
					$touser = $_GET['touser'];
				else $touser = "";
				if (!empty($_GET['tolearner']))
					$tolearner = $_GET['tolearner'];
				else $tolearner = "";
    		$select_users = $connect->query("select id_user, identifiant_user, grade_user from `" . $tblprefix . "users` where id_user != $id_user_session order by grade_user desc;");
    		$select_apprenants = $connect->query("select id_apprenant, id_classe, identifiant_apprenant from `" . $tblprefix . "apprenants` order by id_classe desc;");
    			if (mysqli_num_rows($select_users) > 0 || mysqli_num_rows($select_apprenants) > 0){
    			 echo "\n<form method=\"POST\" action=\"\">";
					 echo "<b><u>" .to. " :</u></b><br /><br />\n";
					 echo "<table border=\"0\"><tr><td align=\"center\">";
					 if (mysqli_num_rows($select_users) > 0){
						echo "<b>".users."</b><br /><select size=\"10\" name=\"destinataires[]\" id=\"destinataires\" multiple=\"multiple\">";
    				while($user = mysql_fetch_row($select_users)){
    					$id_user = $user[0];
    					$identifiant_user = html_ent($user[1]);
    					$grade_user = $grade_tab[$user[2]];
    					echo "\n<option value=\"".$id_user."\"";
    					if ($id_user == $touser)
    						echo " selected=\"selected\"";
    					echo ">".$identifiant_user." (".$grade_user.")</option>";
    				}
						echo "\n</select><br />";
						echo "<input type=\"button\" value=\"".select_all."\" onclick=\"selectAll('destinataires',true)\" /><br />";
						echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('destinataires',false)\" />";
						echo "</td><td align=\"center\">";
					 }
					 if (mysqli_num_rows($select_apprenants) > 0){
						echo "<b>".learners."</b><br /><select size=\"10\" name=\"destinataires_app[]\" id=\"destinataires_app\" multiple=\"multiple\">";
    				while($learner = mysql_fetch_row($select_apprenants)){
    					$id_apprenant = $learner[0];
    					$id_classe = $learner[1];
    					$identifiant_apprenant = html_ent($learner[2]);
    					echo "\n<option value=\"".$id_apprenant."\"";
    					if ($id_apprenant == $tolearner)
    						echo " selected=\"selected\"";
    					echo ">".$identifiant_apprenant;
    					$select_classe = $connect->query("select classe from `" . $tblprefix . "classes` where id_classe = $id_classe;");
    	  			if (mysqli_num_rows($select_classe) == 1){
    						$classe_apprenant = html_ent(mysqli_result($select_classe,0));
    						echo " (".$classe_apprenant.")";
    					}
    					echo "</option>";
    				}
						echo "\n</select><br />";
						echo "<input type=\"button\" value=\"".select_all."\" onclick=\"selectAll('destinataires_app',true)\" /><br />";
						echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('destinataires_app',false)\" />";
					 }
					 if ($need_classe == 1){
					 		echo "</td><td align=\"center\" valign=\"top\">";
	      			echo "<b>" .classe. " : </b><br /><select size=\"10\" name=\"classes_app[]\" id=\"classes_app\" multiple=\"multiple\">";
    					while($classe = mysql_fetch_row($select_classes)){
    						$id_classe = $classe[0];
    						$nom_classe = $classe[1];
								echo "<option value=\"".$id_classe."\">".$nom_classe."</option>";
							}
							echo "\n</select><br />";
							echo "<input type=\"button\" value=\"".select_all."\" onclick=\"selectAll('classes_app',true)\" /><br />";
							echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('classes_app',false)\" />";
						}
						echo "</td></tr></table>".hold_down_ctrl;
    				echo "<p><b><u>" .titre_msg. " :</u></b><br /><br /><input name=\"titre_msg\" type=\"text\" size=\"67\" maxlength=\"100\" value=\"\"></p>";
	      		echo "<p><b><u>" .votre_msg. " :</u></b><br /><br /><textarea name=\"contenu_msg\" id=\"contenu_msg\" rows=\"10\" cols=\"50\"></textarea></p>";
	      		echo "\n<input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    			} else goback(aucun_destinataire,2,"critical",1);
    	}
		} break;

	// ****************** reply **************************
		case "reply" : {
			echo "<div id=\"titre\">".reply."</div><br />";
			$select_this_msg = $connect->query("select * from `" . $tblprefix . "messages` where id_message = $id_msg and id_destinataires like '%-$id_user_session-%';");
    	if (mysqli_num_rows($select_this_msg) == 1) {

    		$this_msg = mysql_fetch_row($select_this_msg);

				$emetteur_this_msg = $this_msg[1];
				$emetteur_app_this_msg = $this_msg[2];
				
				if ($emetteur_this_msg != 0)
    			$select_emetteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $emetteur_this_msg;");
    		else if ($emetteur_app_this_msg != 0)
    			$select_emetteur = $connect->query("select identifiant_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $emetteur_app_this_msg;");

    		if (mysqli_num_rows($select_emetteur) == 1)
    			$emetteur = html_ent(mysqli_result($select_emetteur,0));
    		else $emetteur = inconnu;

				$date_this_msg = $this_msg[11];
				$date_this_msg = set_date($dateformat,$date_this_msg);
				
    		$titre_this_msg = "RE: ".html_ent($this_msg[7]);
				$contenu_this_msg = $this_msg[8];
				$contenu_this_msg = "\n\n\n\n\n\n\n\n\n\n\n\n[#]-----------------\n".$date_this_msg.", ".$emetteur." ".a_ecrit."\n".br_bbcode(bbcode_br(html_ent($contenu_this_msg)))."\n[/#]\n";

			if (isset($_POST['titre_msg']) && isset($_POST['contenu_msg'])){
    	 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 	$_SESSION['random_key'] = $_POST['random'];
				$titre_msg = escape_string(trim($_POST['titre_msg']));
				$contenu_msg = trim($_POST['contenu_msg']);
				if (!empty($titre_msg) && !empty($contenu_msg)){
					$contenu_msg = preg_replace("/\r\n\r\n\r\n\r\n/", "\r\n", $contenu_msg);
					$contenu_msg = preg_replace("/\r\n\r\n\r\n/", "\r\n", $contenu_msg);
					$contenu_msg = preg_replace("/\r\n\r\n/", "\r\n", $contenu_msg);
					$contenu_msg = escape_string($contenu_msg);
					if ($emetteur_this_msg != 0)
						$dest_reply = "-".$emetteur_this_msg."-";
					else $dest_reply = "-";
					if ($emetteur_app_this_msg != 0)
						$dest_app_reply = "-".$emetteur_app_this_msg."-";
					else $dest_app_reply = "-";
					
    			$insertmessage = "INSERT INTO `" . $tblprefix . "messages` VALUES (NULL,$id_user_session,0,'','','$dest_reply','$dest_app_reply','".$titre_msg."','".$contenu_msg."','-','-',".time().",'$dest_reply','$dest_app_reply','0');";
	         $connect->query($insertmessage,$connect);
	         redirection(message_succes,"?inc=messages",3,"tips",1);
				} else goback(tous_champs,2,"error",1);
			 } else goback(err_data_saved,2,"error",1);
			}
			else{
				goback_button();
    		echo "\n<form method=\"POST\" action=\"\">";
    		if ($emetteur_this_msg != 0)
    			echo "\n<p><b><u>".to." :</u></b>&nbsp; <a href=\"../?profiles=".$emetteur_this_msg."\" title=\"".user_profile."\">".$emetteur."</a></p>";
    		else if ($emetteur_app_this_msg != 0)
					echo "\n<p><b><u>".to." :</u></b>&nbsp; <a href=\"../?s_profiles=".$emetteur_app_this_msg."\" title=\"".learner_profile."\">".$emetteur."</a></p>";
    		echo "<p><b><u>" .titre_msg. " :</u></b><br /><br /><input name=\"titre_msg\" type=\"text\" size=\"67\" maxlength=\"100\" value=\"".$titre_this_msg."\"></p>";
	      echo "<p><b><u>" .votre_msg. " :</u></b><br /><br /><textarea name=\"contenu_msg\" id=\"contenu_msg\" rows=\"10\" cols=\"50\">".$contenu_this_msg."</textarea></p>";
	      echo "\n<input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    	}
			} else locationhref_admin("?inc=messages");
		} break;
		
	// ****************** forward **************************
		case "forward" : {
		 echo "<div id=\"titre\">".forward."</div><br />";
		 $select_this_msg = $connect->query("select * from `" . $tblprefix . "messages` where id_message = $id_msg and (id_destinataires like '%-$id_user_session-%' or id_emetteur = $id_user_session);");
     if (mysqli_num_rows($select_this_msg) == 1) {

    		$this_msg = mysql_fetch_row($select_this_msg);

				$emetteur_this_msg = $this_msg[1];
				$emetteur_app_this_msg = $this_msg[2];
				
				if ($emetteur_this_msg != 0)
    			$select_emetteur = $connect->query("select identifiant_user from `" . $tblprefix . "users` where id_user = $emetteur_this_msg;");
    		else if ($emetteur_app_this_msg != 0)
    			$select_emetteur = $connect->query("select identifiant_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $emetteur_app_this_msg;");

    		if (mysqli_num_rows($select_emetteur) == 1)
    			$emetteur = html_ent(mysqli_result($select_emetteur,0));
    		else $emetteur = inconnu;

				$date_this_msg = $this_msg[11];
				$date_this_msg = set_date($dateformat,$date_this_msg);
				
    		$titre_this_msg = "FW: ".html_ent($this_msg[7]);
				$contenu_this_msg = $this_msg[8];
				$contenu_this_msg = "\n\n\n\n\n\n\n\n\n\n\n\n[#]-----------------\n".$date_this_msg.", ".$emetteur." ".a_ecrit."\n".br_bbcode(bbcode_br(html_ent($contenu_this_msg)))."\n[/#]\n";
			if (isset($_POST['titre_msg']) && isset($_POST['contenu_msg'])){
    	 if (!isset($_SESSION['random_key']) || $_SESSION['random_key'] != $_POST['random']){
    	 	$_SESSION['random_key'] = $_POST['random'];
				$titre_msg = escape_string(trim($_POST['titre_msg']));
				$contenu_msg = trim($_POST['contenu_msg']);
				if (!empty($titre_msg) && !empty($contenu_msg)){
					$contenu_msg = preg_replace("/\r\n\r\n\r\n\r\n/", "\r\n", $contenu_msg);
					$contenu_msg = preg_replace("/\r\n\r\n\r\n/", "\r\n", $contenu_msg);
					$contenu_msg = preg_replace("/\r\n\r\n/", "\r\n", $contenu_msg);
					$contenu_msg = escape_string($contenu_msg);
					
				  if (isset($_POST['destinataires']) && !empty($_POST['destinataires']))
				  	$chaine_users = "-".implode("-",$_POST['destinataires'])."-";
				  else $chaine_users = "-";

				  $dest_apprenants = array();
				  if ($need_classe == 1){
				  	if (isset($_POST['classes_app']) && !empty($_POST['classes_app'])){
				  		foreach ($_POST['classes_app'] as $elem_classe){
				  			$select_app_classe = $connect->query("select id_apprenant from `" . $tblprefix . "apprenants` where id_classe = $elem_classe;");
								if (mysqli_num_rows($select_app_classe) > 0){
									while($app_classe = mysql_fetch_row($select_app_classe)){
										if (!in_array($app_classe[0],$dest_apprenants))
											$dest_apprenants[] = $app_classe[0];
									}
								}
				  		}
				  	}
					}
				  if (isset($_POST['destinataires_app']) && !empty($_POST['destinataires_app'])){
				  	foreach ($_POST['destinataires_app'] as $elem_app){
				  		if (!in_array($elem_app,$dest_apprenants))
							$dest_apprenants[] = $elem_app;
						}
				  }
				  if (isset($dest_apprenants) && count($dest_apprenants) > 0)
				  	$chaine_apps = "-".implode("-",$dest_apprenants)."-";
				  else $chaine_apps = "-";
    			if ($chaine_users != "-" || $chaine_apps != "-"){
    				$insertmessage = "INSERT INTO `" . $tblprefix . "messages` VALUES (NULL,$id_user_session,0,'','','$chaine_users','$chaine_apps','".$titre_msg."','".$contenu_msg."','-','-',".time().",'$chaine_users','$chaine_apps','0');";
	          $connect->query($insertmessage,$connect);
	          redirection(message_succes,"?inc=messages",3,"tips",1);
    			} else goback(choisir_distinataire,2,"error",1);
				} else goback(tous_champs,2,"error",1);
			 } else goback(err_data_saved,2,"error",1);
			}
			else{
				echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";
				goback_button();
    		$select_users = $connect->query("select id_user, identifiant_user, grade_user from `" . $tblprefix . "users` where id_user != $id_user_session order by grade_user desc;");
    		$select_apprenants = $connect->query("select id_apprenant, id_classe, identifiant_apprenant from `" . $tblprefix . "apprenants` order by id_classe desc;");
    			if (mysqli_num_rows($select_users) > 0 || mysqli_num_rows($select_apprenants) > 0){
    			 echo "\n<form method=\"POST\" action=\"\">";
					 echo "<b><u>" .to. " :</u></b><br /><br />\n";
					 echo "<table border=\"0\"><tr><td align=\"center\">";
					 if (mysqli_num_rows($select_users) > 0){
						echo "<b>".users."</b><br /><select size=\"10\" name=\"destinataires[]\" id=\"destinataires\" multiple=\"multiple\">";
    				while($user = mysql_fetch_row($select_users)){
    					$id_user = $user[0];
    					$identifiant_user = html_ent($user[1]);
    					$grade_user = $grade_tab[$user[2]];
    					echo "\n<option value=\"".$id_user."\">".$identifiant_user." (".$grade_user.")</option>";
    				}
						echo "\n</select><br />";
						echo "<input type=\"button\" value=\"".select_all."\" onclick=\"selectAll('destinataires',true)\" /><br />";
						echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('destinataires',false)\" />";
						echo "</td><td align=\"center\">";
					 }
					 if (mysqli_num_rows($select_apprenants) > 0){
						echo "<b>".learners."</b><br /><select size=\"10\" name=\"destinataires_app[]\" id=\"destinataires_app\" multiple=\"multiple\">";
    				while($learner = mysql_fetch_row($select_apprenants)){
    					$id_apprenant = $learner[0];
    					$id_classe = $learner[1];
    					$identifiant_apprenant = html_ent($learner[2]);
    					echo "\n<option value=\"".$id_apprenant."\">".$identifiant_apprenant;
    					$select_classe = $connect->query("select classe from `" . $tblprefix . "classes` where id_classe = $id_classe;");
    	  			if (mysqli_num_rows($select_classe) == 1){
    						$classe_apprenant = html_ent(mysqli_result($select_classe,0));
    						echo " (".$classe_apprenant.")";
    					}
    					echo "</option>";
    				}
						echo "\n</select><br />";
						echo "<input type=\"button\" value=\"".select_all."\" onclick=\"selectAll('destinataires_app',true)\" /><br />";
						echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('destinataires_app',false)\" />";
					 }
					 if ($need_classe == 1){
					 		echo "</td><td align=\"center\" valign=\"top\">";
	      			echo "<b>" .classe. " : </b><br /><select size=\"10\" name=\"classes_app[]\" id=\"classes_app\" multiple=\"multiple\">";
    					while($classe = mysql_fetch_row($select_classes)){
    						$id_classe = $classe[0];
    						$nom_classe = $classe[1];
								echo "<option value=\"".$id_classe."\">".$nom_classe."</option>";
							}
							echo "\n</select><br />";
							echo "<input type=\"button\" value=\"".select_all."\" onclick=\"selectAll('classes_app',true)\" /><br />";
							echo "<input type=\"button\" value=\"".deselect_all."\" onclick=\"selectAll('classes_app',false)\" />";
						}
						echo "</td></tr></table>".hold_down_ctrl;
    				echo "<p><b><u>" .titre_msg. " :</u></b><br /><br /><input name=\"titre_msg\" type=\"text\" size=\"67\" maxlength=\"100\" value=\"".$titre_this_msg."\"></p>";
	      		echo "<p><b><u>" .votre_msg. " :</u></b><br /><br /><textarea name=\"contenu_msg\" id=\"contenu_msg\" rows=\"10\" cols=\"50\">".$contenu_this_msg."</textarea></p>";
	      		echo "\n<input type=\"hidden\" name=\"random\" value=\"".fonc_rand(16)."\" /><input type=\"submit\" class=\"button\" value=\"" .btnsend. "\"></form>";
    			} else goback(aucun_destinataire,2,"critical",1);
    	}
		 } else locationhref_admin("?inc=messages");
		} break;

	// ****************** outbox **************************
		case "outbox" : {

   		echo "<table border=\"0\" width=\"80%\" align=\"center\"><tr>";
   		echo "<td align=\"center\" width=\"33%\">";
   		echo "<table border=\"0\" width=\"100%\" align=\"center\"><tr><td align=\"right\"><a href=\"?inc=messages&do=new_msg\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td>";
   		echo "<td align=\"left\"><a href=\"?inc=messages&do=new_msg\"><b>".new_msg."</b></a></td></tr></table>";
   		echo "</td><td align=\"center\" width=\"34%\"><a href=\"?inc=messages\"><b>".inbox."</b></a></td>";
   		echo "<td align=\"center\" width=\"33%\"><b>".outbox."</b></td>";
   		echo "</tr></table><hr />";

	if (isset($_GET['l']) && ctype_digit($_GET['l']))
		$page = intval($_GET['l']);
	else $page = 1;
	
	$select_msg = $connect->query("select * from `" . $tblprefix . "messages` where id_emetteur = $id_user_session and deleted_from_outbox = '0' order by date_message desc;"); 
	$nbr_trouve = mysqli_num_rows($select_msg);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}

			$select_msg_limit = $connect->query("select * from `" . $tblprefix . "messages` where id_emetteur = $id_user_session and deleted_from_outbox = '0' order by date_message desc limit $limit, $nbr_resultats;"); 

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".titre_msg."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".to."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ladate."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".forward."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "</tr>\n";
				
				while($msg = mysql_fetch_row($select_msg_limit)){
					
					$id_msg = $msg[0];
					$titre_msg = html_ent($msg[7]);
					$titre_msg = readmore($titre_msg,$max_len);
					$date_msg = set_date($dateformat,$msg[11]);
					
					echo "<tr bgcolor=\"#cccccc\">\n";
					echo "\n<td class=\"affichage_table\" width=\"40%\"><a href=\"?inc=messages&do=open_msg&id_msg=".$id_msg."\" title=\"".ouvrir_msg."\"><b>".$titre_msg."</b></a></td>";
					echo "\n<td class=\"affichage_table\"><b>";
					$i = 0;
					$destinataires = $msg[12];
				  $select_users = $connect->query("select id_user, identifiant_user from `" . $tblprefix . "users` where id_user != $id_user_session;");
    			if (mysqli_num_rows($select_users) > 0){
    				while($user = mysql_fetch_row($select_users)){
    					$id_user = $user[0];
    					if (strpos($destinataires,"-".$id_user."-") !== false){
    						$identifiant_user = html_ent($user[1]);
    						$identifiant_user = readmore($identifiant_user,$max_len2);
								echo "<a href=\"../?profiles=".$id_user."\" title=\"".user_profile."\">".$identifiant_user."</a>, ";
								$i++;
							}
							if ($i>4) {
								echo "...<br />";
								break;
							}
    				}
    			}
    			$i = 0;
    			$destinataires_app = $msg[13];
    			$select_apps = $connect->query("select id_apprenant, identifiant_apprenant from `" . $tblprefix . "apprenants`;");
    			if (mysqli_num_rows($select_apps) > 0){
    				while($app = mysql_fetch_row($select_apps)){
    					$id_app = $app[0];
    					if (strpos($destinataires_app,"-".$id_app."-") !== false){
       					$identifiant_app = html_ent($app[1]);
    						$identifiant_app = readmore($identifiant_app,$max_len2);
								echo "<a href=\"../?s_profiles=".$id_app."\" title=\"".learner_profile."\">".$identifiant_app."</a>, ";
								$i++;
							}
							if ($i>4){
								echo "...";
								break;
							}
    				}
    			}
    			echo "</b></td>";
    			
					echo "\n<td class=\"affichage_table\"><b>".$date_msg."</b></td>";
					
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=messages&do=forward&id_msg=".$id_msg."\" title=\"".forward."\"><img border=\"0\" src=\"../images/others/forward.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_outbox_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=messages&do=outbox&l=".$page_precedente."\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=messages&do=outbox&l=".$page_precedente."\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=messages&do=outbox&l=".$i."\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=messages&do=outbox&l=".$page_suivante."\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=messages&do=outbox&l=".$page_suivante."\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
			} else echo aucun_message;
		} break;
		
  // ****************** liste_msg **************************	
		default : {
		
   		echo "<table border=\"0\" width=\"80%\" align=\"center\"><tr>";
   		echo "<td align=\"center\" width=\"33%\">";
   		echo "<table border=\"0\" width=\"100%\" align=\"center\"><tr><td align=\"right\"><a href=\"?inc=messages&do=new_msg\"><img border=\"0\" src=\"../images/others/add.png\" /></a></td>";
   		echo "<td align=\"left\"><a href=\"?inc=messages&do=new_msg\"><b>".new_msg."</b></a></td></tr></table>";
   		echo "</td><td align=\"center\" width=\"34%\"><b>".inbox."</b></td>";
   		echo "<td align=\"center\" width=\"33%\"><a href=\"?inc=messages&do=outbox\"><b>".outbox."</b></a></td>";
   		echo "</tr></table><hr />";

	if (isset($_GET['l']) && ctype_digit($_GET['l']))
		$page = intval($_GET['l']);
	else $page = 1;
	if (isset($_GET['t']) && ctype_digit($_GET['t']))
		$page2 = intval($_GET['t']);
	else $page2 = 1;
		
   		echo "<a name=\"user\"><b><u>- ".user_messages." : </u></b></a><br /><br />";

	$select_msg = $connect->query("select * from `" . $tblprefix . "messages` where id_destinataires like '%-$id_user_session-%' order by date_message desc;"); 
	$nbr_trouve = mysqli_num_rows($select_msg);
  if ($nbr_trouve > 0){
		$page_max = ceil($nbr_trouve / $nbr_resultats);
		if ($page <= $page_max && $page > 1 && $page_max > 1)
			$limit = ($page - 1) * $nbr_resultats;
		else {
			$limit = 0;
			$page = 1;
		}
		
		$select_msg_limit = $connect->query("select * from `" . $tblprefix . "messages` where id_destinataires like '%-$id_user_session-%' order by date_message desc limit $limit, $nbr_resultats;"); 

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".titre_msg."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".from."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ladate."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".reply."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".forward."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "</tr>\n";
				
				while($msg = mysql_fetch_row($select_msg_limit)){
					
					$id_msg = $msg[0];
					
					$titre_msg = html_ent($msg[7]);
					$titre_msg = readmore($titre_msg,$max_len);

					$date_msg = set_date($dateformat,$msg[11]);
					$lu_message = $msg[9];
					
					if (strpos($lu_message,"-".$id_user_session."-") !== false)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
	
					echo "\n<td class=\"affichage_table\" width=\"50%\"><a href=\"?inc=messages&do=open_msg&id_msg=".$id_msg."\" title=\"".ouvrir_msg."\"><b>".$titre_msg."</b></a></td>";
					
					$id_emetteur = $msg[1];
					$id_emetteur_app = $msg[2];
					
					if ($id_emetteur != 0){
    				$select_emetteur = $connect->query("select identifiant_user, photo_profil from `" . $tblprefix . "users` where id_user = $id_emetteur;");
    				if (mysqli_num_rows($select_emetteur) == 1){
    					$nom_emetteur = html_ent(mysqli_result($select_emetteur,0,0));
    					$nom_emetteur = readmore($nom_emetteur,$max_len2);
    					$photo_profil = mysqli_result($select_emetteur,0,1);
    				} else $nom_emetteur = inconnu;
    				
    				echo "\n<td class=\"affichage_table\"><a href=\"../?profiles=".$id_emetteur."\" title=\"".user_profile."\"><img border=\"0\" src=\"../docs/".$photo_profil."\" alt=\"".$nom_emetteur."\" width=\"40\" height=\"40\" /></a><br />";
    				echo "\n<a href=\"../?profiles=".$id_emetteur."\" title=\"".user_profile."\"><b>".$nom_emetteur."</b></a></td>";
					}
					else if ($id_emetteur_app != 0){
    				$select_emetteur = $connect->query("select identifiant_apprenant, photo_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $id_emetteur_app;");
    				if (mysqli_num_rows($select_emetteur) == 1){
    					$nom_emetteur = html_ent(mysqli_result($select_emetteur,0,0));
    					$nom_emetteur = readmore($nom_emetteur,$max_len2);
    					$photo_profil = mysqli_result($select_emetteur,0,1);
    				} else $nom_emetteur = inconnu;
    			
					  echo "\n<td class=\"affichage_table\"><a href=\"../?s_profiles=".$id_emetteur_app."\" title=\"".learner_profile."\"><img border=\"0\" src=\"../docs/".$photo_profil."\" alt=\"".$nom_emetteur."\" width=\"40\" height=\"40\" /></a><br />";
    				echo "\n<a href=\"../?s_profiles=".$id_emetteur_app."\" title=\"".learner_profile."\"><b>".$nom_emetteur."</b></a></td>";

					}
					echo "\n<td class=\"affichage_table\"><b>".$date_msg."</b></td>";
					
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=messages&do=reply&id_msg=".$id_msg."\" title=\"".reply."\"><img border=\"0\" src=\"../images/others/reply.png\" width=\"32\" height=\"32\" /></a></td>";
					echo "\n<td class=\"affichage_table\"><a href=\"?inc=messages&do=forward&id_msg=".$id_msg."\" title=\"".forward."\"><img border=\"0\" src=\"../images/others/forward.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";
				
		if ($page_max > 1){
			$page_precedente = $page - 1;
			$page_suivante = $page + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=messages&l=".$page_precedente."&t=".$page2."#user\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=messages&l=".$page_precedente."&t=".$page2."#user\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page) echo "<a href=\"?inc=messages&l=".$i."&t=".$page2."#user\">";
				echo "<b>".$i."</b>";
				if ($i != $page) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=messages&l=".$page_suivante."&t=".$page2."#user\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=messages&l=".$page_suivante."&t=".$page2."#user\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
	} else echo aucun_message;
			
		// visitor messages

			if ($grade_user_session == "3" || $grade_user_session == "2"){
			 
			 echo "<br /><hr /><a name=\"visitor\"><b><u>- ".visitor_messages." : </u></b></a><br /><br />";

			 $select_msg = $connect->query("select * from `" . $tblprefix . "messages` where id_destinataires = '*' and id_destinataires_app = '*' order by date_message desc;"); 
			 $nbr_trouve = mysqli_num_rows($select_msg);
  		 if ($nbr_trouve > 0){
				$page_max = ceil($nbr_trouve / $nbr_resultats);
				if ($page2 <= $page_max && $page2 > 1 && $page_max > 1)
					$limit = ($page2 - 1) * $nbr_resultats;
				else {
					$limit = 0;
					$page2 = 1;
				}

			 $select_msg_limit = $connect->query("select * from `" . $tblprefix . "messages` where id_destinataires = '*' and id_destinataires_app = '*' order by date_message desc limit $limit, $nbr_resultats;"); 

				echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
				echo "\n<td class=\"affichage_table\"><b>".titre_msg."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".from."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".ladate."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".already_read."</b></td>";
				echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
				echo "</tr>\n";
				
				while($msg = mysql_fetch_row($select_msg_limit)){
					
					$id_msg = $msg[0];
					
					$titre_msg = html_ent($msg[7]);
					$titre_msg = readmore($titre_msg,$max_len);

					$nom_emetteur = html_ent($msg[3]);
					$nom_emetteur = readmore($nom_emetteur,$max_len2);
						
					$date_msg = set_date($dateformat,$msg[11]);
					$lu_message = $msg[9];
					
					if (strpos($lu_message,"-".$id_user_session."-") !== false)
						echo "<tr bgcolor=\"#cccccc\">\n";
					else echo "<tr>\n";
	
					echo "\n<td class=\"affichage_table\" width=\"40%\"><a href=\"?inc=messages&do=open_msg&id_msg=".$id_msg."\" title=\"".ouvrir_msg."\"><b>".$titre_msg."</b></a></td>";
					echo "\n<td class=\"affichage_table\"><b>".$nom_emetteur."</b></td>";
					echo "\n<td class=\"affichage_table\"><b>".$date_msg."</b></td>";
					
					echo "\n<td class=\"affichage_table\"><b>";
				  $select_users = $connect->query("select id_user, identifiant_user from `" . $tblprefix . "users` where id_user != $id_user_session and (grade_user = '2' or grade_user = '3');");
    			if (mysqli_num_rows($select_users) > 0){
    				while($user = mysql_fetch_row($select_users)){
    					$id_user = $user[0];
    					$identifiant_user = html_ent($user[1]);
    					$identifiant_user = readmore($identifiant_user,$max_len2);
    					if (strpos($lu_message,"-".$id_user."-") !== false)
								echo $identifiant_user."<br />";
    				}
    			} else echo none;
    			echo "</b></td>";
					
					echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=messages&do=delete_msg&id_msg=".$id_msg."&key=".$key."','".confirm_supprimer_msg."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";

					echo "</tr>\n";
				}
				echo "\n</table>";

		if ($page_max > 1){
			$page_precedente = $page2 - 1;
			$page_suivante = $page2 + 1;
  		echo "<br /><table border=\"0\" align=\"center\"><tr>";
			if ($page_precedente >= 1)
				echo "<td><a href=\"?inc=messages&t=".$page_precedente."&l=".$page."#visitor\"><img border=\"0\" src=\"../images/others/precedent.png\" width=\"32\" height=\"32\" /></a></td><td><a href=\"?inc=messages&t=".$page_precedente."&l=".$page."#visitor\"><b>".page_precedente."</b></a></td>";
			echo "<td>";
			for($i=1;$i<=$page_max;$i++){
				if ($i != $page2) echo "<a href=\"?inc=messages&t=".$i."&l=".$page."#visitor\">";
				echo "<b>".$i."</b>";
				if ($i != $page2) echo "</a>";
				echo "&nbsp; ";
			}
			echo "</td>";
			if ($page_suivante <= $page_max)
				echo "<td><a href=\"?inc=messages&t=".$page_suivante."&l=".$page."#visitor\"><b>".page_suivante."</b></a></td><td><a href=\"?inc=messages&t=".$page_suivante."&l=".$page."#visitor\"><img border=\"0\" src=\"../images/others/suivant.png\" width=\"32\" height=\"32\" /></a></td>";
			echo "</tr></table>";
		}
			 } else echo aucun_message;
			}
		}
	}
} else echo restricted_access;

?>