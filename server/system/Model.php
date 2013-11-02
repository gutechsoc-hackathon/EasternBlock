<?php
    class Model
    {
        //private $data = array ();
        private $var = 1;
        
        public function __set ($var, $val)
        {
            //$this->$var = $val;
            $this->$var = $val;
            echo 'setter';
        }
        
        public function __get ($var)
        {
            return $this->$var;
        }
    }
?>