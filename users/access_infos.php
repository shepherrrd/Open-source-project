<?php
/*
 * 	Manhali - Free Learning Management System
 *	access_infos.php
 *	2012-05-17 19:41
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session) && ($grade_user_session == "3" || $grade_user_session == "2")){

	echo "<div id=\"titre\">".access_infos."</div>";

	if (!empty($_POST['choix_access']))
		$choix_access = html_ent($_POST['choix_access']);
	else $choix_access = "id";
	
	if (!empty($_POST['id_access']))
		$id_access = html_ent($_POST['id_access']);
	else $id_access = "";
	
	if (!empty($_POST['ip_access']))
		$ip_access = html_ent($_POST['ip_access']);
	else $ip_access = "";
	
	if (!empty($_POST['date_1']))
		$date_1 = html_ent($_POST['date_1']);
	else $date_1 = "";

	if (!empty($_POST['date_2']))
		$date_2 = html_ent($_POST['date_2']);
	else $date_2 = "";
	
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/selectall.js\"></script>";

	if (isset($_GET['do'])) $do = $_GET['do'];
	else $do="";
	
	switch ($do){
	// ****************** access_delete **************************
	case "access_delete" : {
		if (isset($_GET['key']) && $_GET['key'] == $key){
			if (isset($_GET['id_acc']) && ctype_digit($_GET['id_acc'])){
				$id_acc = intval($_GET['id_acc']);
				$delete_access = mysql_query("delete from `" . $tblprefix . "infos_acces` where id_acces = $id_acc;");
			}
		}
		locationhref_admin("?inc=access_infos");
	} break;

	// ****************** access_50_delete **************************
	case "access_50_delete" : {
		if (isset($_GET['key']) && $_GET['key'] == $key){
			$delete_access_50 = mysql_query("delete from `" . $tblprefix . "infos_acces` order by date_acces asc limit 50;");
		}
		locationhref_admin("?inc=access_infos");
	} break;

   	// ****************** liste_apprenants **************************	
		default : {
			confirmer();
	if (isset($language) && $language == "fr"){
		$calendar_path = "mycalendar_fr.js";
		$format_date_calendar = "jj/mm/aaaa";
	}
	else {
		$calendar_path = "mycalendar_us.js";
		$format_date_calendar = "mm/dd/yyyy";
	}
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"../styles/".$calendar_path."\"></script>";
	echo "<form method=\"POST\" name=\"f_access_infos\" id=\"f_access_infos\" action=\"\">";
	echo "<u><b>- ".rechercher." : </b></u><br /><br />";
	
	echo "\n<p><input name=\"choix_access\" type=\"radio\" value=\"id\" onclick=\"disabled_text_input(false,true,true,true)\"";
	if ($choix_access == "id")
		echo " checked=\"checked\"";
	echo " /><b>".identifiant." : </b><input name=\"id_access\" id=\"id_access\" type=\"text\" size=\"20\" maxlength=\"100\" value=\"".$id_access."\"></p>";

	echo "\n<p><input name=\"choix_access\" type=\"radio\" value=\"ip\" onclick=\"disabled_text_input(true,false,true,true)\"";
	if ($choix_access == "ip")
		echo " checked=\"checked\"";
	echo " /><b>".ip_access." : </b><input name=\"ip_access\" id=\"ip_access\" type=\"text\" size=\"15\" maxlength=\"15\" value=\"".$ip_access."\"></p>";

	echo "\n<p><input name=\"choix_access\" type=\"radio\" value=\"date\" onclick=\"disabled_text_input(true,true,false,false)\"";
	if ($choix_access == "date")
		echo " checked=\"checked\"";
	echo " /><b>".date_access." : </b>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".debut." : <input name=\"date_1\" id=\"date_1\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".$date_1."\">";
	echo "\n<script type=\"text/javascript\">new tcal({'formname':'f_access_infos','controlname':'date_1'});</script>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".fin." : <input name=\"date_2\" id=\"date_2\" type=\"text\" size=\"10\" maxlength=\"10\" value=\"".$date_2."\">";
	echo "\n<script type=\"text/javascript\">new tcal({'formname':'f_access_infos','controlname':'date_2'});</script>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (".$format_date_calendar.")</p>";
	
	if ($choix_access == "ip")
		echo "<script type=\"text/javascript\">disabled_text_input(true,false,true,true);</script>";
	else if ($choix_access == "date")
		echo "<script type=\"text/javascript\">disabled_text_input(true,true,false,false);</script>";
	else
		echo "<script type=\"text/javascript\">disabled_text_input(false,true,true,true);</script>";

	echo "\n<center><input type=\"submit\" class=\"button\" value=\"".rechercher."\">&nbsp;&nbsp;<input type=\"button\" class=\"button\" value=\"".last_20_access."\" onclick=\"window.location.href='?inc=access_infos'\" /></center></form><br />";
	
	echo "<center><a href=\"#\" onClick=\"confirmer('?inc=access_infos&do=access_50_delete&key=".$key."','".confirm_supprimer_50_access."')\" title=\"".supprimer."\"><b>".supprimer_50_access."</b></a></center><hr />";

// default request
$select_access = mysql_query("select * from `" . $tblprefix . "infos_acces` order by date_acces desc limit 0,20;");

// affichage
if (!empty($_POST['choix_access'])){
	$choix_access = $_POST['choix_access'];
	if ($choix_access == "ip"){
		if (!empty($_POST['ip_access'])){
			$ip_access = escape_string($_POST['ip_access']);
			$select_access = mysql_query("select * from `" . $tblprefix . "infos_acces` where ip_user like '%$ip_access%' order by date_acces desc;");
		}
	}
	else if ($choix_access == "date"){
		if (!empty($_POST['date_1']) && !empty($_POST['date_2'])){
			$date_1_0 = escape_string($_POST['date_1']);
			$date_2_0 = escape_string($_POST['date_2']);
			$date_1 = explode("/",$date_1_0);
			$date_2 = explode("/",$date_2_0);
			if (count($date_1) == 3 && count($date_2) == 3){
				//date1
				if (isset($language) && $language == "fr"){
					$jj = $date_1[0];
					$mm = $date_1[1];
				} else {
					$jj = $date_1[1];
					$mm = $date_1[0];
				}
				$yyyy = $date_1[2];
				if ($yyyy < 100){
					if ($yyyy < date("y",time())) $yyyy += 2000;
					else $yyyy += 1900;
				}
				//date2
				if (isset($language) && $language == "fr"){
					$jj2 = $date_2[0];
					$mm2 = $date_2[1];
				} else {
					$jj2 = $date_2[1];
					$mm2 = $date_2[0];
				}
				$yyyy2 = $date_2[2];
				if ($yyyy2 < 100){
					if ($yyyy2 < date("y",time())) $yyyy2 += 2000;
					else $yyyy2 += 1900;
				}
				if (ctype_digit($jj) && $jj >= 1 && $jj <= 31 && ctype_digit($mm) && $mm >= 1 && $mm <= 12 && $yyyy >= (date("Y",time()) - 10) && $yyyy <= (date("Y",time()) + 10)){
					$date_1_final = mktime(0, 0, 0, $mm, $jj, $yyyy);
					if (ctype_digit($jj2) && $jj2 >= 1 && $jj2 <= 31 && ctype_digit($mm2) && $mm2 >= 1 && $mm2 <= 12 && $yyyy2 >= (date("Y",time()) - 10) && $yyyy2 <= (date("Y",time()) + 10)){
						$date_2_final = mktime(0, 0, 0, $mm2, $jj2, $yyyy2) + 24*3600;
						$select_access = mysql_query("select * from `" . $tblprefix . "infos_acces` where date_acces >= $date_1_final and date_acces <= $date_2_final order by date_acces desc;");
					}
				}
			}
		}
	}
	else {
	 if (!empty($_POST['id_access'])){
		$id_access = escape_string($_POST['id_access']);
		$select_user = mysql_query("select id_user from `" . $tblprefix . "users` where identifiant_user = '$id_access';");
		if (mysql_num_rows($select_user) > 0){
			$id_user = mysql_result($select_user,0);
			$select_access = mysql_query("select * from `" . $tblprefix . "infos_acces` where type_user = 'u' and id_user = $id_user order by date_acces desc;");
		}
		else {
			$select_app = mysql_query("select id_apprenant from `" . $tblprefix . "apprenants` where identifiant_apprenant = '$id_access';");
			if (mysql_num_rows($select_app) > 0){
				$id_app = mysql_result($select_app,0);
				$select_access = mysql_query("select * from `" . $tblprefix . "infos_acces` where type_user = 'l' and id_user = $id_app order by date_acces desc;");
			}
		}
	 }
	}
} else echo "<h3><u>".last_20_access." :</u></h3>";

if (mysql_num_rows($select_access) > 0){
	echo "<table width=\"100%\" align=\"center\" style=\"border: 1px solid #000000;\"><tr bgcolor=\"#f1d3bd\">\n";
	echo "\n<td class=\"affichage_table\"><b>".identifiant."</b></td>";
	echo "\n<td class=\"affichage_table\"><b>".photo_profil."</b></td>";
	echo "\n<td class=\"affichage_table\"><b>".type_user."</b></td>";
	echo "\n<td class=\"affichage_table\"><b>".ip_access."</b></td>";
	echo "\n<td class=\"affichage_table\"><b>".date_access."</b></td>";
	echo "\n<td class=\"affichage_table\"><b>".supprimer."</b></td>";
	echo "</tr>";
	while($access = mysql_fetch_row($select_access)){
		
		$the_id = $access[0];
		$type_user = $access[1];
		$id_user = $access[2];
		$ip_user = html_ent($access[3]);
		$date_access = set_date($dateformat,$access[4]);
		
		if ($type_user == "l"){
			$select_app = mysql_query("select id_classe, identifiant_apprenant, photo_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $id_user;");
			if (mysql_num_rows($select_app) > 0){
				$id_classe = html_ent(mysql_result($select_app,0,0));
				$identifiant = html_ent(mysql_result($select_app,0,1));
				$photo_profil = html_ent(mysql_result($select_app,0,2));
				
				$select_classe = mysql_query("select classe from `" . $tblprefix . "classes` where id_classe = $id_classe;");
    	  if (mysql_num_rows($select_classe) == 1)
    			$classe_apprenant = " (".html_ent(mysql_result($select_classe,0)).")";
    		else $classe_apprenant = "";
			}
			else {
				$identifiant = inconnu;
				$photo_profil = "man.jpg";
				$classe_apprenant = "";
			}
			$profile_link = "s_profiles";
			$type_user = learner.$classe_apprenant;
		}
		else {
			$select_user = mysql_query("select identifiant_user, grade_user, photo_profil from `" . $tblprefix . "users` where id_user = $id_user;");
			if (mysql_num_rows($select_user) > 0){
				$identifiant = html_ent(mysql_result($select_user,0,0));
				$grade_user = html_ent(mysql_result($select_user,0,1));
				$photo_profil = html_ent(mysql_result($select_user,0,2));
				$type_user = $grade_tab[$grade_user];
			}
			else {
				$identifiant = inconnu;
				$photo_profil = "man.jpg";
				$type_user = "";
			}
			$profile_link = "profiles";
		}
		echo "<tr>\n";
		echo "\n<td class=\"affichage_table\"><a href=\"../?".$profile_link."=".$id_user."\" title=\"".learner_profile."\"><b>".$identifiant."</b></a></td>";
		echo "\n<td class=\"affichage_table\"><a href=\"../?".$profile_link."=".$id_user."\" title=\"".learner_profile."\"><img border=\"0\" src=\"../docs/".$photo_profil."\" alt=\"".$identifiant."\" width=\"40\" height=\"40\" /></a></td>";
		echo "\n<td class=\"affichage_table\"><b>".$type_user."</b></td>";
		echo "\n<td class=\"affichage_table\"><b>".$ip_user."</b></td>";
		echo "\n<td class=\"affichage_table\"><b>".$date_access."</b></td>";
		echo "\n<td class=\"affichage_table\"><a href=\"#\" onClick=\"confirmer('?inc=access_infos&do=access_delete&id_acc=".$the_id."&key=".$key."','".confirm_supprimer_access."')\" title=\"".supprimer."\"><img border=\"0\" src=\"../images/others/delete.png\" width=\"32\" height=\"32\" /></a></td>";
		echo "</tr>\n";
	}
	echo "\n</table>";

} else echo aucun_acces."<br />";
}
}
} else echo restricted_access;

?>