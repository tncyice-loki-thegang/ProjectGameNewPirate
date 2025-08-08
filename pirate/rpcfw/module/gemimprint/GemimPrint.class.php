<?php

class GemimPrint implements IGemimPrint
{
	public function materialsInfo() 
	{
		$uid = RPCContext::getInstance()->getUid();
		$info = CruiseDao::get($uid, array('carved_stone'));		
		return $info;
	}
	
	public function gemImprint($item_id=FALSE)
	{
		$item_id = intval($item_id);
		if ($item_id!=0)
		{
			$item = ItemManager::getInstance()->getItem($item_id);
			$itemTempLateId = $item->getItemTemplateID();
			$printLv = $item->getPrintLevel();
			$needStone = btstore_get()->ITEMS[$itemTempLateId][ItemDef::ITEM_ATTR_NAME_GEM_IMPRINT_COST][$printLv];
			$uid = RPCContext::getInstance()->getUid();
			CruiseLogic::subCarvedStone($uid, $needStone);
			// $itemQualityId = btstore_get()->ITEMS[$itemTempLateId][ItemDef::ITEM_ATTR_NAME_GEM_ATTR_QUALITY_ID][$printLv];
			// $itemPrintAttr = btstore_get()->IMPRINT_QUALITY[$itemQualityId][1];			
			$item->setPrintLevel($printLv+1);
			ItemManager::getInstance()->update();
		}
	}
	
}
