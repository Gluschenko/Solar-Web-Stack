<?php
namespace Solar;

class VK
{
	private static $version = "5.54";

	private static $app;
	private static $app_standalone;

	public static function SetVersion($version){ self::$version = $version; }
	public static function GetVersion(){ return self::$version; }

	public static function SetApp(VKAppCredentials $app){ self::$app = $app; }
	public static function GetApp() : VKAppCredentials { return self::$app; }
	public static function SetStandaloneApp(VKAppCredentials $app){ self::$app_standalone = $app; }
	public static function GetStandaloneApp() : VKAppCredentials { return self::$app_standalone; }

	//

	public static $BaseURL = "https://api.vk.com/method";
	public static $OAuthURL = "https://oauth.vk.com/authorize";
	public static $OAuthTokenURL = "https://oauth.vk.com/access_token";

	public static function Request($method, $params = array(), $get_query = false)
	{
		$url = self::$BaseURL."/".$method;

		$params['v'] = self::$version;
		$params['lang'] = 'ru';
		$params['https'] = '1';

		$query_url = $url.'?'.http_build_query($params);

		if(!$get_query)
		{
			$result = json_decode(@file_get_contents($query_url), true);
			return $result;
		}
		else
		{
			return $query_url;
		}

	}

	public static function AppRequest(string $method, $params = array(), $auth_mode = VKAuthMode::NONE, $custom_token = "")
	{
		$url = self::$BaseURL."/".$method;

		switch($auth_mode)
		{
			case VKAuthMode::TOKEN:
				$params['access_token'] = self::$app->token;
				break;
			case VKAuthMode::TOKEN_STANDALONE:
				$params['access_token'] = self::$app_standalone->token;
				break;
			case VKAuthMode::TOKEN_CUSTOM:
				$params['access_token'] = $custom_token;
				break;
			case VKAuthMode::SECRET:
				$params['client_id'] = self::$app->id;
				$params['client_secret'] = self::$app->secret;
				break;
			case VKAuthMode::SECRET_STANDALONE:
				$params['client_id'] = self::$app_standalone->id;
				$params['client_secret'] = self::$app_standalone->secret;
				break;
		}

		$params['v'] = self::$version;
		$params['lang'] = 'ru';
		$params['https'] = '1';

		$result = json_decode(@file_get_contents($url.'?'.http_build_query($params)), true);

		return $result;
	}

	public static function GetAuthURL(string $redirect)
	{
		$params = array(
			"client_id" => self::GetStandaloneApp()->id,
			"redirect_uri" => $redirect,
			"scope" => "email",
			"response_type" => "code",
			"v" => self::$version
		);

		return self::$OAuthURL."?".http_build_query($params);
	}

	public static function GetAccessTokenFromCode(string $code, string $redirect)
	{
		$params = array(
			"client_id" => self::GetStandaloneApp()->id,
			"client_secret" => self::GetStandaloneApp()->secret,
			"code" => $code,
			"redirect_uri" => $redirect
		);

		$query_url = self::$OAuthTokenURL."?".http_build_query($params);
		$response = json_decode(@file_get_contents($query_url), true);

		if(isset($response['user_id']) && isset($response['access_token']))
		{
			$user_id = $response['user_id'];
			$access_token = $response['access_token'];

			return array("user_id" => $user_id, "access_token" => $access_token);
		}

		return false;
	}

	public static function CheckAuthKey(int $user_id, string $auth_key) : bool
	{
		return $auth_key == md5(self::GetApp()->id."_".$user_id."_".self::GetApp()->secret);
	}
}

class VKAppCredentials
{
	public $id;
	public $secret;
	public $token;

	public function __construct(string $id, string $secret, string $token)
	{
		$this->id = $id;
		$this->secret = $secret;
		$this->token = $token;
	}
}

class VKAuthMode
{
	const NONE = 0;
	const SECRET = 1;
	const SECRET_STANDALONE = 2;
	const TOKEN = 3;
	const TOKEN_STANDALONE = 4;
	const TOKEN_CUSTOM = 5;
}