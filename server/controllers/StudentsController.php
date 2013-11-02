<?php
    class StudentsController extends Controller
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
         * index - a list of all students and short stats
         */
        public function indexAction ()
        {
            $this->render ('students/index');
        }
        
        /**
         * person's personal information
         */
        public function personAction ()
        {
            // getting it's id
            $id = Validators::getNum (System::$url[2]);
            $student = Freelancer::findByPk ($id);
            if (!$student)
                throw new GameError ('Person not found');
            $this->render ('students/person', array ('student' => $student));
        }
        
        /**
         * available_people
         */
        public function availableAction ()
        {
            $this->render ('students/available_people');
        }
    }
?>