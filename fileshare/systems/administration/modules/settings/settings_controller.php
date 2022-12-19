<?

class SettingsController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
		$this->className = 'Settings';
        
        UserAccessFilter::Verify(USER_ADMIN, true);
        $GLOBALS["_CONFIG"]["draw_submenu"] = SettingsSubmenu::menu();
	}
	
	function index(){
        $this->forward("email");
	}
    
    function load($section) {
        $current = array();

        $setting = new Setting();
        foreach ($setting->find_all("section = '$section'") as $s) {
            $current[$s->setting] = $s->setting_value;
        }
            
        return $current;
    }
	
    function email(){
        $this->settings = $this->load("email");
    }
    
    function fileshare(){
        $this->settings = $this->load("fileshare");
    }
    
    function save(){
        try {
            if (!is_array($this->arguments->setting)) throw new Exception("Fatal: missing settings");
            
            $user = new User();
            $user->find($_SESSION["authorized"]);
            
            if ($user->id === null) throw new Exception(Language::tag("ERROR_SETTING_USER_NOT_FOUND"));
            
            foreach ($this->arguments->setting as $key => $value) {
                
                $setting = new Setting();
                $setting->find_by_setting($key);
                
                if ($setting->id === null) throw new Exception(Language::tag("ERROR_SETTING_NOT_FOUND"));
                
                $setting->updated_by = $user->username;
                $setting->updated = "now()";
                $setting->setting_value = $value;
                $setting->update();
            }
            
            $this->redirect("index", array("success" => Language::tag("TEXT_SETTING_SAVED")));
        } catch (Exception $e) {
            $this->redirect("email", array("error" => $e->getMessage()));
        }
    }
}
