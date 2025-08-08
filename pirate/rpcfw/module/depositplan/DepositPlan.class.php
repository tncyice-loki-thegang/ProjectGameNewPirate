<?php

class DepositPlan implements IDepositPlan
{

	public function __construct()
	{
		$this->uid = RPCContext::getInstance()->getUid();
	}	

	private static function insertDefault($uid)
	{
		DepositPlanDao::insert($uid, array('va_info' => array()));
	}

	public function getDepositPlanInfo()
	{
		$ret = DepositPlanDao::get($this->uid, array('va_info'));
		if (empty($ret))
		{
			self::insertDefault($this->uid);
			$ret = DepositPlanDao::get($this->uid, array('va_info' => array()));
		}
		return $ret['va_info'];
	}
	
	public function buyDepositPlan($id, $num)
	{
		$va = self::getDepositPlanInfo();
		$gold = btstore_get()->FOUNDATION[$id]['gold'];
		$user = EnUser::getInstance();
		$user->subGold($gold*$num);
		$user->update();
		$conf['id'] = $id;
		$conf['num'] = $num;
		$conf['reward'] = 0;
		$closeTime = DepositPlanConf::CLOSE_YMD.DepositPlanConf::CLOSE_TIME;
		$conf['time'] = strtotime($closeTime);
		array_push($va, $conf);
		DepositPlanDao::update($this->uid, array('va_info'=>$va));
	}
	
	public function receivePrize($pos)
	{
		$va = self::getDepositPlanInfo();
		$data = btstore_get()->FOUNDATION[$va[$pos]['id']];
		$day_num = Util::getDaysBetween($va[$pos]['time']);
		$returnGold = 0;		
		$i=0;
		foreach ($data['return'] as $day => $gold)
		{
			if ($day_num>=$day)
			{
				$returnGold += intval($gold);
				$i++;
				$va[$pos]['reward'] = $i;
			}			
		}
		$user = EnUser::getInstance();
		$user->addGold($returnGold*$va[$pos]['num']);
		$user->update();
		DepositPlanDao::update($this->uid, array('va_info'=>$va));
		self::getDepositPlanInfo();
	}
}
