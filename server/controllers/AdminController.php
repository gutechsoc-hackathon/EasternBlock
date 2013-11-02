<?php
    class AdminController extends Controller
    {
        protected static $access = array (
            'default' => array ('guest'),
        );
        
        /**
         * before everything else
         */
        protected function _beforeAction ()
        {
            // setting admin panel theme
            System::registerConfig ('theme', 'admin');
        }
        
        /**
         * boards
         */
        public function indexAction ()
        {
            $this->render ('admin/index');
        }
    }
?>