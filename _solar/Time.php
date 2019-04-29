<?php
namespace Solar;

class Time
{
	//
	const DATE = 1;
	const TIME = 2;
	const ADVANCED = 4;
	//
	private static $timezone = 3;
	public static function SetTimezone($timezone){ return self::$timezone = $timezone; }
	public static function GetTimezone(){ return self::$timezone; }
	//

	/* Вызвращает простое представление даты */
	public static function GetSimpleDate($time, $options = self::DATE | self::TIME, $utc = false)
	{
		if($utc === false) $utc = self::$timezone;
		$time += (3600 * $utc);

		$format = "";
		if($options & self::DATE && $options & self::TIME) {
			$format = "d.m.Y H:i:s";
		}
		else {
			if($options & self::DATE){
				$format = "d.m.Y";
			}
			if($options & self::TIME){
				$format = "H:i:s";
			}
		}
		//
		return gmdate($format, $time);
	}

	/*  */
	public static function GetDate($time, $options = self::DATE | self::TIME | self::ADVANCED, $utc = false)
	{
		$now = time();
		if($utc === false) $utc = self::$timezone;
		//
		$format = "";
		if($options & self::DATE && $options & self::TIME) {
			$format = Text::isLocale(Text::LC_RU) ? "{date} в {time}" : "{date} at {time}";
		}
		else {
			if($options & self::DATE){
				$format = "{date}";
			}
			if($options & self::TIME){
				$format = "{time}";
			}
		}

		$pad = function($n) {
			return str_pad($n, 2, "0", STR_PAD_LEFT);
		};
		//
		$h = self::Hour($time, $utc);
		$i = self::Minute($time, $utc);
		$s = self::Second($time, $utc);
		$d = self::Day($time, $utc);
		$m = self::Month($time, $utc);
		$y = self::Year($time, $utc);
		$cy = self::Year($now, $utc);
		//
		$values = [
			"date" => Text::Format("{0}.{1}.{2}", $pad($d), $pad($m), $pad($y)),
			"time" => Text::Format("{0}:{1}:{2}", $pad($h), $pad($i), $pad($s))
		];

		if($options & self::ADVANCED) {
			$month_name = self::GetMonthName($m);
			$month_name = Text::ToLowerCase($month_name[1]);
			//
			if(self::isToday($time))
			{
				$values["date"] = Text::isLocale(Text::LC_RU) ? "сегодня" : "today";
			}
			elseif(self::isYesterday($time))
			{
				$values["date"] = Text::isLocale(Text::LC_RU) ? "вчера" : "yesterday";
			}
			elseif(self::isTomorrow($time))
			{
				$values["date"] = Text::isLocale(Text::LC_RU) ? "завтра" : "tomorrow";
			}
			else
			{
				if($y != $cy) {
					$values["date"] = Text::Format("{0} {1} {2}", $d, $month_name, $y);
				}
				else{
					$values["date"] = Text::Format("{0} {1}", $d, $month_name);
				}
			}
		}
		//
		return Text::Format($format, $values);
	}

	public static function GetTime($time, $utc = false)
	{
		return self::GetDate($time, self::TIME, $utc);
	}
	//

	/* Возвращает время полуночи */
	public static function GetMidnight($time, $utc = false) // По умолчанию - минувшая полночь
	{
		$day = self::Day($time);
		$month = self::Month($time);
		$year = self::Year($time);

		return self::Make($day, $month, $year);
	}

	/* Возвращает unix time даты (меняет местами аргументы в нативной функции) */
	public static function Make($day = 0, $month = 0, $year = 0, $hours = 0, $minutes = 0, $seconds = 0)
	{
		return mktime($hours, $minutes, $seconds, $month, $day, $year);
	}

	/* Возвращает год [1970-2038] */
	public static function Year($time, $utc = false)
	{
		return self::GetDateValue("Y", $time, $utc);
	}

	/* Возвращает месяц [1-12] */
	public static function Month($time, $utc = false)
	{
		return self::GetDateValue("m", $time, $utc);
	}

	/* Возвращает день [1-31] */
	public static function Day($time, $utc = false)
	{
		return self::GetDateValue("d", $time, $utc);
	}

	/* Возвращает часы [0-23] */
	public static function Hour($time, $utc = false)
	{
		return self::GetDateValue("G", $time, $utc);
	}

	/* Возвращает минуты [0-59] */
	public static function Minute($time, $utc = false)
	{
		return self::GetDateValue("i", $time, $utc);
	}

	/* Возвращает секунды [0-59] */
	public static function Second($time, $utc = false)
	{
		return self::GetDateValue("s", $time, $utc);
	}

