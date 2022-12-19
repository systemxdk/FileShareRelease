<?

class NetworkController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
		$this->className = 'Network';
        
        UserAccessFilter::Verify(USER_ADMIN, true);
        $GLOBALS["_CONFIG"]["draw_submenu"] = NetworkSubmenu::menu();
	}
	
	function index(){
        //Some testing
        //var_dump(filter_var("; nc -lvnp 1234", FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)); //bool(false)
        //var_dump(filter_var("127.0.0", FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)); //string(7) "127.0.0"
        //var_dump(filter_var("127.0.0", FILTER_VALIDATE_IP)); //bool(false)
        //var_dump(filter_var("127.0.0.1", FILTER_VALIDATE_IP)); //string(9) "127.0.0.1"
        //var_dump(filter_var("; nc -lvnp 1234", FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)); //bool(false)
	}
	
    function ping(){
        try {
            $this->output = null;
            
            if (property_exists($this->arguments, "perform")) {
                
                if (!$this->arguments->target) throw new Exception(Language::tag("TEXT_NETWORK_TARGET_MISSING"));
            
                //This part is very very very important.
                //We trust PHP to do proper validation of IP and Hostname here.
                //If PHP fails here user could potentially breach to cmd and create a reverse shell with nc.
                if (!filter_var($this->arguments->target, FILTER_VALIDATE_IP) && !filter_var($this->arguments->target, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                    throw new Exception(Language::tag("TEXT_NETWORK_TARGET_INVALID"));
                }
                
                $output = shell_exec('ping -c 5 ' . $this->arguments->target);

                $this->output = explode("\n", $output);

            }
        } catch (Exception $e) {
            $this->redirect("users/administration/index", array("error" => $e->getMessage()));
        }
    }
    
    function nslookup(){
        try {
            $this->output = null;
            
            if (property_exists($this->arguments, "perform")) {
                
                if (!$this->arguments->target) throw new Exception(Language::tag("TEXT_NETWORK_TARGET_MISSING"));
            
                //This part is very very very important.
                //We trust PHP to do proper validation of IP and Hostname here.
                //If PHP fails here user could potentially breach to cmd and create a reverse shell with nc.
                if (!filter_var($this->arguments->target, FILTER_VALIDATE_IP) && !filter_var($this->arguments->target, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                    throw new Exception(Language::tag("TEXT_NETWORK_TARGET_INVALID"));
                }

                $output = shell_exec('nslookup ' . $this->arguments->target);

                $this->output = explode("\n", $output);

            }
        } catch (Exception $e) {
            $this->redirect("users/administration/index", array("error" => $e->getMessage()));
        }
    }
    
    function dig(){ //This method works for regular post
        try {
            $this->output = null;
            
            if (property_exists($this->arguments, "perform")) {
                
                if (!$this->arguments->target) throw new Exception(Language::tag("TEXT_NETWORK_TARGET_MISSING"));
            
                //This part is very very very important.
                //We trust PHP to do proper validation of IP and Hostname here.
                //If PHP fails here user could potentially breach to cmd and create a reverse shell with nc.
                if (!filter_var($this->arguments->target, FILTER_VALIDATE_IP) && !filter_var($this->arguments->target, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                    throw new Exception(Language::tag("TEXT_NETWORK_TARGET_INVALID"));
                }

                $output = shell_exec('dig ' . $this->arguments->target);

                $this->output = explode("\n", $output);

            }
        } catch (Exception $e) {
            $this->redirect("users/administration/index", array("error" => $e->getMessage()));
        }
    }
    
    function ajax_dig() {
        header('Content-Type: application/json; charset=utf-8');
        
        $response = new stdClass();
        $response->status = "";
        $response->message = "";
        
        try {
            
            //This part is very very very important.
            //We trust PHP to do proper validation of IP and Hostname here.
            //If PHP fails here user could potentially breach to cmd and create a reverse shell with nc.
            if (!filter_var($this->arguments->target, FILTER_VALIDATE_IP) && !filter_var($this->arguments->target, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                throw new Exception(Language::tag("TEXT_NETWORK_TARGET_INVALID"));
            }
            
            $output = shell_exec('dig ' . $this->arguments->target);
            $response->message = $output;
            $response->status = "success";
            
        } catch (Exception $e) {
            $response->message = $e->getMessage();
            $response->status = "failure";
        }
        
        print json_encode($response);
        die();
    }
    
    function ajax_nslookup() { //todo: consider a wrapper to avoid dry principle here with the ajax above.
        header('Content-Type: application/json; charset=utf-8');
        
        $response = new stdClass();
        $response->status = "";
        $response->message = "";
        
        try {
            
            if (!$this->arguments->target) throw new Exception(Language::tag("TEXT_NETWORK_TARGET_MISSING"));

            //This part is very very very important.
            //We trust PHP to do proper validation of IP and Hostname here.
            //If PHP fails here user could potentially breach to cmd and create a reverse shell with nc.
            if (!filter_var($this->arguments->target, FILTER_VALIDATE_IP) && !filter_var($this->arguments->target, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
                throw new Exception(Language::tag("TEXT_NETWORK_TARGET_INVALID"));
            }

            $output = shell_exec('nslookup ' . $this->arguments->target);
            
            $response->message = $output;
            $response->status = "success";
            
        } catch (Exception $e) {
            $response->message = $e->getMessage();
            $response->status = "failure";
        }
        
        print json_encode($response);
        die();
    }
}
