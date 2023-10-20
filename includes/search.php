<?php
/*
 * 	Manhali - Free Learning Management System
 *	search.php
 *	2009-04-12 23:44
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

$select_statut_search = $connect->query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'search';");
if (mysqli_num_rows($select_statut_search) == 1) {
	$statut_search = $select_statut_search->fetch_row();
	if ($statut_search == 1) {

		echo "<form method=\"GET\" name=\"form1\" class=\"form\">";
		echo "<input name=\"search\" class=\"input\" size=\"15\" type=\"text\" maxlength=\"50\" value=\"";
	
		if(isset($_GET['search']))
			echo html_ent($_GET['search']);
	
		echo "\"> <input type=\"submit\" class=\"searchbtn\" value=\""."rechercher"."\"></form>";
	}
}
?>