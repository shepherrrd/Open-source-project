<?php
/*
 * 	Manhali - Free Learning Management System
 *	vertical_panel.php
 *	2011-11-08 13:22
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

if (!empty($_SESSION['log']) && !empty($_SESSION['id']) && !empty($_SESSION['key'])){
  $id_user_session = escape_string($_SESSION['id']);
  $key = $_SESSION['key'];
}
if (!empty($_SESSION['log']) && $_SESSION['log'] == 2){
	$select_grade_app = $connect->query("select grade_apprenant from `" . $tblprefix . "apprenants` where id_apprenant = $id_user_session;");
	if (mysqli_num_rows($select_grade_app) == 1)
		$grade_app_session = $select_grade_app->fetch_row();
	else $grade_app_session = "None";
} else $grade_app_session = "None";

$select_composants = $connect->query("select * from `" . $tblprefix . "composants` where ordre_composant != 0 order by ordre_composant;");
if (mysqli_num_rows($select_composants) > 0) {
	echo "<table align=\"center\" width=\"181\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
	$not_first = 0;
	while($composant = mysqli_fetch_row($select_composants)){
		$nom_composant = html_ent($composant[1]);
		if ($composant[4] == '1'){
			echo "<tr><td width=\"100%\" align=\"center\" valign=\"top\">";
			if ($not_first == 1) echo "<hr />";
			include_once ("includes/".$nom_composant.".php");
			echo "</td></tr>";
			$not_first = 1;
		}
	}
	echo "<tr><td width=\"100%\" align=\"center\" valign=\"top\" height=\"15\">&nbsp;</td></tr></table>";
}

?>
