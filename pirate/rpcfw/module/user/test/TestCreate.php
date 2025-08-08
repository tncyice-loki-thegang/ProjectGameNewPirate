<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/

require_once (MOD_ROOT . '/user/index.php');

class EnUserTest extends PHPUnit_Framework_TestCase 
{
	private $user;
	private $uid;
	
	protected function setUp() 
	{
		parent::setUp ();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		parent::tearDown ();
	}

	public function test_abc_0()
	{
		$arr = array(
'00',
'000',
'1',
'111',
'111111',
'32423432',
'abcdefg',
'hold住',
'hoping',
'ssssssss',
'亚伦亚索',
'伊罗杜拉',
'俞氏',
'冰块',
'吉米基夫',
'夜猫',
'好可怕',
'妮妮',
'娜芙亚洛儿',
'宝亚若拉',
'小寇',
'布洛基莱德',
'张三',
'德乌姆米霍',
'李四',
'泡面凉了',
'波雅莫妮卡',
'空',
'空空',
'竞技场小丁',
'竞技场路人丙',
'竞技场路人乙',
'竞技场路人甲',
'竞技场酱油',
'艾比歌特',
'请输入昵称',
'贝丝登拉',
'迪布莉莎',
'阿空',
'雷尔罗捷',
'齐亚特门西',
1,
0,
345,
10000,
	);

		$arr = array("0", "000")		;
		
		foreach ($arr as $uname)
		{
			$this->pid = 40000 + rand(0,9999);
			$this->utid = 1;
			$arrRet = UserLogic::createUser($this->pid, $this->utid, $uname);
			if ($arrRet['ret']=='ok')
			{
				var_dump($uname);
			}
			else
			{
				var_dump("fail: $uname");
			}
		}

	}

}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */