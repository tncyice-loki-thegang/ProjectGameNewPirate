<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IPayBack.class.php 34171 2013-01-05 04:02:25Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/payback/IPayBack.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2013-01-05 12:02:25 +0800 (六, 2013-01-05) $
 * @version $Revision: 34171 $
 * @brief 
 *  
 **/



interface IPayBack
{

	/**
	 *  获得当前时间段可用的补偿信息id
	 *  
	 * @return arry  返回当前可用的补偿id,以及该id对应的类型（是跨服战奖励还是系统补偿，以及具体的补偿内容）
	  * <code>
	 * {
	 * 	 id=>array(
	 *       type=>,
	 *       message=>,
	 *       belly=>, 
	 *       experience=>, 
	 *       prestige=>,
	 *       gold=>,
	 *       execution=>,
	 *       item_id=>,
	 *       item_num=>
	 * )
	 * 
	 * }
	 */
	public function  getCurAvailablePayBackIds();
	

	/**
	 * 执行所有的补偿
	 * @param array $arrayid array里是当前要执行的补偿id
	 * @return arry 执行后的返回值
	 * <code>
	 * {
	 *      'ret_status':int     返回值状态 1成功 0失败 -1传过来开的参数为空 -2补偿时间已经过了 -3该玩家已经领取过补偿了
	 *      'type':int           类型，跨服战奖励还是系统补偿
	 *      'message':string	  前端要显示的文字
	 *      'belly':int   	 	  补偿了多少贝里
	 *      'experience':int     补偿了多少阅历
	 *      'gold':int           补偿了多少金币
	 *      'execution':int      补偿了多少行动力
	 *      'prestige':int       补偿了多少声望
	 *      'baginfo':array        补偿的物品信息，如果没补偿物品则为空
	 *           [item_id=>,item_num=>，item_template_id=>，va_item_text=>array，reinforce_level=>]	物品id 物品个数 物品的模版id 等	   	 
	 * }
	 * </code>
	 */
	public function executeAllPayBack($arrayid);
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */