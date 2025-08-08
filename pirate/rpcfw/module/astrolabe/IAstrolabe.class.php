<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IAstrolabe.class.php 32905 2012-12-12 04:45:55Z yangwenhai $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/astrolabe/IAstrolabe.class.php $
 * @author $Author: yangwenhai $(yangwenhai@babeltime.com)
 * @date $Date: 2012-12-12 12:45:55 +0800 (三, 2012-12-12) $
 * @version $Revision: 32905 $
 * @brief 
 *  
 **/
interface IAstrolabe
{
	/**
	 * 请求星盘的初始化信息
	 *
	 * @return 
	 * <code>
	 * {
	 * 		'basic_ast_lev':int  主星盘等级
	 * 		'ast_status':array	  当前各个天赋星盘的状态  0未激活 1可以激活 2已经激活 3已经装备	
	 * 		[id=>stat,]		   	  星盘id => 星盘状态				
	 * 		'talent_ast_id':int  当前装备的天赋星盘的id		
	 *      'skill_stat':int     当前装备的天赋星盘的普通技能状态，0 未激活 1 已激活	
	 *      'cons_status':array  当前装备的天赋星盘内，各个星座的状态0未激活 1可以激活 2已经激活
	 *      [id => stat,]        星座id => 状态
	 *      'cons_lev':array     当前装备的天赋星盘内，各个星座的等级		
	 *      [id => lev]			  星座id =>  等级	
	 *      'stone_num':int      星灵石个数
	 *      'belly_count':int    贝里购买剩余次数
	 *      'vip_count':int		 vip购买剩余次数
	 * 		'can_get_exp':int    能否获得今天的星盘经验 0不能获得 1可以获得
	 * }
	 * </code>
	 *
	 */
	public function askInitInfo ();
	
	/**
	 * 请求升级主星盘
	 * @return
	 * <code>
	 * {
	 * 		'stone_num':int      星灵石个数
	 * 		'basic_ast_lev':int  主星盘等级
	 * 		'activeTray':array	   主星盘升级后会激活其他星盘，看看当前各个天赋星盘的状态  0未激活 1可以激活 2已经激活 3已经装备
	 * 		[id=>stat,]		   	  星盘id => 星盘状态
	 * 		'activeStart':array  主星盘升级后会激活其他星座，看看当前依赖主星盘的天赋星座的状态0未激活 1可以激活 2已经激活
	 * 		[id=>stat,]		   	  星座id => 星座状态
	 * 		'can_get_exp':int    能否获得今天的星盘经验 0不能获得 1可以获得
	 * }
	 * </code>
	 *
	 */
	public function askLevelUpMain ();
	
	/**
	 * 请求升级天赋星座
	 *@param int $consid    天赋星座的id
	 * @return
	 * <code>
	 * {
	 * 		'stone_num':int      星灵石个数
	 * 		'newLevel':int  	   天赋星座升级后的等级
	 * 		'activeTray':array	   天赋星座升级后会激活其他星盘，看看当前各个天赋星盘的状态  0未激活 1可以激活 2已经激活 3已经装备
	 * 		[id=>stat,]		   	  星盘id => 星盘状态
	 * 		'activeStart':array  天赋星座升级后会激活其他星座，看看当前依赖该星座的其他天赋星座的状态 0未激活 1可以激活 2已经激活
	 * 		[id=>stat,]		   	  星座id => 星座状态
	 * 	    'skill_stat'         天赋星座的升级可以使普通技能激活，看看当前依赖该星座的普通技能的状态
	 *      [id=>stat,]          天赋星盘的id=>普通技能的状态
	 * }
	 * </code>
	 *
	 */
	public function askLevelUpCons ($consid);
	
	/**
	 * 请求切换天赋星盘
	 *@param int $consid    天赋星盘的id
	 * @return
	 * <code>
	 * {
	 * 		'cons_status':array	   该天赋星盘内给个星座的状态  0未激活 1可以激活 2已经激活
	 * 		[id=>stat,]		   	  星座id => 星座状态
	 * 		'cons_lev':array     该天赋星盘内给个星座的等级
	 * 		[id=>lev,]		   	  星座id => 星座等级
	 * 	    'skill_stat'：int    该天赋星盘内的普通技能的状态
	 * }
	 * </code>
	 *
	 */
	public function askSwitchTalentAst ($astid);
	
	/**
	 * 请求装备天赋星盘
	 *@param int $consid    天赋星盘的id
	 * @return
	 * <code>
	 * {
	 * 		'talent_ast_id':int	   该天赋星盘的id
	 * 	    'skill_stat'：int    该天赋星盘内的普通技能的状态
	 * }
	 * </code>
	 *
	 */
	public function askEquipTalentAst ($astid);
	
	/**
	 * 请求卸下天赋星盘
	 *@param int $consid    天赋星盘的id
	 * @return
	 */
	public function askUnEquipTalentAst ($astid);
	
	/**
	 * 请求买星灵石
	 *@param int $type    购买类型 0 贝里购买 1金币购买  2 vip购买 3高级购买
	 * @return 			      如果购买失败返回null    
	 * <code>
	 * {
	 *      'stone_num':int      购买后星灵石个数
	 *      'belly_count':int    贝里购买剩余次数
	 *      'vip_count':int		 vip购买剩余次数
	 * }
	 * </code>
	 */
	public function askBuyStone ($type);
	
	/**
	 * *@param
	 * @return 	
	 * <code>
	 * {
	 *      'curuserlevel':int   增加经验后玩家的等级
	 *      'add_exp':int        增加了多少经验
	 *      'cur_exp'：int       当前有多少经验
	 * }
	 * </code>
	 */
	public function askObtainTodayExp();
	
	
	//重置天赋星盘，并将经验返回给玩家
	public function resetTalentAst($astid,$costid);
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */