<?php
/**
 * An object as a wrapper
 *
 * @author Enrique Velarde, twitter.com/EnriqueVelardeG
 * @copyright Tecnologías Web de México S.A. de C.V.
 *
 */
class Container
{
	/**
	 * @var Object instance
	 */
	private static $instance = array();

	protected $values = array();

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

	function __set($id, $value)
	{
		$this->values[$id] = $value;
	}

	function __get($id) {
		return is_callable($this->values[$id]) ? $this->values[$id]($this) : $this->values[$id];
	}

	function __isset($id) {
		return isset($this->values[$id]);
	}
}
