<?php
    /**
     * Class, that supports multiple languages
     */
    class T
    {
        // here we will store language files
        private static $_lang = array ();
        // here we will store opened language files
        private static $_files = array ();
        
        /**
         * initialisation, loading default languages
         */
        public static function init ()
        {
            // by default we will load lang/default.php
            T::$_lang = array_merge (T::$_lang, include BASE_DIR.'lang/'.System::getConfig ('lang').'/default.php');
            T::$_files['default'] = true;
        }
        
        /**
         * loads language file from current controller
         */
        private static function loadCurrent ()
        {
            // getting name of the current controller
            $name = System::getControllerName ();
            // if such controller is already included, then we have a key error
            if (isset (T::$_files[$name]) || !file_exists (BASE_DIR.'lang/'.System::getConfig ('lang').'/'.$name.'.php'))
                return false;
            T::$_lang = array_merge (T::$_lang, include BASE_DIR.'lang/'.System::getConfig ('lang').'/'.$name.'.php');
            T::$_files[$name] = true;
            return true;
        }
        
        /**
         * getting text
         */
        public static function get ($key)
        {
            // if there is no such language text
            if (!isset (T::$_lang[$key]))
                // if we can't load new file or after loading it we still can't quite get the entry
                if (!T::loadCurrent () || !isset (T::$_lang[$key]))
                    // it is an error
                    //throw new Exception ('language entry '.$key.' not found! '.BASE_DIR.'lang/'.System::getConfig ('lang').'/'.System::getControllerName ().'.php');
                    return $key;
            // returning the text
            return T::$_lang[$key];
        }
    }
?>