<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: BossInit.class.php 20479 2012-05-16 09:08:01Z HaidongJia $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/BossInit.class.php $
 * @author $Author: HaidongJia $(jhd@babeltime.com)
 * @date $Date: 2012-05-16 17:08:01 +0800 (三, 2012-05-16) $
 * @version $Revision: 20479 $
 * @brief
 *
 **/

/**
 *
 * 初始化boss
 *
 */
class BossInit extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$bosses = btstore_get()->BOSS;
		foreach ( $bosses as $boss_id => $value )
		{
			$boss_info = BossDAO::getBoss($boss_id, TRUE);
			$bossObj = new Boss();
			if ( empty($boss_info) )
			{
				$bossObj->initBoss($boss_id);
			}
			else
			{
				echo "failed\n";
				return;
			}
		}
		echo "done\n";
		return;
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */