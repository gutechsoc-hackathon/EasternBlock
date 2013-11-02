<?php
class ErrorController extends Controller
{
    //protected $defaultAction = 'go';
    
    public function indexAction ()
    {
        ob_clean ();
        if (defined ('AJAX'))
            $this->ajaxFail (Globals::$g['e']);
        else
            $this->render ('error/index', array ('e' => Globals::$g['e']));
    }
    
}
?>
