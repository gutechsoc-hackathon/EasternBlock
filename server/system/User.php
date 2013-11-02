<?php
    class User extends ActiveRecord
    {
        // table for this class
        protected static $_table = 'users';
        // relations with other tables
        protected static $_relations = array (
            'questions' => array (HAS_MANY, 'Question', 'user_id'),
            'events' => array (HAS_MANY, 'Event', 'user_id'),
            'media' => array (HAS_MANY, 'Media', 'user_id'),
            'session' => array (HAS_MUTABLE_ONE, 'Session', 'user_id'),
        );
        
        /**
         * function, which performs authentication for all cases (guest, login, restore)
         */
        public static function authenticate ($email = null, $pass = null)
        {
            if (System::$user && System::$user->checkAccess ('user'))
                return System::$user;
            else if ($email !== null && $pass !== null)
            {
                // email
                $email = Validators::getEmailFormat ($email);
                $pass = Validators::getPasswordFormat ($pass);
                $u = User::find ("email = '".$email."' AND pass = MD5('".$pass."');");
                // we have maximum one match, or no at all, so if we have one, we return it
                // otherwise we will have guest
                if (isset ($u[0]))
                {
                    $sess = new Session();
                    $sess->sess_id = md5 (microtime ());
                    $sess->user_id = $u[0]->getPk ();
                    $sess->save();
                    return $u[0];
                }
                else
                    throw new GameError ('Bad username or password');
            }
            else if ($email != null)
            {
                // in this case, it is actually session id
                // so, restore
                $sess_id = Validators::getLoginFormat ($email);
                $sess = Session::find("sess_id = '".$sess_id."'");
                if (!empty ($sess))
                    return User::findByPk ($sess[0]->user_id);
            }
            
            // else guest
            return User::getGuestUser ();
        }
        
        /**
         * returns default guest user
         */
        public static function getGuestUser ()
        {
            $guest = new User (array ('id' => 0, 'email' => 'guest@guest.ua', 'roles' => 'guest'));
            $guest->makeTemporary ();
            return $guest;
        }
        
        /**
         * cheks access to a specified role
         */
        public function checkAccess ($role)
        {
            $rbac = System::getConfig ('RBAC');
            $roles = explode ('|', $this->roles);
            $c = count ($roles);
            for ($i = 0; $i < $c; $i++)
            {
                if ($roles[$i] == $role)
                    return true;
                if (User::searchRBACTree ($rbac, $roles[$i], $role))
                    return true;
            }
            
            // if nothing is found
            return false;
        }
        
        /**
         * depth first search on a rbac tree for a given role
         */
        public static function searchRBACTree ($rbac, $parent, $role)
        {
            $c = count ($rbac[$parent]['children']);
            for ($i = 0; $i < $c; $i++)
            {
                if ($rbac[$parent]['children'][$i] == $role)
                    return true;
                if (User::searchRBACTree ($rbac, $rbac[$parent]['children'][$i], $role))
                    return true;
            }
            
            // if nothing is found
            return false;
        }
    }
?>
