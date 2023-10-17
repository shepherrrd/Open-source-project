<?php
/*
 * 	Manhali - Free Learning Management System
 *	tpl.php
 *	2009-04-23 19:26
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

	if (isset($_GET['tpl'])) {
		if (file_exists("images/tpl_img".$_GET['tpl'])) $tpl = $_GET['tpl'];
		else $tpl = 2;
		@setcookie("tpl", $tpl, time()+2592000);
		if(stristr($_SERVER['HTTP_REFERER'],$_SERVER['SERVER_NAME']))
			@header("Location: ".$_SERVER['HTTP_REFERER']);
	}
	else if (isset($_COOKIE['tpl'])) {
		if (file_exists("images/tpl_img".$_COOKIE['tpl'])) $tpl = $_COOKIE['tpl'];
		else $tpl = 2;
	}
	else $tpl = 2;
?>