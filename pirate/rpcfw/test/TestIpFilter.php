<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: TestIpFilter.php 26579 2012-09-03 10:19:05Z HaopingBai $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/TestIpFilter.php $
 * @author $Author: HaopingBai $(hoping@babeltime.com)
 * @date $Date: 2012-09-03 18:19:05 +0800 (一, 2012-09-03) $
 * @version $Revision: 26579 $
 * @brief
 *
 **/
class TestIpFilter extends BaseScript
{

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{

		$arrIpRange = array (0 => array (0 => 3232235776, 1 => 3232235976 ),
				1 => array (0 => 3232236288, 1 => 3232236543 ) );
		if (! Util::ipContains ( $arrIpRange, ip2long ( '192.168.3.2' ) ))
		{
			echo "failed\n";
		}

		if (Util::ipContains ( $arrIpRange, ip2long ( '192.168.5.123' ) ))
		{
			echo "failed\n";
		}

		echo "success\n";
	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */