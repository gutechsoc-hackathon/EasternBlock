<?php
    /**
     * a class, containing system information
     * such as configurations and databases
     * it also takes care of issuing right controller
     */
    class System
    {
        public static $user;
        public static $url;
        private static $_config;
        private static $_dbcnx;
        private static $_controller = 'default';
    
        /**
         * obviously, system initialisation
         */
        public static function init ()
        {
            // all errors will be send there
            set_exception_handler ('registerError');
            // while developing, keeping tracj of times
            Profiler::init ();
            
            // creating guest just in case
            System::$user = User::getGuestUser ();
            
            // loading configuration
            System::loadConfig ();
            // loading language files
            T::init ();
            // making conncetions
            //Profiler::trace ('start');
            System::dbConnect ();
            //Profiler::trace ('mysql');
            
            // starting/resuming session
            session_start ();
            // checking, whether session expired
            if (isset ($_SESSION['lastAction'])
                    && $_SESSION['lastAction'] < $_SERVER['REQUEST_TIME'] - System::$_config['session']['duration'])
            {
                $tmp = User::authenticate ();
                session_destroy ();
                session_start ();
            }
            $_SESSION['lastAction'] = $_SERVER['REQUEST_TIME'];
            
            // creating guest or restoring session
            //System::$user = User::authenticate ('petya', '123');
            System::$user = User::authenticate ();
            //Profiler::trace ('auth');
            
            // running game logic
            Game::runLogic ();
            
            //Profiler::trace ('before controller');
            // running the controller, that should have been provided in the get string
            if (!isset ($_GET['r']) || !$_GET['r'])
                $_GET['r'] = System::$_config['route']['default'];
            System::runController ($_GET['r']);
            //Profiler::trace ('controller');
        }
        
        /**
         * function, that runs a specified controller
         */
        public static function runController ($route)
        {
            $route = explode ('/', preg_replace ('#[^a-z0-9_/]#', '', $route));
            System::$url = $route;
            
            $route[0][0] = strtoupper ($route[0][0]);
            $route[0] .= 'Controller';
            System::$_controller = $route[0];
            
            if (!isset ($route[1]))
                $route[1] = '';
            
            $controller = new $route[0] ($route[1]);
        }
        
        /**
         * returns controller we are currently in (e.g. for including language file)
         */
        public static function getControllerName ()
        {
            return System::$_controller;
        }
        
        /**
         * function, that loads configuration files
         */
        private static function loadConfig ()
        {
            System::$_config = include ('config/config.php');
            foreach (System::$_config['configs'] as $config)
                System::$_config['params'] = array_merge (System::$_config['params'], include 'config/'.$config.'.php');
        }
        
        // getting configuration element
        public static function getConfig ($conf)
        {
            return isset (System::$_config[$conf]) ? System::$_config[$conf] : (isset (System::$_config['params'][$conf]) ? System::$_config['params'][$conf] : null);
        }
        
        /**
         * registers new configuration element (can be used for globalising arrays)
         */
        public static function registerConfig ($conf, $value)
        {
            if (isset (System::$_config[$conf]))
                System::$_config[$conf] = $value;
            else
                System::$_config['params'][$conf] = $value;
        }
        
        // a function for connection to a MySQL database
        private static function dbConnect ()
        {
            System::$_dbcnx = new mysqli (System::$_config['db']['host'], System::$_config['db']['user'], System::$_config['db']['pass'], System::$_config['db']['dbname']);
            if (System::$_dbcnx->connect_error)
                throw new Exception ('<P>Could not connect to mysql... '.System::$_dbcnx->connect_error.'</P>');
            // setting right transaction parameters
            System::doMysql("SET character_set_client='utf8'");
            System::doMysql("SET character_set_results='utf8'");
            System::doMysql("SET collation_connection='utf8_general_ci'");
        }
        
        // a wrapper function for calling single mysql queries
        public static function doMysql ($query)
        {
            $q = System::$_dbcnx->query ($query);
            if (!$q)
                //echo (System::$_dbcnx->error.'<br/> the query: '.$query);
                throw new Exception (System::$_dbcnx->error.'<br/> the query: '.$query);
            return $q;
        }
        
        /**
         * returns id of the last row, inserted into the database
         */
        public static function lastInsertId ()
        {
            return System::$_dbcnx->insert_id;
        }
        
        public static function mysqlNumRows ($result)
        {
            return $result->num_rows;
        }
        
        public static function mysqlResultAssoc ($result)
        {
            return $result->fetch_assoc ();
        }
        
        public static function mysqlRealEscapeString ($str)
        {
            return System::$_dbcnx->real_escape_string ($str);
        }
    }
?>