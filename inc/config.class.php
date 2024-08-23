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
 * Class PluginOcsinventoryngConfig
 */
class PluginGdprcomplianceConfig extends CommonDBTM {

   /**
    * @var string
    */
   static $rightname = "plugin_gdprcompliance_config";

   private $allowFields = [
      'name',
      'password_last_update',
      'phone',
      'phone2',
      'mobile',
      'realname',
      'firstname',
      'locations_id',
      'comment',
      'authtype',
      'last_login',
      'date_mod',
      'date_sync',
      'usertitles_id',
      'usercategories_id',
      'password_forget_token_date',
      'user_dn',
      'registration_number',
      'personal_token',
      'personal_token_date',
      'api_token_date',
      'api_token',
      'cookie_token_date',
      'picture',
      'begin_date',
      'end_date',
      'date_creation',
      'users_id_supervisor',
      'timezone',
      'email'
   ];

   private $textField = [
      "char", "varchar", "text", "longtext"
   ];

   /**
    * @param int $nb
    *
    * @return string|translated
    */
   static function getTypeName($nb = 0) {
      return __("GDPR Configuration", 'gdprcompliance');
   }

   /**
    * getMenuContent
    *
    * @return void
    */
   static function getMenuContent() {
       $menu = parent::getMenuContent();
       //Menu entry in config
       $menu['title'] = self::getTypeName(2);
       $menu['page'] = "/plugins/gdprcompliance/front/config.form.php";
       $menu['links']['search'] = "/plugins/gdprcompliance/front/config.form.php";
       $menu['icon'] = 'fas fa-shield-alt';

       return $menu;
   }

   /**
    * showMenu
    *
    * @return void
    */
   function showMenu(){
      echo "<div class='center'>";
      if (Session::haveRight("plugin_gdprcompliance_config", READ) ||
          Session::haveRight("plugin_gdprcompliance_config", UPDATE) ||
          Session::haveRight("plugin_gdprcompliance_history", READ)) {

         echo "<h2>" . __("GDPR Compliance", 'gdprcompliance') . "</h2>";
         $buttonClass="btn btn-outline-secondary d-block w-50 mx-auto mb-2";
         if (Session::haveRight("plugin_gdprcompliance_config", READ) || Session::haveRight("plugin_gdprcompliance_config", UPDATE)) {
            echo "<a href='./config.form.php?config=1' class='".$buttonClass."'>" . __("Setup") . "</a>";
            echo "<a href='./config.form.php?configdata=1' class='".$buttonClass."'>" . __("User data configuration", 'gdprcompliance') . "</a>";
         }
         if (Session::haveRight("plugin_gdprcompliance_history", READ)) {
            echo "<a href='./history.form.php' class='".$buttonClass."'>" . __("History", 'gdprcompliance') . "</a>";
         }

      } else {
         echo __("You don't have the required rights", 'gdprcompliance');
      }
      echo "</div>";
   }

   /**
    * @param array $options
    *
    * @return bool
    */
   function showForm($ID, $options = []) {
      global $DB;

      $mode = [
         0 => __('Clean', 'gdprcompliance'),
         1 => __('Remove', 'gdprcompliance')
      ];

      $query = "SELECT * FROM glpi_plugin_gdprcompliance_configs";
      $result_glpi = $DB->query($query);
      $saved = [];

      if ($DB->numrows($result_glpi) > 0) {
         while ($data = $DB->fetchArray($result_glpi)) {
            $saved['active'] = $data['active'];
            $saved['mode'] = $data['mode'];
         }
      }

      $form = [
         'action' => $this->getFormURL(),
         'buttons' => [
            [
                'value' => __('Save'),
                'class'  => 'btn btn-secondary',
                'name'  => 'update',
            ],
         ],
         'content' => [
            $this->getTypeName() => [
                'visible' => true,
                'inputs'  => [
                    __('Active automatic action', 'gdprcompliance') => [
                        'type'  => 'checkbox',
                        'name'  => 'active',
                        'value' => $saved['active'],
                    ],
                    __('Mode', 'gdprcompliance') => [
                        'type'  => 'select',
                        'name'  => 'mode',
                        'value' => $saved['mode'],
                        'values' => $mode,
                    ],
                    '' => [
                        'content' => "<span>" . __("Warning, if the mode is on remove, there is a risk of data loss !", 'gdprcompliance') . "</span>",
                        'col_lg' => 12,
                        'col_md' => 12,
                    ]
                ]
            ]
         ]
      ];
      renderTwigForm($form);

      return true;
   }

   /**
    * showConfigData
    *
    * @param  mixed $ID
    * @param  mixed $options
    * @return void
    */
   function showConfigData($ID, $options = []){
      global $DB;

      $query = "SELECT `COLUMN_NAME`, `DATA_TYPE` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`=Database() AND `TABLE_NAME`='glpi_users'";
      $result_glpi = $DB->query($query);

      $userColumns = [];

      if ($DB->numrows($result_glpi) > 0) {
         while ($data = $DB->fetchArray($result_glpi)) {
            if (in_array($data['COLUMN_NAME'], $this->allowFields)) {
               $userColumns[$data['COLUMN_NAME']]['COLUMN_NAME'] = $data['COLUMN_NAME'];
               $userColumns[$data['COLUMN_NAME']]['COLUMN_TYPE'] = $data['DATA_TYPE'];
            }
         }
         $userColumns['email']['COLUMN_NAME'] = 'email';
         $userColumns['email']['COLUMN_TYPE'] = 'varchar';
      }

      $query = "SELECT * FROM glpi_plugin_gdprcompliance_configs";
      $result_glpi = $DB->query($query);
      $saved = [];

      if ($DB->numrows($result_glpi) > 0) {
         while ($data = $DB->fetchArray($result_glpi)) {
            $changes = [];

            if(!is_null($data['change'])) $changes = json_decode($data['change']);

            foreach ($changes as $key => $change) {
               if($change == 999) {
                  $saved[$key]['value'] = 0;
                  $saved[$key]['change'] = null;
               } elseif($change == 1) {
                  $saved[$key]['value'] = 1;
                  $saved[$key]['change'] = null;
               } else {
                  $saved[$key]['value'] = 2;
                  $saved[$key]['change'] = $change;
               }
            }
            $saved['active'] = $data['active'];
            $saved['mode'] = $data['mode'];
         }
      }

      $this->initForm($ID, $options);
		$state = $this->getState();
      $this->showFormHeader(['formtitle' => false]);

      $fields = [
        'user_data' => __("User data", 'gdprcompliance'),
        'action' => __("Action", 'gdprcompliance'),
        'replace' => __("Replace", 'gdprcompliance'),
      ];
      $values = [];

      foreach ($userColumns as $key => $value) {
         $presetValue = "";
         $newValue = [];
         if(isset($saved[$value['COLUMN_NAME']])) $presetValue = $saved[$value['COLUMN_NAME']]['change'];
            $newValue['user_data'] = self::translateField($value['COLUMN_NAME']);
         if(in_array($value['COLUMN_TYPE'], $this->textField)) {
            $newValue['action'] = Dropdown::showFromArray($key,
                [
                    __('Forget', 'gdprcompliance'),
                    __('Keep', 'gdprcompliance'),
                    __('Change', 'gdprcompliance')
                ], [
                    'value' => array_key_exists($value['COLUMN_NAME'], $saved)
                        ? $saved[$value['COLUMN_NAME']]['value'] : 0,
                    'display' => false
            ]);
            $newValue['replace'] = "<input id='change".$value['COLUMN_NAME'].
                "' class='form-control ".$value["COLUMN_NAME"].
                "' name='change_".$value['COLUMN_NAME'].
                "' value='".$presetValue."'>";
         } else {
            $newValue['action'] = Dropdown::showFromArray($value['COLUMN_NAME'],
                [
                    __('Forget', 'gdprcompliance'),
                    __('Keep', 'gdprcompliance')
                ], [
                    'value' => array_key_exists($value['COLUMN_NAME'], $saved)
                        ? $saved[$value['COLUMN_NAME']]['value'] : 0,
                    'display' => false
                ]);
         }
         $values[] = $newValue;
      }
      renderTwigTemplate('table.twig', [
         'minimal' => true,
         'fields' => $fields,
         'values' => $values
      ]);

      $this->showFormButtons($options);

      return true;
   }

   /**
    * updateConfig
    *
    * @param  mixed $idConfig
    * @param  mixed $post
    * @return void
    */
   public function updateConfig($idConfig, $post) {
      global $DB;

      if (array_key_exists('active', $post) && array_key_exists('mode', $post)) {
         $active = $post['active'];
         $mode = $post['mode'];

         $query = "UPDATE glpi_plugin_gdprcompliance_configs SET active = $active, mode = $mode WHERE id = $idConfig";
      } else {
         $changes = [];

         foreach ($post as $key => $value) {
            if (in_array($key, $this->allowFields) && $value == 2) {
               $changes[$key] = addslashes($post['change_'.$key]);
            } elseif (in_array($key, $this->allowFields) && $value == 1) {
               $changes[$key] = 1;
            } elseif(in_array($key, $this->allowFields) && $value == 0) {
               $changes[$key] = 999;
            }
         }

         $changes = json_encode($changes);

         $query = "UPDATE glpi_plugin_gdprcompliance_configs SET `change` = '$changes' WHERE id = $idConfig";
      }

      $DB->query($query);
   }

   /**
    * getState
    *
    * @return void
    */
   private function getState() {
      $allState = [];
      $state = new State();
      $states = $state->find();

      foreach($states as $list) {
         $allState[$list['id']] = $list['name'];
      }

      return $allState;
   }

   /**
    * getSearchOptions
    *
    * @return void
    */
   public function getSearchOptions() {
      $tab = array();

      return $tab;
   }

   /**
    * install
    *
    * @param  mixed $mig
    * @return void
    */
   public function install(Migration $mig) {
      return true;
   }

   /**
    * uninstall
    *
    * @return void
    */
   public function uninstall() {
      return true;
   }

   /**
    * translateField
    *
    * @param  mixed $value
    * @return void
    */
   static function translateField($value)
   {
      $translation = [
         'name'                           => __('Login'),
         'password_last_update'           => __('Last date of password update', 'gdprcompliance'),
         'phone'                          => __('Phone'),
         'phone2'                         => __('Phone 2'),
         'mobile'                         => __('Mobile phone'),
         'realname'                       => __('Surname'),
         'firstname'                      => __('First name'),
         'locations_id'                   => __('Location'),
         'comment'                        => __('Comment'),
         'authtype'                       => __('Authentication type'),
         'last_login'                     => __('Last login'),
         'date_mod'                       => __('Last update'),
         'date_sync'                      => __('Last synchronization'),
         'usertitles_id'                  => __('Title'),
         'usercategories_id'              => __('Category'),
         'password_forget_token_date'     => __('Last date of forget password token generation', 'gdprcompliance'),
         'user_dn'                        => __('User DN', 'gdprcompliance'),
         'registration_number'            => __('Administrative number'),
         'personal_token'                 => __('Personal token'),
         'personal_token_date'            => __('Last date of personal token generation', 'gdprcompliance'),
         'api_token_date'                 => __('Last date of API token generation', 'gdprcompliance'),
         'api_token'                      => __('API token'),
         'cookie_token_date'              => __('Last date of cookie token generation', 'gdprcompliance'),
         'picture'                        => __('Picture'),
         'begin_date'                     => __('Valid since'),
         'end_date'                       => __('Valid until'),
         'date_creation'                  => __('Creation date'),
         'users_id_supervisor'            => __('Responsible'),
         'timezone'                       => __('Timezone'),
         'email'                          => __('Email')
      ];

      return array_key_exists($value, $translation) ? $translation[$value] : $value;
   }
}
