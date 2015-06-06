<?php
/**
 * PHP Raccoon API Framework
 *
 * @author Alejandro Galindo, twitter.com/elalecs
 * @copyright Tecnologías Web de México S.A. de C.V.
 */

$_flags = 1;

define("PHRAPI_INIT_TIME", microtime(true));

define("PHRAPI", ++$_flags);

define("PHRAPI_NAME", "PHP Raccoon API Framework");

define("PHRAPI_DEBUG_FLAG_BODY", PHRAPI << ++$_flags);

define("PHRAPI_DEBUG_FLAG_HEADER", PHRAPI << ++$_flags);

define("PHRAPI_DEBUG_FLAG_LOG", PHRAPI << ++$_flags);

define("PHRAPI_DEBUG_FLAG_ALL", PHRAPI_DEBUG_FLAG_BODY | PHRAPI_DEBUG_FLAG_HEADER | PHRAPI_DEBUG_FLAG_LOG);

define("PHRAPI_DEBUG_FLAG_NONE", 0);

define("PHRAPI_DEBUG", PHRAPI_DEBUG_FLAG_BODY);

$base_path = dirname($_SERVER['SCRIPT_FILENAME']) .  (!file_exists('config.php') && file_exists('phrapi/config.php') ? '/phrapi/' : '');
if (!preg_match('/\/$/', $base_path)) $base_path .= '/';

define("PHRAPI_PATH", $base_path);

define("DS", DIRECTORY_SEPARATOR);

header("X-Framework: " . PHRAPI_NAME);

session_start();

/**
 * Shutdown
 */
//register_shutdown_function('logEnd');

function stopwatch() {
	if (!isset($GLOBALS['internal_stopwatch_start'])) {
		$GLOBALS['internal_stopwatch_start'] = microtime(true);
	}
	$start = $GLOBALS['internal_stopwatch_start'];
	$actual = microtime(true);
	$lapsed = $actual - $start;

	$hrs = "00";
	$min = "00";
	$sec = "00";
	$mic = 0;

	$hrs = floor($lapsed / 3600);
	$lapsed -= 3600 * $hrs;
	$min = floor($lapsed / 60);
	$lapsed -= 60 * $min;
	$sec = floor($lapsed);
	$mic = substr(strrchr($lapsed, "."), 1);

	if ($hrs < 10) {
		$hrs = "0" . $hrs;
	}
	if ($min < 10) {
		$min = "0" . $min;
	}
	if ($sec < 10) {
		$sec = "0" . $sec;
	}

	Console("Stopwatch: {$hrs}:{$min}:{$sec}.{$mic}");
}

/**
 * End to write in this call to the log file
 *
 * @return void
 */
function logEnd()
{
	$tmp_time = (microtime(true) - PHRAPI_INIT_TIME);

	// http://www.ibm.com/developerworks/opensource/library/os-php-v521/
	$mem_app = bytesToHuman(function_exists('memory_get_usage') ? memory_get_usage() : 0);
	$mem_real = bytesToHuman(function_exists('memory_get_usage') ? memory_get_usage(1) : 0);

	$msg = array();
	$msg[] = "Elapsed Estimated Time: {$tmp_time} s";

	// http://blog.rompe.org/node/85
	if (function_exists("getrusage"))
	{
		$data = getrusage();
		$cpuu = ($data['ru_utime.tv_sec'] + $data['ru_utime.tv_usec'] / 1000000);
		$cpus = ($data['ru_stime.tv_sec'] + $data['ru_stime.tv_usec'] / 1000000);
		$renderedtime = round(microtime(1) - PHRAPI_INIT_TIME, 6);

		$msg[] = "Elapsed Time: {$renderedtime}";
	}

	$msg[] = "Memory App: {$mem_app}";
	$msg[] = "Memory Real: {$mem_real}";

	$msg = join(", ", $msg);

	trigger_error($msg);
}

/**
 * Transform a bytes amount into a human readable format
 *
 * @param int Bytes amount
 * @return string Readable format
 */
