<?php
    /**
     * class for LDAP authentification
     */
    class LDAPHelper
    {
        private $ds, $ldap;
        /**
         * connects to a server on construction
         */
        public function __construct ()
        {
            // getting all parameters
            $this->ldap = System::getConfig ('ldap');
            // connecting to the server
            $this->ds = ldap_connect ($this->ldap['host']);
            if (!$this->ds)
                throw new GameError ('Unable to connect to ldap server');
            // binding to it (you need to bind to general searching user, in order to speed up search)
            if (!ldap_bind ($this->ds, $this->ldap['bindRdn'], $this->ldap['bindPass']))
                throw new GameError ('Initial bind was unsuccessfull');
        }
        
        /**
         * returns user information in an array
         */
        public function searchUser ($login, $pass)
        {
            // authentification happens through bind, but for this we need user dn. So:
            // first of all, we need to get user dn
            foreach ($this->ldap['context'] as $con)
            {
                $sr = ldap_search ($this->ds, $con, '(uid='.$login.')');
                //$sr = ldap_search ($this->ds, $con, '(GivenName=Quintin)');
                if (!ldap_count_entries ($this->ds, $sr))
                    continue;
                // getting first entry
                $u = ldap_first_entry ($this->ds, $sr);
                // getting this user's dn
                $udn = ldap_get_dn ($this->ds, $u);
                // binding with it
                $success = ldap_bind ($this->ds, $udn, $pass);
                
                // if bind was successful, then returning user's information
                if ($success)
                {
                    $u = ldap_get_entries ($this->ds, $sr);
                    return $u[0];
                }
            }
            return false;
        }
        
        /**
         * closing connection in destructor
         */
        public function __destruct ()
        {
            ldap_close ($this->ds);
        }
    }
?>