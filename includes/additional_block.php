<?php
/*
 * 	Manhali - Free Learning Management System
 *	additional_block.php
 *	2009-04-13 00:16
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

$select_statut_bloc = mysql_query("select titre_composant, contenu_composant, active_composant from `" . $tblprefix . "composants` where nom_composant = 'additional_block';");
if (mysql_num_rows($select_statut_bloc) == 1) {
	$statut_bloc = mysql_result($select_statut_bloc,0,2);
	if ($statut_bloc == 1) {
		$titre_bloc = mysql_result($select_statut_bloc,0,0);
		$contenu_bloc = mysql_result($select_statut_bloc,0,1);
		
		if (!empty($titre_bloc) && !empty($contenu_bloc)){
			
			$titre_bloc = html_ent($titre_bloc);
			echo "<h3><u>".$titre_bloc."</u></h3>";
			echo $contenu_bloc;
		}
	}
}
?>