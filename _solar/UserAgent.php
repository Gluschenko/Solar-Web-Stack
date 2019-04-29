<?php
namespace Solar;


class UserAgent
{
	/* Определяет ОС по User Agent */
	public static function GetOS($ua = "")
	{
		if($ua == "")$ua = $_SERVER['HTTP_USER_AGENT'];

		$assocs = array(
			"Windows 95", "Windows 95",
			"Windows 98", "Windows 98",
			"Windows ME", "Windows ME",
			"Windows NT 5.0", "Windows 2000",
			"Windows NT 5.1", "Windows XP",
			"Windows NT 5.2", "Windows 2003",
			"Windows NT 6.0", "Windows Vista",
			"Windows NT 6.1", "Windows 7",
			"Windows NT 6.2", "Windows 8",
			"Windows NT 6.3", "Windows 8.1",
			"Windows NT 10.0", "Windows 10",
			"Windows Phone 8.1", "Windows Phone 8.1",
			"Windows Phone 10", "Windows Phone 10",
			"Windows Phone", "Windows Phone",
			"Macintosh", "macOS",
			"Android 2", "Android 2",
			"Android 3", "Android 3",
			"Android 4", "Android 4",
			"Android 4.4", "Android 4.4",
			"Android 5", "Android 5",
			"Android 6", "Android 6",
			"Android 7", "Android 7",
			"Android 8", "Android 8",
			"Android 9", "Android 9",
			"Android", "Android",
			"Ubuntu", "Ubuntu",
			"Linux", "Linux",
			"iPhone OS 10", "iOS 10",
			"iPhone OS 9", "iOS 9",
			"iPhone OS 8", "iOS 8",
			"iPhone OS 7", "iOS 7",
			"iPhone OS 6", "iOS 6",
			"iPhone OS 5", "iOS 5",
			"iPhone OS 4", "iOS 4",
			"iPhone", "iOS",
			"Series 60", "Symbian",
		);

		$name = "";
		for($i = 0; $i < sizeof($assocs); $i += 2)
		{
			if(stristr($ua, $assocs[$i + 0]) !== false)
			{
				$name = $assocs[$i + 1];
				break;
			}
		}

		$arch = "";
		if(self::isOSx64($ua))
		{
			$arch = " (x64)";
		}

		if($name != "")
		{
			return $name.$arch;
		}

		return 'Unknown';
	}

	public static function isOSx64($ua = "") : bool
	{
		if($ua == "")$ua = $_SERVER['HTTP_USER_AGENT'];

		$arch_patterns = array(
			"Win64",
			"WOW64",
			"x86_64",
			"x64",
		);

		for($i = 0; $i < sizeof($arch_patterns); $i++)
		{
			if(stristr($ua, $arch_patterns[$i]) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/* Определяет браузер по User Agent */
	public static function GetBrowser($ua = "")
	{
		if($ua == "")$ua = $_SERVER['HTTP_USER_AGENT'];

		$assocs = array(
			"YaBrowser", "Yandex.Browser",
			"OPR", "Opera",
			"Amigo", "Amigo",
			"Vivaldi", "Vivaldi",
			"UCBrowser", "UC Browser",
			"Edge", "Edge",
			"Chrome", "Chrome", //Все хром-подобные выше этой позиции
			"Firefox", "Firefox",
			"Opera", "Opera",
			"Opera Mini", "Opera Mini",
			"IEMobile", "IE Mobile",
			"Trident/6", "IE 10",
			"Trident/7", "IE 11",
			"MSIE 6", "IE 6",
			"MSIE 7", "IE 7",
			"MSIE 8", "IE 8",
			"MSIE 9", "IE 9",
			"MSIE 10", "IE 10",
			"MSIE 11", "IE 11",
			"MSIE 12", "IE 12",
			"Trident", "IE",
			"AppleWebKit", "Safari",
			"Safari", "Safari",
		);

		for($i = 0; $i < sizeof($assocs); $i += 2)
		{
			if(stristr($ua, $assocs[$i + 0]))return $assocs[$i + 1];
		}

		return 'Unknown';
	}
}