function bytesToHuman($int)
{
	if ($int < 1024)
		$int .= " bytes";
	else if (($int / 1024) < 1024)
		$int = round($int / 1024) ." KB";
	else if ((($int / 1024) / 1024) < 1024)
		$int = round(($int / 1024) / 1024) ." MB";

	return $int;
}

/**
 * Carga librerías en el entorno de ejecución, puede estar almacenadas en /libs/ o en /rapi/libs/
 *
 * Referencia: PHP 5 Objects, Patterns, and Practice, Chapter 5, PHP and Packages
 *
 * @param string Nombre de la clase a cargar
 * @return void
 */
spl_autoload_register(function($classname)
{
	$paths = array(
		"phrapi_framework_libs",
		"phrapi_libs",
		"phrapi_controllers",
		"framework_libs",
		"libs",
		"controllers"
	);

	$loaded = false;
	$class_path = "";

	foreach($paths as $path) {
		$class_path = $path . DS . $classname;
		$class_path = preg_replace('/\_/', DS, $class_path);
		$class_path .= ".php";
		if (file_exists($class_path) && is_readable($class_path)) {
			require_once $class_path;
			$loaded = true;
		}
	}

	if (!$loaded) {
		$paths = join("','", $paths);
		trigger_error("Can't loaded, Paths: '{$paths}'");
	}

	if (!class_exists($classname)) {
		trigger_error("Not exists, Class: {$classname}, Path: {$class_path}");
	}
});

/**
 * http://www.restapitutorial.com/httpstatuscodes.html
 * @param number $code
 */
function status_code($code = 500) {
	$code = (int) $code;
	$codes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing (WebDAV)',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Not-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status (WebDAV)',
		208 => 'Already Reported (WebDAV)',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'User Proxy',
		306 => 'Unused',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect (experimental)',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout ',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'Im a teapot (RFC 2324)',
		420 => 'Enhance Your Calm (Twitter)',
		422 => 'Unprocessable Entity (WebDAV)',
		423 => 'Locked (WebDAV)',
		424 => 'Failed Dependency (WebDAV)',
		425 => 'Reserved for WebDAV',
		426 => 'Upgrade Required',
		428 => 'Precondition Required (draft)',
		429 => 'Too Many Requests (draft)',
		431 => 'Request Header Fields Too Large (draft)',
		444 => 'No Response (Nginx)',
		449 => 'Retry With (Microsoft)',
		450 => 'Blocked by Windows Parental Controls (Microsoft)',
		499 => 'Client Closed Request (Nginx)',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates (Experimental)',
		507 => 'Insufficient Storage (WebDAV)',
		508 => 'Loop Detected (WebDAV)',
		509 => 'Bandwidth Limit Exceeded (Apache)',
		510 => 'Not Extended',
		511 => 'Network Authentication Required (draft)',
		598 => 'Network read timeout error',
		599 => 'Network connect timeout error',
	);

	if (!array_key_exists($code, $codes)) {
		$code = 500;
	}

	if (!headers_sent()) {
		if (!function_exists('http_response_code')) {
			header(':', true, $code);
		} else {
			http_response_code($code);
		}
		//header("HTTP/1.0 {$code} {$codes[$code]}");
		//header("Status: {$code} {$codes[$code]}");
	}

	if ($code >= 400) {
		die;
	}
}

/**
 * Do a redirect into the browser user
 *
 * @param mixed $uri
 * @return void
 */
function redirect($final_uri) {
	if (!preg_match('/^http/', $final_uri)) {
		$final_uri = $GLOBALS['config']['url'] . $final_uri;
	}

	if (headers_sent()) {
		echo "<meta http-equiv=\"refresh\" content=\"0;url={$final_uri}\" />";
		echo "<script>top.location.href = '{$final_uri}';</script>";
	} else {
		header("Location: {$final_uri}");
	}

	exit();
}

/**
 * Muestra un mensaje (debug)
 *
 * @param mixed $data
 * @param string $type (normal|error)
 */
