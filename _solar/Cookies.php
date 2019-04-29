<?php
namespace Solar;


class Cookies
{
	public static function Set($key, $value, $days = 30)
	{
		setcookie($key, $value, time() + (3600 * 24 * $days), "/");
	}

	public static function Get($key, $alt = "")
	{
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : $alt;
	}

	public static function HasKey($key)
	{
		return isset($_COOKIE[$key]);
	}

	public static function Delete($key)
	{
		setcookie($key, "", time() - 3600, "/");
	}
}