<?php
/**
 * @package     gdprcompliance
 * @author      Rudy Laurent
 * @copyright   Copyright (c) 2015-2019 FactorFX
 * @license     AGPL License 3.0 or (at your option) any later version
 *              http://www.gnu.org/licenses/agpl-3.0-standalone.html
 * @link        https://www.factorfx.com
 * @since       2019
 *
 * --------------------------------------------------------------------------
 */

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_gdprcompliance() {
    global $PLUGIN_HOOKS,$CFG_GLPI;

    $PLUGIN_HOOKS['change_profile']['gdprcompliance'] = array(PluginGdprcomplianceProfile::class,'initProfile');
    Plugin::registerClass('PluginGdprcompliance', array('addtabon' => array('Tools')));
    Plugin::registerClass(PluginGdprcomplianceConfig::class, ['addtabon' => 'Config']);
    Plugin::registerClass(PluginGdprcomplianceAction::class, ['addtabon' => 'Entity']);
    Plugin::registerClass(PluginGdprcomplianceProfile::class, array('addtabon' => 'Profile'));

    $PLUGIN_HOOKS['change_profile']['gdprcompliance'] = ['PluginGdprcomplianceProfile', 'initProfile'];

    $PLUGIN_HOOKS['menu_toadd']['gdprcompliance'] = ['tools' => 'PluginGdprcomplianceConfig'];

    $PLUGIN_HOOKS['csrf_compliant']['gdprcompliance'] = true;


}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_gdprcompliance() {
    return [
        'name'           => 'Plugin Gdprcompliance',
        'version'        => '1.0',
        'author'         => 'Rudy Laurent',
        'license'        => 'AGPLv3+',
        'minGlpiVersion' => '9.4'
    ];
}

/**
 * @return bool
 */
function plugin_gdprcompliance_check_prerequisites() {

   if (version_compare(GLPI_VERSION,'9.4','lt') || version_compare(GLPI_VERSION,'9.6','ge')) {
      echo "This plugin requires GLPI >= 9.4";
      return false;
   }
   return true;
}


/**
 * @param bool $verbose
 * @return bool
 */
function plugin_gdprcompliance_check_config($verbose=false) {
   return true;
}