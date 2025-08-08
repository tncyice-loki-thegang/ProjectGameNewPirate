<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestCrypt.php 16897 2012-03-20 02:32:46Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestCrypt.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-03-20 10:32:46 +0800 (二, 2012-03-20) $
 * @version $Revision: 16897 $
 * @brief
 *
 **/
class TestCrypt extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$encPid = 0x12345678;
		$data = BabelCrypt::encryptNumber ( $encPid );
		$decPid = BabelCrypt::decryptNumber ( $data );
		if ($encPid == $decPid)
		{
			echo "ok\n";
		}
		else
		{
			echo "failed\n";
		}
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */