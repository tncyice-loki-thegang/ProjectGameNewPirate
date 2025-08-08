<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: partitionTable.class.php 28054 2012-09-23 09:05:00Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/mergeServer/lib/partitionTable.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-09-23 17:05:00 +0800 (日, 2012-09-23) $
 * @version $Revision: 28054 $
 * @brief
 *
 **/

class partitionTable
{
	const XMLPATH = "/home/pirate/rpcfw/data/dataproxy.xml";

	public static $XMLS = NULL;

	public static $PARTITION = array();

	private static function loadXML()
	{
		if ( isset(self::$XMLS) )
		{
			return self::$XMLS;
		}

		if ( file_exists(self::XMLPATH) == FALSE )
		{
			throw new Exception(self::XMLPATH . 'not exist!');
		}

		self::$XMLS = simplexml_load_file(self::XMLPATH);
		return self::$XMLS;
	}

	private static function getPartition($table)
	{
		if ( !isset(self::$PARTITION[$table]) )
		{
			$xmls = self::loadXML();
			foreach ( $xmls->table as $key => $value )
			{
				if ( $value->name == $table )
				{
					if ( !isset($value->partition) )
					{
						if ( !isset(self::$PARTITION[$table]) )
						{
							self::$PARTITION[$table] = array();
						}
					}
					else
					{
						if ( !isset(self::$PARTITION[$table]) )
						{
							self::$PARTITION[$table] = array(
								'key' => $value->partition->key,
								'method' => $value->partition->method,
								'value' => $value->partition->value,
							);
						}
					}
				}
			}
			if ( !isset(self::$PARTITION[$table]) )
			{
				self::$PARTITION[$table] = array();
			}
		}
		return self::$PARTITION[$table];
	}

	public static function getTableName($table, $key, $value)
	{
		$table_name = '';
		$partition = self::getPartition($table);
		if ( empty($partition) )
		{
			$table_name = $table;
		}
		else
		{
			if ( $key != $partition['key'] )
			{
				throw new Exception("invalid key for $table!");
			}

			switch ( $partition['method'] )
			{
				case 'div':
					$table_name = $table . "_" . intval($value / $partition['value']);
					break;
				case 'mod':
					$table_name = $table . "_" . intval($value % $partition['value']);
					break;
				default:
					break;
			}
		}
		return $table_name;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */