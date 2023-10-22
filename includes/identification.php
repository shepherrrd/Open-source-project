<?php
/*
 * 	Manhali - Free Learning Management System
 *	identification.php
 *	2010-12-24 01:26
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

$select_statut_identification = $connect->query("select titre_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'identification';");
if (mysqli_num_rows($select_statut_identification) == 1) {
	$statut_identification = $select_statut_identification->fetch_row()[1];
	if ($statut_identification == 1) {
		$titre_identification = $select_statut_identification->fetch_row();
		$titre_identification = html_ent($titre_identification);
		echo "<h3><u>".$titre_identification."</u></h3>";
		$connnow1 = $connect->query("update `" . $tblprefix . "users` set connected_now = '0' where last_connect + 900 < ".time().";");
		$connnow2 = $connect->query("update `" . $tblprefix . "apprenants` set connected_now_apprenant = '0' where last_connect_apprenant + 900 < ".time().";");
		if (!empty($_SESSION['log']) && !empty($_SESSION['id']) && !empty($_SESSION['key'])){
  		$id_user_session = escape_string($_SESSION['id']);
  		$key = $_SESSION['key'];
  		if($_SESSION['log'] == 1){
  			
  			$update_conn = $connect->query("update `" . $tblprefix . "users` set connected_now = '1', nbr_pages = nbr_pages + 1 where id_user = $id_user_session;");
				
				$grade_user_session = $_SESSION['grade'];
				$select_pseudo_user = $connect->query("select identifiant_user, photo_profil, last_connect from `" . $tblprefix . "users` where id_user = $id_user_session;");
    		if (mysqli_num_rows($select_pseudo_user) == 1){
    			$pseudo = html_ent($select_pseudo_user->fetch_row()[0]);
    			$pseudo = wordwrap($pseudo,20,"<br />",true);
    			$photo = $select_pseudo_user->fetch_row()[1] ?? null;
    			$last_connect = $select_pseudo_user->fetch_row()[2] ?? null;
    		}
    		else {
    			$pseudo = "";
    			$photo = "";
    			$last_connect = time();
    		}
			}
			else if($_SESSION['log'] == 2) {
				
				$update_conn = $connect->query("update `" . $tblprefix . "apprenants` set connected_now_apprenant = '1', nbr_pages = nbr_pages + 1 where id_apprenant = $id_user_session;");
				
				$select_pseudo_apprenant = $connect->query("select id_classe, identifiant_apprenant, photo_apprenant, last_connect_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
    		if (mysqli_num_rows($select_pseudo_apprenant) == 1){
    			$classe = $select_pseudo_apprenant->fetch_row()[0] ?? null;
    			$pseudo = html_ent($select_pseudo_apprenant->fetch_row()[1] ?? null);
    			$pseudo = wordwrap($pseudo,20,"<br />",true);
    			$photo = $select_pseudo_apprenant->fetch_row()[2] ?? null;
    			$last_connect = $select_pseudo_apprenant->fetch_row()[3] ?? null;
    			
    			$select_classe = $connect->query("select classe from `" . $tblprefix . "classes` where id_classe = $classe;");
    	  	if (mysqli_num_rows($select_classe) == 1){
    				$classe_apprenant = html_ent($select_classe->fetch_row()[0] ?? null);
    				$classe_apprenant = wordwrap($classe_apprenant,20,"<br />",true);
    			} else $classe_apprenant = "";
    		}
    		else {
    			$pseudo = "";
    			$photo = "";
    			$last_connect = time();
    		}
			}
			if (isset($_GET['task']) && $_GET['task'] == "logout") {
        if($_SESSION['log'] == 1)
        	$update_connectednow = $connect->query("update `" . $tblprefix . "users` set connected_now = '0' where id_user = $id_user_session;");
        else if($_SESSION['log'] == 2)
        	$update_connectednow = $connect->query("update `" . $tblprefix . "apprenants` set connected_now_apprenant = '0' where id_apprenant = $id_user_session;");
        close_session();
				locationhref_admin("index.php");
			}
			else {
				if (!isset($_SESSION['timeout'])) $_SESSION['timeout']=time();
				if(time() - $_SESSION['timeout'] > 900) locationhref_admin("?task=logout");
				else {
					$last_duration = time() - $_SESSION['timeout'];
        	if($_SESSION['log'] == 1)
        		$update_connectednow = $connect->query("update `" . $tblprefix . "users` set last_duration = last_duration + $last_duration, total_duration = total_duration + $last_duration where id_user = $id_user_session;");
        	else if($_SESSION['log'] == 2)
        		$update_connectednow = $connect->query("update `" . $tblprefix . "apprenants` set last_duration = last_duration + $last_duration, total_duration = total_duration + $last_duration where id_apprenant = $id_user_session;");
					$_SESSION['timeout']=time();
				}
				if($_SESSION['log'] == 1){
					if (isset($adminfolder)){
						if (substr($adminfolder,-1,1)=="/")
							$adminfolder = substr($adminfolder,0,strlen($adminfolder)-1);
						echo "<a href=\"".$adminfolder."/admin_home.php\"><b>"."page_administration"."</b></a><br /><br />";
						echo "<a href=\"?documents\"><b>"."document_sharing"."</b></a><br /><br />";
						echo "<a href=\"".$adminfolder."/admin_home.php?inc=messages\"><b>"."messagerie";
						if ($grade_user_session == "3" || $grade_user_session == "2")
							$select_msg_nonlus = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where lu_message not like '%-$id_user_session-%' and (id_destinataires like '%-$id_user_session-%' or (id_destinataires = '*' and id_destinataires_app = '*'));");
						else
							$select_msg_nonlus = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where lu_message not like '%-$id_user_session-%' and id_destinataires like '%-$id_user_session-%';");
						if ($select_msg_nonlus){
							$nbr_msg = $select_msg_nonlus->fetch_row();
							if (ctype_digit($nbr_msg) && $nbr_msg > 0)
								echo " (".$nbr_msg.")";
						}
						echo "</b></a><br /><br />";
					}
					echo "<a href=\"?profiles\" title=\""."user_profile"."\"><img border=\"0\" src=\"docs/".$photo."\" alt=\"".$pseudo."\" width=\"100\" height=\"100\" /></a><br />";
        	echo "<a href=\"?profiles\" title=\""."user_profile"."\"><b>".$pseudo."</b></a><br />(".$grade_tab[$grade_user_session].")<br />";
				}
				else if($_SESSION['log'] == 2){
					echo "<a href=\"?documents\"><b>"."document_sharing"."</b></a><br /><br />";
					echo "<a href=\"?s_messages\"><b>"."messagerie";
					$select_msg_nonlus = $connect->query("select count(id_message) from `" . $tblprefix . "messages` where lu_message_app not like '%-$id_user_session-%' and id_destinataires_app like '%-$id_user_session-%';");
					if ($select_msg_nonlus){
						$nbr_msg = $select_msg_nonlus->fetch_row();
						if (ctype_digit($nbr_msg) && $nbr_msg > 0)
							echo " (".$nbr_msg.")";
					}
					echo "</b></a><br /><br />";
					echo "<a href=\"?s_profiles\" title=\""."learner_profile"."\"><img border=\"0\" src=\"docs/".$photo."\" alt=\"".$pseudo."\" width=\"100\" height=\"100\" /></a><br />";
        	echo "<a href=\"?s_profiles\" title=\""."learner_profile"."\"><b>".$pseudo."</b></a><br />";
        	if ($classe_apprenant != "")
						echo "(".$classe_apprenant.")<br />";
				}
        echo "<input type=\"button\" class=\"searchbtn\" value=\""."linkdeconnection"."\" onclick=\"window.location.href='?task=logout'\" />";
      }
		}
		else {
			echo "\n<form method=\"POST\" action=\"login.php\">";
			echo "\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" align=\"center\" width=\"100%\">\n";
			echo "\n<tr><td align=\"left\" width=\"100%\"><b>"."identifiant"."</b></td></tr>";
			echo "\n<tr><td align=\"center\" width=\"100%\"><input name=\"login\" class=\"input\" type=\"text\" maxlength=\"30\" value=\"\"></td></tr>";
			echo "\n<tr><td align=\"left\" width=\"100%\"><b>"."password"."</b></td></tr>";
			echo "\n<tr><td align=\"center\" width=\"100%\"><input name=\"password\" class=\"input\" type=\"password\" maxlength=\"30\" value=\"\"></td></tr>";
			echo "\n<tr><td align=\"center\" width=\"100%\"><input type=\"submit\" class=\"searchbtn\" value=\""."btnconnection"."\"></td></tr>";
			
			$select_inscription = $connect->query("select inscription from `" . $tblprefix . "site_infos`;");
			if (mysqli_num_rows($select_inscription) == 1) {
				$inscription = $select_inscription->fetch_row();
				if ($inscription == 1)
					echo "\n<tr><td align=\"center\" width=\"100%\"><a href=\"?register\"><b>"."create_account"."</b></a></td></tr>";
			}
			echo "\n<tr><td align=\"center\" width=\"100%\"><a href=\"?reset_pass\"><b>"."pass_oublie"." ?</b></a></td></tr>";
			
			echo "</table></form>";
		}
	}
}

?>