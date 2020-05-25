<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2003      Jean-Louis Bergamo   <jlb@j1b.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2011-2012 Juanjo Menent		<jmenent@2byte.es>
 * Copyright (C) 2012      J. Fernando Lagrange <fernando@demo-tic.org>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
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
 *   	\file       htdocs/adherents/admin/adherent.php
 *		\ingroup    member
 *		\brief      Page to setup the module Foundation
 */

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/member.lib.php';


$langs->loadLangs(array("admin","members"));

if { (! $user->admin) accessforbidden(); }

$CHAIN='chaine';
$ALPHA='alpha';
$type=array('yesno','texte','CHAIN');

$action = GETPOST('action', 'ALPHA');

const ADHERENT_LOGIN_NOT_REQUIRED='ADHERENTLOGINNOTREQUIRED';
const ADHERENT_MAIL_REQUIRED='ADHERENTMAILREQUIRED';
const ADHERENT_DEFAULT_SENDINFOBYMAIL='ADHERENTDEFAULTSENDINFOBYMAIL'
const ADHERENT_BANK_USE='ADHERENTBANKUSE';
const ADHERENT_VAT_FOR_SUBSCRIPTIONS='ADHERENTVATFORSUBSCRIPTIONS';
const ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS='ADHERENTPRODUCTIDFORSUBSCRIPTIONS';
if ($action == 'updateall')
{
    $db->begin();
    $res1=$res2=$res3=$res4=$res5=$res6=0;
    $res1=dolibarr_set_const($db, 'ADHERENT_LOGIN_NOT_REQUIRED', GETPOST('ADHERENT_LOGIN_NOT_REQUIRED', 'ALPHA')?0:1, 'CHAIN', 0, '', $conf->entity);
    $res2=dolibarr_set_const($db, 'ADHERENT_MAIL_REQUIRED', GETPOST('ADHERENT_MAIL_REQUIRED', 'ALPHA'), 'CHAIN', 0, '', $conf->entity);
    $res3=dolibarr_set_const($db, 'ADHERENT_DEFAULT_SENDINFOBYMAIL', GETPOST('ADHERENT_DEFAULT_SENDINFOBYMAIL', 'ALPHA'), 'CHAIN', 0, '', $conf->entity);
    $res4=dolibarr_set_const($db, 'ADHERENT_BANK_USE', GETPOST('ADHERENT_BANK_USE', 'ALPHA'), 'CHAIN', 0, '', $conf->entity);
    
    if ($conf->facture->enabled)
    {
        $res4=dolibarr_set_const($db, 'ADHERENT_VAT_FOR_SUBSCRIPTIONS', GETPOST('ADHERENT_VAT_FOR_SUBSCRIPTIONS', 'ALPHA'), 'CHAIN', 0, '', $conf->entity);
        $res5=dolibarr_set_const($db, 'ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', GETPOST('ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', 'ALPHA'), 'CHAIN', 0, '', $conf->entity);
        if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
        {
            $res6=dolibarr_set_const($db, 'ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', GETPOST('ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', 'ALPHA'), 'CHAIN', 0, '', $conf->entity);
        }
    }
    if ($res1 < 0 || $res2 < 0 || $res3 < 0 || $res4 < 0 || $res5 < 0 || $res6 < 0)
    {
        setEventMessages('ErrorFailedToSaveDate', null, 'errors');
        $db->rollback();
    }
    else
    {
        setEventMessages('RecordModifiedSuccessfully', null, 'mesgs');
        $db->commit();
    }
}


