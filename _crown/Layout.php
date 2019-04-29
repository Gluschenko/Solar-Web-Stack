<?php
namespace Crown;

class Layout
{
	public static function Create()
	{
		if(Engine::GetCurrentSection() != null)
		{
			$file_name = Engine::GetCurrentSection()->layout;
			$path = Engine::GetTemplatesDir()."/".$file_name;
			if(file_exists($path))
			{
				require($path);
			}
			else
			{
				echo("No file: ".$path);
			}
		}
		else
		{
			echo "No section";
		}
	}
	
	public static function Compose($format, $object, $extra = array())
	{
		$fields = array();
		if(gettype($object) == "object")
		{
			$fields = get_object_vars($object);
		}
		if(gettype($object) == "array")
		{
			$fields = $object;
		}
		
		foreach($extra as $k => $v)
		{
			$fields[$k] = $v;
		}
		
		foreach($fields as $k => $v)
		{
			$type = gettype($v);
			
			if($type == "boolean"
			|| $type == "integer"
			|| $type == "double"
			|| $type == "float"
			|| $type == "string")
			{
				$format = str_replace("{".$k."}", $v, $format);
			}
		}
		return $format;
	}
	
	public static function CreateSectionsList($item_template)
	{
		$sections = Engine::GetSections();
		
		$res = "";
		
		foreach($sections as $key => $section)
		{
			if(!$section->hidden)
			{
				ob_start();
				require(Engine::GetTemplatesDir()."/".$item_template);
				$template_layout = ob_get_clean();
				//
				$fields = $section->GetPublicFields();
				$item = $template_layout;
				foreach($fields as $k => $v)
				{
					$type = gettype($v);
					
					if($type == "boolean"
					|| $type == "integer"
					|| $type == "double"
					|| $type == "float"
					|| $type == "string")
					{
						$item = str_replace("{".$k."}", $v, $item);
					}
				}
				
				$res .= $item."\n";
			}
		}
		
		return $res;
	}
	
	public static function CreateSectionContent()
	{
		if(Engine::GetCurrentSection() != null)
		{
			$id = Engine::GetCurrentSection()->id;
			$path = Engine::GetSectionsDir()."/".$id.".php";
			//
			if(file_exists($path))
			{
				ob_start();
				require($path);
				return ob_get_clean();
			}
			return "No such file: ".$path;
		}
		else
		{
			return "No section";
		}
	}
	
	public static function CreateCustom($template)
	{
		$path = Engine::GetTemplatesDir()."/".$template;
		//
		if(file_exists($path))
		{
			ob_start();
			require($path);
			return ob_get_clean();
		}
		return "No such file: ".$path;
	}
}

