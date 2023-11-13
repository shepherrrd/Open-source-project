<?php
/*
 * 	Manhali - Free Learning Management System
 *	error.php
 *	2009-05-14 12:35
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

	header('Content-type: text/html; charset=UTF-8');
	mb_internal_encoding("UTF-8");
	
	include_once ("includes/dbconfig.php");
	include_once ("includes/language.php");
	mysqli_close($connect);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali installation</title>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="title" content="Manhali" />
<meta name="description" content="Manhali - Free Learning Management System" />
<meta name="author" content="EL HADDIOUI ISMAIL" />
<meta name="copyright" content="El Haddioui Ismail 2009-2014" />

</head>
<body>
<br />
<?php
if (isset($_GET['err']) && !empty($_GET['err'])){
	switch ($_GET['err']){
		
		case "aspiration" : {
			$err_msg1 = aspi_msg1."<br /><br />".aspi_msg2;
		} break;

		case "db" : {
			$err_msg1 = err_connect_db;
		} break;

		default : {
			$err_msg1 = err_unknown;
		}
	}

}
else
	$err_msg1 = err_unknown;

echo $err_msg1."<br /><br />".err_msg2."\n<br /><br /><a href=\"index.php\">".actualiser."</a><br /><br />\n</body></html>";
?>