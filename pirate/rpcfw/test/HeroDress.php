<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: HeroDress.php 38778 2013-02-20 08:05:07Z HongyuLan $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/test/HeroDress.php $
 * @author $Author: HongyuLan $(lanhongyu@babeltime.com)
 * @date $Date: 2013-02-20 16:05:07 +0800 (三, 2013-02-20) $
 * @version $Revision: 38778 $
 * @brief 
 *  
 **/

/**
 * Enter description here ...
 * @author idyll
 *
 */

class HeroDress extends BaseScript
{
	protected function get($limit, $offset)
	{
		$data = new CData();
		$ret =$data->select(array('hid', 'uid', 'va_hero'))->from('t_hero')->where('hid', '>', '0')
			->orderBy('hid', true)
			->limit($offset, $limit)->query();
		return $ret;
	}
	
	protected function update($hid, $arrField)
	{
		$data = new CData();
		$data->update('t_hero')->set($arrField)->where('hid', '=', $hid)->query();
	}
	
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript($arrOption)
	{
		$limit = 100;
		$offset = 0;
		
		$total=10000;
		while($total-->0)
		{			
			$arrHero = $this->get($limit, $offset);
			foreach ($arrHero as $hero)
			{
				$keys = array_keys($hero['va_hero']);
				
				if (in_array('dress', $keys))
				{					
					if ($hero['va_hero']['dress']===null || $hero['va_hero']['dress']===false || $hero['va_hero']['dress']===0)
					{
						unset($hero['va_hero']['dress']);
						Logger::warning('update uid %d hid %d va_hero %s', $hero['uid'], $hero['hid'], $hero['va_hero']);
						$this->update($hero['hid'], array('va_hero'=>$hero['va_hero']));
					}
				}
			}						
			$offset += $limit;
		}
		
		
		echo "ok\n";
	}
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */