<?php
    class ErrorController extends Controller
    {
        //protected $defaultAction = 'go';
        
        public function indexAction ()
        {
            ob_clean ();
            $this->render ('error/index', array ('e' => Globals::$g['e']));
        }
        
    }
?>