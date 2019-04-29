<?php
namespace Solar;

class Core
{
	const NAME = "Solar Core";
	const VERSION = "0.1";
	const DefaultComponents = [
		"SQL", "URL", "Debug", "Time", "StopWatch", "Color", "Text", "TextFilters", "Cookies", "JSON", "VK", "Doc", "UserAgent"
	];

	public static function Run($components = array())
	{
		if(sizeof($components) == 0)$components = self::DefaultComponents;

		foreach ($components as $component_name)
		{
			$name = $component_name.".php";
			require_once($name);
		}
	}

	public static function Hello(){
		echo("
<pre>
".self::NAME." v".self::VERSION."

#####  #####  #       ###   ####    
#      #   #  #      #   #  #   #         |\      _,,,---,,_
#####  #   #  #      #####  ####    ZZZzz /,`.-'`'    -.  ;-;;,_
    #  #   #  #      #   #  #   #        |,4-  ) )-,_. ,\ (  `'-'
#####  #####  #####  #   #  #   #       '---''(_/--'  `-'\_) 

</pre>
		");
	}
}