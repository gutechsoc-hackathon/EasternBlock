<?php
    class TestController extends Controller
    {
        protected static $access = array (
            'default' => array ('admin'),
        );
        protected $defaultAction = 'form';
        
        /**
         * before everything else
         */
        protected function _beforeAction ()
        {
        
        }
        
        /**
         * testing forms
         */
        public function formAction ()
        {
            $this->render ('test/form');
        }
    }
?>