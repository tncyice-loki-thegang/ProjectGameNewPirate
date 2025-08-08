<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: MM_explore.php 21007 2012-05-22 01:08:45Z HongyuLan $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/MM_explore.php $
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

class MM_explore extends BaseScript
{

	private function getExplore($offset, $limit)
	{
		$data = new CData();
		$ret = $data->select(array('uid', 'exploreId', 'va_explore'))->from('t_explore')->where('uid', '>', '0')
			->orderBy('uid', true)->orderBy('exploreId', true)->limit($offset, $limit)->query();
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
			$arrRet = $this->getExplore($offset, $limit);
			foreach ($arrRet as $ret)
			{
				$isModify = false;
				$va_explore = $ret['va_explore'];
				$arrItem = $va_explore['items'];
				foreach ($arrItem as $key => $itemId)
				{
					$tmp = ItemManager::getInstance()->getItem($itemId);
					if ($tmp==null)
					{
						$str =  'uid:' . $ret['uid'] . "\t" . 'exploreId:' . $ret['exploreId'] ;
						$str .= "\t" . 'item:' . $itemId;
						echo $str . "\n";

						unset($va_explore['items'][$key]);						
						$isModify = true;
					}					
				}
				
				//TODO
				if ($isModify)
				{
					$va_explore['items'] = array_merge($va_explore['items']);
					//ExploreDao::update($ret['uid'], $ret['exploreId'], array('va_explore' => $va_explore));	
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