function Console($data, $type = "normal") {
	$data = print_r($data, true);

	$style = "background:#FFFFCC; color:#1E1E1E;";
	if ($type == "error") {
		$style = "background:#FFCC99; color:#FF0000;";
	}

	$backtrace = "";
	$last_func = "";
	foreach(debug_backtrace() as $_trace) {
		if (isset($_trace['function']) && in_array($_trace['function'], array('Console'))) {
			continue;
		}
		$backtrace[] = basename($_trace['file']) . ":" . $_trace['line'];
	}
	krsort($backtrace);
	$backtrace = join(" > ", $backtrace);

	if (PHRAPI_DEBUG_FLAG_HEADER === (PHRAPI_DEBUG & PHRAPI_DEBUG_FLAG_HEADER) && !headers_sent()) {
		ConsoleHeader($backtrace);
		foreach(preg_split('/\n/', $data) as $data_line) {
			ConsoleHeader($data_line);
		}
	}

	if (PHRAPI_DEBUG_FLAG_BODY === (PHRAPI_DEBUG & PHRAPI_DEBUG_FLAG_BODY)) {
		$data = trim($data);
		$data = $backtrace . "\n" . $data . "\n";
		if (!isset($_GET['resource'])) {
			$data = htmlspecialchars($data);
			$data = "<p style=\"white-space:pre-wrap; font-family:'Menlo', 'Lucida Console'; text-align:left; padding:10px; font-size:xx-small; {$style}\">{$data}</p>";
		}
		print($data);
		flush();
	}
}
function ConsoleHeader($data) {
	$index = ConsoleHeaderIndex();
	header("X-PHRAPI-DEBUG-{$index}: $data");
}
function ConsoleHeaderIndex() {
	if (!isset($GLOBALS['debug-header-index'])) {
		$GLOBALS['debug-header-index'] = 0;
	}
	$GLOBALS['debug-header-index']++;
	$index = (string) $GLOBALS['debug-header-index'];
	if (strlen($index) < 3)
		$index = str_repeat('0', 3 - strlen($index)) . $index;

	return $index;
}
/**
 * Abreviacion de Console
 * @param mixed $data
 */
function D($data) {
	Console($data);
}
/**
 * Abreviacion de Console pero para errores
 * @param mixed $data
 */
function E($data) {
	Console($data, "error");
}
/**
 * Abreviacion de Console pero para errores finales
 * @param mixed $data
 */
function F($data) {
	Console($data, "error");
	exit;
}

/**
 * Obtiene un recurso remoto,
 *
 * @param array $config [url=string, method=get|post, return=raw|json, args=hash]
 */
function getRemote($config) {
	$user_agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_2) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16 Safari/535.7";
	if (is_string($config)) {
		$config = (object) array(
			'url' => $config,
			'method' => 'get',
			'return' => 'raw',
			'cookie' => '',
			'cookie_file' => '',
			'cookie_jar' => '',
			'user_agent' => $user_agent,
			'referer' => '',
			'debug' => false,
			'save' => false,
			'args' => array()
		);
	} else {
		$config = (object) ($config + array(
			'url' => '',
			'method' => 'get',
			'return' => 'raw',
			'cookie' => '',
			'cookie_file' => '',
			'cookie_jar' => '',
			'user_agent' => $user_agent,
			'referer' => '',
			'debug' => false,
			'save' => false,
			'args' => array()
		));
	}

	if (empty($config->url)) {
		return false;
	}

	$config->original_url = $config->url;

	if (sizeof($config->args) > 0 && $config->method == 'get') {
		$args = array();
		foreach($config->args as $arg_name => $arg_value) {
			$args[] = "{$arg_name}=" . urlencode($arg_value);
		}
		$config->url .= "?" . join("&", $args);
	}

	if ($config->debug) {
		D($config);
	}

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $config->url);
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_USERAGENT, $config->user_agent);
	if (!empty($config->referer)) {
		curl_setopt($curl, CURLOPT_REFERER, $config->referer);
	}
	if (preg_match('/$https/', $config->url)) {
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	}
	if (!empty($config->cookie)) {
		curl_setopt($curl, CURLOPT_COOKIE, $config->cookie);
	}
	if (!empty($config->cookie_file)) {
		curl_setopt($curl, CURLOPT_COOKIEFILE, $config->cookie_file);
	}
	if (!empty($config->cookie_jar)) {
		curl_setopt($curl, CURLOPT_COOKIEJAR, $config->cookie_jar);
	}
	if (sizeof($config->args) > 0 && $config->method == 'post') {
		curl_setopt($curl, CURLOPT_POSTFIELDS, $config->args);
	}
	$response = curl_exec($curl);

	$response_content_type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

	curl_close($curl);

	if (!empty($config->save)) {
		if ((file_exists($config->save) && is_writable($config->save)) OR is_writable(dirname($config->save))) {
			if (file_put_contents($config->save, $response)) {
				if ($config->debug) {
					D("Se guardó '{$config->save}'");
				}
			} else {
				if ($config->debug) {
					D("Error al guardar '{$config->save}'");
				}
			}
		}
	}

	if ($config->return == "json" || preg_match('/application\/json/', $response_content_type)) {
		return json_decode($response);
	}

	return $response;
}

/**
 * Obtiene un JSON remoto
 *
 * @param array $config [url=string, method=get|post, args=hash]
 */
function getRemoteJSON($config = array()) {
	if (is_string($config)) {
		$config = array(
			'url' => $config,
			'method' => 'get',
			'return' => 'json',
			'args' => array()
		);
	} else {
		$config = $config + array(
			'url' => '',
			'method' => 'get',
			'return' => 'json',
			'args' => array()
		);
	}

	return getRemote($config);
}

/**
 * Obtiene un valor de un arreglo (simple o multidimencional) o un objeto (simple o compuesto)
 *
 * Ejemplo:
 * <code>
 *   $id = getValueFrom($_POST, "id", 0);
 *
 *   $x = getValueFrom(array("a" => array("b" => array("c" => 123))), "a.b.c");
 * </code>
 *
 * @todo make a xpath implementation
 * @return mixed
 * @param array $data
 * @param string $path
 * @param mixed $default[optional]
 */
function getValueFrom($data, $path = null, $default = null, $sanitize = null)
{
	if (empty($path) || $path == null) {
		return Sanitize::by($default, $sanitize);
	}

	if (!is_array($data) && !is_object($data)) {
		return Sanitize::by($default, $sanitize);
	}

	// without a path
	if (strpos($path, ".") === false) {
		if (is_array($data) && isset($data[$path]))
			return Sanitize::by($data[$path], $sanitize);

		if (is_object($data) && isset($data->$path))
			return Sanitize::by($data->$path, $sanitize);

		return Sanitize::by($default, $sanitize);
	}

	// with a path
	$value = $data;
	foreach(explode(".", $path) as $crumb) {
		if (is_array($value) && isset($value[$crumb])) {
			$value = $value[$crumb];
		}
		elseif (is_object($value) && isset($value->$crumb)) {
			$value = $value->$crumb;
		} else {
			Sanitize::by($default, $sanitize);
		}
	}

	return Sanitize::by($value, $sanitize);
}

/**
 * Regresa un hash, se puede configurar pasando un arreglo como argumento.
 *
 * Ejemplo:
 * <code>
 * $hash = getHash($_POST, array(
 *   "arg1" => FILTER_SANITIZE_STRING,
 *   "arg2" => FILTER_SANITIZE_STRING
 * ));
 * $hash = getHash($_POST, array(
 *   array(
 *     "name" => "id",
 *     "default" => 0,
 *     "type" => "int"
 *   ),
 *   array(
 *     "name" => "name",
 *     "default" => "",
 *     "sanitize" => FILTER_SANITIZE_STRING
 *     )
 * ));
 * </code>
 *
 * @param array $from [$_POST, $_GET, $_REQUEST, $_SERVER, $_SESSION, etc]
 * @param array $config [[name=>string, default=>mixed, type=[string, integer, float, boolean], sanitize=>FILTER_SANITIZE_]]
 * </p>
 */
