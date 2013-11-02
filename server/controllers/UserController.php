<?php
    class UserController extends Controller
    {
        protected static $access = array (
            'default' => array ('guest'),
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
                $this->ajaxRespond ('auth_response', array (
                    'sess_id' => System::$user->session->sess_id,
                    'name' => System::$user->name,
                ) );
            else
                throw new GameError ('Authorisation failed');
        }

        /**
         * register
         */
        public function registerAction ()
        {
            $email = Validators::getEmailFormat ($_REQUEST['email']);
            $pass = Validators::getPasswordFormat ($_REQUEST['pass']);
            $name = Validators::getMysqlSafe ($_REQUEST['name']);

            if (!$email || !$pass)
                throw new GameError ('All fields are required');
            if (strlen ($pass) < 6)
                throw new GameError ('Password should be at least 6 characters long');
            if (!$name)
                $name = $email;
            if (count (User::find ("email = '".$email."'")) != 0)
                throw new GameError ('Such user already exists');

            $u = new User();
            $u->email = $email;
            $u->pass = md5 ($pass);
            $u->name = $name;
            $u->save();

            // now that's a definition of a hack...
            // shame on me, as I should have thought about that
            // two years ago, while doing the framework...
            $_REQUEST['email'] = $email;
            $_REQUEST['pass'] = $pass;
            $this->loginAction();
        }

        /**
         * log out
         */
        public function logoutAction ()
        {
            System::doMysql ("DELETE FROM ".Session::getTableName()." WHERE user_id = ".(System::$user->getPk()).";");
            $this->ajaxSuccess();
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
