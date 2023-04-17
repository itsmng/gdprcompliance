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
 * Class PluginOcsinventoryngConfig
 */
class PluginGdprcomplianceConfig extends CommonDBTM {

   /**
    * @var string
    */
   static $rightname = "plugin_gdprcompliance_config";

   /**
    * @param int $nb
    *
    * @return string|translated
    */
   static function getTypeName($nb = 0) {
      return __("GDPR Configuration", 'gdprcompliance');
   }
   
   static function getMenuContent() {
     
       $menu = parent::getMenuContent();
       //Menu entry in config
       $menu['title'] = self::getTypeName(2);
       $menu['page'] = "/plugins/gdprcompliance/front/config.form.php";
       $menu['links']['search'] = "/plugins/gdprcompliance/front/config.form.php";
       $menu['icon'] = 'fas fa-shield-alt';

       return $menu;
   }

   function showMenu(){
      global $DB, $CFG_GLPI;

      $user = new User();
      $assos = $user->rawSearchOptions();

      echo "<div class='center'>";
      if (Session::haveRight("plugin_gdprcompliance_config", READ) || 
          Session::haveRight("plugin_gdprcompliance_config", UPDATE) ||
          Session::haveRight("plugin_gdprcompliance_history", READ)) {

         echo "<table class='tab_cadre'>";
         echo "<tr>";
         echo "<th>" . __("GDPR Plugin", 'gdprcompliance') . "</th>";
         echo "</tr>";
         if (Session::haveRight("plugin_gdprcompliance_config", READ) || Session::haveRight("plugin_gdprcompliance_config", UPDATE)) {
            echo "<tr>";
            echo "<td><a href='./config.form.php?config=1'>" . __("Configuration", 'gdprcompliance') . "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td><a href='./config.form.php?configdata=1'>" . __("Data configuration", 'gdprcompliance') . "</td>";
            echo "</tr>";
         }
         if (Session::haveRight("plugin_gdprcompliance_history", READ)) {
            echo "<tr>";
            echo "<td><a href='./history.form.php'>" . __("History", 'gdprcompliance') . "</td>";
            echo "</tr>";
         }
         echo "</table>";

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
         $i = 0;
         while ($data = $DB->fetchArray($result_glpi)) {
            if (!is_null($data['change'])) {
               $changes = explode(',', $data['change']);
            } else {
               $changes = [];
            }
            foreach ($changes as $key => $change) {
               if ($change == "") {
                  continue;
               }
               $test = explode(';', $change);
               if (count($test) > 1) {
                  $saved[$test[0]]['value'] = 2;
                  $saved[$test[0]]['change'] = $test[1];
               } else {
                  $saved[$change]['value'] = 1;
                  $saved[$change]['change'] = "";
               }
            }
            $saved['active'] = $data['active'];
            $saved['mode'] = $data['mode'];
            $i++;
         }
      }

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      echo "<td> " . __('Active automitic action', 'gdprcompliance') . " </td><td>";
      Dropdown::showYesNo("active", $saved['active']);

      echo "<tr class='tab_bg_1'>";
      echo "<td> " . __('Mode', 'gdprcompliance') . " </td><td>";
      Dropdown::showFromArray('mode', $mode, ['value' => $saved['mode']]);
      echo "</td>";
      echo "</tr>";
      echo "<tr><td><span>" . __("Warning, if the mode is on remove, there is a risk of data loss !", 'gdprcompliance') . "</span></td></tr>";

      $this->showFormButtons($options);

      return true;
   }

