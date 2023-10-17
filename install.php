<?php
/*
 * 	Manhali - Free Learning Management System
 *	install.php
 *	2009-01-01 23:38
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
	
	include_once ("includes/display_functions.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali installation</title>
<link rel="stylesheet" href="styles/style1.css" type="text/css" />
<link rel="shortcut icon" href="styles/favicon.gif" type="image/x-icon" />

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="title" content="Manhali" />
<meta name="description" content="Manhali - Free Learning Management System" />
<meta name="author" content="EL HADDIOUI ISMAIL" />
<meta name="copyright" content="El Haddioui Ismail 2009-2014" />

<!--[if lt IE 7]>
<script defer type="text/javascript" src="styles/pngfix.js"></script>
<![endif]-->

</head>
<body>
<br />
<center><h2>Manhali installation</h2></center>
<br />

<?php

if (!file_exists("includes/dbconfig.php")) {
	echo "<h3><img src=\"images/icones/error.png\" /><font color=\"red\">ERROR, the configuration file does not exist or path is incorrect: <u>includes/dbconfig.php</u></font></h3>";
	echo "<a href=\"#\" onClick=\"window.location.reload();\">Reload</a>";
}
else {
	include_once ("includes/dbconfig.php");

	if (isset($_POST['pass']) && $_POST['pass'] == $installpass){
		
		if (isset($_POST['lang']) && !empty($_POST['lang'])) {
		 $language = $_POST['lang'];
		 if (file_exists("language/".$language."/home.ini")) {
			include_once ("includes/language.php");
			
			if (!file_exists("install/next_install.php")) {
				echo "<h3><img src=\"images/icones/error.png\" /><font color=\"red\">" .install_err. ": <u>install/next_install.php</u></font></h3>";
				echo "<a href=\"#\" onClick=\"window.location.reload();\">".actualiser."</a>";
			}
			else {
				// $select = "SELECT COUNT(*) FROM `articles`;";
				// $query = @mysqli_query($connect,$select);

				// if($query){
				// 	echo "<h3><img src=\"images/icones/critical.png\" /><font color=\"red\">" .deja_install. "</h3>";
				// 	echo "<h3>" .deja_install2. "</font></h3>";
				// }

				echo "<hr />";
				echo "<b><img src=\"images/icones/info.png\" />".text_install." <u>includes/bdonfig.php</u><br /><br />";
				echo form_verif."</b>";

				echo "<br /><br /><form name=\"form1\" method=\"POST\" action=\"install/next_install.php\"><input type=\"hidden\" name=\"pass\" value=\"".md5($_POST['pass'])."\"><input type=\"hidden\" name=\"etape\" value=\"2\">";
				echo "<input type=\"hidden\" name=\"lang\" value=\"".$language."\"><input type=\"submit\" class=\"button\" value=\"" .btn_verif. "\"></form>";

				@mysqli_close($connect);
			}
		 }
		 else {
		 		echo "<h3><img src=\"images/icones/error.png\" /><font color=\"red\">Language file not found : language/".$language."/home.ini</font></h3>";
		 		echo "<a href=\"#\" onClick=\"window.location.reload();\">Reload</a>";
		 }
		}
		else {
			if($dir = opendir("language")){
				echo "<h3><form method=\"post\">Choose language : <select name=\"lang\">";
				while($lang = readdir($dir)) {
					if ($lang != ".." && $lang != "." && strtolower(substr($lang,0,5) != "index")) {
						if ($fd = @fopen("language/".$lang."/home.ini","r")){
							while (!feof($fd)) {
								$line = fgets($fd);
  							if (strpos($line,"language=")===0 || strpos($line,"language="))
  								break;
  						}
  						@fclose($fd);
  						$line = substr($line,strpos($line,"=")+1);
						}
						else $line = "";
						echo "<option  value=\"".$lang."\">".$line." (".$lang.")</option>";
					}
				}
				echo "</select> <input type=\"submit\" class=\"button\" value=\"OK\" /><input type=\"hidden\" name=\"pass\" value=\"".$_POST['pass']."\"></form></h3>";
				closedir($dir);
			}		
		}
	}
	else {
		echo "<h3><form method=\"post\">Setup Password : <input type=\"password\" name=\"pass\" /> <input type=\"hidden\" name=\"envoie\" value=\"ok\"><input type=\"submit\" class=\"button\" value=\"OK\" /></form></h3>";
		echo "<b><img src=\"images/icones/info.png\" />The setup password is in the configuration file: <u>includes/bdonfig.php</u></b>";
		if (isset($_POST['envoie'])) echo "<h3><font color=\"red\">Invalid password</font></h3>";
	}
}

echo "</body></html>";

?>