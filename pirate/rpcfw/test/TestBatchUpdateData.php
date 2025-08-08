<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestBatchUpdateData.php 16425 2012-03-14 02:58:27Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestBatchUpdateData.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-14 10:58:27 +0800 (三, 2012-03-14) $
 * @version $Revision: 16425 $
 * @brief
 *
 **/

class TestBatchUpdateData extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$batchData = new BatchData ();
		$data = $batchData->newData ();
		$arrBody = array ('uid' => 3, 'uname' => 'a' );
		$data->insertInto ( 't_test' )->values ( $arrBody )->query ();
		$data = $batchData->newData ();
		$arrBody = array ('uid' => 2, 'uname' => 'b' );
		$data->insertInto ( 't_test' )->values ( $arrBody )->query ();
		$arrRet = $batchData->query ();
		var_dump ( $arrRet );

	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
