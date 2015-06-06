<?php defined("PHRAPI") or die("Direct access not allowed!");
/**
 * Interactua con posts/posts_contents/getPostContent
 *
 * @author Enrique Velarde, twitter.com/EnriqueVelardeG
 * @copyright Tecnologías Web de México S.A. de C.V.
 *
 */
class Posts
{
	public $db;
	public $lang;
	/**
	 * @var Object instance
	 */
	private static $instance;

	public function __construct($config_index) {

		$this->db = db::getInstance($config_index);
		$this->lang = Persistent::getInstance()->lang;
		if(empty($this->lang)) $this->lang = 'es_MX';
	}

	/**
	 * Singleton pattern http://en.wikipedia.org/wiki/Singleton_pattern
	 * @return object Class Instance
	 */
	public static function getInstance($config_index = null)
	{
		if ($config_index === null || $config_index == 'default' || empty($config_index)) {
			$config_index = 0;
		}

		if (!isset(self::$instance[$config_index]) || !self::$instance[$config_index] instanceof self)
			self::$instance[$config_index] = new self($config_index);

		return self::$instance[$config_index];
	}

	public function getSQLContent($id_post = '') {
		$sql = " getPostContent({$id_post},'{$this->lang}') ";
		return $sql;
	}

	public function getPostContent($id_post = 0, $field = 'content'){
		$id_post = (int) $id_post;
		$result = $this->db->queryOne("
			SELECT
				{$field}
			FROM
				posts_contents
			WHERE
				lang = '{$this->lang}'
			AND
				id_post = '{$id_post}'
			ORDER BY
				modified_at DESC, id_content DESC
			LIMIT 1
		");
		return $result;
	}

	public function getPostContentlang($id_post = 0, $lang = 'es_MX', $field = 'content'){
		$id_post = (int) $id_post;
		$result = $this->db->queryOne("
			SELECT
				{$field}
			FROM
				posts_contents
			WHERE
				lang = '{$lang}'
			AND
				id_post = '{$id_post}'
			ORDER BY
				modified_at DESC, id_content DESC
			LIMIT 1
		");
		return $result;
	}

	public function getPostID($description = ''){
		$this->db->query("INSERT INTO posts (label,created_at,status) VALUES ('{$description}',NOW(),'Activo')");
		$id_post = $this->db->getLastID();
		return $id_post;
	}

	public function getListCategories($parent = 'NULL', $prefix = '', $array = array()) {
		$config = $GLOBALS['config'];

		$sql_parent = "";
		if ($parent == 'NULL') {
			$sql_parent = "c.id_category_parent IS NULL";
		} else {
			$sql_parent = "c.id_category_parent = '{$parent}'";
		}
		$categories = $this->db->queryAllSpecial("
			SELECT
				getPostContent(c.id_post_title,'{$this->lang}') as label,
				c.id_category as id
			FROM
				categories c
			WHERE
				c.id_website = '{$config['website']}'
				AND
				{$sql_parent}
			ORDER BY
				position
		");

		foreach($categories as $id => $label) {
			$array[$id] = $prefix . $label;
			$array = $this->getListCategories($id, $prefix . $label . "/", $array);
		}
		return $array;
	}

}
