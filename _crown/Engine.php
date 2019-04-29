<?php
namespace Crown;

if(!class_exists("Solar\\Core")) { exit("Crown requires Solar namespace!"); }

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('html_errors', 1);

require_once("Layout.php");
require_once("Secure.php");
require_once("Section.php");

class Engine
{
	const NAME = "Crown Engine";
	const VERSION = "0.1";

	private static $templates_dir = __DIR__;
	public static function SetTemplatesDir($path){ self::$templates_dir = $path; }
	public static function GetTemplatesDir(){ return self::$templates_dir; }
	
	private static $sections_dir = __DIR__;
	public static function SetSectionsDir($path){ self::$sections_dir = $path; }
	public static function GetSectionsDir(){ return self::$sections_dir; }
	
	private static $current_section = null;
	public static function SetCurrentSection($section){ self::$current_section = $section; }
	public static function GetCurrentSection() : Section { return self::$current_section; }
	
	private static $sections_list = array();
	
	public static function GetSections(){ return self::$sections_list; }

	//

	public static function AddSection($id, $title, $layout)
	{ 
		self::$sections_list[$id] = new Section($id, $title, $layout);
		return self::$sections_list[$id];
	}
	
	public static function GetSection($id)
	{
		if(isset(self::$sections_list[$id]))
		{
			return self::$sections_list[$id];
		}
		return false;
	}
	
	//
	
	private static $title = "";
	public static function SetTitle($title){ self::$title = $title; } 
	public static function GetTitle(){ return self::$title; }
	
	//

	public static function DefaultAccess($section_id)
	{
		return $section_id;
	}

	public static function CreateLayout($request, $error_section, $access_action)
	{
		$sections = self::GetSections();
		$section_id = isset($request['section']) ? $request['section'] : "main";
		$section = null;
		
		if($access_action != null)
		{
			$section_id = $access_action($section_id);
		}
		
		if(isset($sections[$section_id]))
		{
			$section = $sections[$section_id];
		}
		else
		{
			$section = $sections[$error_section];
		}
		//
		self::SetCurrentSection($section);
		//
		Layout::Create();
		//
		if($section->load_action != null)
		{
			$f = $section->load_action;
			$f();
		}
	}
}
