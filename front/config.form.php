<?php
/**
 * @package     gdprcompliance
 * @author      Rudy Laurent
 * @copyright   Copyright (c) 2015-2016 FactorFX
 * @license     AGPL License 3.0 or (at your option) any later version
 *              http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link        https://www.factorfx.com
 * @since       2015
 *
 * --------------------------------------------------------------------------
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
   Html::header(__("ITSM GDPR", "gdprcompliance"), '', "tools", "plugingdprcomplianceconfig", "config");
   $config->showMenu();
   Html::footer();

}
