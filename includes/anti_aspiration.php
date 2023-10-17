<?php
/*
 * 	Manhali - Free Learning Management System
 *	anti_aspiration.php
 *	2009-05-14 00:22
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

	@mysql_query("DELETE FROM `" . $tblprefix . "antiaspirateur` WHERE MINUTE(heure_aspi)<>".date('i',time()));

	$select_compt = @mysql_query("SELECT compteur_aspi FROM `" . $tblprefix . "antiaspirateur` WHERE ip_aspi ='".$_SERVER['REMOTE_ADDR']."'");

	if (mysql_num_rows($select_compt) > 0) {
		$iCmpt = mysql_result($select_compt,0);
		
		if($iCmpt < 20)
			@mysql_query("UPDATE `" . $tblprefix . "antiaspirateur` SET compteur_aspi = compteur_aspi+1 WHERE ip_aspi ='".$_SERVER['REMOTE_ADDR']."'");

		else {
			header("Location: error.php?err=aspiration");
			@mysql_close($connect);
			exit;
		}
	}
	else
		@mysql_query("INSERT INTO `" . $tblprefix . "antiaspirateur` (ip_aspi,heure_aspi) VALUES ('".$_SERVER['REMOTE_ADDR']."',Now())");

?>