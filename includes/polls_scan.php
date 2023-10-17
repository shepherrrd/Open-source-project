<?php
/*
 * 	Manhali - Free Learning Management System
 *	polls_scan.php
 *	2009-12-03 23:33
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

@mysql_query("DELETE FROM `" . $tblprefix . "sondage_ip` WHERE HOUR(heure_vote)<>".date('H',time())." and heure_vote <> '00:00:00';");
@mysql_query("DELETE FROM `" . $tblprefix . "sondage_ip` WHERE MINUTE(heure_vote)<>".date('i',time())." and id_question = 0;");

$ip_user = $_SERVER['REMOTE_ADDR'];
$select_id_question = mysql_query("select * from `" . $tblprefix . "sondage_ip` where ip_vote='$ip_user';");
if (mysql_num_rows($select_id_question) > 0) {
	while($ip_question = mysql_fetch_row($select_id_question)){
		$cookie_question = "poll".$ip_question[3];
		if (!isset($_COOKIE[$cookie_question]))
			@setcookie($cookie_question, "1", time()+60*60*24*30);
	}
}
?>