<?php
/*
 * 	Manhali - Free Learning Management System
 *	vertical_menu.php
 *	2011-11-08 13:38
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

echo "<script type=\"text/javascript\" src=\"styles/dynMenu.js\"></script>";
echo "<script type=\"text/javascript\" src=\"styles/browserdetect.js\"></script>";

$select_statut_menu = mysql_query("select titre_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'vertical_menu';");
if (mysql_num_rows($select_statut_menu) == 1) {
	$titre_menu = mysql_result($select_statut_menu,0,0);
	$statut_menu = mysql_result($select_statut_menu,0,1);
	if ($statut_menu == 1) {

		$selectmenus = mysql_query("select * from `" . $tblprefix . "vermenu` where active_vermenu = '1' order by ordre_vermenu;");
		if (mysql_num_rows($selectmenus)> 0) {
			echo "<h3><u>".html_ent($titre_menu)."</u></h3>";
			echo "<ul id=\"menu_articles\">\n";
			while($mymenu = mysql_fetch_row($selectmenus)){
				$idvermenu = $mymenu[0];
				echo "\t<li><a href=\"?vermenu=".$idvermenu."\">".html_ent($mymenu[1])."</a>\n";		
				$selectarticles = mysql_query("select * from `" . $tblprefix . "articles` where id_menu_ver = $idvermenu and publie_article = '1' order by ordre_article_ver;");

				if (mysql_num_rows($selectarticles)> 0) {
					echo "\t\t<ul>\n";
					while($article = mysql_fetch_row($selectarticles)){
						echo "\t\t\t<li><a href=\"?article=".$article[0]."\">".html_ent($article[3])."</a>\n";
						echo "\t\t\t</li>\n";
					}
					echo "\t\t</ul>\n";
				}
				echo "\t</li>\n";
			}
			echo "</ul>\n";
		}
		echo "<script type=\"text/javascript\">initMenu('menu_articles');</script>";
	}
}
?>
