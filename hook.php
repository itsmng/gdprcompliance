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
 * Plugin install process
 *
 * @return boolean
 */
function plugin_gdprcompliance_install() {
   global $DB;

   if (!$DB->tableExists("glpi_plugin_gdprcompliance_history")) {
      $query = "CREATE TABLE `glpi_plugin_gdprcompliance_history` (
                  `id` int(11) NOT NULL auto_increment,
                  `date` datetime default NULL,
                  `sucess` int NOT NULL default '0',
                  `error` int NOT NULL default '0',
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

      $DB->query($query) or die("error creating glpi_plugin_gdprcompliance_history ". $DB->error());
   }

   if (!$DB->tableExists("glpi_plugin_gdprcompliance_configs")) {
      $query = "CREATE TABLE `glpi_plugin_gdprcompliance_configs` (
                  `id` int(11) NOT NULL auto_increment,
                  `active` boolean default FALSE,
                  `mode` int NOT NULL default '0',
                  `change` VARCHAR(255) default NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

      $DB->query($query) or die("error creating glpi_plugin_gdprcompliance_configs ". $DB->error());
   }

   $query = "INSERT INTO `glpi_plugin_gdprcompliance_configs`
                  VALUES ('1',
                        'FALSE',
                        '0', NULL);";
   $DB->query($query) or die($DB->error());

   CronTask::Register('PluginGdprcomplianceAction', 'CleanUsers', DAY_TIMESTAMP);

   return true;
}


/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_gdprcompliance_uninstall() {
   global $DB;
   
   // Old version tables
   if ($DB->tableExists("glpi_plugin_gdprcompliance_history")) {
      $query = "DROP TABLE `glpi_plugin_gdprcompliance_history`";
      $DB->query($query) or die("error deleting glpi_plugin_gdprcompliance_history");
   }
   if ($DB->tableExists("glpi_plugin_gdprcompliance_configs")) {
      $query = "DROP TABLE `glpi_plugin_gdprcompliance_configs`";
      $DB->query($query) or die("error deleting glpi_plugin_gdprcompliance_configs");
   }
   return true;
}