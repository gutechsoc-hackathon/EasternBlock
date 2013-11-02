<?php
    class Html
    {
        /**
         * returns a string with address in the game format
         */
        public static function getUrl ($path)
        {
            return WEB_ROOT.'index.php?r='.$path;
        }
        
        /**
         * returns valid html link (array contains raw data)
         */
        public static function getLink ($param, $name)
        {
            $a = '<a ';
            foreach ($param as $key => $val)
                $a .= $key.'="'.$val.'" ';
            return $a.'>'.$name.'</a>';
        }
        
        /**
         * returns valid html link from a configurated array
         */
        public static function getParsedLink ($param)
        {
            // loading the config
            $param['href'] = Html::getUrl (implode ('/', $param['href']));
            if (isset ($param['title']))
                $param['title'] = T::get ($param['title']);
            $name = T::get ($param['label']);
            unset ($param['label']);
            if (isset ($param['params']))
            {
                $params = array ();
                foreach ($param['params'] as $key => $val)
                    $params[] = $key.'='.$val;
                $param['href'] .= '/?'.implode('&', $params);
                unset ($param['params']);
            }
            // standard html
            $a = '<a ';
            foreach ($param as $key => $val)
                $a .= $key.'="'.$val.'" ';
            return $a.'>'.$name.'</a>';
        }
        
        /**
         * returns nonbreakable text
         */
        public static function getNonbreakable ($str)
        {
            return str_replace (' ', '&nbsp;', $str);
        }
        
        /**
         * returns an email address slightly modified, to harden work of email parsers
         */
        public static function getEmail ($email)
        {
            $e = explode ('@', $email);
            return $e[0].Html::getLink (array ('href' => 'mailto:'.$email, 'class' => 'email_link'), '@').$e[1];
        }
        
        /**
         * returns date in the British format
         */
        public static function getDate ($date)
        {
            $date = explode ('-', substr ($date, 0, 10));
            return $date[2].'/'.$date[1].'/'.$date[0];
        }
        
        /**
         * returns code needed to start form
         */
        public static function beginForm ($form)
        {
            $meta = $form->getMeta ();
            $f = '<form ';
            foreach ($meta as $key => $val)
                $f .= $key.'="'.$val.'" ';
            return $f.' ><input type="hidden" name="'.$meta['id'].'_submitted" value="1" />';
        }
        
        /**
         * returns code needed to end form
         */
        public static function endForm ()
        {
            return '</form>';
        }
        
        /**
         * returns code needed to display an input field
         */
        public static function getInput ($form, $name)
        {
            $html = $form->getParams ($name);
            $input = '<input ';
            foreach ($html as $key => $val)
                $input .= $key.'="'.$val.'" ';
            return $input.' />';
        }
        
        /**
         * returns code needed to display select field
         */
        public static function getSelect ($form, $name)
        {
            $html = $form->getParams ($name);
            $s = '<select ';
            foreach ($html as $key => $val)
                if ($key != 'options')
                    $s .= $key.'="'.$val.'" ';
            $s .= '>';
            foreach ($html['options'] as $key => $val)
                $s .= '<option value="'.$key.'" '.(($html['value'] == $key) ? 'selected="selected"' : '' ).'>'.$val.'</option>';
            return $s.'</select>';
        }
        
        /**
         * returns code to display text-area
         */
        public static function getTextArea ($form, $name)
        {
            $html = $form->getParams ($name);
            $t = '<textarea ';
            foreach ($html as $key => $val)
                if ($key != 'value')
                    $t .= $key.'="'.$val.'" ';
            return $t.'>'.((isset ($html['value'])) ? $html['value'] : '').'</textarea>';
        }
        
        /**
         * returns code to display form label
         */
        public static function getLabel ($form, $name)
        {
            $html = $form->getLabel ($name);
            $l = '<label ';
            foreach ($html as $key => $val)
                if ($key != 'label')
                    $l .= $key.'="'.$val.'" ';
            return $l.'>'.$html['label'].'</label>';
        }
        
        /**
         * returns code, to display error, tied to a particular form field
         */
        public static function getError ($form, $name)
        {
            $html = $form->getError ($name);
            if ($html)
                return '<div class="form_error">'.$html.'</div>';
            return '';
        }
    }
?>