<?

class LanguageController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
		$this->className = 'Language';
	}
	
	function index(){
        
        //Fetch translations from langauge helper
        $language = Language::get();
        
        //Extract some keys for looping and register view
        $this->language_key = array_key_first($language);
        $this->keys = array_keys($language);
        
        //Do some key sorting here so it prints in order in view
        $tags = $language[$this->language_key];
        ksort($tags);
        $language[$this->language_key] = $tags;
        
        $this->language = $language;
	}
	
    function save(){
        try {
            
            //Fetch variables from language helper
            $delimiter = Language::get_delimiter();
            $path = Language::get_path();
            
            foreach (array_keys($this->arguments->translation) as $language_key) {
                
                //Prepare to write language file
                $file = $path . $language_key . ".txt";
                if (!file_exists($file)) throw new Exception(Language::tag("ERROR_LANGUAGE_FILE_MISSING"));
                
                //Create file contents
                $content = "";
                foreach ($this->arguments->translation[$language_key] as $tag_id => $translation) {
                    $content .= $tag_id . $delimiter . str_replace("\n", "", $translation) . "\n"; //Replace newlines as this will mess up the language file
                }
                
                //Save language file
                $fp = fopen($file, "w");
                fwrite($fp, $content);
                fclose($fp);
            }
            
            $this->redirect("index", array("success" => Language::tag("TEXT_TRANSLATIONS_SAVED")));
            
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
    
}
