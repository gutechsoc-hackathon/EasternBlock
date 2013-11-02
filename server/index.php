<?php
    // © Maksim Solovjov, 2011
    // © Eastern Block, 2013
    // 01/11/2013
    // --------------------------------------------------------
    // base directory constant
    define ('BASE_DIR', dirname (__FILE__).'/');
    // same constant, used for client side, such as including css
    define ('WEB_ROOT', 'http://localhost/hackaton/EasternBlock/server/');
    // other constants
    include BASE_DIR.'system/constants.php';
    // system functions, most notably: __autoload
    include BASE_DIR.'system/func.php';
    
    // in ajax scenario, route will be provided in POST
    if (isset ($_POST['r']))
    {
        $_GET['r'] = $_POST['r'];
        define ('AJAX', true);
    }
    
    // initialising system
    System::init ();
?>
