<?php

include 'DbConfig.php';

class DatabaseConnection extends DbConfig{
	public $connectionString;
	public $connPDO;
	public $dataSet;
	private $sqlQuery;
    protected $group;
	protected $databaseName;
	protected $databaseT9Name;
    protected $hostName;
    protected $portServer;
    protected $userName;
    protected $passCode;
	
    private static $instances = null;
	
    protected function __construct() {
		$this -> connectionString = NULL;
		$this -> connPDO = NULL;
		$this -> connT9PDO = NULL;
		$this -> sqlQuery = NULL;
		$this -> dataSet = NULL;
		$dbPara = new DbConfig();
		$this -> group = $dbPara -> group;
		$this -> databaseName = $dbPara -> dbName;
		$this -> databaseT9Name = $dbPara -> dbT9Name;
		$this -> hostName = $dbPara -> serverName;
		$this -> portServer = $dbPara -> serverPort;
		$this -> userName = $dbPara -> userName;
		$this -> passCode = $dbPara ->passCode;
		$dbPara = NULL;
	}
	
	public static function getInstance() {
        if (self::$instances !== null) {
            return self::$instances;
        }

        self::$instances = new self();
		self::$instances -> dbConnect();
		self::$instances -> dbT9Connect();
        return self::$instances;
    }
	
	function dbConnect(){
		$this -> connectionString = sprintf("mysql:host=%s;dbname=%s", 
                $this -> hostName,
                $this -> databaseName);
		$this -> connPDO = new \PDO($this -> connectionString, $this -> userName, $this -> passCode);
		return $this -> connPDO;
	}
	
	function dbT9Connect(){
		$this -> connectionT9String = sprintf("mysql:host=%s;dbname=%s", 
                $this -> hostName,
                $this -> databaseT9Name);
		$this -> connT9PDO = new \PDO($this -> connectionT9String, $this -> userName, $this -> passCode);
		return $this -> connT9PDO;
	}
	
	function getConnect(){
		return $this -> connPDO;
	}
	
	function getT9Connect(){
		return $this -> connT9PDO;
	}
	
	function getServerIP(){
		return $this -> hostName;
	}
	
	function getDatabseName(){
		return $this -> databaseName;
	}
	
	function getGroup(){
		return $this -> group;
	}

	function dbDisconnect(){
		$this -> connectionString = NULL;
		$this -> connPDO = NULL;
		$this -> connT9PDO = NULL;
		$this -> sqlQuery = NULL;
		$this -> dataSet = NULL;
		$this -> group = NULL;
		$this -> databaseName = NULL;
		$this -> databaseT9Name = NULL;
		$this -> hostName = NULL;
		$this -> portServer = NULL;
		$this -> userName = NULL;
		$this -> passCode = NULL;
	}
}