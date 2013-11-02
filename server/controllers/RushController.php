<?php
    class RushController extends Controller
    {
        protected static $access = array (
            'default' => array ('guest'),
        );
        protected $defaultAction = 'available';
        
        /**
         * before everything else
         */
        protected function _beforeAction ()
        {
            // categories will be used a lot
            System::registerConfig ('categories', Category::find ());
            System::registerConfig ('submenu', array (
                'available_people' => array (
                    'href' => array ('rush', 'available'),
                    'title' => 'available_people',
                    'label' => 'available_people',
                ),
                'hot_jobs' => array (
                    'href' => array ('rush', 'hot_jobs'),
                    'title' => 'hot_jobs',
                    'label' => 'hot_jobs',
                ),
            ));
        }
        
        /**
         * available_people
         */
        public function availableAction ()
        {
            $this->render ('rush/available_people');
        }
        
        /**
         * hot jobs
         */
        public function hot_jobsAction ()
        {
            $this->render ('rush/hot_jobs');
        }
    }
?>