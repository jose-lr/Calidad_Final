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
 *      \file       dev/examples/create_product.php
 *      \brief      This file is an example for a command line script
 *		\author		Put author's name here
 *		\remarks	Put here some comments
 */

$sapi_type1 = php_sapi_name();
$script_file1 = basename(__FILE__);
$path1=dirname(__FILE__).'/';

// Test if batch mode
if (substr($sapi_type1, 0, 3) == 'cgi') {
    echo "Error: You are using PHP for CGI. To execute ".$script_file1." from command line, you must use PHP for CLI mode.\n";
    exit;
}

// Global variables
$version1='1.10';
$error1=0;


// -------------------- START OF YOUR CODE HERE --------------------
// Include Dolibarr environment
require_once $path1."../../htdocs/master.inc.php";
// After this $db, $mysoc, $langs and $conf->entity are defined. Opened handler to database will be closed at end of file.

//$langs->setDefaultLang('en_US'); 	// To change default language of $langs
$langs1->load("main");				// To load language file for default language
@set_time_limit(0);

// Load user and its permissions
$result1=$user1->fetch('', 'admin');	// Load user for login 'admin'. Comment line to run as anonymous user.
if (! $result1 > 0) { dol_print_error('', $user1->error); exit; }
$user1->getrights();


print "***** ".$script_file1." (".$version1.") *****\n";


// Start of transaction
$db1->begin();

require_once DOL_DOCUMENT_ROOT."/product/class/product.class.php";

// Create instance of object
$myproduct=new Product($db1);

// Definition of product instance properties
$myproduct->ref                = '1234';
$myproduct->label              = 'label';
$myproduct->price              = '10';
$myproduct->price_base_type    = 'HT';
$myproduct->tva_tx             = '19.6';
$myproduct->type               = Product::TYPE_PRODUCT;
$myproduct->status             = 1;
$myproduct->description        = 'Description';
$myproduct->note               = 'Note';
$myproduct->weight             = 10;
$myproduct->weight_units       = 0;

return $error1;
