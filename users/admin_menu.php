<?php
/*
 * 	Manhali - Free Learning Management System
 *	admin_menu.php
 *	2009-04-21 01:41
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session)){

	function class_menu($var_inc){
		if ((!empty($_GET['inc']) && $var_inc == $_GET['inc']) || (empty($_GET['inc']) && $var_inc == "statistics"))
			return "menu_admin_selected";
		else
			return "menu_admin";
	}
// ****************************
	echo "<a href=\"?inc=statistics\" class=\"".class_menu("statistics")."\">".statistiques."</a><br /><br />";
	
// ****************************

	echo "<a href=\"?inc=messages\" class=\"".class_menu("messages")."\">".messagerie;

	if ($grade_user_session == "3" || $grade_user_session == "2")
		$select_msg_nonlus = mysql_query("select count(id_message) from `" . $tblprefix . "messages` where lu_message not like '%-$id_user_session-%' and (id_destinataires like '%-$id_user_session-%' or (id_destinataires = '*' and id_destinataires_app = '*'));");
	else
		$select_msg_nonlus = mysql_query("select count(id_message) from `" . $tblprefix . "messages` where lu_message not like '%-$id_user_session-%' and id_destinataires like '%-$id_user_session-%';"); 

	if ($select_msg_nonlus){
		$nbr_msg = mysql_result($select_msg_nonlus,0);
		if (ctype_digit($nbr_msg) && $nbr_msg > 0)
			echo " (".$nbr_msg.")";
	}
	echo "</a><br /><br />";

// ****************************
	if($grade_user_session == "3" || $grade_user_session == "2")
		echo "<a href=\"?inc=users\" class=\"".class_menu("users")."\">".gestion_utilisateurs."</a><br /><br />";
	else
		echo "<a href=\"?inc=perso_infos\" class=\"".class_menu("perso_infos")."\">".modifier_perso."</a><br /><br />";
		
// ****************************
	echo "<a href=\"?inc=learners\" class=\"".class_menu("learners")."\">".gestion_apprenants."</a><br /><br />";

// ****************************
	if($grade_user_session == "3" || $grade_user_session == "2" || $grade_user_session == "1")
		echo "<a href=\"?inc=edit_tutorials\" class=\"".class_menu("edit_tutorials")."\">".gestion_tutoriels."</a><br /><br />";
	else
		echo "<a href=\"?inc=tutorials\" class=\"".class_menu("tutorials")."\">".gestion_tutoriels."</a><br /><br />";

// ****************************
	if($grade_user_session == "3" || $grade_user_session == "2" || $grade_user_session == "1")
		echo "<a href=\"?inc=edit_articles\" class=\"".class_menu("edit_articles")."\">".gestion_articles."</a><br /><br />";
	else
		echo "<a href=\"?inc=articles\" class=\"".class_menu("articles")."\">".gestion_articles."</a><br /><br />";

// ****************************
		echo "<a href=\"?inc=documents\" class=\"".class_menu("documents")."\">".gestion_documents."</a><br /><br />";
		
// ****************************
	if($grade_user_session == "3" || $grade_user_session == "2"){

// ****************************
		echo "<a href=\"?inc=site_config\" class=\"".class_menu("site_config")."\">".configuration_generale."</a><br /><br />";
		
// ****************************
		echo "<a href=\"?inc=poll_manager\" class=\"".class_menu("poll_manager")."\">".gestion_sondage."</a><br /><br />";
				
// ****************************
		echo "<a href=\"?inc=horizontal_menu\" class=\"".class_menu("horizontal_menu")."\">".gestion_horizontal_menu."</a><br /><br />";

// ****************************
		echo "<a href=\"?inc=vertical_menu\" class=\"".class_menu("vertical_menu")."\">".gestion_vertical_menu."</a><br /><br />";
		
// ****************************
		echo "<a href=\"?inc=home_config\" class=\"".class_menu("home_config")."\">".gestion_accueil."</a><br /><br />";

// ****************************
		echo "<a href=\"?inc=components\" class=\"".class_menu("components")."\">".gestion_composants."</a><br /><br />";

// ****************************
		echo "<a href=\"?inc=access_infos\" class=\"".class_menu("access_infos")."\">".access_infos."</a><br /><br />";
	}
} else echo restricted_access;
?>