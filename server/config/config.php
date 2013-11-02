<?php
    return array (
        // language
        'lang' => 'en',
        'langs' => array (
            'en'
        ),
        // database configurations
        'db' => array (
            'host' => 'localhost',
            'dbname' => 'hackaton',
            'user' => 'root',
            'pass' => ''
        ),
        // additional configuration files
        'configs' => array (
            'site',
        ),
        // route configurations
        'route' => array (
            'default' => 'index/index', // default 'index' controller
        ),
        // session and authentication
        'session' => array (
            'duration' => 600,
        ),
        // Role Based Access Controll
        'RBAC' => array (
            'admin' => array (
                'children' => array (
                    'moderator',
                ),
            ),
            'moderator' => array (
                'children' => array (
                    'freelancer', 'employer'
                ),
            ),
            'user' => array (
                'children' => array (
                    'guest'
                ),
            ),
            'guest' => array (
                'children' => array (
                ),
            ),
        ),
        // default rbac element
        'defaultRole' => 'guest',
        
        'params' => array (),
    );
?>