   function showConfigData($ID, $options = []){
      global $DB;

      $user = new User();
      $assos = $user->rawSearchOptions();
      $assoc = [];

      foreach ($assos as $key => $value) {
         if (array_key_exists('field', $value) && array_key_exists('name', $value)) {
            $assoc[$value['field']] = $value['name'];
         }
      }

      //var_dump("<pre>", $assoc , "</pre>");

      $query = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`=Database() AND `TABLE_NAME`='" . 'glpi_users' . "'";
      $result_glpi = $DB->query($query);
      
      $snmpLinks = [];
      
      $exclud = ['id', 'entities_id', 'password', 'is_global', 'is_template', 'groups_id', 'users_id', 'is_dynamic', 'is_active', 'is_deleted', 'password_forget_token'];
      
      if ($DB->numrows($result_glpi) > 0) {
         $i = 0;
         while ($data = $DB->fetchArray($result_glpi)) {
            if (!in_array($data['COLUMN_NAME'], $exclud)) {
               $snmpLinks[$data['COLUMN_NAME']] = $data['COLUMN_NAME'];
               $i++;
            }
         }
      }

      $query = "SELECT * FROM glpi_plugin_gdprcompliance_configs";
      $result_glpi = $DB->query($query);
      $saved = [];
      if ($DB->numrows($result_glpi) > 0) {
         $i = 0;
         while ($data = $DB->fetchArray($result_glpi)) {
            if (!is_null($data['change'])) {
               $changes = explode(',', $data['change']);
            } else {
               $changes = [];
            }
            foreach ($changes as $key => $change) {
               if ($change == "") {
                  continue;
               }
               $test = explode(';', $change);
               if (count($test) > 1) {
                  $saved[$test[0]]['value'] = 2;
                  $saved[$test[0]]['change'] = $test[1];
               } else {
                  $saved[$change]['value'] = 1;
                  $saved[$change]['change'] = "";
               }
            }
            $saved['active'] = $data['active'];
            $saved['mode'] = $data['mode'];
            $i++;
         }
      }


      $this->initForm($ID, $options);
		$state = $this->getState();
      $this->showFormHeader(['formtitle' => false]);

      echo "<tr>";
      echo "<th>" . __("Data", 'gdprcompliance') . "</th>";
      echo "<th>" . __("Action", 'gdprcompliance') . "</th>";
      echo "<th>" . __("Replace", 'gdprcompliance') . "</th>";
      echo "</tr>";

      foreach ($snmpLinks as $key => $value) {
         echo "<tr class='tab_bg_1'>";
         echo "<td>";
         //echo array_key_exists($value, $assoc) ? $assoc[$value] : $value;
         echo self::translateField($value);
         echo "</td>";
         echo "<td>";
         Dropdown::showFromArray($value, [__('Forgot', 'gdprcompliance'), __('Keep', 'gdprcompliance'), __('Change', 'gdprcompliance')], ['value' => array_key_exists($value, $saved) ? $saved[$value]['value'] : 0]);
         echo "</td>";
         echo "<td>";
         if (array_key_exists($value, $saved)) {
            echo "<input id='change' class='$value' name='change_" . $value . "' value='" . $saved[$value]['change'] . "'>";
         } else {
            echo "<input id='change' class='$value' name='change_" . $value . "'>";
         }
         echo "</td>";
         echo "</tr>";
      }

      $this->showFormButtons($options);

      return true;
   }

   public function updateConfig($idConfig, $post) {
      global $DB;
      

      if (array_key_exists('active', $post) && array_key_exists('mode', $post)) {

         $active = $post['active'];
         $mode = $post['mode'];

         $query = "UPDATE glpi_plugin_gdprcompliance_configs SET active = $active, mode = $mode WHERE id = $idConfig";
      } else {
         $changes = "";
         foreach ($post as $key => $value) {
            if (substr( $key, 0, 7 ) === "change_") {
               continue;
            }
            if ($value == 2) {
               $changes .= $key . ';' . $post['change_' . $key] . ',';
            } elseif ($value == 1) {
               $changes .= $key . ',';
            }
         }
         $query = "UPDATE glpi_plugin_gdprcompliance_configs SET `change` = '$changes' WHERE id = $idConfig";
      }

      $DB->query($query);
   }

   private function getState() {
     $allState = [];
     $state = new State();
     $states = $state->find();
     foreach($states as $list) {
        $allState[$list['id']] = $list['name'];
     }

     return $allState;
   }

   public function getSearchOptions() {
     $tab = array();
     
     return $tab;
   }

   public function install(Migration $mig) { 	
        return true;
  }

   public function uninstall() {
     return true;
   }

   static function translateField($value)
   {
      $translation = [
         'name'                           => __('Login', 'gdprcompliance'),
         'password_last_update'           => __('Password last update', 'gdprcompliance'),
         'phone'                          => __('Phone', 'gdprcompliance'),
         'phone2'                         => __('Phone 2', 'gdprcompliance'),
         'mobile'                         => __('Mobile', 'gdprcompliance'),
         'realname'                       => __('Lastname', 'gdprcompliance'),
         'firstname'                      => __('Firstname', 'gdprcompliance'),
         'locations_id'                   => __('Locations', 'gdprcompliance'),
         'language'                       => __('Language', 'gdprcompliance'),
         'use_mode'                       => __('Use mode', 'gdprcompliance'),
         'list_limit'                     => __('List limit', 'gdprcompliance'),
         'comment'                        => __('Comment', 'gdprcompliance'),
         'auths_id'                       => __('Auth', 'gdprcompliance'),
         'authtype'                       => __('Auth type', 'gdprcompliance'),
         'last_login'                     => __('Last login', 'gdprcompliance'),
         'date_mod'                       => __('Date modification', 'gdprcompliance'),
         'date_sync'                      => __('Date synchronisation', 'gdprcompliance'),
         'profiles_id'                    => __('Profiles', 'gdprcompliance'),
         'usertitles_id'                  => __('user titles', 'gdprcompliance'),
         'usercategories_id'              => __('User categories', 'gdprcompliance'),
         'date_format'                    => __('Date format', 'gdprcompliance'),
         'number_format'                  => __('Number format', 'gdprcompliance'),
         'names_format'                   => __('Name format', 'gdprcompliance'),
         'csv_delimiter'                  => __('csv delimiter', 'gdprcompliance'),
         'is_ids_visible'                 => __('ids is visible', 'gdprcompliance'),
         'use_flat_dropdowntree'          => __('Use flat dropdowntree', 'gdprcompliance'),
         'show_jobs_at_login'             => __('Show jobs at login', 'gdprcompliance'),
         'priority_1'                     => __('Priority 1', 'gdprcompliance'),
         'priority_2'                     => __('Priority 2', 'gdprcompliance'),
         'priority_3'                     => __('Priority 3', 'gdprcompliance'),
         'priority_4'                     => __('Priority 4', 'gdprcompliance'),
         'priority_5'                     => __('Priority 5', 'gdprcompliance'),
         'priority_6'                     => __('Priority 6', 'gdprcompliance'),
         'followup_private'               => __('Followup private', 'gdprcompliance'),
         'task_private'                   => __('Task private', 'gdprcompliance'),
         'default_requesttypes_id'        => __('Default request types', 'gdprcompliance'),
         'password_forget_token_date'     => __('password forget token date', 'gdprcompliance'),
         'user_dn'                        => __('User DN', 'gdprcompliance'),
         'registration_number'            => __('Registration number', 'gdprcompliance'),
         'show_count_on_tabs'             => __('Show count on tabs', 'gdprcompliance'),
         'refresh_views'                  => __('Refresh views', 'gdprcompliance'),
         'set_default_tech'               => __('Set default tech', 'gdprcompliance'),
         'personal_token_date'            => __('Personal token date', 'gdprcompliance'),
         'api_token_date'                 => __('API token date', 'gdprcompliance'),
         'cookie_token_date'              => __('Cookie token Date', 'gdprcompliance'),
         'display_count_on_home'          => __('Display count on Home', 'gdprcompliance'),
         'notification_to_myself'         => __('Notification to myself', 'gdprcompliance'),
         'duedateok_color'                => __('Due date OK color', 'gdprcompliance'),
         'duedatewarning_color'           => __('Due date Warning color', 'gdprcompliance'),
         'duedatecritical_color'          => __('Due date Critical color', 'gdprcompliance'),
         'duedatewarning_less'            => __('Due date warning less', 'gdprcompliance'),
         'duedatecritical_less'           => __('Due date critical less', 'gdprcompliance'),
         'duedatewarning_unit'            => __('Due date warning Unit', 'gdprcompliance'),
         'duedatecritical_unit'           => __('Due date critical Unit', 'gdprcompliance'),
         'display_options'                => __('Display options', 'gdprcompliance'),
         'is_deleted_ldap'                => __('Is deleted LDAP', 'gdprcompliance'),
         'pdffont'                        => __('PDF font', 'gdprcompliance'),
         'picture'                        => __('Picture', 'gdprcompliance'),
         'begin_date'                     => __('Begin date', 'gdprcompliance'),
         'end_date'                       => __('End Date', 'gdprcompliance'),
         'keep_devices_when_purging_item' => __('Keep devices when purging item', 'gdprcompliance'),
         'privatebookmarkorder'           => __('Private bookmark order', 'gdprcompliance'),
         'backcreated'                    => __('back created', 'gdprcompliance'),
         'task_state'                     => __('task state', 'gdprcompliance'),
         'layout'                         => __('layout', 'gdprcompliance'),
         'palette'                        => __('palette', 'gdprcompliance'),
         'set_default_requester'          => __('set default requester', 'gdprcompliance'),
         'lock_autolock_mode'             => __('lock autolock mode', 'gdprcompliance'),
         'lock_directunlock_notification' => __('lock direct unlock notification', 'gdprcompliance'),
         'date_creation'                  => __('date creation', 'gdprcompliance'),
         'highcontrast_css'               => __('high contrast CSS', 'gdprcompliance'),
         'plannings'                      => __('plannings', 'gdprcompliance'),
         'sync_field'                     => __('synchronisation field', 'gdprcompliance'),
         'users_id_supervisor'            => __('supervivor', 'gdprcompliance'),
         'timezone'                       => __('Timezone', 'gdprcompliance'),
         'default_dashboard_central'      => __('Default dashboard central', 'gdprcompliance'),
         'default_dashboard_assets'       => __('Default dashboard assets', 'gdprcompliance'),
         'default_dashboard_helpdesk'     => __('Default dashboard helpdesk', 'gdprcompliance'),
         'default_dashboard_mini_ticket'  => __('Default dashboard mini ticket', 'gdprcompliance'),
         'access_zoom_level'              => __('access zoom level', 'gdprcompliance'),
         'access_font'                    => __('Access font', 'gdprcompliance'),
         'access_shortcuts'               => __('Access shortcuts', 'gdprcompliance'),
         'access_custom_shortcuts'        => __('Accesss custom shortcut', 'gdprcompliance'),
      ];

      return array_key_exists($value, $translation) ? $translation[$value] : $value;
   }
}
