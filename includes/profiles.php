<?php
/*
 * 	Manhali - Free Learning Management System
 *	profiles.php
 *	2009-11-23 00:01
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
	echo"<script>console.log('ohoh')</script>";
	if (!empty($_GET['profiles']) && ctype_digit($_GET['profiles']))
		$id_user = intval($_GET['profiles']);
	else if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 1)
		$id_user = $id_user_session;
	if (isset($id_user) && !empty($id_user)){
		echo "<div id=\"titre\">".user_profile."</div>";
		
		$select_user = $connect->query("select * from `" . $tblprefix . "users` where id_user = $id_user;");
		
    if (mysqli_num_rows($select_user) == 1){
    		$user = mysqli_fetch_row($select_user);
					
					$nom_user = html_ent($user[1]);
					$identifiant_user = html_ent($user[2]);
					
					$email_user = html_ent($user[4]);
					$email_user = mail_antispam($email_user,0);
					
					$active_user = $user[5];
					$grade_user = $grade_tab[$user[6]];
					$photo_profil = $user[7];
					
					if ($user[8] == "F") $sexe_user = female;
					else $sexe_user = male;
					
					$date_inscription = set_date($dateformat,$user[9]);
					
					if ($user[10] == 0)
						$last_connect = never;
					else
						$last_connect = set_date($dateformat,$user[10]);
						
					$online = $user[11];
					
					$derniere_duree = calcule_duree($user[15]);
					$duree_totale = calcule_duree($user[16]);
					$nbr_of_connexion = $user[17];
					$nbr_pages = $user[18];

					// ******** modifier compte & envoyer message
					if (isset($_SESSION['log']) && isset($adminfolder)){
						if (substr($adminfolder,-1,1)=="/")
							$adminfolder = substr($adminfolder,0,strlen($adminfolder)-1);
						$link_edit = $adminfolder."/admin_home.php?";
						if ($id_user == $id_user_session && $_SESSION['log'] == 1){
							if (isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2"))
								$link_edit .= "inc=users&do=update_user&id_user=".$id_user;
							else $link_edit .= "inc=perso_infos";
							echo "<a href=\"".$link_edit."\"><b>".modifier_perso."</b></a>";
						}
						else {
							if ($_SESSION['log'] == 1)
								$link_edit .= "inc=messages&do=new_msg&touser=".$id_user;
							else if ($_SESSION['log'] == 2)
								$link_edit = "?s_messages&do=new_msg&touser=".$id_user;
							echo "<a href=\"".$link_edit."\"><b>".send_msg_to_user."</b></a>";
						}
					}
					
					// ******** infos
					if (!empty($photo_profil))
						echo "<p align=\"center\"><img border=\"0\" src=\"docs/".$photo_profil."\" alt=\"".$nom_user."\" width=\"100\" height=\"100\" /></p>";
					
					echo "<h3 align=\"center\">".$identifiant_user." (".$sexe_user.")</h3>";
					echo "<h3 align=\"center\"><font color=\"blue\">".$grade_user."</font></h3>";
					
					echo "<br /><table width=\"100%\" align=\"center\"><tr><td width=\"50%\" valign=\"top\"><fieldset>";
					echo "<ul>";
					
					echo "<li><p><b>" .nom_complet. " : ".$nom_user."</b></p></li>";
					echo "<li><p><b>" .email. " : ".$email_user."</b></p></li>";

					echo "<li><p><b>" .online. " : ";
					if ($online == 1)
						echo "<img border=\"0\" src=\"images/others/valide.png\" width=\"32\" height=\"32\" />";
					else
						echo "<img border=\"0\" src=\"images/others/delete.png\" width=\"32\" height=\"32\" />";
					echo "</p></li>";
					
				if (isset($id_user_session) && !empty($id_user_session) && isset($_SESSION['log']) && $_SESSION['log'] == 1){

					echo "<li><p><b>" .date_inscription. " : ".$date_inscription."</b></p></li>";
					echo "<li><p><b>" .last_connect. " : ".$last_connect."</b></p></li>";
					echo "<li><p><b>" .duration_last. " : ".$derniere_duree."</b></p></li>";
					echo "<li><p><b>" .active. " : ";
					if ($active_user == 1)
						echo "<img border=\"0\" src=\"images/others/valide.png\" width=\"32\" height=\"32\" />";
					else
						echo "<img border=\"0\" src=\"images/others/delete.png\" width=\"32\" height=\"32\" />";
					echo "</p></li>";
					
					echo "</ul>";
					echo "</fieldset></td>";

					echo "<td width=\"50%\" valign=\"top\"><fieldset>";
					echo "<ul>";
					echo "<li><p><b>" .time_spent. " : ".$duree_totale."</b></p></li>";
					echo "<li><p><b>" .number_connections. " : ".$nbr_of_connexion."</b></p></li>";
					echo "<li><p><b>" .number_visited_pages. " : ".$nbr_pages."</b></p></li>";
					
					$select_count_tutos = $connect->query("select count(id_tutoriel) from `" . $tblprefix . "tutoriels` where id_user = $user[0];");
					$nbr_tutos = mysqli_result($select_count_tutos,0);
					
					$select_count_articles = $connect->query("select count(id_article) from `" . $tblprefix . "articles` where id_user = $user[0];");
					$nbr_articles = mysqli_result($select_count_articles,0);
					
					echo "<li><p><b>" .number_courses. " : ".$nbr_tutos."</b></p></li>";
					echo "<li><p><b>" .number_articles. " : ".$nbr_articles."</b></p></li>";

					$select_count_comments = $connect->query("select count(id_post) from `" . $tblprefix . "commentaires` where type_user = 'u' and id_user = $user[0];");
					$nbr_comments = mysqli_result($select_count_comments,0);

					$select_count_messages = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where id_emetteur = $user[0];");
					$nbr_messages = mysqli_result($select_count_messages,0);

					echo "<li><p><b>" .number_comments. " : ".$nbr_comments."</b></p></li>";
					echo "<li><p><b>" .number_messages. " : ".$nbr_messages."</b></p></li>";
					echo "</ul></fieldset>";
				} else echo "</ul></fieldset>";
				echo "</td></tr></table>";
		} else accueil();
	} else accueil();

?>