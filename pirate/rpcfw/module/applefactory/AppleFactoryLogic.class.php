<?php

class AppleFactoryLogic
{
	private static $arrField = array('apple_experience', 'demon_kernel');

	private static function insertDefault($uid)
	{
		AppleFactoryDao::insert($uid, array('apple_experience' => 0, 'demon_kernel' => 0));
	}

	public static function getInfo($uid) 
	{
		$ret = AppleFactoryDao::get($uid, self::$arrField);		
		if (empty($ret))
		{			
			self::insertDefault($uid);
			$ret = AppleFactoryDao::get($uid, self::$arrField);
		}		
		return $ret;
	}
	
	public static function compose($uid, $item_temp_id)
	{
		$info = btstore_get()->DAIMONAPPLE_REBIRTH[$item_temp_id];
		$user = EnUser::getUserObj();
		$bag = BagManager::getInstance()->getBag();
		$bag->deleteItemsByTemplateID($info['item_need']);
		if ($bag->addItemByTemplateID($item_temp_id,1) == FALSE || $user->subGold($info['cost_gold']) == FALSE)
		{
			return FALSE;
		}
		$user->update();
		$ret = array('baginfo' => $bag->update() , 'curgold' => $user->getGold());
		return $ret;
	}
	
	public static function updateExpKernel($uid, $exp, $kernel)
	{
		$set=array();
		if (!($exp===NULL))
		{
			$exp= ($exp< 0)?0:$exp;
			$set['apple_experience']=$exp;
		}
		if (!($kernel===NULL))
		{
			$kernel= ($kernel< 0)?0:$kernel;
			$set['demon_kernel']=$kernel;
		}
		if (empty($set))
		{
			Logger::warning('AppleFactoryLogic.updateExpKernel empty set');
			return false;
			throw new Exception('fake');
		}
		
		return AppleFactoryDao::update($uid, $set);
	}
	
}
