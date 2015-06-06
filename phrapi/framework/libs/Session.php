<? defined('PHRAPI') or die('Direct access not allowed!');
/**
 *
 * @author Alecs Galindo, twitter.com/elalecs
 * @copyright Tecnologías Web de México S.A. de C.V.
 *
 */
class Session {
	private $uid  = "";
	private $ssid = "";
	protected $session = array();

	/**
	 * @var Object instance
	 */
	private static $instance = array();

	public function __construct() {
		$config = $GLOBALS['config'];
		$this->uid = $config['uid'];

		if (!$this->session_started() && !headers_sent())
			session_start();

		$this->ssid = session_id();

		if (!isset($_SESSION['PHRAPI'.$this->uid])) {
			$_SESSION['PHRAPI'.$this->uid] = array();
		}

		$this->session = &$_SESSION['PHRAPI'.$this->uid];
	}

	/**
	 * Singleton pattern http://en.wikipedia.org/wiki/Singleton_pattern
	 * @return object Class Instance
	 */
	public static function getInstance()
	{
		if (!self::$instance instanceof self)
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Validate if the session has been started
	 *
	 * @return boolean
	 */
	private function session_started()
	{
		if(isset($_SESSION))
			return true;

		return false;
	}

	function __set($name, $value = '')
	{
		$this->session[$this->uid . $name] = $value;

		return true;
	}

	function __get($name) {
		if (isset($this->session[$this->uid . $name])) {
			return $this->session[$this->uid . $name];
		}

		return NULL;
	}

	function __isset($name) {
		return isset($this->session[$this->uid . $name]);
	}

	function __unset($name) {
		if (isset($this->session[$this->uid . $name])) {
			unset($this->session[$this->uid . $name]);
		}

		return false;
	}

}