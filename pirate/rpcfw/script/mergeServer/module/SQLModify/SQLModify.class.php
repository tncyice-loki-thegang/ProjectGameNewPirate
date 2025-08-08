<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: SQLModify.class.php 32357 2012-12-04 09:09:11Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/module/SQLModify/SQLModify.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-12-04 17:09:11 +0800 (二, 2012-12-04) $
 * @version $Revision: 32357 $
 * @brief
 *
 **/

class SQLModify
{
	/**
	 *
	 * 缓冲TableColumn
	 *
	 * @var array
	 */
	private static $TableColumns = array();

	/**
	 *
	 * 缓冲TableList
	 * @var array
	 */
	private static $TableList = array();

	/**
	 *
	 * 缓冲最大的Id
	 * @var array
	 */
	private static $MaxId = array();

	/**
	 *
	 * 缓冲最小的Id
	 * @var array
	 */
	private static $MinId = array();

	/**
	 *
	 * 得到ID的偏移量
	 *
	 * @param string $id
	 */
	public static function getIdOffset($game_id, $id, $table)
	{
		$key = array_search($game_id, MergeServer::$MERGE_SERVER_IDS);
		if ( $key == 0 )
		{
			return 0;
		}
		else
		{
			return self::getIdOffset(MergeServer::$MERGE_SERVER_IDS[$key-1], $id, $table) + 1 + self::getMaxId(MergeServer::$MERGE_SERVER_IDS[$key-1], $id, $table)
				- self::getMinId($game_id, $id, $table);
		}
	}

	/**
	 *
	 * 得到最大的ID
	 *
	 * @param int $id
	 *
	 * @return int
	 */
	public static function getMaxId($game_id, $id, $table)
	{
		if ( isset(self::$MaxId[$game_id.$id.$table]) )
		{
			return self::$MaxId[$game_id.$id.$table];
		}
		$table_list = SQLModifyDAO::getTablesList($game_id, $table);
		$max_id = 0;
		foreach ( $table_list as $sub_table )
		{
			$table_max_id = SQLModifyDAO::getMaxId($game_id, $sub_table, $id);
			if ( $table_max_id > $max_id )
			{
				$max_id = $table_max_id;
			}
		}
		self::$MaxId[$game_id.$id.$table] = $max_id;
		return $max_id;
	}

	/**
	 *
	 * 得到最小的ID
	 *
	 * @param int $id
	 *
	 * @return int
	 */
	public static function getMinId($game_id, $id, $table)
	{
		if ( isset(self::$MinId[$game_id.$id.$table]) )
		{
			return self::$MinId[$game_id.$id.$table];
		}
		$table_list = SQLModifyDAO::getTablesList($game_id, $table);
		$min_id = 2000000000;
		foreach ( $table_list as $sub_table )
		{
			$table_min_id = SQLModifyDAO::getMinId($game_id, $sub_table, $id);
			if ( $table_min_id < $min_id )
			{
				$min_id = $table_min_id;
			}
		}
		self::$MinId[$game_id.$id.$table] = $min_id;
		return $min_id;
	}

	/**
	 *
	 * 得到某个表的相关分表
	 *
	 * @param string $game_id
	 * @param string $table
	 *
	 * @return array
	 */
	public static function getTableList($game_id, $table)
	{
		if ( isset(self::$TableList[$game_id.$table]) )
		{
			return self::$TableList[$game_id.$table];
		}
		$table_list = SQLModifyDAO::getTablesList($game_id, $table);
		self::$TableList[$game_id.$table] = $table_list;
		return self::$TableList[$game_id.$table];
	}

	/**
	 *
	 * 得到某个表的所有column
	 *
	 * @param string $table
	 *
	 * @return array
	 */
	public static function getTableColumns($game_id, $table)
	{
		if ( isset(self::$TableColumns[$game_id.$table]) )
		{
			return self::$TableColumns[$game_id.$table];
		}
		else
		{
			$result = SQLModifyDAO::getTableColumns($game_id, $table);
			self::$TableColumns[$game_id.$table] = $result;
			return $result;
		}
	}

	/**
	 *
	 * 得到表需要更新的字段
	 *
	 * @param string $table
	 *
	 * @return array
	 */
	public static function getTableColumnModify($game_id, $main_table, $table)
	{
		$return = array();
		if ( !empty(SQLTableConf::$SQLMODIFYTABLE[$main_table][$table]) )
		{
			foreach ( SQLTableConf::$SQLMODIFYTABLE[$main_table][$table] as $column => $id )
			{
				if ( strrpos(strtolower($column), $id) !== FALSE )
				{
					$return[$column] = self::getIdOffset($game_id, $id, SQLTableConf::$SQLMODIFYID[$id]);
				}
			}
		}

		if ( isset(SQLTableConf::$SQLMODIFYNAME[$table]) )
		{
			$return[SQLTableConf::$SQLMODIFYNAME[$table]] = Util::getSuffixName($game_id);
		}
		return $return;
	}

	/**
	 *
	 * 得到相关联的数据
	 *
	 * @param string $table
	 * @param string $relative_column
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function getRelativeData($game_id, $table, $relative_column, $value)
	{
		$result = SQLModifyDAO::getRelativeData($game_id, $table, $relative_column, $value);
		return $result;
	}

	/**
	 *
	 * 导出数据
	 *
	 * @param array $row
	 * @param string $table
	 */
	public static function exportData($game_id, $rows, $table, $key, $value)
	{
		$table = partitionTable::getTableName($table, $key, $value);

		$columns = "";
		$column_values = "";
		foreach ( $rows as $row => $row_value )
		{
			if ( !empty($columns) )
			{
				$columns .= ", ";
			}
			$columns .= "`$row`";

			if ( !empty($column_values) )
			{
				$column_values .= ", ";
			}

			//deal va
			if ( strpos($row, "va_") === 0 )
			{
				$row_value = Util::AMFEncode($row_value);
				$column_values .= "UNHEX(\"" . bin2hex($row_value) . "\")";
			}
			else
			{
				if ( is_string($row_value) )
				{
					$column_values .= "UNHEX(\"" . bin2hex($row_value) . "\")";
				}
				else
				{
					$column_values .= "\"$row_value\"";
				}
			}
		}

		$sql = "INSERT IGNORE INTO $table ($columns) values($column_values)";
		SQLModifyDAO::exportData($game_id, $sql);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */