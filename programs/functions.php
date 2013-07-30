<?php
/************************************************************************
 * OVIDENTIA http://www.ovidentia.org                                   *
 ************************************************************************
 * Copyright (c) 2003 by CANTICO ( http://www.cantico.fr )              *
 *                                                                      *
 * This file is part of Ovidentia.                                      *
 *                                                                      *
 * Ovidentia is free software; you can redistribute it and/or modify    *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2, or (at your option)  *
 * any later version.                                                    *
 *                                                                        *
 * This program is distributed in the hope that it will be useful, but  *
 * WITHOUT ANY WARRANTY; without even the implied warranty of            *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                    *
 * See the  GNU General Public License for more details.                *
 *                                                                        *
 * You should have received a copy of the GNU General Public License    *
 * along with this program; if not, write to the Free Software            *
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,*
 * USA.                                                                    *
************************************************************************/

include_once "base.php";
include_once $GLOBALS['babInstallPath'].'utilit/registerglobals.php';
include_once $GLOBALS['babInstallPath']."admin/acl.php";
include_once $GLOBALS['babInstallPath'].'utilit/addonsincl.php';
include_once $GLOBALS['babInstallPath'].'utilit/inifileincl.php';

class skins_listing_bab_addons_list_theme {
    function skins_listing_bab_addons_list_theme() {
        include_once $GLOBALS['babInstallPath'].'utilit/addonsincl.php';

        $this->title = false;

        bab_addonsInfos::insertMissingAddonsInTable();
        bab_addonsInfos::clear();

        $this->res = $this->getRes();
    }

    function getRes() {
        $return = array();
        foreach(bab_addonsInfos::getDbAddonsByName() as $name => $addon) {
            if ($this->display($addon)) {
                $return[$name] = $addon;
            }
        }

        bab_sort::ksort($return, bab_sort::CASE_INSENSITIVE);
        return $return;
    }

    function display($addon) {
        if (!$addon) {
            return false;
        }

        $type = $addon->getAddonType();
        return 'THEME' === $type;
    }

    function getnext() {
        if( list(,$addon) = each($this->res)) {
            /*@var $addon bab_addonInfos */

            $this->title        = bab_toHtml($addon->getName());

            $addon->updateInstallStatus();

            $this->id_addon     = $addon->getId();

            $this->description  = bab_toHtml($addon->getDescription(), BAB_HTML_ALL);
            $this->iconpath     = bab_toHtml($addon->getIconPath());
            $this->imagepath    = bab_toHtml($addon->getImagePath());

            $confurl = $addon->getConfigurationUrl();
            if (null !== $confurl && $addon->isAccessValid()) {
                $this->configurationurl    = bab_toHtml($confurl);
            } else {
                $this->configurationurl    = false;
            }

            return true;
        }

        return false;
    }
}

function skins_listing_themeList() {
    global $babBody;

    $temp = new skins_listing_bab_addons_list_theme();
    $babBody->babecho(bab_printTemplate($temp, "addons/skins_listing/theme_bar.html", "skins_listing"));
}

function skins_listing_chosetheme() {
    require_once $GLOBALS['babInstallPath'].'utilit/urlincl.php';
    require_once $GLOBALS['babInstallPath'].'utilit/skinincl.php';
    global $babDB;
    global $babBody;

    $row = bab_addonsInfos::getDbRow(bab_rp('id'));

    $skin = new bab_skin($row['title']);
    if (!$skin->isAccessValid()) {
        return;
    }

    $arr = $skin->getStyles();

    $babDB->db_query('UPDATE bab_sites SET skin='.$babDB->quote($skin->getName()).', style='.$babDB->quote(reset($arr)).'  WHERE name='.$babDB->quote($GLOBALS['babSiteName']));

    if (isset($_SERVER['HTTP_REFERER'])) {
        header('Location: '.$_SERVER['HTTP_REFERER']);
    } else {
        header('Location: index.php');
    }
}
