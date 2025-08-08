<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MM_hero.php 21007 2012-05-22 01:08:45Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/MM_hero.php $
 * @author $Author: HongyuLan $(hoping@babeltime.com)
 * @date $Date: 2012-05-22 09:08:45 +0800 (二, 2012-05-22) $
 * @version $Revision: 21007 $
 * @brief
 *
 **/

/**
 *  警告
 *  使用前检查一下代码
 */


require_once (LIB_ROOT . '/RPCProxy.class.php');

class MM_hero extends BaseScript
{	
	public function getHero($offset, $limit)
	{
		$data = new CData();
		$ret = $data->select(array('hid', 'va_hero'))->from('t_hero')->where('hid', '>', '0')
			->orderBy('hid', true)->query();
			
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$num = 10000;
		$offset = 0;
		$limit = CData::MAX_FETCH_SIZE;
		while ($num-->0)
		{
			$arrRet = $this->getHero($offset, $limit);
			foreach ($arrRet as $ret)
			{
				$isModify = false;
				$va_hero = $ret['va_hero'];
				$arrItem = $va_hero['arming'];
				foreach ($arrItem as $key => $itemId)
				{
					if ($itemId==0)
					{
						continue;
					}
					$tmp = ItemManager::getInstance()->getItem($itemId);
					if ($tmp==null)
					{
						$str =  'hid:' . $ret['hid'] ;
						$str .= "\t" . 'item:' . $itemId;
						echo $str . "\n";

						$arrItem[$key] = 0;
						$isModify = true;
					}					
				}
				
				//TODO
				if ($isModify)
				{
					$va_hero['arming'] =$arrItem;
					//HeroDao::update($ret['hid'], array('va_hero'=>$va_hero));
				}				
			}
			
			

			if (count($ret) < CData::MAX_FETCH_SIZE)
			{
				break;
			}
			$offset += $limit;
		}
		echo "end\n";
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
