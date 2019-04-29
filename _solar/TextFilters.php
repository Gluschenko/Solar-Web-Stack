<?php
namespace Solar;

/* Текстовые фильтры.
 * Вынесены в отдельный класс, чтобы отличасть анализ и преорбразование
 * текста от прямого вмешательства в значение содержимого.
 * */
class TextFilters
{
	/* Вырезает HTML из текста */
	public static function StripHTML($text, $allowable_tags = array())
	{
		$allowable_tags_str = "";
		foreach($allowable_tags as $tag_name)
		{
			$allowable_tags_str .= "<".$tag_name.">";
		}

		$out = strip_tags($text, $allowable_tags_str);
		$out = str_replace("	", "", $out);
		$out = str_replace("'", "", $out);
		$out = str_replace("\"", "", $out);
		$out = str_replace("\r", "", $out);
		$out = str_replace("\n", " ", $out);
		return $out;
	}

	/* Конвертирует текст пользователя в представление, безопасное для отображения в HTML */
	public static function UserText($text)
	{
		// Не понимаю я эти ваши регэкспы
		// Вставка ссылок
		//$text = preg_replace("#(https?|ftp)://\S+[^\s.,> )\];'\"!?]#", '<a class="link text" target="_blank" href="\\0">\\0</a>', $text);
		$text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/i", "$1$2[a]$3[t]$3[/a]", $text); // поиск http://
		$text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/i", "$1$2[a]http://$3[t]$3[/a]", $text); // поиск www
		$text = preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1[a]mailto:$2@$3[t]$2@$3[/a]", $text); // поиск почтового адреса
		//
		$keys = array(
			array("<<", "«"),
			array(">>", "»"),
			array("<", "&lt;"),
			array(">", "&gt;"),
			array("\n", "<br/>"),
			//
			array("--", "—"),
			//BB-коды
			array("[a]", "<a target='_blank' href='"),
			array("[t]", "'>"),
			array("[/a]", "</a>"),
			array("[s]", "<s>"),
			array("[/s]", "</s>"),
			array("[b]", "<b>"),
			array("[/b]", "</b>"),
			array("[i]", "<i>"),
			array("[/i]", "</i>"),
		);

		for($i = 0; $i < sizeof($keys); $i++)
		{
			$text = str_replace($keys[$i][0], $keys[$i][1], $text);
		}

		return $text;
	}

	/* Конвертация */
	public static function HTML2Text($text)
	{
		$keys = array(
			array("<", "&lt;"),
			array(">", "&gt;"),
			array("\n", "<br/>"),
			array("\t", "<span style='width: 2em; display: inline-block;'></span>"),
		);

		for($i = 0; $i < sizeof($keys); $i++)
		{
			$text = str_replace($keys[$i][0], $keys[$i][1], $text);
		}

		return $text;
	}

	/* Транслитерация: кот -> kot, гоблин -> goblin, щёлкать -> schelkat */
	public static function Translit($text)
	{
		// Ассоциации букв: кир лат, кир, лат, кир...
		$letters = array(
			"А", "A", "а", "a",
			"Б", "B", "б", "b",
			"В", "V", "в", "v",
			"Г", "G", "г", "g",
			"Д", "D", "д", "d",
			"Е", "E", "е", "e",
			"Ё", "E", "е", "e",
			"Ж", "ZH", "ж", "zh",
			"З", "Z", "з", "z",
			"И", "I", "и", "i",
			"Й", "Y", "й", "y",
			"К", "K", "к", "k",
			"Л", "L", "л", "l",
			"М", "M", "м", "m",
			"Н", "N", "н", "n",
			"О", "O", "о", "o",
			"П", "P", "п", "p",
			"Р", "R", "р", "r",
			"С", "S", "с", "s",
			"Т", "T", "т", "t",
			"У", "U", "у", "u",
			"Ф", "F", "ф", "f",
			"Х", "H", "х", "h",
			"Ц", "TS", "ц", "ts",
			"Ч", "CH", "ч", "ch",
			"Ш", "SH", "ш", "sh",
			"Щ", "SCH", "щ", "sch",
			"Ь", "", "ь", "",
			"Ы", "Y", "ы", "y",
			"Ъ", "", "ъ", "",
			"Э", "E", "э", "e",
			"Ю", "IU", "ю", "iu",
			"Я", "YA", "я", "ya",
		);

		for($i = 0; $i < sizeof($letters); $i += 2)
		{
			$text = str_replace($letters[$i], $letters[$i + 1], $text);
		}

		return $text;
	}
}