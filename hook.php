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

/**
 * plugin_gdprcompliance_install
 *
 * @return void
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
                  `change` json default NULL,
                  PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

      $DB->query($query) or die("error creating glpi_plugin_gdprcompliance_configs ". $DB->error());

      $query = "INSERT INTO `glpi_plugin_gdprcompliance_configs` VALUES ('1', 'FALSE', '0', NULL);";
      $DB->query($query) or die($DB->error());
   }

   CronTask::Register('PluginGdprcomplianceAction', 'CleanUsers', DAY_TIMESTAMP);

   return true;
}

/**
 * plugin_gdprcompliance_uninstall
 *
 * @return void
 */
function plugin_gdprcompliance_uninstall() {
   global $DB;
   
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