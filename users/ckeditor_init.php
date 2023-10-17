<?php
/*
 * 	Manhali - Free Learning Management System
 *	ckeditor_init.php
 *	2013-03-15 13:04
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

if (isset($_SESSION['log']) && $_SESSION['log'] == 1 && isset($grade_user_session)){
	$_SESSION['IsAuthorized'] = 1;
	
 function ckeditor_replace($ck_lang,$ckeditor_txtarea) {
	
	if (file_exists("../jscripts/ckeditor/lang/".$ck_lang.".js"))
		$ckeditor_lang = $ck_lang;
	else $ckeditor_lang = "en";
	
	echo "<script src=\"../jscripts/ckeditor/ckeditor.js\"></script>";
	echo "<script type=\"text/javascript\">
	 CKEDITOR.replace( '$ckeditor_txtarea',
	{
	uiColor : '#c89664',
	language : '$ckeditor_lang',
	filebrowserBrowseUrl : '../jscripts/ckfinder/ckfinder.html',
	filebrowserImageBrowseUrl : '../jscripts/ckfinder/ckfinder.html?type=Images',
	filebrowserFlashBrowseUrl : '../jscripts/ckfinder/ckfinder.html?type=Flash',
	filebrowserUploadUrl : '../jscripts/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
	filebrowserImageUploadUrl : '../jscripts/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
	filebrowserFlashUploadUrl : '../jscripts/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	});
	</script>";
 }
} else echo restricted_access;
?>