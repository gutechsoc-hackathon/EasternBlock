<?php
    class Game
    {
        // true instances
        public static $dataPk = array ();
        
        //------------------------- logic ----------------------------------------------------------
        public static function runLogic ()
        {
            // dealing with login/logout in menu
            // unauthorised users
            $menu = System::getConfig ('top_menu');
            if (System::$user->login == 'guest')
            {
                $menu['login'] = array (
                        'href' => array ('#'),
                        'label' => 'Log in',
                        'title' => 'Log in',
                        'onclick' => "this.href = '#'; popup({r: 'user/login'}); ",
                );
                unset ($menu['dashboard']);
            }
            else 
                $menu['logout'] = array (
                        'href' => array ('#'),
                        'label' => 'Log out',
                        'title' => 'Log out',
                        'onclick' => "this.href = '#'; window.location.replace ('".Html::getUrl ('user/logout')."')",
                );
            System::registerConfig ('top_menu', $menu);
        }
    }
?>