<?php
/*
 * 	Manhali - Free Learning Management System
 *	index.php
 *	2009-01-01 23:54
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
	include_once ("admin_language.php");
	include_once ("../includes/display_functions.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<title>Manhali administration</title>
<link rel="stylesheet" href="../styles/style1.css" type="text/css" />
<link rel="shortcut icon" href="../styles/favicon.gif" type="image/x-icon" />

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="title" content="Manhali" />
<meta name="description" content="Manhali - Free Learning Management System" />
<meta name="author" content="EL HADDIOUI ISMAIL" />
<meta name="copyright" content="El Haddioui Ismail 2009-2014" />

<!--[if lt IE 7]>
<script defer type="text/javascript" src="../styles/pngfix.js"></script>
<![endif]-->

</head>
<body>
<table height="500" align="center">
<tr><td align="center" valign="middle">
<?php

		if (isset($_SESSION['log']) && $_SESSION['log'] == 1){
    	redirection("connect","admin_home.php",3,"tips",1);
    }
    else {
?>
<div id="connexion">
   <br /> <p align="center"><b><font color="#114E7D"><h3><?php echo authsys; ?> Manhali</h3></font></b></p><br />
  <form name="form1" method="POST" action="auth.php">
  <table width="553" cellpadding="5" cellspacing="0" align="center">
    <tr>
        <td width="200" rowspan="2"><img src="../images/others/auth.png" width="200" height="200" border="0" /></td>
        <td valign="top" width="150" align="left"><b><?php echo identifiant; ?></b></td>
        <td valign="top" align="left">


	<input name="login" class="input" type="text" maxlength="30" value=""><br />
        </td>
    </tr>
    <tr>
    	<td valign="top" width="150" align="left"><b><?php echo password; ?></b></td>
    	<td valign="top" align="left">
   <input name="password" class="input" type="password" maxlength="30" value=""><br /><br />
	<input type="submit" class="button" value="<?php echo btnconnection; ?>">

    	</td>
    </tr>
    <tr><td align="center" colspan="3"><a href="../?reset_pass"><b><?php echo pass_oublie." ?"; ?></b></a><br /><br /></td></tr>
</table>
  </form>
</div>
<?php } ?></td></tr>
<tr><td align="center" valign="top">
<h4><a href="../?"><?php echo home_page; ?></a></h4>
</td></tr></table>

</body>
</html>