if ($action == 'update' || $action == 'add')
{
	$constname=GETPOST('constname', 'ALPHA');
	$constvalue=(GETPOST('constvalue_'.$constname) ? GETPOST('constvalue_'.$constname) : GETPOST('constvalue'));

	if (($constname=='ADHERENT_CARD_TYPE' || $constname=='ADHERENT_ETIQUETTE_TYPE' || $constname=='ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS') && $constvalue == -1){ $constvalue=''; }
	if ($constname=='ADHERENT_LOGIN_NOT_REQUIRED') 
	{
		if ($constvalue) { $constvalue=0;
	      } else { $constvalue=1;
              }
	}

	$consttype=GETPOST('consttype', 'ALPHA');
	$constnote=GETPOST('constnote');
	$res=dolibarr_set_const($db, $constname, $constvalue, $type[$consttype], 0, $constnote, $conf->entity);

	if (! $res > 0){ $error++;}

	if (! $error)
	{
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	}
	else
	{
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
}


if ($action == 'set')
{
    $result=dolibarr_set_const($db, GETPOST('name', 'ALPHA'), GETPOST('value'), '', 0, '', $conf->entity);
    if ($result < 0)
    {
        print $db->error();
    }
}


if ($action == 'unset')
{
    $result=dolibarr_del_const($db, GETPOST('name', 'ALPHA'), $conf->entity);
    if ($result < 0)
    {
        print $db->error();
    }
}



/*
 * View
 */

$form = new Form($db);

$help_url='EN:Module_Foundations|FR:Module_Adh&eacute;rents|ES:M&oacute;dulo_Miembros';

llxHeader('', $langs->trans("MembersSetup"), $help_url);


$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($langs->trans("MembersSetup"), $linkback, 'title_setup');
const CELDA_CERRADA= '</td>';
const CREACION_FILA = '</tr>\n'
	
$head = member_admin_prepare_head();

dol_fiche_head($head, 'general', $langs->trans("Members"), -1, 'user');

print '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="updateall">';

print load_fiche_titre($langs->trans("MemberMainOptions"), '', '');
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Description").'CELDA_CERRADA';
print '<td>'.$langs->trans("Value").'CELDA_CERRADA';
print "CREACION_FILA";
const TR_CLASS_TD='<tr class="oddeven"><td>';
const CELDA_CERRADA_CELDA_ABIERTA='</td><td>';
const CERRAR_TD_TR='</td></tr>\n';
print 'TR_CLASS_TD'.$langs->trans("AdherentLoginRequired").'CELDA_CERRADA_CELDA_ABIERTA';
print $form->selectyesno('ADHERENT_LOGIN_NOT_REQUIRED', (! empty($conf->global->ADHERENT_LOGIN_NOT_REQUIRED)?0:1), 1);
print "CERRAR_TD_TR";


print 'TR_CLASS_TD'.$langs->trans("AdherentMailRequired").'CELDA_CERRADA_CELDA_ABIERTA';
print $form->selectyesno('ADHERENT_MAIL_REQUIRED', (! empty($conf->global->ADHERENT_MAIL_REQUIRED)?$conf->global->ADHERENT_MAIL_REQUIRED:0), 1);
print "CERRAR_TD_TR";


print 'TR_CLASS_TD'.$langs->trans("MemberSendInformationByMailByDefault").'CELDA_CERRADA_CELDA_ABIERTA';
print $form->selectyesno('ADHERENT_DEFAULT_SENDINFOBYMAIL', (! empty($conf->global->ADHERENT_DEFAULT_SENDINFOBYMAIL)?$conf->global->ADHERENT_DEFAULT_SENDINFOBYMAIL:0), 1);
print "CERRAR_TD_TR";


print 'TR_CLASS_TD'.$langs->trans("MoreActionsOnSubscription").'CELDA_CERRADA';
$arraychoices=array('0'=>$langs->trans("None"));
if (! empty($conf->banque->enabled)){ $arraychoices['bankdirect']=$langs->trans("MoreActionBankDirect"); }
if (! empty($conf->banque->enabled) && ! empty($conf->societe->enabled) && ! empty($conf->facture->enabled)) { $arraychoices['invoiceonly']=$langs->trans("MoreActionInvoiceOnly"); }
if (! empty($conf->banque->enabled) && ! empty($conf->societe->enabled) && ! empty($conf->facture->enabled)) { $arraychoices['bankviainvoice']=$langs->trans("MoreActionBankViaInvoice"); }
print '<td>';
print $form->selectarray('ADHERENT_BANK_USE', $arraychoices, $conf->global->ADHERENT_BANK_USE, 0);
if ($conf->global->ADHERENT_BANK_USE == 'bankdirect' || $conf->global->ADHERENT_BANK_USE == 'bankviainvoice')
{
    print '<br><div style="padding-top: 5px;"><span class="opacitymedium">'.$langs->trans("ABankAccountMustBeDefinedOnPaymentModeSetup").'</span></div>';
}
print 'CELDA_CERRADA';
print "CREACION_FILA";


if ($conf->facture->enabled)
{
	print 'TR_CLASS_TD'.$langs->trans("VATToUseForSubscriptions").'CELDA_CERRADA';
	if (! empty($conf->banque->enabled))
	{
		print '<td>';
		print $form->selectarray('ADHERENT_VAT_FOR_SUBSCRIPTIONS', array('0'=>$langs->trans("NoVatOnSubscription"),'defaultforfoundationcountry'=>$langs->trans("Default")), (empty($conf->global->ADHERENT_VAT_FOR_SUBSCRIPTIONS)?'0':$conf->global->ADHERENT_VAT_FOR_SUBSCRIPTIONS), 0);
		print 'CELDA_CERRADA';
	}
	else
	{
		print '<td class="right">';
		print $langs->trans("WarningModuleNotActive", $langs->transnoentities("Module85Name"));
		print 'CELDA_CERRADA';
	}
	print "CREACION_FILA";

	if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
	{
		print 'TR_CLASS_TD'.$langs->trans("ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS").'CELDA_CERRADA';
		print '<td>';
		$form->select_produits($conf->global->ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS, 'ADHERENT_PRODUCT_ID_FOR_SUBSCRIPTIONS', '', 0);
		print 'CELDA_CERRADA';
	}
	print "CREACION_FILA";
}

print '</table>';

print '<div class="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Update").'" name="Button">';
print '</div>';

print '</form>';

print '<br>';





$constantes=array(
		'ADHERENT_CARD_TYPE',
//		'ADHERENT_CARD_BACKGROUND',
		'ADHERENT_CARD_HEADER_TEXT',
		'ADHERENT_CARD_TEXT',
		'ADHERENT_CARD_TEXT_RIGHT',
		'ADHERENT_CARD_FOOTER_TEXT'
		);

print load_fiche_titre($langs->trans("MembersCards"), '', '');

$helptext='*'.$langs->trans("FollowingConstantsWillBeSubstituted").'<br>';
$helptext.='__DOL_MAIN_URL_ROOT__, __ID__, __FIRSTNAME__, __LASTNAME__, __FULLNAME__, __LOGIN__, __PASSWORD__, ';
$helptext.='__COMPANY__, __ADDRESS__, __ZIP__, __TOWN__, __COUNTRY__, __EMAIL__, __BIRTH__, __PHOTO__, __TYPE__, ';
$helptext.='__YEAR__, __MONTH__, __DAY__';

form_constantes($constantes, 0, $helptext);

print '<br>';





$constantes=array('ADHERENT_ETIQUETTE_TYPE','ADHERENT_ETIQUETTE_TEXT');

print load_fiche_titre($langs->trans("MembersTickets"), '', '');

$helptext='*'.$langs->trans("FollowingConstantsWillBeSubstituted").'<br>';
$helptext.='__DOL_MAIN_URL_ROOT__, __ID__, __FIRSTNAME__, __LASTNAME__, __FULLNAME__, __LOGIN__, __PASSWORD__, ';
$helptext.='__COMPANY__, __ADDRESS__, __ZIP__, __TOWN__, __COUNTRY__, __EMAIL__, __BIRTH__, __PHOTO__, __TYPE__, ';
$helptext.='__YEAR__, __MONTH__, __DAY__';

form_constantes($constantes, 0, $helptext);

dol_fiche_end();


llxFooter();
$db->close();
