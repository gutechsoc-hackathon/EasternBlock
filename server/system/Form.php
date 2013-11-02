<?php
    /**
     * A generic form model
     * has all validation functions
     */
    class Form
    {
        /**
         * _meta: form specific information (type, name...)
         * _fields: names of fields => html parameters; if there is no value parameter - required
         * _labels: names of fields => label
         * _values: values of form, for each field
         * _rules: validation rules
         * _errors: errors, connected to fields
         */
        protected $_meta = array ();
        protected $_fields = array (); // = array ('login' => array ('class' => 'input_field'));
        protected $_labels = array ();
        protected $_values = array ();
        protected $_rules = array ();
        protected $_errors = array ();
        protected $_valid = true;
        
        /**
         * constructor, setting up action
         */
        public function __construct ($action = '')
        {
            if (!$action)
                $action = Html::getUrl (implode ('/', System::$url));
            $this->_meta['action'] = $action;
        }
        
        /**
         * magic accessor, for accessing forms properties
         */
        public function __get ($name)
        {
            if (!isset ($this->_values[$name]))
                throw new Exception ('form field does not exist: '.$name);
            return $this->_values[$name];
        }
        
        /**
         * magic mutator, for setting form field values
         */
        public function __set ($name, $val)
        {
            if (!isset ($this->_fields[$name]))
                throw new Exception ('Form field does not exist: '.$name);
            $this->_values[$name] = $val;
            $this->_fields[$name]['value'] = $val;
        }
        
        /**
         * mutator for setting form field labels
         */
        public function setLabel ($name, $label)
        {
            if (!isset ($this->_fields[$name]))
                throw new Exception ('Form field does not exist: '.$name);
            $this->_labels[$name]['label'] = $label;
        }
        
        /**
         * mutator for setting form field parameters
         */
        public function setFieldParameter ($name, $key, $val)
        {
            if (!isset ($this->_fields[$name]))
                throw new Exception ('Form field does not exist: '.$name);
            $this->_fields[$name][$key] = $val;
        }
        
        /**
         * function, which gets parameters from user
         * returns true, if parameters were sent
         */
        public function retrieveData ()
        {
            // deciding on the way we retrieve data
            //$method = ($this->_meta['method'] == 'get') ? $_GET : $_POST;
            $method = &$_POST;
            if ($this->_meta['method'] == 'get')
                $method = &$_GET;
            if (!isset ($method[$this->_meta['id'].'_submitted']))
                return false;
            // getting the information for each field
            foreach ($this->_fields as $key => $val)
                // if a field is missing, make form invalid
                if (!isset ($method[$key]) || $method[$key] === '')
                {
                    if (!isset ($val['value']))
                    {
                        if ($val['type'] != 'file')
                        {
                            $this->_errors[$key] = 'required';
                            $this->_valid = false;
                        }
                        else
                            $this->_values[$key] = 1;
                    }
                    else
                    {
                        $this->_values[$key] = $val['value'];
                        $this->_fields[$key]['value'] = $val['value'];
                    }
                }
                else
                {
                    $this->_values[$key] = $method[$key];
                    $this->_fields[$key]['value'] = $method[$key];
                }
            return true;
        }
        
        /** 
         * function, which validates data against validators
         * by default stops after first error
         */
        public function validate ($all = false)
        {
            foreach ($this->_fields as $key => $val)
                foreach ($this->_rules[$key] as $validator => $params)
                    if (!Validators::validate ($this->_values[$key], $validator, $params))
                    {
                        $this->_valid = false;
                        $this->_errors[$key] = $validator;
                        if (!$all)
                            return false;
                    }
            return $this->_valid;
        }
        
        /**
         * function, that cheks whether form is valid
         */
        public function isValid ()
        {
            return $this->_valid;
        }
        
        /**
         * storing form values in the database
         */
        public function storeTo ($className)
        {
            // if we had loaded the data from some AR object – getting it
            if (isset ($this->_values['id']))
                $destination = $className::findByPk ($this->id);
            // otherwise we just create a new one
            else
                $destination = new $className ();
            
            foreach ($this->_values as $key => $val)
                if ($key != 'id' && $this->_fields[$key]['type'] != 'submit')
                    $destination->$key = System::mysqlRealEscapeString ($val);
            $destination->save ();
        }
        
        /**
         * loads values from an AR object
         */
        public function loadFrom ($obj)
        {
            foreach ($this->_fields as $key => $val)
                if ($val['type'] != 'submit')
                    $this->$key = $obj->$key;
            $this->_values['id'] = $obj->id;
        }
        
        /**
         * getting array of parameters used in Html, for display
         */
        public function getParams ($name)
        {
            if (!isset ($this->_fields[$name]))
                throw new Exception ('form field undefined!');
            // automatically adding name parameter
            $this->_fields[$name]['name'] = $name;
            // other parameters are in there
            return $this->_fields[$name];
        }
        
        /**
         * getting array of labels used in Html, for display
         */
        public function getLabel ($name)
        {
            if (!isset ($this->_labels[$name]))
                throw new Exception ('form field undefined!');
            // automatically adding for parameter
            $this->_labels[$name]['for'] = $name;
            // other parameters are in there
            return $this->_labels[$name];
        }
        
        /**
         * getting meta data
         */
        public function getMeta ()
        {
            return $this->_meta;
        }
        
        /**
         * getting error for a particular form field
         */
        public function getError ($name)
        {
            if (!isset ($this->_errors[$name]))
                return null;
            else
                return $this->_errors[$name];
        }
        
        /**
         * getting all arrors
         */
        public function getErrors ()
        {
            return $this->_errors;
        }
        
        /**
         * returns form HTML
         */
        public function getFormHtml ()
        {
            $html = Html::beginForm ($this);
            
            // looping all fields in order
            foreach ($this->_fields as $name => $field)
            {
                $error = Html::getError ($this, $name);
                $html .= '<fieldset '.(($error) ? 'class="error"' : '').' '.(($field['type'] == 'hidden') ? ' style="display:none"' : '').'>';
                
                // label
                if (isset ($this->_labels[$name]))
                    $html .= Html::getLabel ($this, $name);
                // main field
                switch ($field['type'])
                {
                    case 'textarea': $html .= Html::getTextArea ($this, $name); break;
                    case 'select': $html .= Html::getSelect ($this, $name); break;
                    default: $html .= Html::getInput ($this, $name); break;
                }
                // and errors if present
                $html .= $error;
                
                $html .= '</fieldset>';
            }
            return $html;
        }
    }
?>