	private static function GetDateValue($format, $time, $utc = false)
	{
		if($utc === false) $utc = self::$timezone;

		$n = gmdate($format, $time + ($utc * 3600));
		return intval($n);
	}

	/* Возвращает номер дня недели (понедельник = 0) */
	public static function DayOfWeek($time, $utc = false)
	{
		if($utc === false) $utc = self::$timezone;
		//
		$w = (int)gmdate('w', $time + ($utc * 3600));

		$w_number = 0;
		if($w == 1)$w_number = 0; //Костыль костылёвский (для наглядности расписано)
		if($w == 2)$w_number = 1;
		if($w == 3)$w_number = 2;
		if($w == 4)$w_number = 3;
		if($w == 5)$w_number = 4;
		if($w == 6)$w_number = 5;
		if($w == 0)$w_number = 6;

		return $w_number;
	}

	//

	/* Возвращает названия месяца по номеру [1-12] */
	public static function GetMonthName($number)
	{
		if(Text::isLocale(Text::LC_RU)) {
			$names = [
				["Январь", "Янв", "Я"],
				["Февраль", "Фев", "Ф"],
				["Март", "Мар", "М"],
				["Апрель", "Апр", "А"],
				["Май", "Май", "М"],
				["Июнь", "Июн", "И"],
				["Июль", "Июл", "И"],
				["Август", "Авг", "А"],
				["Сентябрь", "Сен", "С"],
				["Октябрь", "Окт", "О"],
				["Ноябрь", "Ноя", "Н"],
				["Декабрь", "Дек", "Д"],
			];
		}
		else {
			$names = [
				["January", "Jan", "J"],
				["February", "Feb", "F"],
				["March", "Mar", "M"],
				["April", "Apr", "A"],
				["May", "May", "M"],
				["June", "Jun", "J"],
				["July", "Jul", "J"],
				["August", "Aug", "A"],
				["September", "Sep", "S"],
				["October", "Oct", "O"],
				["November", "Nov", "N"],
				["December", "Dec", "D"],
			];
		}

		$number--;
		$number %= sizeof($names);
		return $names[$number];
	}

	/* Возвращяет месяц в родительном падеже (1...12) */
	public static function GetMonthNameGenitive($number)
	{
		if(Text::isLocale(Text::LC_RU)) {
			$names = [
				"Января", "Февраля", "Марта",
				"Апреля", "Мая", "Июня",
				"Июля", "Августа", "Сентября",
				"Октября", "Ноября", "Декабря"
			];
		}
		else {
			$names = [
				"of January", "of February", "of March",
				"of April", "of May", "of June",
				"of July", "of August", "of September",
				"of October", "of November", "of December",
			];
		}

		$number--;
		$number %= sizeof($names);
		return $names[$number];
	}

	/* Возвращает названия дня недели по номеру [0-6] */
	public static function GetWeekDayName($number)
	{
		if(Text::isLocale(Text::LC_RU)) {
			$names = [
				["Понедельник", "Пн", "П"],
				["Вторник", "Вт", "В"],
				["Среда", "Ср", "С"],
				["Четверг", "Чт", "Ч"],
				["Пятница", "Пт", "П"],
				["Суббота", "Сб", "С"],
				["Воскресенье", "Вс", "В"]
			];
		}
		else {
			$names = [
				["Monday", "Mon", "M"],
				["Tuesday", "Tue", "T"],
				["Wednesday", "Wed", "W"],
				["Thursday", "Thu", "T"],
				["Friday", "Fri", "F"],
				["Saturday", "Sat", "S"],
				["Sunday", "Sun", "S"]
			];
		}

		$number %= sizeof($names);
		return $names[$number];
	}

	//

	/* Проверяет принадлежность точки во времени к сегодняшнему дню */
	public static function isToday($time, $relative_time = -1)
	{
		if($relative_time == -1) $relative_time = time();
		$RD = self::Day($relative_time);
		$RM = self::Month($relative_time);
		$RY = self::Year($relative_time);

		$D = self::Day($time);
		$M = self::Month($time);
		$Y = self::Year($time);

		return $RD == $D && $RM == $M && $RY == $Y;
	}

	/* Проверяет принадлежность точки во времени к вчерашнему дню */
	public static function isYesterday($time, $relative_time = -1)
	{
		if($relative_time == -1) $relative_time = time();
		return self::isToday($time, $relative_time - (24 * 3600));
	}

	/* Проверяет принадлежность точки во времени к завтрашнему дню */
	public static function isTomorrow($time, $relative_time = -1)
	{
		if($relative_time == -1) $relative_time = time();
		return self::isToday($time, $relative_time + (24 * 3600));
	}
}