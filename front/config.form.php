<?php
/**
 * ---------------------------------------------------------------------
 * ITSM-NG
 * Copyright (C) 2022 ITSM-NG and contributors.
 *
 * https://www.itsm-ng.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of ITSM-NG.
 *
 * ITSM-NG is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * ITSM-NG is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ITSM-NG. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

include ("../../../inc/includes.php");

$plugin = new Plugin();

if (!$plugin->isInstalled('gdprcompliance') || !$plugin->isActivated('gdprcompliance')) {
	echo "Plugin not installed or activated";
	return;
}

$config = new PluginGdprcomplianceConfig();

if (isset($_POST["update"])) {
	Session::checkRight("plugin_gdprcompliance_config", UPDATE);
	$config->updateConfig(1, $_POST);

	Session::addMessageAfterRedirect(__("Configuration sauvegardée avec succès !", "gdprcompliance"), true);
	Html::back();
} elseif(!empty($_GET) && array_key_exists('config', $_GET)) {
	Html::header(__("ITSM GDPR", "gdprcompliance"), '', "tools", "plugingdprcomplianceconfig", "config");
	$config->showForm(1);
	Html::footer();
} elseif(!empty($_GET) && array_key_exists('configdata', $_GET)) {
	Html::header(__("ITSM GDPR", "gdprcompliance"), '', "tools", "plugingdprcomplianceconfig", "config");
	$config->showConfigData(1);
	Html::footer();
} else {
	Html::header(__("ITSM GDPR", "gdprcompliance"), $_SERVER['PHP_SELF'], "tools", "PluginGdprcomplianceConfig", "gdprcompliance");
	$config->showMenu();
	Html::footer();
}
