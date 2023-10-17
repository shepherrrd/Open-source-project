<?php
/*
 * 	Manhali - Free Learning Management System
 *	security_functions.php
 *	2009-01-01 23:51
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
function escape_string($value){
include("dbconfig.php");

    $magic_quotes_active = (bool) ini_get('magic_quotes_gpc');
    $new_enough_php = function_exists("mysql_real_escape_string");
    if($new_enough_php){
    		if($magic_quotes_active){
        		$value = stripslashes($value);
    		}
    		$value = mysqli_real_escape_string($connect,$value);
    }
    else{
        if(!$magic_quotes_active){
            $value = addslashes($value);
        }
    }
    return $value;
}

function fonc_rand($length) {
	$rndm 		= "abcdefghijklmnopqrstuvwxyz0123456789";
	$randpass	= '';
	mt_srand(10000000*(double)microtime());
	for ($i = 0; $i < $length; $i++)
		$randpass .= $rndm[mt_rand(0,35)];
	return $randpass;
}

function html_ent($value) {
	$value = htmlentities($value,ENT_QUOTES,"UTF-8");
	return $value;
}

function mail_valide($adrmail) {
   		$Syntaxe="#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#";
   		if((preg_match($Syntaxe,$adrmail)) && (strlen($adrmail)<51))
      		return true;
   		else
     		return false;
}

function open_session($folder){
	@ini_set('session.use_only_cookies',1);
	@ini_set('session.cookie_httponly',1);
	$chars = array('/', '\\', ' ', '-', '.', ',');
	$code0sess = substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/'.$folder)+1);
	$code1sess = str_replace($chars,'_',$code0sess);
	$code2sess = str_replace('.','_',$_SERVER['REMOTE_ADDR']);
	session_name("manhali".$code1sess.$code2sess);
	session_start();
	session_regenerate_id();
}

function close_session(){
	session_unset();
	session_destroy();
}
?>