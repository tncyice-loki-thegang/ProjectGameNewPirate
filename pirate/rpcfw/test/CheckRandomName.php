<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: CheckRandomName.php 30991 2012-11-13 09:30:39Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/CheckRandomName.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2012-11-13 17:30:39 +0800 (二, 2012-11-13) $
 * @version $Revision: 30991 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */
class CheckRandomName extends BaseScript
{
	
	public function getRandomName($offset, $limit, $arrField = array('name'))
	{
		$data = new CData();
		$ret = $data->select($arrField)->from('t_random_name')->where('1', '=', '1')
			->limit($offset, $limit)->query();
		return $ret;
		
	}
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		$num=10000;
		$offset = 0;
		$limit = 100;
		
		$exe = 100;
		$belly = 8000000;
		$gold = 2000;
		
		$hexiNum = 0;
		$count = 0;
		
		Logger::fatal('attention. add  for all user.');
		while ( $num-- > 0 )
		{
			$arrName = $this->getRandomName($offset, $limit);
			
			if (empty($arrName))
			{
				break;
			}
			
			foreach($arrName as $name)
			{
				$ret = trie_filter_search($name['name']);
				if (!empty($ret))
				{
					++$hexiNum;
				}
			}

			++$count;
			echo "$count \n";
			
			
			
			$offset += $limit;
		}
		
		var_dump($hexiNum);
	}
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */