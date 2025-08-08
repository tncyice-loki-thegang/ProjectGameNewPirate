<?php
class CheckUserVa extends BaseScript
{
	protected function executeScript($arrOption)
	{			
		$arrField = array('uid', 'pid', 'va_user');

		$data = new CData();
		$arrRet = $data->select($arrField)->from('t_user')->where('uid', '=',10001)->query();
		
		foreach ($arrRet as $data)
		{
			
			$uid = $data['uid'];
			$va = $data['va_user'];
			var_dump($va);
		}
	}
}