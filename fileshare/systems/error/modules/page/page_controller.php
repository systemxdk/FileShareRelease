<?

class PageController extends shController {
	
	function __construct(array $arguments = array()){
		parent::__construct($arguments);
		
		$this->className = 'Page';
	}
	
	function index(){
        //Consider doing some page 404 logging here for fileshare users
	}
	
}
