<? defined('PHRAPI') or die('Direct access not allowed!');
/**
 * Factory of controllers
 *
 * @author Alecs Galindo, twitter.com/elalecs
 * @copyright Tecnologías Web de México S.A. de C.V.
 *
 */
class Factory {
	protected $controllers = array();
	protected $config;

	public function __construct() {
		$this->config = $GLOBALS['config'];

		$resource = getValueFrom($_GET, 'resource', '', FILTER_SANITIZE_STRING);

		if(!empty($resource) AND isset($this->config['routing']) AND is_array($this->config['routing']) AND sizeof($this->config['routing'])) {
			$params = array(
				'controller' => '',
				'action' => '',
				'method' => $_SERVER['REQUEST_METHOD']
			);

			foreach($this->config['routing'] as $routing_regex => $routing_params) {
				$routing_regex = preg_replace('/\//', '\\\/', $routing_regex);
				if(preg_match('/'.$routing_regex.'/', $resource, $routing_matches)) {
					$params = $routing_params + $params;
					break;
				}
			}

			if (!empty($params['controller'])) {
				$control = $this->{$params['controller']};

				if (!empty($params['action'])) {
					if (!method_exists($params['controller'], $params['action'])) {
						D("500: acción no definida");
						status_code();
						die;
					}

					$response = $control->{$params['action']}();

					if (!headers_sent())
						header("Content-type: text/plain; charset=UTF-8");
					echo json_pretty(json_encode($response));
				}
			}
		}
	}

	function __get($control) {
		if (isset($this->controllers[$control]) && $this->controllers[$control] instanceof $control) {
			return $this->controllers[$control];
		}

		$controller_filename = $this->config['controllers_path'] . $control . ".php";

		if (empty($control) OR !file_exists($controller_filename)) {
			D("500: control inexistente");
			status_code();
			die;
		}

		if (!include_once($controller_filename)) {
			D("500: control sin acceso");
			status_code();
			die;
		}

		if (!class_exists($control, false)) {
			D("500: control no definido");
			status_code();
			die;
		}

		$this->controllers[$control] = new $control;

		return $this->controllers[$control];
	}
}
