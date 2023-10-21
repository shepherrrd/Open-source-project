<?php
/*
 * 	Manhali - Free Learning Management System
 *	admin_language.php
 *	2010-04-14 20:40
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

$langfolder = "../language/";

if (file_exists($langfolder)){

	if(!isset($language)){
		if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($_SESSION['id']) && ctype_digit($_SESSION['id'])){
			$this_user_id = escape_string($_SESSION['id']);
			$selectlanguage = mysqli_query($connect,"select langue_user from `" . $tblprefix . "users` where id_user = $this_user_id;",);
		} else 
			$selectlanguage = mysqli_query($connect,"select langue_site from `" . $tblprefix . "site_infos`;");
		if ($selectlanguage){
			if (mysqli_num_rows($selectlanguage) > 0)
				$language = $selectlanguage->fetch_assoc()['langue_site'] ?? null;
			else $language = "en";
		} else $language = "en";

	}
	
	if (isset($language) && !empty($language) && file_exists($langfolder.$language."/admin.ini"))
		$file_lang = $langfolder.$language."/admin.ini";
	else if (file_exists($langfolder."en/admin.ini"))
		$file_lang = $langfolder."en/admin.ini";
	else if (file_exists($langfolder."fr/admin.ini"))
		$file_lang = $langfolder."fr/admin.ini";
	else die ("Manhali, no language file found in the folder : language");

	$fd = @fopen($file_lang,"r") or die ("Manhali, no language file found in the folder : language");
	while (!feof($fd)) {
		$line = fgets($fd);
  	if (strpos($line,"=")){
  		$line = str_replace("\r\n","",$line);
			$chaine = explode("=",$line,2);
			$name = $chaine[0];
			$value = $chaine[1];
			define($name,$value);
		}
	}
	@fclose ($fd);
	
	if (isset($language) && !empty($language) && file_exists($langfolder.$language."/home.ini"))
		$file_lang2 = $langfolder.$language."/home.ini";
	else if (file_exists($langfolder."en/home.ini"))
		$file_lang2 = $langfolder."en/home.ini";
	else if (file_exists($langfolder."fr/home.ini"))
		$file_lang2 = $langfolder."fr/home.ini";
	else die ("Manhali, no language file found in the folder : language");

	$fd2 = @fopen($file_lang2,"r") or die ("Manhali, no language file found in the folder : language");
	while (!feof($fd2)) {
		$line = fgets($fd2);
  	if (strpos($line,"=")){
  		$line = str_replace("\r\n","",$line);
			$chaine = explode("=",$line,2);
			$name = $chaine[0];
			$value = $chaine[1];
			if (!defined($name))
				define($name,$value);
		}
	}
	@fclose ($fd2);
} else die ("Manhali, no language folder found");

?>