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

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}

class PluginGdprcomplianceProfile extends Profile
{

	static $rightname = "profile";

	static $all_profile_rights = array(
		'plugin_gdprcompliance_config',
	);

	public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
		if ($item->getType() == 'Profile') {
			return PluginGdprcomplianceConfig::getTypeName(0);
		}
		return '';
	}

	static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
		$_profile = new self();
		if ($item->getType() == 'Profile') {
			$id = $item->getID();
			self::addRight($id, 0);
			$_profile->showForm($id);
		}

		return true;
	}

	static function install(Migration $migration) {
		// Create my right access
		self::addRight(false, 5);
	}

	static function uninstall() {
		// Remove all profiles
		self::removeAllRight();
		return true;
	}

	static function removeAllRight() {
		$_profile = new ProfileRight();
		foreach ($_profile->find("`name` LIKE 'plugin_gdprcompliance_%'") as $data) {
			$_profile->delete($data);
		}
	}

	/**
	 * Addright for current session if no id current session is selected
	 *
	 * @param $profiles_id
	 * @param $right_value
	 */
	static function addRight($profiles_id, $right_value) {
		$_profile = new ProfileRight();
		foreach (self::$all_profile_rights as $profile_name) {
			if (!$_profile->find(["profiles_id" => $profiles_id, "name" => $profile_name])) {
				$right['profiles_id'] = $profiles_id ?: $_SESSION['glpiactiveprofile']['id'];
				$right['name'] = $profile_name;
				$right['rights'] = $right_value;
				$_profile->add($right);
			}
		}
	}


	/**
	 *  Create form for search
	 *
	 * @param int $profiles_id
	 * @param bool|true $openform
	 * @param bool|true $closeform
	 * @return bool
	 */
	public function showForm($profiles_id = 0, $openform = true, $closeform = true) {
		$profile = new Profile();
		$canedit = Session::haveRightsOr(self::$rightname, array(READ, UPDATE));

		echo "<div class='firstbloc'>";
		if ($canedit && $openform) {
			echo "<form method='post' action='" . $profile->getFormURL() . "'>";
		}

		$profile->getFromDB($profiles_id);

		$config_right = $this->getAllRights();
		$profile->displayRightsChoiceMatrix($config_right, array(
			'canedit' => $canedit,
			'default_class' => 'tab_bg_2',
			'title' => __s('General'))
		);

		if ($canedit && $closeform) {
			echo "<div class='center'>";
			echo Html::hidden('id', array('value' => $profiles_id));
			echo Html::submit(_sx('button', 'Save'), array('name' => 'update'));
			echo "</div>";
			Html::closeForm();
		}

		echo "</div>";

		$this->showLegend();

		return true;
	}

	static function getAllRights() {

		$rights = array(
			array(
				'itemtype' => 'PluginGdprcomplianceConfig',
				'label' => __('Setup'),
				'field' => 'plugin_gdprcompliance_config',
				'rights' => [READ =>__('Read'), UPDATE => __('Update')]
			),
			array(
				'itemtype' => 'PluginGdprcomplianceHistory',
				'label' => __('History', 'gdprcompliance'),
				'field' => 'plugin_gdprcompliance_history',
				'rights' => [READ=>__('Read')]
			),
		);

		return $rights;
	}
}
