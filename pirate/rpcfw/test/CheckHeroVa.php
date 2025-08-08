<?php
class CheckHeroVa extends BaseScript
{
	protected function executeScript($arrOption)
	{			
		$arrField = array('hid', 'uid', 'va_hero');

		$data = new CData();
		$arrRet = $data->select($arrField)->from('t_hero')->where('hid', '=',10000001)->query();
		
		foreach ($arrRet as $data)
		{
			
			$uid = $data['uid'];
			$va = $data['va_hero'];
			var_dump($va);
		}
	}
}