<?php

class DaimonApple implements IDaimonApple
{	
	public function getInfo()
	{
		
		return array();
	}
	
	public function transfer($src, $des)
	{
		$ret = array('transfer_success'=>'err');
		$uid = RPCContext::getInstance()->getUid();
		$user = EnUser::getUserObj();
		if ($user->subGold(50) == FALSE)
		{
			return $ret;
		}

		$src_item = ItemManager::getInstance()->getItem($src);
		$des_item = ItemManager::getInstance()->getItem($des);
		
		$exp = $src_item->getExp();
		
		$uid = RPCContext::getInstance()->getUid();
		$maxExp = $des_item->getMaxLevelExp();
		$allExp = $des_item->getExp() + $exp;
		$overExp = $allExp - $maxExp;
		if($overExp > 0)
		{
			$des_item->setExp($maxExp);
			$cur_exp = AppleFactoryDao::get($uid, array('apple_experience'));
			$cur_exp = $cur_exp['apple_experience'];
			AppleFactoryDao::update($uid, array('apple_experience'=>$cur_exp+$overExp));
		} else $des_item->setExp($allExp);
		$src_item->setExp(0);
		$user->update();
		ItemManager::getInstance()->update();
		$olditeminfo = ItemManager::getInstance()->itemInfo($src);
		$newiteminfo = ItemManager::getInstance()->itemInfo($des);
		$ret = array('transfer_success' => 'ok', 'olditeminfo'=>$olditeminfo, 'newiteminfo'=>$newiteminfo);
		return $ret;
	}
	
	public function composite($item_id1, $item_id2, $composite_id)
	{
		$info = btstore_get()->DAIMONAPPLE_FUSE[$composite_id];
		
		$user = EnUser::getUserObj();
		$bag = BagManager::getInstance()->getBag();
		$obj = SoulObj::getInstance();

		$bag->deleteItem($item_id1);
		$bag->deleteItem($item_id2);
		$item_id = ItemManager::getInstance()->addItem($info['fuseApple']);
		if ($bag->addItemByTemplateID($info['fuseApple'],1) == FALSE || 
			$bag->deleteItemByTemplateID(129305,$info['itemCost']) == FALSE ||
			$user->subBelly($info['belly']) == FALSE ||
			$obj->subPurple($info['soul']))
		{
			return FALSE;
		}					

		$user->update();
		$obj->save();
		$itemInfo = ItemManager::getInstance()->itemInfo($item_id[0]);			
		$ret = array('bag' => $bag->update(), 'item' => $itemInfo);
		return $ret;
	}
	
	public function split($item_id)
	{
		$item = ItemManager::getInstance()->getItem($item_id);
		$item_template_id = $item->getItemTemplateID();
		$info = btstore_get()->DAIMONAPPLE_SPLIT[$item_template_id];
		$user = EnUser::getInstance();
		$user->subGold($info[2]);
		$user->update();
		$item->returnExpKernel();
		$bag = BagManager::getInstance()->getBag();
		$bag->addItemByTemplateID($info[0],1);
		$bag->addItemByTemplateID($info[1],1);
		$bag->deleteItem($item_id);
		return array('bag'=>$bag->update());
	}
}