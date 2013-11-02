<?php
    // TBD: full query implementation
    /**
     * A generic query builder, designed to work together with AR
     */
    class Query
    {
        private $_type = null;
        private $_table = null;
        private $_where = null;
        private $_order = array ();
        private $_limit = null;
        private $_glue = ' AND ';
        
        function __construct ($type = SELECT, $table = null)
        {
            $this->_type = $type;
            $this->_table = $table;
            if ($type & SEARCH)
                $this->_glue = 'OR';
            return $this;
        }
        
        /**
         * setting the table
         */
        public function from ($table)
        {
            $this->_table = $table;
            return $this;
        }
        
        /**
         * where clauses
         */
        public function where ($first, $second = null, $sign = '=') 
        {
            if ($second !== null)
            {
                if (is_string ($second))
                    $add = $first.' '.$sign." '".$second."'";
                else
                    $add = $first.' '.$sign.' '.$second;
            }
            else
                $add = $first;
            
            if ($this->_where === null)
                $this->_where = $add;
            else
                $this->_where = $this->_glue.$add;
                
            return $this;
        }
        
        /**
         * order parameters
         */
        public function order ($param, $type = null) 
        {
            if ($type === null)
                $this->_order[] = $param.' '.$type;
            else
                $this->_order[] = $param;
            return $this;
        }
        
        /**
         * adding limits
         */
        public function limit ($from, $len = null) 
        {
            if ($len === null)
                $this->_limit = $from;
            else
                $this->_limit = $from.', '.$len;
            return $this;
        }
        
        /**
         * getting the after WHERE part for AR::find();
         */
        public function getFilters ()
        {
            $q = '1=1';
            if ($this->_where)
                $q = $this->_where;
            if (!empty ($this->_order))
                $q .= ' ORDER BY '.implode (', ', $this->_order);
            if ($this->_limit)
                $q .= 'LIMIT '.$this->_limit;
            return $q;
        }
    }
?>