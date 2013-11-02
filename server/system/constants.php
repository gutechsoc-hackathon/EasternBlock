<?php
    /**
     * mysql relations binary constants
     */
    define ('HAS_ONE', 1);
    define ('HAS_MANY', 2);
    define ('HAS_MUTABLE_ONE', 4);
    define ('BELONGS_TO', 8);
    define ('MANY_MANY', 16);
    
    /**
     * ingame error codes
     */
    define ('FORM_FIELD_MISSING', 1);
    define ('VARIABLE_UNDEFINED', 2);
    
    /**
     * project statuses
     */
    define ('OPEN', 1);
    define ('IN_PROGRESS', 2);
    define ('COMPLETED', 4);
    define ('ALL_COOL', 8); // completed and payment received
    define ('CANCELLED', 16);
    
    /**
     * query types
     */
    define ('SELECT', 1);
    define ('UPDATE', 2);
    define ('INSERT', 4);
    define ('DELETE', 8);
    define ('SEARCH', 17);
    
?>