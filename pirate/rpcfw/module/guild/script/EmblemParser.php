<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: EmblemParser.php 16423 2012-03-14 02:57:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/script/EmblemParser.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:57:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16423 $
 * @brief
 *
 **/

class EmblemParser extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$csvFile = $arrOption [0];
		$btstoreFile = $arrOption [1];
		$parser = new EmblemConverter ();
		$arrData = $parser->parse ( $csvFile . '/emblem.csv' );
		$data = serialize ( $arrData );
		file_put_contents ( $btstoreFile . '/EMBLEM', $data );
	}

}

require_once (LIB_ROOT . '/AbstractParser.class.php');
require_once (DEF_ROOT . '/Guild.def.php');

class EmblemColumn
{

	const ID = 0;

	const TPL_NAME = 1;

	const NAME = 2;

	const DESC = 3;

	const PATH = 4;

	const TYPE = 5;

	const LEVEL = 6;

	const COST = 7;
}

class EmblemConverter extends AbstractParser
{

	/* (non-PHPdoc)
	 * @see AbstractParser::parseRow()
	 */
	protected function parseRow($arrRow, $rowCounter)
	{

		$arrRet = array ();
		foreach ( $arrRow as $index => $value )
		{
			switch ($index)
			{
				case EmblemColumn::ID :
					$arrRet ["id"] = intval ( $value );
					break;
				case EmblemColumn::COST :
					$arrRet ['cost'] = intval ( $value );
					break;
				case EmblemColumn::LEVEL :
					$arrRet ['level'] = intval ( $value );
					break;
				case EmblemColumn::TYPE :
					$arrRet ['type'] = intval ( $value );
					break;
				case EmblemColumn::DESC :
				case EmblemColumn::NAME :
				case EmblemColumn::PATH :
				case EmblemColumn::TPL_NAME :
					break;

			}
		}
		return $arrRet;
	}

	/* (non-PHPdoc)
	 * @see AbstractParser::validate()
	 */
	protected function validate($arrConfig)
	{

		foreach ( $arrConfig as $arrRow )
		{
			if (! in_array ( $arrRow ['type'], EmblemType::$ARR_VALIB ))
			{
				echo sprintf ( "emblem:%d has invalid type:%d", $arrRow ['id'], $arrRow ['type'] );
				throw new Exception ( 'fake' );
			}
		}
		return Util::arrayIndex ( $arrConfig, "id" );
	}

	/* (non-PHPdoc)
	 * @see AbstractParser::getColumnCount()
	 */
	protected function getColumnCount()
	{

		return EmblemColumn::COST + 1;
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */