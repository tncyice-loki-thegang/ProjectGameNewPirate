<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: LocalQueryCache.class.php 38809 2013-02-20 09:48:34Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/data/LocalQueryCache.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2013-02-20 17:48:34 +0800 (三, 2013-02-20) $
 * @version $Revision: 38809 $
 * @brief
 *
 **/
class LocalQueryCache implements IQueryCache
{

	/**
	 * 当前使用的缓存
	 * @var key/value对的信息存储
	 */
	private $arrCache = array ();

	/**
	 * 是否需要在afterQuery时进行addCache操作
	 * @var bool
	 */
	private $needCache = false;

	/* (non-PHPdoc)
	 * @see IQueryCache::beforeQuery()
	 */
	public function beforeQuery(&$arrData, &$moreResult)
	{

		$this->needCache = false;
		$table = $arrData ['table'];
		if (empty ( QueryCacheConf::$ARR_TABLE_DEF [$table] ))
		{
			Logger::debug ( 'table:%s not found in table defination', $table );
			return array ();
		}
		
		$command = $arrData ['command'];
		if ($command != 'select')
		{
			Logger::debug ( "command:%s ignore beforeQuery", $command );
			return array ();
		}
		
		$db = '';
		if (! empty ( $arrData ['db'] ))
		{
			$db = $arrData ['db'];
		}
		$arrCond = $arrData ['where'];
		
		$arrKey = $this->genKeys ( $table, $arrCond, $db );
		if (empty ( $arrKey ))
		{
			Logger::debug ( 'no key defined, ignore beforeQuery' );
			return array ();
		}
		
		$this->needCache = true;
		$arrRet = array ();
		$moreResult = false;
		foreach ( $arrKey as $key )
		{
			if (! empty ( $this->arrCache [$key] ))
			{
				Logger::debug ( 'key:%s found in cache', $key );
				$arrRet [] = $this->arrCache [$key];
			}
			else
			{
				Logger::debug ( 'key:%s not found in cache', $key );
				$moreResult = true;
			}
		}
		
		if ($moreResult && ! empty ( $arrRet ))
		{
			Logger::debug ( 'need more result, should filter where condition' );
			$arrData ['where'] = $this->filterKeys ( $arrRet, $arrData ['where'] );
		}
		
		return $arrRet;
	}

	/**
	 * 过滤一下key
	 * @param array $arrRow 缓存中得到的列
	 * @param array $arrData 请求数据
	 */
	private function filterKeys($arrRow, $arrCond)
	{

		$inKey = '';
		foreach ( $arrCond as $key => $arrOp )
		{
			if ($arrOp [0] == 'IN')
			{
				$inKey = $key;
				$inValue = $arrOp [1];
				break;
			}
		}
		
		if (empty ( $inKey ))
		{
			Logger::fatal ( "impossible branch" );
			throw new Exception ( 'inter' );
		}
		
		Logger::debug ( 'inKey:%s, inValue:%s', $inKey, $inValue );
		
		foreach ( $arrRow as $row )
		{
			$value = $row [$inKey];
			$key = array_search ( $value, $inValue );
			if (false === $key)
			{
				Logger::fatal ( "impossible branch" );
				throw new Exception ( 'inter' );
			}
			unset ( $inValue [$key] );
		}
		$inValue = array_merge ( $inValue );
		
		if (empty ( $inValue ))
		{
			Logger::fatal ( "impossible branch" );
			throw new Exception ( 'inter' );
		}
		
		$arrCond [$inKey] [1] = $inValue;
		return $arrCond;
	}

	/**
	 * 生成查询所用的key
	 * @param array $arrData
	 * @throws Exception
	 */
	private function genKeys($table, $arrCond, $db)
	{

		$arrPrimaryKey = QueryCacheConf::$ARR_TABLE_DEF [$table];
		$arrKey = array ();
		$arrKey [0] = $table;
		if (! empty ( $db ))
		{
			$arrKey [0] = $db . '.' . $arrKey [0];
		}
		
		foreach ( $arrPrimaryKey as $key )
		{
			if (empty ( $arrCond [$key] ))
			{
				Logger::debug ( "column:%s not found in condition, no keys generated", $key );
				return false;
			}
			$arrOp = $arrCond [$key];
			switch ($arrOp [0])
			{
				case 'IN' :
					if (count ( $arrKey ) != 1)
					{
						Logger::fatal ( 'only one primary key allow IN operation' );
						throw new Exception ( 'inter' );
					}
					
					$keyPrefix = $arrKey [0];
					foreach ( $arrOp [1] as $i => $value )
					{
						if ($i == 0)
						{
							$arrKey [0] = $keyPrefix . '#' . $value;
						}
						else
						{
							$arrKey [] = $keyPrefix . '#' . $value;
						}
					}
					break;
				case '=' :
				case '==' :
					foreach ( $arrKey as $i => $key )
					{
						$arrKey [$i] = $key . '#' . $arrOp [1];
					}
					break;
				default :
					Logger::debug ( "operand:%s not supported by cache, no keys generated", 
							$arrOp [0] );
					return false;
			}
		}
		
		return $arrKey;
	}

	/**
	 * 用于update以及insertOrUpdate的key生成
	 * @param array $arrData 请求
	 * @return string 数据的key
	 * @throws Exception
	 */
	private function genKey($table, $arrCond, $db, $throw = false)
	{

		$arrPrimaryKey = QueryCacheConf::$ARR_TABLE_DEF [$table];
		$rowKey = $table;
		foreach ( $arrPrimaryKey as $key )
		{
			if (empty ( $arrCond [$key] ))
			{
				if ($throw)
				{
					Logger::fatal ( 'table:%s has primary keys defined, key:%s not found in where', 
							$table, $key );
					throw new Exception ( 'inter' );
				}
				else
				{
					Logger::debug ( 'table:%s has primary keys defined, key:%s not found in where', 
							$table, $key );
					return false;
				}
			}
			
			$arrOp = $arrCond [$key];
			if (is_array ( $arrOp ))
			{
				if ($arrOp [0] != '=' && $arrOp [0] != '==')
				{
					if ($throw)
					{
						Logger::fatal ( 
								'table:%s has primary keys defined, key:%s only allow equality', 
								$table, $key );
						throw new Exception ( 'inter' );
					}
					else
					{
						Logger::debug ( 
								'table:%s has primary keys defined, key:%s only allow equality', 
								$table, $key );
						return false;
					}
				
				}
				$rowKey = $rowKey . "#" . $arrOp [1];
			}
			else
			{
				$rowKey = $rowKey . '#' . $arrOp;
			}
		}
		
		if (! empty ( $db ))
		{
			$rowKey = $db . '.' . $rowKey;
		}
		
		return $rowKey;
	}

	private function updateCache($arrData, $arrRet)
	{

		$table = $arrData ['table'];
		$command = $arrData ['command'];
		if ($command == 'update')
		{
			$arrCond = $arrData ['where'];
		}
		else if ($command == 'insertOrUpdate')
		{
			$arrCond = $arrData ['values'];
			if (! empty ( $arrData ['unique'] ))
			{
				$uniqueKey = $arrData ['unique'];
				$arrCond [$uniqueKey] = array ('=', $arrRet [$uniqueKey] );
				$arrData ['values'] = $arrCond;
			}
		}
		else
		{
			Logger::fatal ( "command:%s ignored in afterQuery", $command );
			throw new Exception ( 'inter' );
		}
		
		$affectedRows = $arrRet ['affected_rows'];
		if ($affectedRows == 0)
		{
			Logger::debug ( "affected rows zoro for command:%s, ignore afterQuery", $command );
			return;
		}
		
		$db = '';
		if (! empty ( $arrData ['db'] ))
		{
			$db = $arrData ['db'];
		}
		
		$key = $this->genKey ( $table, $arrCond, $db );
		if (empty ( $this->arrCache [$key] ))
		{
			Logger::debug ( "key:%s not found in cache", $key );
			return;
		}
		
		$arrRow = $this->arrCache [$key];
		$arrValue = $arrData ['values'];
		
		if ($affectedRows == 2 && ! empty ( $arrData ['duplicate'] ))
		{
			Logger::debug ( 'command:%s affectedRows:%d update values', $command, $affectedRows );
			
			$arrDup = $arrData ['duplicate'];
			$arrPrimaryKey = QueryCacheConf::$ARR_TABLE_DEF [$table];
			
			$arrNewValue = array ();
			foreach ( $arrPrimaryKey as $primaryKey )
			{
				$arrNewValue [$primaryKey] = $arrValue [$primaryKey];
			}
			
			foreach ( $arrDup as $dupKey )
			{
				$arrNewValue [$dupKey] = $arrValue [$dupKey];
			}
			
			$arrValue = $arrNewValue;
		}
		
		foreach ( $arrValue as $col => $arrOp )
		{
			switch ($arrOp [0])
			{
				case '=' :
					$arrRow [$col] = $arrOp [1];
					break;
				default :
					Logger::debug ( 'op:%s not safe, clear cache', $arrOp [0] );
					unset ( $this->arrCache [$key] );
					return;
			}
		}
		
		Logger::debug ( "update key:%s", $key );
		$this->arrCache [$key] = $arrRow;
	}

	private function addCache($arrData, $arrRet)
	{

		$table = $arrData ['table'];
		$db = '';
		if (! empty ( $arrData ['db'] ))
		{
			$db = $arrData ['db'];
		}
		
		foreach ( $arrRet as $arrRow )
		{
			$key = $this->genKey ( $table, $arrRow, $db, false );
			if (empty ( $key ))
			{
				break;
			}
			Logger::debug ( "add key:%s into cache now", $key );
			$this->arrCache [$key] = $arrRow;
		}
	}

	/* (non-PHPdoc)
	 * @see IQueryCache::afterQuery()
	 */
	public function afterQuery($arrData, $arrRet)
	{

		$table = $arrData ['table'];
		if (empty ( QueryCacheConf::$ARR_TABLE_DEF [$table] ))
		{
			Logger::debug ( 'table:%s not found in table defination', $table );
			return;
		}
		
		$command = $arrData ['command'];
		if ($command == 'update' || $command == 'insertOrUpdate')
		{
			$this->updateCache ( $arrData, $arrRet );
		}
		else if ($command == 'select' && $this->needCache)
		{
			$this->addCache ( $arrData, $arrRet );
		}
		else
		{
			Logger::debug ( "command:%s ignored in afterQuery", $command );
			return;
		}
	
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */