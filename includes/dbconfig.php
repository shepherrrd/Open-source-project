<?php
/*
 * 	Manhali - Free Learning Management System
 *	dbconfig.php
 *	2009-01-01 23:47
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

// *****************************************
// ********** THE DATABASE SERVER **********
$host = "localhost";

// *********************************************************
// ********** YOUR USERNAME ASSOCIATED WITH MySQL **********
$user = "root";

// *********************************************************
// ********** YOUR PASSWORD ASSOCIATED WITH MySQL **********
$passwd = "";

// ******************************************************************************
// ********** THE NAME OF THE DATABASE WHICH YOU HAVE MADE FOR Manhali **********
$dbname = "manhali";

// **********************************
// ********** TABLE PREFIX **********
//	Prefix that your tables have in the database.
// It is RECOMMENDED to change the prefix for security purposes.
$tblprefix = "manhali_";

// ********************************************
// ********** PATH TO ADMINISTRATION **********
// This setting allows you to change the name of the administration folder.
// It is RECOMMENDED to do this for security purposes.
// Please note that if you change the name of the directory here, you will still need to manually change the name of the directory on the server.
$adminfolder = "users";

// *******************************************
// ********** SETUP PASSWORD **********
// The system will ask password in the installation page.
// It is RECOMMENDED to change this password for security purposes.
$installpass = "manhaliadmin";

// *******************************************
// *************** Date Format ***************
// Numeric value only, Choose 1, 2, 3 or 4
// 1. day / month / year (European format)
// 2. day - month - year (European format)
// 3. month / day / year (American format)
// 4. month - day - year (American format)

$dateformat = 3;



// ************************************************************************
// ************************************************************************
// ************************** DO NOT EDIT THIS ! **************************
$connect = @mysqli_connect ($host,$user,$passwd,$dbname);
 $db = @mysqli_select_db ($connect,$dbname);


if ($db) {
$setnames = mysqli_query($connect,"SET NAMES 'UTF8' ");
// 	$bd_test_req = mysqli_query($connect,"select count(id_site) from site_infos");
}

?>