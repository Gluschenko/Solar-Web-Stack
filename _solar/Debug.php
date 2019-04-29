<?php
namespace Solar;

class Debug
{
	const NO_OPTIONS = 0;
	const LOG_STACKTRACE = 1;

	public static function Log($message, $options = self::LOG_STACKTRACE)
	{
		$data = ["message" => $message];
		$format = "{message}";
		if($options & self::LOG_STACKTRACE)
		{
			$data["call_trace"] = self::GetSmallCallTrace();
			$format .= " | Stack trace: {call_trace}";
		}

		$text = Text::Format($format, $data);

		error_log($text);
		return $text;
	}

	public static function Deprecated($alternative = "")
	{
		return ($alternative == "") ? self::Log("Method is deprecated") : self::Log("Method is deprecated. Use it ".$alternative);
	}

	public static function GetCallTrace()
	{
		return implode("\n", self::GetCallStackList(true));
	}

	public static function GetSmallCallTrace()
	{
		return implode(" => ", self::GetCallStackList(false));
	}

	public static function GetCallStackList($file_paths)
	{
		$e = new \Exception();
		$trace = explode("\n", $e->getTraceAsString());

		$trace = array_reverse($trace);              // reverse array to make steps line up chronologically
		array_shift($trace);                         // remove {main}
		array_pop($trace);                           // remove call to this method
		$length = sizeof($trace);
		$result = [];

		for ($i = 0; $i < $length; $i++)
		{
			$entry = substr($trace[$i], strpos($trace[$i], " "));

			if(!$file_paths)
			{
				$entry = substr($entry, strpos($entry, ": "));
				$entry = substr($entry, 2);
			}

			$entry = str_replace($_SERVER['DOCUMENT_ROOT'], "", $entry);
			$result[] = $entry;
		}

		return $result;
	}
}