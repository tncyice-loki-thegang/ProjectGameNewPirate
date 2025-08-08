<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AbstractParser.class.php 16418 2012-03-14 02:51:55Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/lib/AbstractParser.class.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:51:55 +0800 (三, 2012-03-14) $
 * @version $Revision: 16418 $
 * @brief
 *
 **/


abstract class AbstractParser
{

	/**
	 * 配置文件句柄
	 * @var resource
	 */
	protected $config;

	/**
	 * 解析文件
	 * @param string $filename
	 */
	function parse($filename)
	{

		if (! file_exists ( $filename ) || ! is_file ( $filename ))
		{
			Logger::fatal ( 'file %s not found', $filename );
			throw new Exception ( 'config' );
		}

		$this->config = fopen ( $filename, 'r' );
		if (empty ( $this->config ))
		{
			Logger::fatal ( 'file %s cant be opened for reading', $filename );
			throw new Exception ( 'config' );
		}

		//忽略第一行标题栏
		$rowCounter = 2;
		fgetcsv ( $this->config );
		fgetcsv ( $this->config );

		$arrRet = array ();

		while ( ! feof ( $this->config ) )
		{
			$arrRow = fgetcsv ( $this->config );
			if (empty ( $arrRow ))
			{
				break;
			}
			$rowCounter ++;
			if (count ( $arrRow ) < $this->getColumnCount ())
			{
				Logger::fatal ( 'invalid row:%d, expected %d columns, found:%d columns, %s',
						$rowCounter, $this->getColumnCount (), count ( $arrRow ), $arrRow, true );
				throw new Exception ( 'config' );
			}
			$arrRet [] = $this->parseRow ( $arrRow, $rowCounter );
		}

		return $this->validate ( $arrRet );
	}

	/**
	 * 解析其中一行
	 * @param array $arrRow
	 * @return array
	 */
	abstract protected function parseRow($arrRow, $rowCounter);

	/**
	 * 检查结果
	 * @param array $arrConfig
	 */
	abstract protected function validate($arrConfig);

	/**
	 * 得到配置的列数
	 * @return int
	 */
	abstract protected function getColumnCount();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */