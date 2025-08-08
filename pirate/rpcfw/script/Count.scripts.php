<?php
/**********************************************************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: Count.scripts.php 19706 2012-05-02 08:14:04Z YangLiu $
 * 
 **********************************************************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/script/Count.scripts.php $
 * @author $Author: YangLiu $(liuyang@babeltime.com)
 * @date $Date: 2012-05-02 16:14:04 +0800 (三, 2012-05-02) $
 * @version $Revision: 19706 $
 * @brief 
 *  
 **/

class CountDao
{
	/******************************************************************************************************************
     * 成员变量定义
     ******************************************************************************************************************/
	// 定义表的名称
	private static $tblCopy = 't_copy';
	private static $tblUser = 't_user';
	// 数据是否已经被删除（每个SQL基本都会用到）
	private static $status = array('status', '!=', DataDef::DELETED);

	public static function getUserCopy($uid, $copyID)
	{
		// 使用 uid 作为条件
		$data = new CData();
		$arrRet = $data->select(array('va_copy_info'))
		               ->from(self::$tblCopy)
					   ->where(array("uid", "=", $uid))
					   ->where(array("copy_id", "=", $copyID))
					   ->where(self::$status)->query();
		// 没打过副本，则返回0
		$ret = empty($arrRet) ? 0 : 1;
		// 如果打过副本，则查看是否达到最后了
		if ($ret === 1 && isset($arrRet[0]['va_copy_info']['progress'][33]))
		{
			$ret = 2;
		}
		return $ret;
	}

	public static function getUidByPid($pid)
	{
		// 使用 pid 作为条件
		$data = new CData();
		$arrRet = $data->select(array('uid'))
		               ->from(self::$tblUser)
					   ->where(array("pid", "=", $pid))->query();

		return empty($arrRet) ? 0 : $arrRet[0]['uid'];
	}
}


class Count extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		
		$file = fopen('COUNT.csv', 'w');
		fwrite($file, "用户名,是否登录,是否打副本,是否通关\n");
		
		$loginUserNum = 0;
		$attackNum = 0;
		$passNum = 0;

		// 统计所有用户 pid
		for ($i = 10000; $i < 15000; ++$i)
		{
			// 通过 pid 获取 uid
			$uid = CountDao::getUidByPid($i);
			echo "\n".$i."\n";

			// 通过uid 和 副本id 获取用户是否攻打副本的信息 
			$copy = CountDao::getUserCopy($uid, 1);

			$str = "test".$i;
			if ($uid == 0)
			{
				$str .= ",否,否,否\n";
			}
			else 
			{
				++$loginUserNum;

				if ($copy == 0)
				{
					$str .= ",是,否,否\n";
				}
				else if ($copy == 1)
				{
					++$attackNum;
					$str .= ",是,是,否\n";
				}
				else if ($copy == 2)
				{
					++$passNum;
					$str .= ",是,是,是\n";
				}
				else 
				{
					$str = "刘洋弄错了一行\n";
				}
			}

			// 写数据
			fwrite($file, $str);
		}

		fclose($file);

		echo "总登录用户: ".$loginUserNum."\n";
		echo "总攻打未通关用户: ".$attackNum."\n";
		echo "总通关用户: ".$passNum."\n";
		echo "\n".'ok'."\n";
	}
}



/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */