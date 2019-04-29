<?php
namespace Crown;

use Solar\JSONObject;

class Section extends JSONObject
{
	public $id = "";
	public $title = "";
	public $layout = "";
	public $hidden = false;
	public $load_action = null;
	private $title_format = "";

	private static $default_title_format = "{site_name} | {title}";

	public function __construct($id, $title, $layout)
	{
		$this->id = $id;
		$this->title = $title;
		$this->layout = $layout;
	}

	public function SetHidden($state = true)
	{
		$this->hidden = $state;
		return $this;
	}

	public function SetLoadAction($func)
	{
		$this->load_action = $func;
		return $this;
	}

	//

	public function GetTitle()
	{
		if($this->title == "") return Engine::GetTitle();

		$format = $this->title_format != "" ? $this->title_format : self::$default_title_format;

		$format = str_replace("{site_name}", Engine::GetTitle(), $format);
		$format = str_replace("{title}", $this->title, $format);

		return $format;
	}

	public function SetTitleFormat($format)
	{
		$this->title_format = $format;
		return $this;
	}

	public static function SetDefaultTitleFormat($format)
	{
		self::$default_title_format = $format;
	}
}