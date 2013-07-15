<?php
//-------------------------------------------------------------------------
// OVIDENTIA http://www.ovidentia.org
// Ovidentia is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2, or (at your option)
// any later version.
//
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
// USA.
//-------------------------------------------------------------------------
/**
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @copyright Copyright (c) 2008 by CANTICO ({@link http://www.cantico.fr})
 */
 
require_once dirname(__FILE__) . '/functions.php';

function skins_listing_upgrade($version_base, $version_ini)
{
    global $babDB;
    require_once $GLOBALS['babInstallPath'] . 'utilit/upgradeincl.php';

    bab_addEventListener('bab_eventPageRefreshed', 'skins_listing_onPageRefreshed', 'addons/skins_listing/init.php', 'theme_crm_like');

    return true;
}

// Lors de la suppression du module
function skins_listing_onDeleteAddon() {
    include_once $GLOBALS['babInstallPath'].'utilit/eventincl.php';

    bab_removeEventListener('bab_eventPageRefreshed', 'skins_listing_onPageRefreshed', 'addons/skins_listing/init.php');
    
    return true;
}

function skins_listing_onPageRefreshed()
{
    global $babBody;
   
    // Ajout de la feuille de style
     
    $stylesheet = 'addons/skins_listing/style.css';
    $babBody->addStyleSheet($stylesheet);

    // Affichage

    skins_listing_themeList();
}

