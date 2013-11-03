<?php
    class Validators
    {
        /**
         * a block of functions, that edit their parameters
         */
        public static function getAlfaNum ($data)
        {
            return preg_replace ('/[^a-zA-Z0-9_]/', '', $data);
        }
        
        public static function getNum ($data)
        {
            return preg_replace ('/[^0-9-]/', '', $data);
        }

        public static function getFloat ($data)
        {
            return preg_replace ('/[^0-9.-]/', '', $data);
        }
        
        public static function getLetters ($data)
        {
            return preg_replace ('/[^a-zA-Z_]/', '', $data);
        }
        
        public static function getLoginFormat ($data)
        {
            return preg_replace ('/[^a-zA-Z0-9_]/', '', $data);
        }
        
        public static function getPasswordFormat ($data)
        {
            return System::mysqlRealEscapeString ($data);
        }
        
        public static function getEmailFormat ($data)
        {
            return preg_replace ('/[^a-z0-9_.@-]/i', '', $data);
        }

        public static function getDateFormat ($data)
        {
            return preg_replace ('/[^0-9 :-]/', '', $data);
        }

        public static function getSeo ($data)
        {
            $source = array (' ', ',', '_', '–', '—', '+', '@');
            $destin = array ('-', '-', '-', '-', '-', '-', '-');
            return str_replace ($source, $destin, strtolower (preg_replace ('/[^a-zA-Z0-9,_–—+@-]/', '', $data)));
        }

        public static function getMysqlSafe ($data)
        {
            return System::mysqlRealEscapeString ($data);
        }
        
        /**
         * wrapper for given validators 
         */
        public static function validate (&$value, $validator, $params)
        {
            $validator .= 'Validator';
            return Validators::$validator ($value, $params);
        }
        
        /**
         * a block of internal validators
         */
        public static function emailValidator ($email, $tmp)
        {
            return preg_match ('/[-0-9a-z_]+@[-0-9a-z_^\.]+\.[a-z]{2,6}/i', $data);
        }
        
        public static function loginValidator ($data, $tmp)
        {
            return preg_match ('/[a-zA-Z0-9_-]/', $data);
        }
        
        public static function passwordValidator ($data, $tmp)
        {
            return preg_match ('/[a-zA-Z0-9_-]/', $data);
        }
        
        public static function integerValidator ($data, $tmp)
        {
            return preg_match ('/[0-9]/', $data);
        }

        public static function seoValidator ($data, $tmp)
        {
            return preg_match ('/[a-z0-9-]/', $data);
        }
        
        public static function noScriptValidator ($data, $tmp)
        {
            return !preg_match ('/<script|<iframe|<embed/', $data);
        }
        
        public static function notNullValidator ($data, $tmp)
        {
            return ($data) ? true : false;
        }
        
        public static function maxLengthValidator ($data, $len)
        {
            return strlen ($data) <= $len;
        }
        
        public static function minLengthValidator ($data, $len)
        {
            return strlen ($data) >= $len;
        }
        
        /**
         * Validators, that modify the parameter passed
         */
        
        public static function _fileValidator (&$file, $v)
        {
            $uf = $_FILES[$v['name']];
            if ($uf['error'] 
                || isset ($v['allowedTypes']) && !in_array ($uf['type'], $v['allowedTypes']) 
                || isset ($v['maxSize']) && $uf['size'] > $v['maxSize'])
                return false;
                
            $file = substr (md5 (microtime ()), 0, 9).'.pdf';
            move_uploaded_file ($uf['tmp_name'], BASE_DIR.$v['uploadsDir'].$file);
            $file = WEB_ROOT.$v['uploadsDir'].$file;
            
            return true;
        }
    }
?>
