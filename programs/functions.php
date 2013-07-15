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
 * any later version.													*
 *																		*
 * This program is distributed in the hope that it will be useful, but  *
 * WITHOUT ANY WARRANTY; without even the implied warranty of			*
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.					*
 * See the  GNU General Public License for more details.				*
 *																		*
 * You should have received a copy of the GNU General Public License	*
 * along with this program; if not, write to the Free Software			*
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,*
 * USA.																	*
************************************************************************/

include_once "base.php";
include_once $GLOBALS['babInstallPath'].'utilit/registerglobals.php';
include_once $GLOBALS['babInstallPath']."admin/acl.php";
include_once $GLOBALS['babInstallPath'].'utilit/addonsincl.php';
include_once $GLOBALS['babInstallPath'].'utilit/inifileincl.php';

class skins_listing_bab_addons_list
	{
	var $name;
	var $url;
	var $desctxt;

	var $arr = array();
	var $db;
	var $count;
	var $res;
	var $catchecked;
	var $disabled;
	var $checkall;
	var $uncheckall;
	var $update;
	var $view;
	var $viewurl;
	var $altbg = true;

	function bab_addons_list()
		{
		include_once $GLOBALS['babInstallPath'].'utilit/addonsincl.php';

		$this->display_in_form = true;
		$this->title = false;

		$this->name = bab_translate("Name");
		$this->desctxt = bab_translate("Description");
		$this->upgradetxt = bab_translate("Upgrade");
		$this->disabled = bab_translate("Disabled");
		$this->uncheckall = bab_translate("Uncheck all");
		$this->checkall = bab_translate("Check all");
		$this->update = bab_translate("Update");
		$this->t_access = bab_translate("Access");
		$this->view = bab_translate("Rights");
		$this->versiontxt = bab_translate("Version");
		$this->t_delete = bab_translate("Delete");
		$this->t_historic = bab_translate("Historic");
		$this->t_download = bab_translate("Download");
		$this->t_configure = bab_translate("Configuration");
		$this->t_install = bab_translate("Install");
		$this->chosetheme = bab_translate("Use this theme");
		$this->confirmdelete = bab_toHtml(bab_translate("Are you sure you want to delete this add-on ?"), BAB_HTML_JS);

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
		return 'EXTENSION' === $type;
	}

	function getnext()
		{

		if( list(,$addon) = each($this->res))
			{
			$this->altbg = !$this->altbg;

			/*@var $addon bab_addonInfos */

			$this->title 			= bab_toHtml($addon->getName());
			$this->requrl 			= bab_toHtml($GLOBALS['babUrlScript']."?tg=addons&idx=requirements&item=".$addon->getId());
			$this->viewurl 			= bab_toHtml($GLOBALS['babUrlScript']."?tg=addons&idx=view&item=".$addon->getId());
			$this->exporturl 		= bab_toHtml($GLOBALS['babUrlScript']."?tg=addons&idx=export&item=".$addon->getId());
			$this->deleteurl		= bab_toHtml($GLOBALS['babUrlScript']."?tg=addons&idx=del&item=".$addon->getId());

			$addon->updateInstallStatus();

			$this->id_addon 		= $addon->getId();

			$this->catchecked 		= $addon->isDisabled();
			$this->access_control 	= $addon->hasAccessControl();
			$this->delete 			= $addon->isDeletable();
			$this->addversion 		= bab_toHtml($addon->getDbVersion());
			$this->description 		= bab_toHtml($addon->getDescription(), BAB_HTML_ALL);
			$this->iconpath			= bab_toHtml($addon->getIconPath());
			$this->imagepath        = bab_toHtml($addon->getImagePath());

			$confurl = $addon->getConfigurationUrl();
			if (null !== $confurl && $addon->isAccessValid()) {
				$this->configurationurl	= bab_toHtml($confurl);
			} else {
				$this->configurationurl	= false;
			}

			if ($addon->isUpgradable()) {
				$this->upgradeurl = bab_toHtml($GLOBALS['babUrlScript']."?tg=addons&idx=upgrade&item=".$addon->getId());
			} else {
				$this->upgradeurl = false;
			}

			if ('THEME' === $addon->getAddonType() && $addon->isAccessValid() && $GLOBALS['babSkin'] !== $addon->getName())
			{
				$this->chosethemeurl = bab_toHtml($GLOBALS['babUrlScript']."?tg=addons&idx=chosetheme&item=".$addon->getId());
			} else {
				$this->chosethemeurl = false;
			}

			return true;
			}

		return false;
		}
	}

class skins_listing_bab_addons_list_theme extends skins_listing_bab_addons_list {

	function skins_listing_bab_addons_list_theme() {
		parent::bab_addons_list();

		$this->display_in_form = false;

	}

	function display($addon) {
		return 'THEME' === $addon->getAddonType();
	}
}

function skins_listing_themeList()
	{
	global $babBody;

	$temp = new skins_listing_bab_addons_list_theme();
	$babBody->babecho(bab_printTemplate($temp, "addons/skins_listing/theme_bar.html", "skins_listing"));
	}
