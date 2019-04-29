<?php
namespace Solar;


class JSON
{
	public static function GetPublicFields($object)
	{
		return get_object_vars($object);
	}

	public static function Encode($object)
	{
		return json_encode($object, true);
	}

	public static function Decode($json_string)
	{
		return json_decode($json_string, true);
	}
}

class JSONObject
{
	public function Encode()
	{
		$fields = JSON::GetPublicFields($this);
		return JSON::Encode($fields);
	}

	public function Decode($json_string)
	{
		$fields = JSON::Decode($json_string);
		$this->SetFields($fields);
		return $this;
	}

	public function SetFields($data)
	{
		foreach ($data AS $key => $value)
		{
			if (is_array($value))
			{
				$sub = new JSONObject;
				$sub->SetFields($value);
				$value = $sub;
			}
			$this->{$key} = $value;
		}
	}

	public function GetPublicFields()
	{
		return JSON::GetPublicFields($this);
	}
}