<?php
class CDBInterface {

	// 私有变量 mysqli 实例
	private static $m_DBInterface = null;

	/**
	 * 关闭数据库连接
	 */
    public static function destroyConnection() {

        self::$m_DBInterface->kill(self::$m_DBInterface->thread_id);
        self::$m_DBInterface->close();
    }

    /**
     * 执行SQL
     * @param string $strSql					将要执行的SQL语句
     * @return 返回 $stmt
     */
    public static function execute($strSql) {

        // 准备SQL语句
        $stmt = self::$m_DBInterface->prepare($strSql);
        // 错误处理
        if (!$stmt) {
        	echo self::$m_DBInterface->error;
        }
        // 执行SQL
        $stmt->execute();
        // 返回 stmt
        return  $stmt;
    }

    /**
     * 连接数据库
     */
     public static function connect($ip, $dbName, $user, $pass) {

        self::$m_DBInterface = new mysqli($ip, $user, $pass);
        if (self::$m_DBInterface->connect_error != null) {
            printf("Could not connect to MySQL: %s\n", self::$m_DBInterface->error);
            exit();
        }
        // 选择需要连接的实例
        self::$m_DBInterface->select_db($dbName);
        // 更改字符集
        self::$m_DBInterface->query("SET NAMES utf8;");
        // 关闭自动提交
        self::$m_DBInterface->autocommit(false);

     }

	public static function getAllTblNames()
	{
		$arr = array();

		$result = self::$m_DBInterface->query("show tables;");

		$row = null;
		while (1) {
			$row = $result->fetch_array(3);
			if ($row != null)
				$arr[] = $row[0];
			else
				break;
		}

		return $arr;
	}
} // CDBInterface

$ids = array('grid' => 't_guild_record',
             'guild_id' => 't_guild',
             'hid' => 't_hero',
             'signup_id' => 't_world_resource_attack',
             'tid' => 't_timer',
             'uid' => 't_user',
			 'payback_id' => 't_pay_back_info',
             'pid' => '',
             'boat_id' => '');

$ids_01 = array(
             'brid' => 't_battle_record',
             'item_id' => 't_item',
             'kid' => 't_task',
             'mid' => 't_mail');


function getMaxIDNoPartition($idName, $tblName)
{
	$id = 0;

	switch ($idName)
	{
	case 'grid':
	case 'guild_id':
	case 'hid':
	case 'payback_id':
	case 'signup_id':
	case 'tid':
	case 'uid':
		$sql = "select max(".$idName.") from ".$tblName.";";
		break;
	default:
		return $id;
	}

	$stmt = CDBInterface::execute($sql);
	$stmt->bind_result($id);
	$stmt->fetch();
	$stmt->close();

	// 最大的下一个id
	return intval(($id + 99) / 100) * 100;
}

function getMaxIDPartition($idName, $tblName, $allTbls)
{
	$max = 0;
	$id = 0;
	$strLength = strlen($tblName);
	foreach ($allTbls as $tbl)
	{
		// 如果前缀相等
		if (substr($tbl, 0, $strLength) == $tblName)
		{
			$sql = "select max(".$idName.") from ".$tbl.";";
			$stmt = CDBInterface::execute($sql);
			$stmt->bind_result($id);
			$stmt->fetch();
			$stmt->close();

			if ($id > $max)
				$max = $id;
		}
	}

	// 最大的下一个id
	return intval(($max + 99) / 100) * 100;
}

$help = "argv1: ip, argv2: dbname, argv3: user, argv4: pass\n";
if ($argc != 5)
{
	exit($help);
}

// 链接数据库  ip, dbname, user, pass
CDBInterface::connect($argv[1], $argv[2], $argv[3], $argv[4]);

$id = array();

foreach ($ids as $id_name => $tbl_name)
{
	$id[$id_name] = getMaxIDNoPartition($id_name, $tbl_name);
}

$allTbls = CDBInterface::getAllTblNames();

foreach ($ids_01 as $id_name => $tbl_name)
{
	$id[$id_name] = getMaxIDPartition($id_name, $tbl_name, $allTbls);
}

// 断开数据库
CDBInterface::destroyConnection();

var_dump($id);


foreach ($id as $key => $v)
{
	file_put_contents($key, pack("I", $v));
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */