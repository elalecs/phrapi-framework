<?php defined('PHRAPI') or die("Direct access not allowed!");
/**
 * Class to build HTML elements from PHP
 *
 * @author Alejandro Galindo, twitter.com/elalecs
 * @copyright Tecnologías Web de México S.A. de C.V.
 *
 */
final class Html
{
	/**
	 * <select><option>
	 *
	 * @static
	 * @param array [idname=>string, class=>string, selected=>mixed, options=>array]
	 * @return boolean
	 */
	static function Select($config)
	{
		$config = Html::getConfig($config);

		if (isset($config['value']) && !empty($config['value']) && !isset($config['selected']))
		{
			$config['selected'] = $config['value'];
		}
		unset($config['value']);

		$options = array();
		if (isset($config['options']))
		{
			$value_selected = "";
			if (isset($config['selected']) && !empty($config['selected']))
				$value_selected = $config['selected'];

			// 1-9 ó 1990-1900
			if (is_string($config['options']) && preg_match('/^\d+-\d+$/', $config['options']))
			{
				$tmp = explode("-", $config['options']);

				if ($tmp[1] > $tmp[0])
					for($i = $tmp[0]; $i <= $tmp[1]; $i++)
					{
						$selected = "";
						if ($i == $value_selected)
							$selected = " selected";
						$options[] = "<option value=\"{$i}\"{$selected}>{$i}</option>";
					}
				else
					for($i = $tmp[0]; $i >= $tmp[1]; $i--)
					{
						$selected = "";
						if ($i == $value_selected)
							$selected = " selected";
						$options[] = "<option value=\"{$i}\"{$selected}>{$i}</option>";
					}
			}
			elseif (is_array($config['options']))
				foreach ($config['options'] as $value => $option)
				{
					$selected = "";
					if ($value == $value_selected)
						$selected = " selected";

					$options[] = "<option value=\"{$value}\"{$selected}>{$option}</option>";
				}
		}
		unset($config['options']);

		$attributes = array();
		foreach ($config as $attr => $value)
		{
			if ($attr == "selected")
				continue;

			$attributes[] = "{$attr}=\"{$value}\"";
		}

		echo "<select ".implode(" ", $attributes).">\n".implode("\n", $options)."\n</select>\n";

		return true;
	}

	static function Options($options = array(), $value_selected = null) {
		if (!is_array($options)) {
			return false;
		}

		$options_str = "";
		foreach ($options as $value => $option)
		{
			if (is_string($value) && is_array($option)) {
				$options_str[] = "<optgroup label=\"{$value}\">";
				foreach ($option as $_value => $_option) {
					$selected = "";
					if (is_scalar($value_selected) && $_value == $value_selected)
						$selected = " selected";
					if (is_array($value_selected) && in_array($_value, $value_selected))
						$selected = " selected";

					$options_str[] = "<option value=\"{$_value}\"{$selected}>{$_option}</option>";
				}
				$options_str[] = "</optgroup>";
			}
			elseif (is_numeric($value) && is_object($option)) {
				if (isset($option->id) && isset($option->label)) {
					$selected = ($value_selected == $option->id) ? ' selected' : '';
					$options_str[] = "<option value=\"{$option->id}\"{$selected}>{$option->label}</option>";
				}
			}
			elseif (is_numeric($value) && is_array($option)) {
				if (isset($option['id']) && isset($option['label'])) {
					$selected = ($value_selected == $option['id']) ? ' selected' : '';
					$options_str[] = "<option value=\"{$option['id']}\" {$selected}>{$option['label']}</option>";
				}
			}
			else {
				$selected = "";
				if (is_scalar($value_selected) && $value == $value_selected)
					$selected = " selected";
				if (is_array($value_selected) && in_array($value, $value_selected))
					$selected = " selected";

				$options_str[] = "<option value=\"{$value}\"{$selected}>{$option}</option>";
			}
		}
		if (is_array($options_str)) {
			echo implode("\n", $options_str);
			return true;
		}

		return true;
	}

	/**
	 * <input>
	 *
	 * @static
	 * @uses Html::Input(array(id => email, className => 'myclass', type => hidden, value => $myvar))
	 * @param array All the config
	 * @return boolean
	 */
	static function Input($config)
	{
		$config = Html::getConfig($config + array(
			"type" => "text"
		));

		$attributes = array();
		foreach ($config as $attr => $value)
		{
			$attributes[] = "{$attr}=\"{$value}\"";
		}

		echo "<input ".implode(" ", $attributes)." />";

		return true;
	}

	/**
	 * <input>
	 *
	 * @static
	 * @uses Html::Checkbox(array(id => email, className => 'myclass', type => hidden, value => $myvar))
	 * @param array All the config
	 * @return boolean
	 * @author OLAF
	 */
	static function Checkbox($config) {
		$config = Html::getConfig($config + array(
			"type" => "checkbox"
		));

		$attributes = array();
		foreach ($config as $attr => $value)
		{
			$attributes[] = "{$attr}=\"{$value}\"";
		}

		echo "<input ".implode(" ", $attributes)." />";

		return true;
	}

	/**
	 * <texteare>
	 *
	 * @static
	 * @uses Html::Textarea(array(id => email, className => 'myclass', value => $myvar))
	 * @param array All the config
	 * @return boolean
	 */
	static function Textarea($config)
	{
		$config = Html::getConfig($config);

		$textarea_value = "";
		if (isset($config['value']))
			$textarea_value = $config['value'];

		$attributes = array();
		foreach ($config as $attr => $value)
		{
			if ($attr == "value")
				continue;

			$attributes[] = "{$attr}=\"{$value}\"";
		}

		echo "<textarea ".implode(" ", $attributes).">".$textarea_value."</textarea>";

		return true;
	}

	/**
	 * Prepare the config array with the default values
	 *
	 * @param array $config
	 * @return array
	 */
	private static function getConfig($config = null) {
		$default = array(
			"id" => "",
			"name" => "",
			"class" => "",
			"value" => ""
		);

		if (!is_array($config))
		{
			return $config;
		}

		if (isset($config['idname'])) {
			$config['id'] = $config['idname'];
			$config['name'] = $config['idname'];
			unset($config['idname']);
		}

		$config = $config + array(
			"id" => "",
			"name" => "",
			"class" => "",
			"value" => ""
		);

		return $config;
	}

	/**
	 * Do a back move into the browser, but first show a message
	 *
	 * @uses Html::Back("ups, an error!");
	 *
	 * @param string Message to use
	 */
	static function Back($message)
	{
		echo "<script type=\"text/javascript\">alert(\"$message\");window.history.back();</script>\n";
		exit();
	}

	static function safe($string, $allowed_tags = "<h1><h2><h3><h4><p><span><a><br><hr><ul><ol><li><i><em><b><strong><table><tr><td><th><tbody><thead>", $allow_attrs = false) {
		$string = strip_tags($string, $allowed_tags);
		$string = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>'.$string;
		$dom = new DOMDocument;
		@$dom->loadHTML($string);
		if(!$allow_attrs) {
			$xpath = new DOMXPath($dom);
			$nodes = $xpath->query('//@*');
			foreach ($nodes as $node) {
				$node->parentNode->removeAttribute($node->nodeName);
			}
		}
		$string = preg_replace('/^<!DOCTYPE.+?>/', '',
			str_replace(
				array('<html>', '</html>', '<head>', '</head>', '<meta>', '</meta>', '<body>', '</body>'),
				array('', '', '', '', '', '', '', ''),
				$dom->saveHTML()
			)
		);

		return $string;
	}
}
