<?php
namespace Solar;

class SQL
{
	const SILENT = 0;
	const TRACE = 1;
	const LOG = 2;

	// Connection
	private static $connection = null;
	public static function GetConnection() : SQLConnection {
		if(self::$connection == null) {
			self::Connect();
		}
		return self::$connection;
	}
	public static function SetConnection(\mysqli $connection){ self::$connection = $connection; }
	// Auth data
	private static $host;
	private static $database;
	private static $user;
	private static $password;
	public static function  SetHost($host){ self::$host = $host; }
	public static function  SetDatabase($database){ self::$database = $database; }
	public static function  SetUser($user){ self::$user = $user; }
	public static function  SetPassword($password){ self::$password = $password; }
	// Stats
	private static $queries_count = 0;
	public static function GetQueriesCount(){ return self::$queries_count; }
	//
	public static function Setup($host = "", $database = "", $user = "", $password = "")
	{
		self::$host = $host;
		self::$database = $database;
		self::$user = $user;
		self::$password = $password;
	}

	public static function Connect() : SQLConnection
	{
		self::$connection = new SQLConnection(self::$host, self::$database, self::$user, self::$password);
		self::$connection->Start();
		return self::$connection;
	}

	public static function Query(string $sql, $options = SQL::TRACE | SQL::LOG)
	{
		self::$queries_count++;
		return self::GetConnection()->Query($sql, $options);
	}

	public static function isConnected() : bool
	{
		return self::$connection != null ? self::GetConnection()->isConnected() : false;
	}

	public static function FetchAssoc(\mysqli_result $query)
	{
		return $query->fetch_assoc();
	}

	public static function FetchArray(\mysqli_result $query)
	{
		return $query->fetch_array();
	}

	public static function FetchAll(\mysqli_result $query)
	{
		return $query->fetch_all();
	}

	public static function GetID(string $table, string $field = "id") : int
	{
		$table = self::Escape($table);
		$field = self::Escape($field);

		$query = self::Query("SELECT `".$field."` FROM `".$table."` ORDER BY `".$field."` DESC LIMIT 1;");
		$row = self::FetchAssoc($query);
		$id = intval($row[$field]) + 1;

		return $id;
	}

	public static function Escape($obj)
	{
		return self::GetConnection()->Escape($obj);
	}
}

class SQLConnection
{
	public $host;
	public $database;
	public $user;
	public $password;

	private $connection;
	public function GetConnection() : \mysqli { return $this->connection; }

	public function __construct($host, $database, $user, $password)
	{
		$this->host = $host;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
	}

	public function Start()
	{
		if(!$this->isConnected())
		{
			$connection = new \mysqli($this->host, $this->user, $this->password, $this->database) or die("Ошибка подключения к MySQL");
			$connection->query('SET NAMES utf8') or die("Невозможно включить UTF-8");
			$connection->query('SET CHARACTER SET utf8') or die("Невозможно включить UTF-8");
			$this->connection = $connection;
		}
		return $this;
	}

	public function Close()
	{
		if($this->isConnected()) {
			$result = $this->GetConnection()->close();
			$this->connection = null;
			return $result;
		}
		return true;
	}

	public function isConnected()
	{
		return $this->connection != null;
	}

	public function Query($sql, $options = SQL::TRACE | SQL::LOG)
	{
		if(!$this->isConnected()) {
			$this->Start();
		}

		$query = $this->GetConnection()->query($sql);

		if($query){
			return $query;
		}
		else{
			$e = mysqli_error($this->connection);
			$error_text = "SQL error: ".$e;

			if($options & SQL::TRACE)
			{
				echo($error_text."<br/>");
			}
			if($options & SQL::LOG)
			{
				Debug::Log($error_text);
			}

			return false;
		}
	}

	public function Escape($obj)
	{
		if(is_array($obj))
		{
			$new_arr = [];

			foreach ($obj as $k => $v){
				$new_arr[$this->Escape($k)] = $this->Escape($v);
			}

			return $new_arr;
		}

		if(is_string($obj))
		{
			return (string)$this->GetConnection()->escape_string($obj);
		}

		return $obj;
	}
}