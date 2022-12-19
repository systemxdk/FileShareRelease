<?

class AccountController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
        UserAccessFilter::Verify(USER_ACCOUNT);
        
		$this->className = 'Account';
	}
	
	function index(){
        $user = new User();
        $user->find($_SESSION["authorized"]);
        $this->user = $user;
	}

    function save() {
        try {
            
            $user = new User();
            $user->find($_SESSION["authorized"]);
            $user->firstname = $this->arguments->firstname;
            $user->lastname = $this->arguments->lastname;
            $user->update();
            $this->redirect("index", array("success" => Language::tag("TEXT_USER_SAVED")));
        } catch (Exception $e) {
            $this->redirect("index", array("error" => $e->getMessage()));
        }
    }
}
