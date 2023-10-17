<?php
/*
 * 	Manhali - Free Learning Management System
 *	meta.php
 *	2009-04-12 23:44
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

echo "\n<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
echo "\n<meta name=\"title\" content=\"".$title_site."\" />";
echo "\n<meta name=\"description\" content=\"".$description_site."\" />";
echo "\n<meta name=\"keywords\" content=\"".$keywords_site."\" />";
echo "\n<meta name=\"indentifier-url\" content=\"".$url_site."\" />";
echo "\n<meta name=\"author\" content=\"EL HADDIOUI ISMAIL\" />";
echo "\n<meta name=\"language\" content=\"".$language."\" />";
echo "\n<meta name=\"revisit-after\" content=\"15\" />";
echo "\n<meta name=\"copyright\" content=\"Manhali 2009-2014\" />";
echo "\n<meta name=\"robots\" content=\"Index, follow\" />";
echo "\n<meta name=\"googlebot\" content=\"index,follow,all\" />";

?>