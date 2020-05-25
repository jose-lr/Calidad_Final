#!/usr/bin/env php
<?php
/* Copyright (C) 2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *      \file       dev/examples/create_user.php
 *      \brief      This file is an example for a command line script
 *		\author		Put author's name here
 *		\remarks	Put here some comments
 */

$sapi_type2 = php_sapi_name();
$script_file2 = basename(__FILE__);
$path2=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type2, 0, 3) == 'cgi') {
    echo "Error: You are using PHP for CGI. To execute ".$script_file2." from command line, you must use PHP for CLI mode.\n";
    exit;
}

// Global variables
$version2='1.7';
$error2=0;


// -------------------- START OF YOUR CODE HERE --------------------
// Include Dolibarr environment
require_once $path2."../../htdocs/master.inc.php";
// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs2->load("main");				// To load language file for default language
@set_time_limit(0);

// Load user and its permissions
$result2=$user2->fetch('', 'admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
if (! $result2 > 0) { dol_print_error('', $user2->error); exit; }
$user2->getrights();


print "***** ".$script_file2." (".$version2.") *****\n";

// Start of transaction
$db2->begin();

require_once DOL_DOCUMENT_ROOT."/user/class/user.class.php";

// Create user object
$obj = new User($db2);

$obj->login = 'ABCDEF';
$obj->nom   = 'ABCDEF';

return $error2;
