<?php

namespace Solar;

class StopWatch
{
	const DEFAULT_TIMER = "default";

	private static $timers = [];

	public static function Start($name = self::DEFAULT_TIMER)
	{
		$timer_start = microtime(true);
		self::$timers[$name] = $timer_start;
	}

	public static function Get($name = self::DEFAULT_TIMER, $round = 1)
	{
		$start = self::$timers[$name];
		$end = microtime(true);
		return round(($end - $start) * 1000, $round);
	}

	public static function Stop($name = self::DEFAULT_TIMER, $round = 1)
	{
		$result = self::Get($name, $round);
		unset(self::$timers[$name]);
		return $result;
	}
}