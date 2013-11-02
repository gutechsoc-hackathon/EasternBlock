<?php
    class ActiveRecord
    {
        protected $_old = false;
        protected $_edited = array ();
        protected $_attributes;
        private $_notTemp = true;
        private $_relUpdateNeeded = false;
        
        protected static $_pk = 'id';
        protected static $_table = null;
        protected static $_relations = array ();
        
        protected $_serialisation = null;
        
        //--------------------------------------- System -------------------------------------------
        public function __construct ($attributes = null)
        {
            $this->_attributes = $attributes;
            if ($attributes != null)
                $this->_old = true;
        }
        
        public function __destruct ()
        {
            if ($this->_notTemp)
                $this->save ();
        }
        
        // saves record to a database
        public function save ()
        {
            if ($this->_old)
                $this->update ();
            else
                $this->create ();
        }
        
        // magic setter for attributes (collumns)
        public function __set ($var, $val)
        {
            // saves names of cullumns, that were modified
            // so that we can update them on exit
            $this->_edited[$var] = true;
            // stores the value of modified collumn
            $this->_attributes[$var] = $val;
        } 
        
        // magic getter for attributes (collumns)
        public function __get ($var)
        {
            // if relUpdate is needed, then we need to reload the relation
            if (isset ($this->_attributes[$var])) // && !($this->_attributes[$var] instanceof ActiveRecord && $this->_attributes[$var]->relUpdateNeeded ()))
                return $this->_attributes[$var];
            $class = get_called_class();
            if (isset ($class::$_relations[$var]))
                return $this->loadRelation ($var);
            return null; 
        }
        
        // returns table name
        public static function getTableName ()
        {
            $class = get_called_class ();
            return $class::$_table;
            //return eval ('$class::$$_table');
        }
        
        /**
         * returns whether update to a relation to this object is required
         */
        public function relUpdateNeeded () 
        {
            return $this->_relUpdateNeeded;
        }
        
        
        //--------------------------------------- Create -------------------------------------------
        /**
         * creates new row, stores it pk
         * it is not automatically added to external relations, so, code must be structured in a way
         * that after all inserts old relations must be updated
         */
        private function create ()
        {
            // dropping all relations
            $this->dropAllRelations ();
            // building a insert query
            $class = get_called_class ();
            $_table = $class::$_table;
            $_pk = $class::$_pk;
            //$q = 'INSERT INTO '.$class::$_table.' (';
            $q = 'INSERT INTO '.$_table.' (';
            
            // if we did not specify id, it is specified as it is
            //if (!isset ($this->_attributes[$class::$_pk]))
            if (!isset ($this->_attributes[$_pk]))
                $this->_attributes[$_pk] = 0;
            
            // getting collumns
            $notFirst = false;
            foreach ($this->_attributes as $key => $val)
                if ($notFirst)
                    $q .= ', '.$key;
                else
                {
                    $q .= $key;
                    $notFirst = true;
                }
            
            $q .= ') VALUES (';
            // getting values
            $notFirst = false;
            foreach ($this->_attributes as $val)
                if ($notFirst)
                {
                    if (is_string ($val))
                        $q .= ', \''.$val.'\'';
                    else if ($val === null)
                        $q .= ', NULL';
                    else
                        $q .= ', '.$val;
                }
                else
                {
                    if (is_string ($val))
                        $q .= '\''.$val.'\'';
                    else if ($val === null)
                        $q .= 'NULL';
                    else
                        $q .= $val;
                    $notFirst = true;
                }
            
            $q .= ');';
            // executting it
            System::doMysql ($q);
            // storing id
            $this->_attributes[ActiveRecord::$_pk] = System::lastInsertId ();
            // making it old
            $this->_old = true;
            $this->eraseEdited ();
        }
        
        // when AR is temporary, it will not save its data to DB at the end of script
        public function makeTemporary ()
        {
            $this->_notTemp = false;
        }
        
        // erases array of edited collumns
        // because when we create an instance
        // system will still think, that we have updated them
        public function eraseEdited ()
        {
            $this->_edited = array ();
        }
        
        
        //---------------------------------------- Read --------------------------------------------
        // return a reference to an instance of class OR an array of references
        // that satisfies given select clause
        // or it may return actual instances, depends on configuration
        public static function find ($where = '1=1')
        {
            // getting static class prperties
            $class = get_called_class ();
            $t = $class::$_table;
            $pk = $class::$_pk;
            
            // searching for record
            $q = System::doMysql ('SELECT * FROM '.$t.' WHERE '.$where.';');
            // saving results into an array
            $result = array ();
            $i = 0;
            while ($r = System::mysqlResultAssoc ($q))
            {
                if (isset (Game::$dataPk[$t][$r[$pk]]))
                    $result[$i++] = Game::$dataPk[$t][$r[$pk]];
                else
                {
                    Game::$dataPk[$t][$r[$pk]] = new $class ($r);
                    $result[$i++] = Game::$dataPk[$t][$r[$pk]];
                }
            }
            
            // the end
            return $result;
        }
        
        // return a reference to an instance of class
        // that satisfies given select clause
        // or it may return actual instance, depends on configuration
        public static function findByPk ($pk)
        {
            $class = get_called_class ();
            $_table = $class::$_table;
            $_pk = $class::$_pk;
            
            // if pk is string, we will add quotes
            if (is_string ($pk))
                $pk = "'".$pk."'";
                
            // searching, if an object already exists (then returning it)
            if (isset (Game::$dataPk[$_table][$pk]))
                return Game::$dataPk[$_table][$pk];
            
            // otherwise getting it from the database
            $q = System::doMysql ('SELECT * FROM '.$_table.' WHERE '.$_pk.' = '.$pk.';');
            // if there are none found - raise game error
            if (System::mysqlNumRows ($q) == 0)
                throw new GameError ('can not find the '.$_table.' record with id '.$pk);
                
            // getting results and storing them in the game 
            Game::$dataPk[$_table][$pk] = new $class (System::mysqlResultAssoc ($q));
            // returning the reference
            return Game::$dataPk[$_table][$pk];
        }
        
        // returns a primary key for this object
        public function getPk ()
        {
            if (!$this->_old)
                $this->create ();
                
            $class = get_called_class ();
            return $this->_attributes[$class::$_pk];
        }
        
        // returns name of a primary key
        public static function getPkName ()
        {
            $class = get_called_class ();
            return $class::$_pk;
        }
        
        //--------------------------------------- Update -------------------------------------------
        // updates mysql database, if nescessary
        private function update ()
        {
            $c = count ($this->_edited);
            if ($c != 0)
            {
                $class = get_called_class ();
                $q = 'UPDATE '.$class::$_table.' SET ';
                $notFirst = false;
                foreach ($this->_edited as $key => $val)
                    if ($notFirst)
                    {
                        if ($this->_attributes[$key] === null)
                            $q .= ', '.$key.' = NULL';
                        else if (is_string ($this->_attributes[$key]))
                            $q .= ', '.$key.' = \''.$this->_attributes[$key].'\'';
                        else
                            $q .= ', '.$key.' = '.$this->_attributes[$key];
                    }
                    else
                    {
                        if ($this->_attributes[$key] === null)
                            $q .= $key.' = NULL';
                        else if (is_string ($this->_attributes[$key]))
                            $q .= $key.' = \''.$this->_attributes[$key].'\'';
                        else
                            $q .= $key.' = '.$this->_attributes[$key];
                        $notFirst = true;
                    }
                $q .= ' WHERE '.ActiveRecord::$_pk.' = '.$this->_attributes[ActiveRecord::$_pk].';';
                //Profiler::trace ('before update');
                //echo $q.'<br/>';
                System::doMysql ($q);
                //Profiler::trace ('update');
                $this->eraseEdited ();
            }
        } 
        
        //--------------------------------------- Delete -------------------------------------------
        // deletes the record from database
        public function delete ()
        {
            $class = get_called_class ();
            System::doMysql ('DELETE FROM '.$class::$_table.' WHERE '.ActiveRecord::$_pk.' = '.$this->_attributes[ActiveRecord::$_pk].';');
            $this->makeTemporary ();
            // fixing relations: 
            $this->_relUpdateNeeded = true;
        }
        
        //-------------------------------------- Relations -----------------------------------------
        public function loadRelation ($var)
        {
            $class = get_called_class();
            $_rel = $class::$_relations;
            $_pk = $class::$_pk;
            // 0 -> relation, 1 -> class, 2 -> primary or foreign key, 3 restriction (if exists)
            $relClass = $_rel[$var][1];
            // has one ralation: WHERE his id = this id
            if ($_rel[$var][0] & HAS_ONE)
            {
                $q = ($relClass::getPkName()).' = \''.$this->_attributes[$_pk].'\' ';
                // restriction
                if (isset ($_rel[$var][3]))
                    $q .= $_rel[$var][3];
                $this->_attributes[$var] = $relClass::find ($q);
                if (isset ($this->_attributes[$var][0]))
                    $this->_attributes[$var] = $this->_attributes[$var][0];
            }
            // belongs to relation: where his id = this parameter
            else if ($_rel[$var][0] & BELONGS_TO)
            {
                $q = ($relClass::getPkName()).' = \''.$this->_attributes[$_rel[$var][2]].'\' ';
                // restriction
                if (isset ($_rel[$var][3]))
                    $q .= $_rel[$var][3];
                $this->_attributes[$var] = $relClass::find ($q);
                if (isset ($this->_attributes[$var][0]))
                    $this->_attributes[$var] = $this->_attributes[$var][0];
            }
            // has one, which is not connected by respectful PK, relation: where his parameter = this pk
            else if ($_rel[$var][0] & HAS_MUTABLE_ONE)
            {
                $q = $_rel[$var][2].' = \''.$this->_attributes[$_pk].'\' ';
                // restriction
                if (isset ($_rel[$var][3]))
                    $q .= $_rel[$var][3];
                $this->_attributes[$var] = $relClass::find ($q);
                if (isset ($this->_attributes[$var][0]))
                    $this->_attributes[$var] = $this->_attributes[$var][0];
            }
            // has many relation: where his parameter = this pk
            else if ($_rel[$var][0] & HAS_MANY)
            {
                $q = $_rel[$var][2].' = \''.$this->_attributes[$_pk].'\' ';
                // restriction
                if (isset ($_rel[$var][3]))
                    $q .= $_rel[$var][3];
                $this->_attributes[$var] = $relClass::find ($q);
            }
            // many to many relation
            // 0 -> relation, 1 -> class, 2 -> rel_table, 3 -> foreign key of source, 4 -> foreign key of destination, 5 restriction (if exists)
            else if ($_rel[$var][0] & MANY_MANY)
            {
                $q = $relClass::getPkName().' IN (SELECT '.$_rel[$var][4].' FROM '.$_rel[$var][2].' WHERE '.$_rel[$var][3].' = '.$this->_attributes[$_pk].') ';
                // restriction
                if (isset ($_rel[$var][5]))
                    $q .= $_rel[$var][5];
                $this->_attributes[$var] = $relClass::find ($q);
            }
            // dropping relation if there is none
            if ($this->_attributes[$var] == null)
            {
                unset ($this->_attributes[$var]);
                return null;
            }
            return $this->_attributes[$var];
        }
        
        public function dropRelation ($rel, $ind = null)
        { 
            if ($ind === null)
                // null unsets the variable, and forces another db request next time
                unset ($this->_attributes[$rel]);
            else
                unset ($this->_attributes[$rel][$ind]);
        }
        
        public function dropAllRelations ()
        {
            $class = get_called_class();
            foreach ($class::$_relations as $key => $val)
                if (isset ($this->_attributes[$key]))
                    unset ($this->_attributes[$key]);
        }
        
        public function setRelation ($rel, $value)
        {
            $this->_attributes[$rel] = $value;
        }
        
        //------------------------ serialisation ---------------------------------------------------
        /**
         * writing out the whole structure to associative array
         */
        public function formArray ()
        {
            // array we will store values in
            $json = array ();
            // if serialisation is not specified, serialise everything you can find
            if ($this->_serialisation == null)
                $this->_serialisation = $this->_attributes;
            // creating array representation for every serialisable parameter
            foreach ($this->_serialisation as $key => $val)
                // object has his own array representation
                if (is_object ($this->$key))
                    $json[$key] = $this->$key->formArray ();
                // a level of inception for array
                else if (is_array ($this->$key) && isset ($this->$key[0]))
                    foreach ($this->$key as $nestedKey => $val2)
                        if (is_object ($val2))
                            $json[$key][$nestedKey] = $val2->formArray ();
                        else
                            $json[$key][$nestedKey] = $val2;
                // or simply the value otherwise
                else
                    $json[$key] = $this->$key;
            return $json;
        }
        
        /**
         * forming a json document out of class
         */
        public function serialise ()
        {
            return json_encode ($this->formArray ());
        }
    }
?>