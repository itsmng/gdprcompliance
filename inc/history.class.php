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

class PluginGdprcomplianceHistory extends CommonDBTM {
    static $rightname = "plugin_gdprcompliance_history";

    /**
     * @param int $nb
     *
     * @return string|translated
     */
    static function getTypeName($nb = 0) {
       return __("History", 'gdprcompliance');
    }

    public static function showHistory(){
        global $DB;

        if (Session::haveRight("plugin_gdprcompliance_history", READ)) {
            $query = "SELECT * FROM glpi_plugin_gdprcompliance_history ORDER BY id DESC";
            $result_glpi = $DB->query($query);
            $history = [];
            if ($DB->numrows($result_glpi) > 0) {
                echo "<div class='center'>";
                echo "<table class='tab_cadrehov'>";
                echo "<tr>";
                echo "<th>" . __("Date", 'gdprcompliance') . "</th>";
                echo "<th>" . __("Success", 'gdprcompliance') . "</th>";
                echo "<th>" . __("Error", 'gdprcompliance') . "</th>";
                while ($data = $DB->fetchArray($result_glpi)){
                    echo "<tr>";
                    echo "<td>" . $data['date'] . "</td>";
                    echo "<td>" . $data['sucess'] . "</td>";
                    echo "<td>" . $data['error'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
            }
        } else {
            echo __("You don't have the required rights", "gdprcompliance");
        }
    }
}