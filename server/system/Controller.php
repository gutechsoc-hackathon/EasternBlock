<?php
    // a base controller
    // provides all funcitonality, underlying controllers above
    class Controller
    {
        protected $defaultAction = 'index';
        protected $url = array (); 
        protected static $access = array ('default' => array ('guest'));
        
        // sending to a desired action at the moment of creation
        public function __construct ($action)
        {
            if (!$action)
                $action = $this->defaultAction;
                
            // checking access
            // getting right parameter (or default)
            $class = get_called_class ();
            if (!isset ($class::$access[$action]))
                $act = 'default';
            else
                $act = $action;
            // ordinary search
            $acc = false;
            foreach ($class::$access[$act] as $val)
                if (System::$user->checkAccess ($val))
                {
                    $acc = true;
                    break;
                }
            // prevent from entering if you have no access
            if (!$acc)
                throw new GameError ('403: you do not have sufficient permissions. Possibly your session has ended, try loging in again.');
            
            // executing a before action, prior to normal one
            if (method_exists ($this, '_beforeAction'))
                $this->_beforeAction();
                
            $action .= 'Action';
            if (!method_exists ($this, $action))
                throw new GameError ('No such action found: '.$action);
            $this->$action ();
        }
        
        // just for the sake of it
        public function indexAction ()
        {
            echo 'default base index<br/>';
        }

        public function ajaxFail ($response)
        {
            $this->ajaxRespond ('error', array ('msg' => $response));
        }

        public function ajaxSuccess ()
        {
            $this->ajaxRespond ('success');
        }
        
        public function ajaxRespond ($type, $data = array ())
        {
            $response = array ('type' => $type, 'data' => $data);
            $json = json_encode ($response);
            $this->render ('ajax/respond', array ('json' => $json) );
        }
        
        // this method runs specified view, providing it with the variables
        // so that those variables seem local
        public function render ($route, $vars = array ())
        {
            // splitting route, in order to search for custom headers, etc.
            $route = explode ('/', $route);
            // path to the theme folder
            $_path = BASE_DIR.'views/'.System::getConfig ('theme').'/';
            $_webPath = WEB_ROOT.'views/'.System::getConfig ('theme').'/';
            define ('THEME_DIR', $_webPath);
            // extracting variables, so that view can use it
            extract ($vars, EXTR_OVERWRITE);
            // main script address
            $_main = $_path.'views/'.$route[0].'/'.$route[1].'.php';
            // main script directory
            $_mainDir = $_path.'views/'.$route[0].'/';
            
            // loading default header info
            $headerInfo = include $_path.'layout/headerInfo.php';
            if (file_exists ($_mainDir.'headerInfo.php'))
                $headerInfo = array_merge_recursive ($headerInfo, include $_mainDir.'headerInfo.php');
                
            // headerInfo array, header, footer and layout file:
            $_header = (file_exists ($_mainDir.'header.php')) ? $_mainDir.'header.php' : $_path.'layout/header.php';
            $_footer = (file_exists ($_mainDir.'footer.php')) ? $_mainDir.'footer.php' : $_path.'layout/footer.php';
            $_layout = (file_exists ($_mainDir.'layout.php')) ? $_mainDir.'layout.php' : $_path.'layout/layout.php';
            
            //Profiler::trace ('before display');
            ob_start ();
            include $_layout;
            ob_end_flush ();
            //Profiler::trace ('flush');
            
            // end of script
            exit ();
        }
    }
?>
