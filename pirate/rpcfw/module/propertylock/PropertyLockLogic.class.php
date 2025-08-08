<?php

class PropertyLockLogic
{
	private static $AllField = array('failures_password', 'failures_question', 'failures_time', 'status', 'va_property_lock');
	
	private static function insertDefault($uid)
	{
		$arrField = array('va_property_lock'=>array('password', 'question_num', 'answer'));
		PropertyLockDao::insert($uid, $arrField);
	}

	public static function getStatus($uid)
	{
		$ret = PropertyLockDao::get($uid, self::$AllField);
		if (empty($ret))
		{
			self::insertDefault($uid);
			$ret = PropertyLockDao::get($uid, self::$AllField);
			return $ret;
		}
		if (!Util::isSameDay($ret['failures_time']))
		{
			$ret['failures_password'] = 0;
			$ret['failures_question'] = 0;
			$ret['failures_time'] = Util::getTime();
			PropertyLockDao::update($uid, $ret);
		}
		return $ret;
	}

	public static function initPassword($uid, $pass1, $pass2, $ques, $ans)
	{
		$info['password'] = $pass1;
		$info['question_num'] = $ques;
		$info['answer'] = $ans;
		PropertyLockDao::update($uid, array('status'=>2, 'va_property_lock'=>$info));
		return array('ret' => 'ok');
	}
	
	public static function unlock($uid, $pass, $type)
	{
		$ret = array('ret' => 'err');
		$info = self::getStatus($uid);
		if ($pass != $info['va_property_lock']['password'])
		{
			$info['failures_password']++;
			$info['failures_time'] = Util::getTime();
			PropertyLockDao::update($uid, array('failures_password'=>$info['failures_password'], 'failures_time'=>$info['failures_time']));
			$ret['failures_password'] = $info['failures_password'];
			return $ret;
		}
		$ret = array('ret' => 'ok');
		return $ret;
	}
	
	public static function questionReset($uid, $ques, $ans)
	{
		$ret = array('ret' => 'err');
		$info = self::getStatus($uid);
		if ($ques != $info['va_property_lock']['question_num'] || $ans != $info['va_property_lock']['answer'])
		{
			$info['failures_question']++;
			$info['failures_time'] = Util::getTime();
			PropertyLockDao::update($uid, $info);
			$ret['failures_question'] = $info['failures_question'];
			return $ret;
		}
		$ret = array('ret' => 'ok');
		return $ret;
	}
	
	public static function reset($uid, $oldPass, $pass1, $pass2)
	{
		$ret = array('ret' => 'err');
		$info = self::getStatus($uid);

		if ($oldPass == $info['va_property_lock']['password'] && $pass1 == $pass2)
		{
			$info['password'] = $pass1;
			PropertyLockDao::update($uid, array('va_property_lock'=>$info['va_property_lock']));
			return array('ret' => 'ok');
		}
		$info['failures_password']++;
		$info['failures_time'] = Util::getTime();
		PropertyLockDao::update($uid, array('failures_password'=>$info['failures_password'], 'failures_time'=>$info['failures_time']));
		$ret['failures_password'] = $info['failures_password'];		
		return $ret;
	}
}
