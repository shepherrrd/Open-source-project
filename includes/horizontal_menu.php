<?php
/*
 * 	Manhali - Free Learning Management System
 *	horizontal_menu.php
 *	2009-01-02 00:02
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

$select_statut_menu = $connect->query("select active_composant from `" . $tblprefix . "composants` where nom_composant = 'horizontal_menu';");
if (mysqli_num_rows($select_statut_menu) == 1) {
	$statut_menu = $select_statut_menu->fetch_row()[0];
	if ($statut_menu == 1) {

		$selectmenus = $connect->query("select * from `" . $tblprefix . "hormenu` where active_hormenu = '1' order by ordre_hormenu;");

		if (mysqli_num_rows($selectmenus)> 0) {
		
			$cellulewidth = round(100 / mysqli_num_rows($selectmenus));
			
			echo "<table cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"100%\"><tr>";

			while($mymenu = mysqli_fetch_row($selectmenus)){
				
				$titre_menu = html_ent($mymenu[1]);
				$titre_menu = readmore($titre_menu,50);
				echo "\n<td align=\"center\" width=\"".$cellulewidth."%\"><a ";
				if (isset($_GET['menu']) && $_GET['menu'] == $mymenu[0])
					echo "class=\"horizontalmenu_selected\"";
				else echo "class=\"horizontalmenu\"";
				echo " href=\"?menu=".$mymenu[0]."\">".$titre_menu."</a></td>\n";
			}
			echo "</tr></table>";
		}
	}
}
?>
