<?php 

/*
 * 	Manhali - Free Learning Management System
 *	download.php
 *	2011-11-21 22:26
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

define("access_const","access_const");

$a_telecharger = $_GET['f'];

switch(strrchr(basename($a_telecharger), ".")) {
	case ".gz": $type = "application/x-gzip"; break;
	case ".tgz": $type = "application/x-gzip"; break;
	case ".zip": $type = "application/zip"; break;
	case ".pdf": $type = "application/pdf"; break;
	case ".png": $type = "image/png"; break;
	case ".gif": $type = "image/gif"; break;
	case ".jpg": $type = "image/jpeg"; break;
	case ".txt": $type = "text/plain"; break;
	case ".htm": $type = "text/html"; break;
	case ".html": $type = "text/html"; break;
	default: $type = "application/octet-stream"; break;
}

	$ext = substr($a_telecharger, strrpos($a_telecharger, '.') + 1);
	if (substr($a_telecharger,0,1) != "." && $ext != "php"){
		if (file_exists("../docs/".$a_telecharger)){
			header("Content-disposition: attachment; filename=".$a_telecharger);
			header("Content-Type: application/force-download");
			header("Content-Transfer-Encoding: ".$type."\n");
			header("Content-Length: ".filesize("../docs/".$a_telecharger));
			header("Pragma: no-cache");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
			header("Expires: 0");
			readfile("../docs/".$a_telecharger);
		} else echo "Restricted access";
	} else echo "Restricted access";
?>