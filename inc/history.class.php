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

class PluginGdprcomplianceHistory extends CommonDBTM {
    static $rightname = "plugin_gdprcompliance_config";

    /**
     * @param int $nb
     *
     * @return string|translated
     */
    static function getTypeName($nb = 0) {
       return __("IHistorique", 'gdprcompliance');
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
                echo "<th>" . __("Sucess", 'gdprcompliance') . "</th>";
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