<?php
    class Profiler
    {
        private static $_time = 0;
        private static $_lastTime = 0;
        
        /**
         * initialises profiler
         */
        public static function init ()
        {
            Profiler::$_time = microtime ();
            Profiler::$_lastTime = Profiler::$_time;
        }
        
        /**
         * returns time from the start of profiler
         */
        public static function getTotalTime ()
        {
            Profiler::$_lastTime = microtime ();
            return Profiler::$_lastTime - Profiler::$_time;
        }
        
        /**
         * gets time interval from the last time this function was called (or beginning)
         */
        public static function getITime ()
        {
            $t = microtime ();
            $r = $t - Profiler::$_lastTime;
            Profiler::$_lastTime = $t;
            return $r;
        }
        
        /**
         * prints out getITime time interval formatted, along with supplied text
         */
        public static function trace ($t = 't')
        {
            echo '<pre>'.$t.': '.Profiler::getITime ().'</pre>';
        }
    }
?>