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

class PluginGdprcomplianceAction extends CommonDBTM
{
       /**
    * @param int $nb
    *
    * @return translated
    */
    static function getTypeName($nb = 0) {
        return __('ITSM GDPR', 'gdprcompliance');
    }
    

   /**
    * Install gestiondesstocks notifications.
    *
    * @return array 'success' => true on success
    */
    static function install($migration) {
        global $DB;

        return ['success' => true];
    }


    /**
    * Give cron information
    *
    * @param $name : automatic action's name
    *
    * @return arrray of information
   **/
   static function cronInfo($name) {

    switch ($name) {
       case 'CleanUsers' :
          return array('description' => __('Clean inactive Users', 'gdprcompliance'));
    }
    return [];
 }

    static function cronCleanUsers($task){
        global $DB;

        $cronTask = new CronTask();
        if ($cronTask->getFromDBbyName("PluginGdprcomplianceAction", "CleanUsers")) {
           if ($cronTask->fields["state"] == CronTask::STATE_DISABLE) {
              return 0;
           }
        } else {
           return 0;
        }
        $cron_status = 0;

        $query = "SELECT * FROM glpi_plugin_gdprcompliance_configs";
        $result_glpi = $DB->query($query);
        $config = [];
        if ($DB->numrows($result_glpi) > 0) {
           $i = 0;
           while ($data = $DB->fetchArray($result_glpi)) {
              $config['active'] = $data['active'];
              $config['mode'] = $data['mode'];
              if (!is_null($data['change'])) {
                 $changes = explode(',', $data['change']);
              }
              foreach ($changes as $key => $change) {
                 if ($change == "") {
                    continue;
                 }
                 $test = explode(';', $change);
                 if (count($test) > 1) {
                    $config['changes'][$test[0]]['value'] = 2;
                    $config['changes'][$test[0]]['change'] = $test[1];
                 } else {
                    $config['changes'][$change]['value'] = 1;
                    $config['changes'][$change]['change'] = "";
                 }
              }
              $i++;
           }
        }

        $listUsers = [];

        if ($config['active']) {

            $query = "SELECT * FROM glpi_users";
            $users_result = $DB->query($query);

            while ($user = $DB->fetchArray($users_result)) {
                if (!$user['is_active'] && !$user['is_deleted']) {
                    $listUsers[] = self::changeUserData($user, $config['changes']);
                }
            }
        }
        $sucess = 0;
        $errors = 0;

        foreach ($listUsers as $key => $user) {
            try {
                if (!$config['mode']) {
                    self::cleanUsers($user);
                } else {
                    self::removeUsers($user);
                }
                $sucess += 1;
            } catch(Exception $e){
                $errors += 1;
            }
        }

        $query = "INSERT INTO glpi_plugin_gdprcompliance_history (`date`, sucess, error) VALUES (now(), $sucess, $errors)";
        $DB->query($query);

        return $cron_status;
    }

    static private function changeUserData($user, $changes = []){
        $exclude = ['id', 'name' => 1, 'locations_id' => 1,'use_mode' => 1,'is_active' => 1,'auths_id' => 1,'authtype' => 1,'is_deleted' => 1,'profiles_id' => 1,'entities_id' => 1,'usertitles_id' => 1,'usercategories_id' => 1,'is_deleted_ldap' => 1,'groups_id' => 1,'users_id_supervisor' => 1,'password' => 1];
        foreach ($user as $key => $value) {

            if (is_int($key)) {
                unset($user[$key]);
                continue;
            }

            if (array_key_exists($key, $changes)) {
                if ($changes[$key]['value'] == 1) {
                    # code...
                } elseif ($changes[$key]['value'] == 2) {
                    $user[$key] = $changes[$key]['change'];
                }
            } else {
                if (in_array($key, $exclude)) {
                    if ($key != 'id') {
                        unset($user[$key]);
                    }
                } else {
                    $user[$key] = "";
                }
            }
        }

        return $user;

    }

    static private function cleanUsers($user){
        global $DB;

        $nUser = new User();
        $user['is_deleted'] = true;

        $nUser->update($user);
    }

    static private function removeUsers($user){
        global $DB;
        
        $query = "DELETE FROM glpi_users WHERE id = " . $user['id'];

        $DB->query($query);
    }
}