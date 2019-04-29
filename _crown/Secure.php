<?php
namespace Crown;

use Solar\Cookies;

class Secure
{
	const TOKEN_COOKIE_KEY = "dashboard_token";
	
	private static $password_hash = "";
	private static $salt = "";
	
	public static function SetPasswordHash($password_hash){ self::$password_hash = $password_hash; }
	public static function SetSalt($salt){ self::$salt = $salt; }
	
	public static function GetPasswordHash($password)
	{
		return self::SHA256($password);
	}
	
	public static function GetAuthToken($password_hash)
	{
		return self::SHA256($password_hash."_".self::$salt."_".$_SERVER['HTTP_USER_AGENT']);
	}
	
	public static function CheckPasswordHash($password_hash)
	{
		return self::$password_hash == $password_hash;
	}
	
	public static function Login($password)
	{
		$new_hash = self::GetPasswordHash($password);
		if(self::$password_hash == $new_hash)
		{
			$token = self::GetAuthToken($new_hash);
			Cookies::Set(self::TOKEN_COOKIE_KEY, $token);
			return true;
		}
		return false;
	}
	
	public static function Logout()
	{
		Cookies::Delete(self::TOKEN_COOKIE_KEY);
		return true;
	}
	
	public static function isLogged()
	{
		$token = Cookies::Get(self::TOKEN_COOKIE_KEY, "");
		$true_token = self::GetAuthToken(self::$password_hash);
		return $token == $true_token;
	}
	
	public static function SHA256($str)
	{
		return hash("sha256", $str);
	}
}
