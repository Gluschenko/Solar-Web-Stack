<?php
namespace Solar;

/* Текстовые утилиты. Ни в коем случае не костыли */
class Text
{
	const LC_EN = 0;
	const LC_RU = 1;

	private static $locale = self::LC_EN;

	public static function SetLocale($lc){ return self::$locale = $lc; }
	public static function GetLocale(){ return self::$locale; }
	public static function isLocale($lc){ return self::$locale == $lc; }

	/*
	 * Склоняет слова по значению входного числа
	 * words - слова по принципу: 1 | 2,3,4 | 5,6,7,8,9
	 * num - число (int)
	 */
	public static function WordByNumber($words, $num)
	{
		$num = abs($num);

		if(sizeof($words) == 3)
		{
			if(is_int($num))
			{
				if($num >= 10 && $num <= 20)
				{
					return $words[2];
				}

				$num %= 10;

				switch ($num){
					case 0:
						return $words[2];
						break;
					case 1:
						return $words[0];
						break;
					case 2:
					case 3:
					case 4:
						return $words[1];
						break;
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
						return $words[2];
						break;
				}
			}
			else
			{
				return $words[1];
			}
		}
		elseif(sizeof($words) == 2)
		{
			$num %= 10;
			if($num == 1) {
				return $words[0];
			}
			else {
				return $words[1];
			}
		}
		return $words[0];
	}

	/* Обрезает текст на опреденный лимит*/
	public static function SubStr(string $text, int $start, int $limit, string $end = "...")
	{
		if(mb_strlen($text, "UTF-8") > $limit)
		{
			$text = mb_substr($text, $start, $limit, "UTF-8").$end;
		}

		return $text;
	}

	/* Определяет, содержит ли строка вхождение*/
	public static function Contains(string $str, string $search)
	{
		return strpos($str, $search) !== false;
	}

	/* Формирует строку из паттерна и значений: StringFormat("{0}: {1}", 1, 2);*/
	public static function Format(string $pattern, ...$keys)
	{
		$all_keys = [];
		foreach ($keys as $key => $value)
		{
			if(is_array($value)) {
				foreach ($value as $k => $v){
					$all_keys[$k] = $v;
				}
			}
			else {
				$all_keys[$key] = $value;
			}
		}

		foreach ($all_keys as $key => $value)
		{
			$pattern = str_replace("{".$key."}", $value, $pattern);
		}
		return $pattern;
	}


	/* Длина строки в UTF-8*/
	public static function Length(string $str)
	{
		return mb_strlen($str, "UTF-8");
	}

	/* Разбивает строку на символы*/
	public static function ToCharArray(string $str)
	{
		return preg_split("/(?<!^)(?!$)/u", $str);
	}

	/* Переводит UTF-8 в верхний редистр*/
	public static function ToUpperCase(string $str)
	{
		return mb_strtoupper($str, "UTF-8");
	}

	/* Переводит UTF-8 в нижний редистр*/
	public static function ToLowerCase(string $str)
	{
		return mb_strtolower($str, "UTF-8");
	}

	/* Заменяет первые буквы слов на верхний регистр */
	public static function ToNameCase(string $text)
	{
		$chars = Text::ToCharArray($text);

		for($i = 0; $i < sizeof($chars); $i++)
		{
			$up = false;

			if($i == 0) {
				$up = true;
			}
			elseif ($chars[$i - 1] == " ") {
				$up = true;
			}

			if($up) {
				$chars[$i] = Text::ToUpperCase($chars[$i]);
			}
		}

		return implode("", $chars);
	}

	/* Генерирует случайную последовательность символов */
	public static function GenerateRandomString(int $length, string $chars = "") {
		if($chars == "")$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$len = self::Length($chars);
		$result = "";
		for ($i = 0; $i < $length; $i++) {
			$result .= $chars[rand(0, $len - 1)];
		}
		return $result;
	}

	/* Хеширование текста */
	public static function HashCode(string $str)
	{
		if(self::Length($str) > 0)
		{
			$chars = str_split($str);
			$sum = 0;
			for($i = 0; $i < sizeof($chars); $i++)
			{
				if($i % 3 == 0)
					$sum += ord($chars[$i]);
				else
					$sum -= ord($chars[$i]);
			}
			$sum = abs($sum);
			$hash = $sum + sizeof($chars);
			return $hash;
		}
		return 0;
	}

	/* Преобразует число байт в наиболее удобные единицы*/
	public static function GetDataUnits($bytes, $raw = false)
	{
		$units = array("B", "KB", "MB", "GB", "TB", "PB");

		for($i = 0; $bytes >= 1024 && $i < sizeof($units) - 1; $i++ )
		{
			$bytes /= 1024;
		}

		if($raw)
		{
			return new ValueUnitPair($bytes, $units[$i]);
		}
		return round($bytes, 2)." ".$units[$i];
	}

	/* Конвертирует миллисекунды в человекопонятные отрезки времени */
	public static function GetTimeUnits($ms, $raw = false)
	{
		$units = self::isLocale(self::LC_RU) ?
			array("мс", "с", "мин", "ч",     "дн",   "мес",    "лет") :
			array("ms", "s", "min", "hours", "days", "months", "year");

		$sizes = array(
			0,
			1000,
			1000 * 60,
			1000 * 60 * 60,
			1000 * 60 * 60 * 24,
			1000 * 60 * 60 * 24 * 30,
			1000 * 60 * 60 * 24 * 365,
		);

		$t = $ms;
		$unit = $units[0];
		for($i = sizeof($sizes) - 1; $i >= 1; $i--)
		{
			if($t >= $sizes[$i])
			{
				$t /= $sizes[$i];
				$unit = $units[$i];
				break;
			}
		}

		if($raw)
		{
			return new ValueUnitPair($t, $unit);
		}
		return round($t, 1)." ".$unit;
	}

	/* Превращает число в сокращенную форму */
	public static function GetNumericSuffix($n, $raw = false)
	{
		$units = array("", "K", "M", "G", "T", "P");

		for($i = 0; $n >= 1000 && $i < sizeof($units) - 1; $i++ )
		{
			$n /= 1000;
		}
		$n = round($n);

		if($raw)
		{
			return new ValueUnitPair($n, $units[$i]);
		}
		return $n.$units[$i];
	}
}

class ValueUnitPair
{
	public $value;
	public $units;

	public function __construct($value, $units)
	{
		$this->value = $value;
		$this->units = $units;
	}

	public function __toString()
	{
		return $this->value." ".$this->units;
	}
}
