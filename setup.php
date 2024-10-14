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
 * plugin_init_gdprcompliance
 *
 * @return void
 */
function plugin_init_gdprcompliance() {
    global $PLUGIN_HOOKS;

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
 * plugin_version_gdprcompliance
 *
 * @return void
 */
function plugin_version_gdprcompliance() {
    return [
        'name'           => __('GDPR Compliance', 'gdprcompliance'),
        'version'        => '2.0.0',
        'author'         => 'Rudy Laurent, Charlène Auger',
        'license'        => 'AGPLv3+',
        'minGlpiVersion' => '9.5'
    ];
}

/**
 * plugin_gdprcompliance_check_prerequisites
 *
 * @return void
 */
function plugin_gdprcompliance_check_prerequisites() {
    if (version_compare(GLPI_VERSION,'9.5','lt') || version_compare(GLPI_VERSION,'9.6','ge')) {
        echo "This plugin requires GLPI >= 9.5";
        return false;
    }
    return true;
}

/**
 * plugin_gdprcompliance_check_config
 *
 * @param  mixed $verbose
 * @return void
 */
function plugin_gdprcompliance_check_config($verbose=false) {
    return true;
}
