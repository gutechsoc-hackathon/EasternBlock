<?php
    class BuilderController extends Controller
    {
        protected static $access = array (
            'default' => array ('admin'),
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
         * site tree builder
         */
        public function indexAction ()
        {
            $this->render ('builder/index');
        }
        
        /**
         * a form for adding a page
         */
        public function new_itemAction ()
        {
            $this->render ('builder/new_item');
        }
    }
?>