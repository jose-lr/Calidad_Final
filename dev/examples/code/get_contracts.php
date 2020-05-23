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
 *      \file       dev/examples/get_contracts.php
 *      \brief      This file is an example for a command line script
 *		\author		Put author's name here
 *		\remarks	Put here some comments
 */
print "***** ".$script_file." (".$version.") *****\n";
if (! isset($argv[1])) {	// Check parameters
    print "Usage: ".$script_file." id_thirdparty ...\n";
    exit;
}
print '--- start'."\n";
print 'Argument id_thirdparty='.$argv[1]."\n";


// Start of transaction
$db->begin();

require_once DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php";

// Create contract object
$obj = new Contrat($db);
$obj->socid=$argv[1];

$listofcontractsforcompany=$obj->getListOfContracts('all');

print $listofcontractsforcompany;


// -------------------- END OF YOUR CODE --------------------

$db->close();

return $error;
