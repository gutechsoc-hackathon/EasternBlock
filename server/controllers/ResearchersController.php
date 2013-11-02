<?php
    class ResearchersController extends Controller
    {
        protected static $access = array (
            'default' => array ('guest'),
        );
        
        /**
         * before everything else
         */
        protected function _beforeAction ()
        {
            // categories will be used a lot
            System::registerConfig ('categories', Category::find ());
        }
        
        /**
         * index page is basically a html page.
         */
        public function indexAction ()
        {
            $this->render ('researchers/index');
        }
        
        /**
         * person's personal information
         */
        public function personAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $person = Employer::findByPk ($id);
            if (!$person)
                throw new GameError ('Person not found');
            $this->render ('researchers/person', array ('person' => $person));
        }
        
        /**
         * available_people
         */
        public function availableAction ()
        {
            $this->render ('researchers/available_people');
        }
    }
?>