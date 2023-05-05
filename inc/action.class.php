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
    
    /**
     * cronCleanUsers
     *
     * @param  mixed $task
     * @return void
     */
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
            while ($data = $DB->fetchArray($result_glpi)) {
                $config['active'] = $data['active'];
                $config['mode'] = $data['mode'];

                $changes = [];

                if (!is_null($data['change'])) $changes = json_decode($data['change']);

                foreach ($changes as $key => $change) {
                    if($change == 999) {
                        $config['changes'][$key]['value'] = 999;
                    } elseif($change == 1) {
                        $config['changes'][$key]['value'] = 1;
                    } else {
                        $config['changes'][$key]['value'] = 2;
                        $config['changes'][$key]['change'] = $change;
                    }
                }
            }
        }

        $listUsers = [];

        if ($config['active']) {
            $User = new User();

            $users = $User->find(["is_active" => 0, "is_deleted" => 0]);

            foreach($users as $id => $values) {
                $listUsers[] = self::changeUserData($values, $config['changes']);
            }
        }

        $sucess = 0;
        $errors = 0;

        foreach ($listUsers as $key => $user) {
            try {
                if (!$config['mode']) {
                    self::cleanUsers($user["user"]);
                    self::cleanUserEmail($user["mail"]);
                } else {
                    self::removeUsers($user["user"]);
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
    
    /**
     * changeUserData
     *
     * @param  mixed $user
     * @param  mixed $changes
     * @return void
     */
    static private function changeUserData($user, $changes = []) {
        $usersToUpdate['user'] = $user;
        $usersToUpdate['mail'] = [];

        foreach ($user as $key => $value) {
            if (array_key_exists($key, $changes)) {
                if ($changes[$key]['value'] == 2) {
                    $usersToUpdate['user'][$key] = $changes[$key]['change'];
                } elseif($changes[$key]['value'] == 999) {
                    $usersToUpdate['user'][$key] = null;
                }
            }
        }

        // User email process
        if($changes["email"]["value"] == 2) {
            $usersToUpdate["mail"]["update"] = [
                "users_id"  => $user["id"],
                "email"     => $changes["email"]["change"]
            ];
        } elseif($changes["email"]["value"] == 999) {
            $usersToUpdate["mail"]["delete"] = [
                "users_id"  => $user["id"]
            ];
        }

        return $usersToUpdate;
    }
    
    /**
     * cleanUsers
     *
     * @param  mixed $user
     * @return void
     */
    static private function cleanUsers($user){
        $nUser = new User();
        $user['is_deleted'] = true;

        $nUser->update($user);
    }
    
    /**
     * cleanUserEmail
     *
     * @param  mixed $userEmail
     * @return void
     */
    static function cleanUserEmail($userEmail) {
        $UserEmail = new UserEmail();

        if(isset($userEmail["update"])) {
            $userEmails = $UserEmail->find(["users_id" => $userEmail["update"]["users_id"]]);
            // increment and concat to email for field unicity
            $index = 0;

            foreach($userEmails as $id => $mail) {
                $mail["email"] = $userEmail["update"]["email"].$index;
                $UserEmail->update($mail);
                $index++;
            }
        } elseif($userEmail["delete"]) {
            $userEmails = $UserEmail->find(["users_id" => $userEmail["delete"]["users_id"]]);
            foreach($userEmails as $id => $mail) {
                $mail["email"] = $userEmail["update"]["email"];
                $UserEmail->delete($mail);
            }
        }
    }
    
    /**
     * removeUsers
     *
     * @param  mixed $user
     * @return void
     */
    static private function removeUsers($user){
        $nUser = new User();
        $userToPurge = [
            "id" => $user["id"]
        ];

        $nUser->delete($userToPurge, 1);
    }
}