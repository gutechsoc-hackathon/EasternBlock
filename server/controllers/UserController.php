<?php
    class UserController extends Controller
    {
        protected static $access = array (
            'default' => array ('user'),
            'login' => array ('guest'),
        );
        protected $defaultAction = 'dashboard';
        
        /**
         * index page is basically a html page.
         */
        public function indexAction ()
        {
            $this->render ('user/index');
        }
        
        /**
         * dashboard
         */
        public function dashboardAction ()
        {
            $this->render ('user/dashboard');
        }
        
        /**
         * log in
         */
        public function loginAction ()
        {
            if (System::$user->checkAccess ('user'))
            {
                // if already logged in -- log out
                System::doMysql ("DELETE FROM ".Session::getTableName()." WHERE user_id = ".(System::$user->getPk()).";");
            }

            if (isset ($_REQUEST['email']) && isset ($_REQUEST['pass']))
                System::$user = User::authenticate ($_REQUEST['email'], $_REQUEST['pass']);

            if (System::$user->checkAccess ('user'))
                $this->ajaxRespond ( array (
                    'type' => 'auth_response',
                    'data' => array (
                        'sess_id' => System::$user->session->sess_id,
                    ),
                ) );
            else
                throw new GameError ('Authorisation failed');
        }
        
        /**
         * log out
         */
        public function logoutAction ()
        {
            session_destroy ();
            header ('Location: '.Html::getUrl ('boards'));
        }
        
        /**
         * editting tags
         */
        public function edit_tagsAction ()
        {
            // creating the form
            $form = new TextAreaForm ();
            
            // setting right label and placeholder
            $form->setLabel ('info', 'Tags: ');
            $form->setFieldParameter ('info', 'placeholder', 'Enter tags related to you, comma-sepparated. Example: C++, NLP, Usability');
            
            // default values
            $source = (System::$user->employer) ? System::$user->employer : System::$user->freelancer;
            $form->info = $source->short_descr;
            
            // retrieving
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('user/edit', array ('form' => $form));
            else 
            {
                $source->short_descr = System::mysqlRealEscapeString ($form->info);
                $this->dashboardAction ();
            }
        }
        
        /**
         * editting description
         */
        public function edit_descrAction ()
        {
            // creating the form
            $form = new TextAreaForm ();
            
            // setting right label and placeholder
            $form->setLabel ('info', 'Description: ');
            $form->setFieldParameter ('info', 'placeholder', 'Type here what describes you best.');
            
            // default values
            $source = (System::$user->employer) ? System::$user->employer : System::$user->freelancer;
            $form->info = $source->description;
            
            // retrieving
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('user/edit', array ('form' => $form));
            else 
            {
                $source->description = System::mysqlRealEscapeString ($form->info);
                $this->dashboardAction ();
            }
        }
        
        /**
         * uploading CV
         */
        public function upload_cvAction ()
        {
            // creating the form
            $form = new FileForm ();
            
            $form->setLabel ('file', 'Your CV (.pdf): ');
            
            // retrieving
            if (!$form->retrieveData () || !$form->validate ())
                $this->render ('user/edit', array ('form' => $form));
            else 
            {
                // deleting old cv, if there was such
                if (System::$user->freelancer->cv)
                    unlink (BASE_DIR.substr (System::$user->freelancer->cv, strlen (WEB_ROOT)));
                
                System::$user->freelancer->cv = $form->file;
                System::$user->freelancer->cv_uploaded = date ('Y-m-d H:i:s');
                $this->dashboardAction ();
            }
        }
    }
?>
