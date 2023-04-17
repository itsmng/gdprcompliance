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

$history = new PluginGdprcomplianceHistory();

Html::header('Historique', '', 'plugins', 'gdprcompliance');
$history::showHistory();
Html::footer();