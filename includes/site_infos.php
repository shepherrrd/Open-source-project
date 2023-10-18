<?php
/*
 * 	Manhali - Free Learning Management System
 *	site_infos.php
 *	2009-01-02 00:01
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

$title = "";
$url_site = "";
$description_site = "";
$keywords_site = "";
$footer_site = "";
	
$select_site_infos = $connect->query("select * from `" . $tblprefix . "site_infos`;");

if ($select_site_infos && mysqli_num_rows($select_site_infos) > 0) {
	$site_infos = mysqli_fetch_row($select_site_infos);
	
	$title = $site_infos[2];
	$url_site = $site_infos[3];
	$description_site = $site_infos[4];
	$keywords_site = $site_infos[5];
	$footer_site = html_ent($site_infos[7]);
}

$title_site = $title . " - Powered by Manhali";
$description_site = $description_site . " - Powered by Manhali, a free Learning Management System";
$footer_site = $footer_site . " - Powered by <a href=\"http://www.manhali.com\" target=\"_blank\">Manhali</a>";

?>