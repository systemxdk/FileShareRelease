<?php 

class Language {
    
    public static $language = null;
    
    private static $known_languages = array();
    
    private static $definitions = array();
    
    private static $delimiter = ":::";
    
    private static $path = "";
    
    static private function get_i18n_files() {
        return glob(self::$path . "*.txt");
    }
    
    static public function get_available_languages() {
        $languages = array();
        
        $language_files = self::get_i18n_files();
        foreach ($language_files as $language) {
            
            $language = basename($language);
            $language = str_replace(".txt", "", $language);
            
            $languages[] = $language;
        }
        
        return $languages;
    }
    
    static public function load() {
        
        //Find languages in i18n folder and register them
        //If there are no language files on disk return nothing
        $i18n_files = self::get_i18n_files();
        if (!count($i18n_files)) return; 
        
        //Iterate languages files and register all tags in them
        foreach ($i18n_files as $file) {
            
            //Fetch the language from language file and retrieve its contents
            $language = str_replace(".txt", "", basename($file));
            $file = file_get_contents($file);
            
            //Iterate language definitions and register to $definitions
            $definitions = array_filter(explode("\n", $file));
            foreach ($definitions as $def) {
                list($tag, $text) = explode(self::$delimiter, $def);
                
                self::$definitions[$language][$tag] = $text;
            }
            
            //Register language to known languages 
            self::$known_languages[] = $language;
        }
    }
    
    static public function set_default_language($language) {
        self::$language = $language;
    }
    
    static public function set_path($path) {
        self::$path = $path;
    }
    
    static public function get_path() {
        return self::$path;
    }
    
    static public function get_delimiter() {
        return self::$delimiter;
    }
    
    static public function tag($tag) {
        
        try {
            if (isset($_SESSION["language"]) && in_array($_SESSION["language"], self::$known_languages)) {
                $language = $_SESSION["language"]; //Language picked from select box, so use that one.
            } else {
                $language = self::$language; //Use default language from settings
            }
            
            //Some checks
            if (empty($tag)) throw new Exception("Language tag called without a tag.");
            if (!array_key_exists($language, self::$definitions)) self::$definitions[$language] = array();
            
            //Check for tag existence in language file
            //If it does not exist append it to language file
            if (!array_key_exists($tag, self::$definitions[$language])) {
                self::append($tag); //Add tag to language files
                return $tag; //Return the tag itself the first time for the developer to see
            }
            
            return self::$definitions[$language][$tag];
            
        } catch (Exception $e) {
            error_log("An error occured: " . $e->getMessage());
        }
        
        return $tag_id;
    }
    
    static private function append($tag) {
        
        $i18n_files = self::get_i18n_files();
        foreach ($i18n_files as $file) {
            
            $fp = fopen($file, "a");
            fwrite($fp, $tag . self::$delimiter . $tag . "\n");
            fclose($fp);
            
        }
        
    }
    
    static public function get() {
        return self::$definitions;
    }
    
}