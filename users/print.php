<?php
/*
 * 	Manhali - Free Learning Management System
 *	print.php
 *	2011-02-14 01:01
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
	include_once ("../includes/dbconfig.php");
	include_once ("../includes/security_functions.php");
	open_session($adminfolder);
	@mysql_close($connect);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali Learners List</title>
<link rel="shortcut icon" href="../styles/favicon.gif" type="image/x-icon" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="title" content="Manhali" />
<meta name="description" content="Manhali - Free Learning Management System" />
<meta name="author" content="EL HADDIOUI ISMAIL" />
<meta name="copyright" content="El Haddioui Ismail 2009-2014" />
</head>
<body>
	
<?php
if (isset($_SESSION['log']) && $_SESSION['log'] == 1){

	if (isset($_POST['learners_login']) && !empty($_POST['learners_login']) && isset($_POST['learners_pass']) && !empty($_POST['learners_pass'])){
		$learners_login = $_POST['learners_login'];
		$learners_pass = $_POST['learners_pass'];
		$login_tab = explode(";",$learners_login);
		$pass_tab = explode(";",$learners_pass);
		echo "<table width=\"100%\" align=\"center\" cellpadding=\"10\" style=\"border: 1px solid #000000;\">";
		$i = 0;
					while ($i < count($login_tab)){
						echo "<tr>";
						$j = 0;
						while ($i < count($login_tab) && $j < 3){
							echo "<td width=\"33%\" style=\"border: 1px solid #000000;\">Username : <b>".html_ent($login_tab[$i])."</b><br />Password : <b>".html_ent($pass_tab[$i])."</b></td>";
							$i++;
							$j++;
						}
						echo "</tr>";
					}
					echo "</table><br />";
	}
} else echo "Restricted access";

?>

</body>
</html>
