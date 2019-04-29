<?php
namespace Solar;

class Doc
{
	public static function GetFunctions()
	{
		/*
		ini_set("opcache.enable", 1);
		ini_set("opcache.save_comments", 1);
		ini_set("opcache.load_comments", 1);
		*/

		$all_functions = get_defined_functions();
		$functions = $all_functions['user'];

		$result = array();

		for($f = 0; $f < sizeof($functions); $f++)
		{
			$reflection = new \ReflectionFunction($functions[$f]);
			//
			if(!$reflection->isInternal())
			{
				$params = $reflection->getParameters();
				$params_names = array();
				//
				$file_path = self::NormalizeFileName($reflection->getFileName());
				//
				for($p = 0; $p < sizeof($params); $p++)
				{
					$params_names[] = $params[$p]->GetName();
				}
				$name = $reflection->GetName();
				$comment = self::NormalizeComment($reflection->getDocComment());

				$sign = Text::Format("{0}({1})", $name, implode(", ", $params_names));
				//
				$result[] = [
					"signature" => $sign,
					"name" => $name,
					"params" => $params_names,
					"comment" => $comment,
					"file_path" => $file_path,
					//"reflection" => $reflection,
				];
			}
		}

		return $result;
	}

	public static function GetClasses()
	{
		$namespaces = [];

		$all_classes = get_declared_classes();
		$result = [];

		foreach ($all_classes as $class)
		{
			$reflection = new \ReflectionClass($class);

			if(!$reflection->isInternal())
			{
				$file_path = self::NormalizeFileName($reflection->getFileName());

				$name = $reflection->GetName();
				$comment = self::NormalizeComment($reflection->getDocComment());

				$namespace = $reflection->getNamespaceName();

				if(!isset($namespaces[$namespace]))
				{
					$namespaces[$namespace] = [];
				}

				$reflection_properties = $reflection->getProperties();
				$reflection_methods = $reflection->getMethods();

				$properties = [];
				$methods = [];

				foreach($reflection_properties as $reflection_property)
				{
					$p_name = $reflection_property->getName();
					$p_comment = self::NormalizeComment($reflection_property->getDocComment());

					$properties[] = [
						"name" => $p_name,
						"comment" => $p_comment,
					];
				}

				foreach ($reflection_methods as $reflection_method)
				{
					$m_name = $reflection_method->getName();
					$m_comment = self::NormalizeComment($reflection_method->getDocComment());
					$m_params = [];

					$m_reflection_params = $reflection_method->getParameters();
					foreach ($m_reflection_params as $param)
					{
						$m_params[] = $param->getName();
					}

					$sign = Text::Format("{0}({1})", $m_name, implode(", ", $m_params));

					$methods[] = [
						"signature" => $sign,
						"name" => $m_name,
						"params" => $m_params,
						"comment" => $m_comment,
					];
				}

				$namespaces[$namespace][] = [
					"name" => $name,
					"namespace" => $namespace,
					"comment" => $comment,
					"file_path" => $file_path,

					"properties" => $properties,
					"methods" => $methods,
					//"reflection" => $reflection,
				];
			}
		}

		//return $result;
		ksort($namespaces);
		return $namespaces;
	}

	public static function NormalizeComment($comment)
	{
		$comment = str_replace("/**", "", $comment);
		$comment = str_replace("*/", "", $comment);
		$comment = trim($comment);
		return $comment;
	}

	public static function NormalizeFileName($file_path)
	{
		$file_path = str_replace(array('\\'), array('/'), $file_path);
		$file_path = explode($_SERVER["HTTP_HOST"], $file_path);
		$file_path = $file_path[sizeof($file_path) - 1];
		return $file_path;
	}
}