function getHash($from = null, $config = null) {
	// argumentos invalidos?
	if (!is_array($from) || !is_array($config)) {
		return null;
	}

	$data = array();
	foreach($config as $param_key => $param) {
		$_data = null;

		// se intenta accedor al valor por <nombre>:<sanitizacion>
		if (is_string($param_key) && !is_array($param) && !is_object($param)) {
			$data[$param_key] = getValueFrom($from, $param_key, null, $param);
		}

		// se intenta acceder al valor esquema en arreglo
		if (is_integer($param_key) && is_array($param)) {
			$name = getValueFrom($param, 'name', '');
			$default = getValueFrom($param, 'default', '');
			$sanitize = getValueFrom($param, 'sanitize', FILTER_DEFAULT);
			$type = getValueFrom($param, 'type', 'string');
			if (!empty($name)) {
				$_data = getValueFrom($from, $name, $default, $sanitize);
			}

			if (!empty($type) && is_string($type)) {
				settype($_data, $type);
			}

			$data[$name] = $_data;
		}
	}

	return $data;
}


/**
 * Pretty-print JSON string
 *
 * Use 'format' option to select output format - currently html and txt supported, txt is default
 * Use 'indent' option to override the indentation string set in the format - by default for the 'txt' format it's a tab
 *
 * http://www.molengo.com/blog.article/id/215/title/php-json-pretty-print
 *
 * @param string $json Original JSON string
 * @param array $options Encoding options
 * @return string
 */
function json_pretty($json, $options = array())
{
	$tokens = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
	$result = '';
	$indent = 0;

	$format = 'txt';

	//$ind = "\t";
	$ind = "    ";

	if (isset($options['format'])) {
		$format = $options['format'];
	}

	switch ($format) {
		case 'html':
			$lineBreak = '<br />';
			$ind = '&nbsp;&nbsp;&nbsp;&nbsp;';
			break;
		default:
		case 'txt':
			$lineBreak = "\n";
			//$ind = "\t";
			$ind = "    ";
			break;
	}

	// override the defined indent setting with the supplied option
	if (isset($options['indent'])) {
		$ind = $options['indent'];
	}

	$inLiteral = false;
	foreach ($tokens as $token) {
		if ($token == '') {
			continue;
		}

		$prefix = str_repeat($ind, $indent);
		if (!$inLiteral && ($token == '{' || $token == '[')) {
			$indent++;
			if (($result != '') && ($result[(strlen($result) - 1)] == $lineBreak)) {
				$result .= $prefix;
			}
			$result .= $token . $lineBreak;
		} elseif (!$inLiteral && ($token == '}' || $token == ']')) {
			$indent--;
			$prefix = str_repeat($ind, $indent);
			$result .= $lineBreak . $prefix . $token;
		} elseif (!$inLiteral && $token == ',') {
			$result .= $token . $lineBreak;
		} else {
			$result .= ( $inLiteral ? '' : $prefix ) . $token;

			// Count # of unescaped double-quotes in token, subtract # of
			// escaped double-quotes and if the result is odd then we are
			// inside a string literal
			if ((substr_count($token, "\"") - substr_count($token, "\\\"")) % 2 != 0) {
				$inLiteral = !$inLiteral;
			}
		}
	}
	return $result;
}

/**
* Get the user IP
*
* @uses $control->getUserIP();
*
* @return string The user IP
*/
function getUserIP() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	elseif (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	elseif (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	elseif (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "0.0.0.0";

	return ($ip);
}

/*
 * Sort an array by the key length
 *
 * http://stackoverflow.com/questions/3955536/php-sort-hash-array-by-key-length
 */
function sortByLengthReverse($a, $b){
	return strlen($b) - strlen($a);
}

/*
 * Search for a value in a field into an array
 * 
 */
function in_array_field($needle, $needle_field, $haystack, $object = true) {
	foreach ($haystack as $item_index => $item)
		if (isset($item->$needle_field) && $item->$needle_field === $needle)
			return ($object) ? $item : $item_index;

	return null;
} 
