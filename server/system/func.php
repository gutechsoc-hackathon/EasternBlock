<?php
    // autoloading classes
    function __autoload ($className)
    {
        // model
        if (file_exists (BASE_DIR.'models/'.$className.'.php'))
            include BASE_DIR.'models/'.$className.'.php';
        // controller
        else if (file_exists (BASE_DIR.'controllers/'.$className.'.php'))
            include BASE_DIR.'controllers/'.$className.'.php';
        // system
        else if (file_exists (BASE_DIR.'system/'.$className.'.php'))
            include BASE_DIR.'system/'.$className.'.php';
        // unspecified class
        else
            throw new Exception ('A class undefined');
    }
    
    // error function
    function registerError ($e)
    {
        // for ajax error, exit with json
        if (get_class ($e) == 'AjaxError' || defined ('AJAX'))
        {
            ob_clean ();
            exit (json_encode (array ('error' => $e->getMessage ())));
        }
        else
        {
            // else, run controller
            Globals::$g['e'] = $e;
            System::runController ('error/index');
        }
    }
    
    /**
     * tracing variables
     */
    function pr ($t)
    {
        echo '<pre>';
        print_r ($t);
        echo '</pre>';
    }
    
    /**
     * getting widget include address
     */
    function getWidget ($name) 
    {
        return BASE_DIR.'views/'.System::getConfig ('theme').'/'.'widgets/'.$name.'.php';
    }
?>
