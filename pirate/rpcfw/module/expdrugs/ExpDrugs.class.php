<?php

class ExpDrugs implements IExpDrugs
{
	public function addExpbyDrugs($gid, $hid)
	{
		$user = EnUser::getUserObj();
		$bag = BagManager::getInstance()->getBag();
		$itemInfo = $bag->gridInfo($gid);
		$item = ItemManager::getInstance()->getItem($itemInfo['item_id']);
		$useInfo = $item->useInfo();
		$exp = $useInfo[ItemDef::ITEM_ATTR_NAME_USE_PILL_EXP];
		$bag->decreaseItem($itemInfo['item_id'],1);
		$heroObj = $user->getHeroObj($hid);
		$heroObj->addExp($exp);
		$user->update();		
		$ret = array('hid' => $hid, 'exp' => $exp, 'lv' => $heroObj->getLevel(), 'baginfo' => $bag->update());
		return $ret;
	}
}
