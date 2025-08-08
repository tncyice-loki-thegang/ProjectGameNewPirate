<?php

class LuckyPointer implements ILuckyPointer
{
	public function getInfo()
	{
		$ret = array(	'freetimes' => 10,
						'goldtimes' => 2,
						'multiple' => 3,
						'rewardid' =>20009
					);
		return $ret;
	}
	
	public function roll()
	{
		$bag = BagManager::getInstance()->getBag();
		$ret = array(
						'needGold' => 100,
						'reward' => array(
										array('id'=> 20009, 'multiple'=>2),
										array('id'=> 20001, 'multiple'=>3)
										),
						'baginfo' => $bag->update()
					);
		return $ret;
	}
	
	public function getRollLog()
	{
		
	}
}