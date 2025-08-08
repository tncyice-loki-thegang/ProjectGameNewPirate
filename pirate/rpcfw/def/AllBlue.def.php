<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: AllBlue.def.php 36976 2013-01-24 09:16:54Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/AllBlue.def.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-01-24 17:16:54 +0800 (四, 2013-01-24) $
 * @version $Revision: 36976 $
 * @brief 
 *  
 **/
/**********************************************************************************************************************
 * Class       : AllBlueDef
 * Description : 伟大的航道数据常量定义类
 * Inherit     : 
 **********************************************************************************************************************/
class AllBlueDef
{
	const ALLBLUE_COLLECT_GOLD_LEVEL_NUM = 2;										// 金币采集有几档

	const ALLBLUE_FISHQUEUE_STATUS_NOOPEN = 0;										// 0:未开通,1:已开通
	const ALLBLUE_FISHQUEUE_STATUS_OPEN = 1;										// 0:未开通,1:已开通
	const ALLBLUE_FISH_STATUS_FREE = 0;												// 养鱼的状态 0:空闲
	const ALLBLUE_FISH_STATUS_FARM = 1;												// 养鱼的状态 1:养殖中
	const ALLBLUE_FISH_STATUS_RIPE = 2;												// 养鱼的状态 2:成熟
	
	const ALLBLUE_COLLECT_ID = 'allblue_collect_id';								// ID
	const ALLBLUE_COLLECT_BELLYCOUNT = 'allblue_collect_bellycount';				// 每采集区贝里采集次数
	const ALLBLUE_COLLECT_GOLDCOUNT = 'allblue_collect_goldcount';					// 每采集区金币采集次数
	const ALLBLUE_COLLECT_BASEBELLY = 'allblue_collect_basebelly';					// 贝里采集花费贝里
	const ALLBLUE_COLLECT_BASEGOLD = 'allblue_collect_basegold';					// 金币采集基础金币
	const ALLBLUE_COLLECT_ADDGOLD = 'allblue_collect_addgold';						// 金币采集每次递增金币
	const ALLBLUE_COLLECT_GETMONSTERWEIGHT = 'allblue_collect_getmonsterweight';	// 采集遇怪权重
	const ALLBLUE_COLLECT_MONSTERID = 'allblue_collect_monsterid';					// 海怪部队ID组
	const ALLBLUE_COLLECT_MONSTERIDWEIGHT = 'allblue_collect_monsteridweight';		// 海怪部队权重组
	const ALLBLUE_COLLECT_REMARKETGOLD = 'allblue_collect_remarketgold';			// 市场金币刷新基础金币
	const ALLBLUE_COLLECT_REMARKETADDGOLD = 'allblue_collect_remarketaddgold';		// 市场金币刷新每次递增金币
	const ALLBLUE_COLLECT_DAILYMARKETCOUNT = 'allblue_collect_dailymarketcount';	// 市场每日兑换次数
	const ALLBLUE_COLLECT_GOODS = 'allblue_collect_goods';							// 采集场掉落表ID
	const ALLBLUE_COLLECT_GOLD_LEVEL1 = 'allblue_collect_gold_level1';				// 1档金币花费金币
	const ALLBLUE_COLLECT_ADDGOLD_LEVEL1 = 'allblue_collect_addgold_level1';		// 1档金币花费每次递增金币
	const ALLBLUE_COLLECT_GOLD_LEVEL2 = 'allblue_collect_gold_level2';				// 2档金币采集花费金币
	const ALLBLUE_COLLECT_ADDGOLD_LEVEL2 = 'allblue_collect_addgold_level2';		// 2档金币花费每次递增金币
	const ALLBLUE_COLLECT_GOODS_LEVEL1 = 'allblue_collect_goods_level1';			// 1档金币采集掉落表组
	const ALLBLUE_COLLECT_GOODS_LEVEL2 = 'allblue_collect_goods_level2';			// 2档金币采集掉落表组
	const ALLBLUE_MARKET_REFRESH = 'allblue_market_refresh';						// 市场刷新时间间隔
	const ALLBLUE_MARKET_REFRESH_STARTTIME = 'allblue_market_refresh_starttime';	// 市场刷新开始时间
	const ALLBLUE_MARKET_REFRESH_ENDTIME = 'allblue_market_refresh_endtime';		// 市场刷新结束时间
	const ALLBLUE_MONSTER_FAIL_TIMES = 'allblue_monster_fail_times';				// 攻击还怪失败次数

	const ALLBLUE_COLLECT_MONSTERS = 'allblue_collect_monsters';					// 海怪部队
	const ALLBLUE_COLLECT_GOLD_LEVEL = 'allblue_collect_gold_level';				// 花费金币
	const ALLBLUE_COLLECT_ADDGOLD_LEVEL = 'allblue_collect_addgold_level';			// 金币花费每次递增金币
	const ALLBLUE_COLLECT_GOODS_LEVEL = 'allblue_collect_goods_level';				// 金币采集掉落表组
	
	// 兑换市场
	const ALLBLUE_MARKET_GOODSID = 'allblue_market_goodsid';						// 市场物品的ID
	const ALLBLUE_MARKET_EXGOODSID = 'allblue_market_exgoodsid';					// 可兑换物品ID
	const ALLBLUE_MARKET_EXGOODSNUM = 'allblue_market_exgoodsnum';					// 可兑换物品数量
	const ALLBLUE_MARKET_EXNEEDGOODSID = 'allblue_market_exneedgoodsid';			// 兑换需要物品ID
	const ALLBLUE_MARKET_EXNEEDGOODSNUM = 'allblue_market_exneedgoodsnum';			// 兑换需要物品数量
	const ALLBLUE_MARKET_REFRESHWEIGHT = 'allblue_market_refreshweight';			// 刷新权重
	
	// 养鱼
	const ALLBLUE_FARMFISH_TIMES = 'allblue_farmfish_times';						// 每日可养鱼次数
	const ALLBLUE_FARMFISH_QUEUE1GOLD = 'allblue_farmfish_queue1gold';				// 养鱼额外队列1所需金币
	const ALLBLUE_FARMFISH_QUEUE2GOLD = 'allblue_farmfish_queue2gold';				// 养鱼额外队列2所需金币
	const ALLBLUE_FARMFISH_KRILLGOLD = 'allblue_farmfish_krillgold';				// 捞鱼苗初始金币
	const ALLBLUE_FARMFISH_KRILLADDGOLD = 'allblue_farmfish_krilladdgold';			// 捞鱼苗递增金币
	const ALLBLUE_FARMFISH_KRILLINITGOLD = 'allblue_farmfish_krillinitgold';		// 鱼苗重置花费金币
	const ALLBLUE_FARMFISH_GROUPSEAFISH = 'allblue_farmfish_groupseafish';			// 海鱼ID组
	const ALLBLUE_FARMFISH_KRILLCOUNT = 'allblue_farmfish_krillcount';				// 鱼池初始鱼苗数
	const ALLBLUE_FARMFISH_DAILYWISHCOUNT = 'allblue_farmfish_dailywishcount';		// 每日可祝福次数
	const ALLBLUE_FARMFISH_QUEUEWISHCOUNT = 'allblue_farmfish_queuewishcount';		// 每序列可被祝福次数
	const ALLBLUE_FARMFISH_WISHSUBTIME = 'allblue_farmfish_wishsubtime';			// 祝福减少成熟时间
	const ALLBLUE_FARMFISH_QUEUETHIEFCOUNT = 'allblue_farmfish_queuethiefcount';	// 每序列可被偷取次数
	const ALLBLUE_FARMFISH_THIEFFISHCOUNT = 'allblue_farmfish_thieffishcount';		// 每次偷取获得鱼数量
	const ALLBLUE_FARMFISH_OPENBOOTGOLD = 'allblue_farmfish_openbootgold';			// 保护罩开启花费金币
	const ALLBLUE_FARMFISH_DAILYTHIEFCOUNT = 'allblue_farmfish_dailythiefcount';	// 每天可偷鱼次数
	const ALLBLUE_FARMFISH_WISHREWARD = 'allblue_farmfish_wishreward';				// 祝福获得贝利基础值
	
	const ALLBLUE_FARMFISH_ID = 'id';												// ID
	const ALLBLUE_FARMFISH_TEMPLATENAME = 'tname';									// 物品模板名称
	const ALLBLUE_FARMFISH_NAME = 'name';											// 物品名称
	const ALLBLUE_FARMFISH_INTROUDUCE = 'detail';									// 物品描述
	const ALLBLUE_FARMFISH_SMALLPIC = 's_ico';										// 物品小图标
	const ALLBLUE_FARMFISH_BIGPIC = 'b_ico';										// 物品大图标
	const ALLBLUE_FARMFISH_QUALITY = 'quality';										// 物品品质
	const ALLBLUE_FARMFISH_SELLABLE = 'sellable';									// 可否出售
	const ALLBLUE_FARMFISH_SELLTYPE = 'sell_type';									// 卖店可得类型ID
	const ALLBLUE_FARMFISH_SELLNUM = 'sell_num';									// 卖店可以获得该类型的数量
	const ALLBLUE_FARMFISH_MAXSTACK = 'maxstack';									// 堆叠上限
	const ALLBLUE_FARMFISH_BINDINGTYPE = 'bind_type';								// 绑定类型
	const ALLBLUE_FARMFISH_CANDESTROY = 'can_destroy';								// 可否摧毁
	const ALLBLUE_FARMFISH_PROCESSMODE = 'process_mode';							// 处理方式
	const ALLBLUE_FARMFISH_PETUPVALUE = 'feed_base';								// 宠物成长基础值值
	const ALLBLUE_FARMFISH_GETBELLY = 'get_belly_base';								// 喂养获得贝里基础值
	const ALLBLUE_FARMFISH_RIPETIME = 'ripe_time';									// 成熟时间
	const ALLBLUE_FARMFISH_GETFISHCOUNT = 'get_fish_count';							// 收获数量
	const ALLBLUE_FARMFISH_FISHINGWEIGHT = 'fishing_weight';						// 鱼苗捕获权重
	const ALLBLUE_FARMFISH_ISQUALIF = 'is_qualifications';							// 是否改变宠物资质
	const ALLBLUE_FARMFISH_QUALIFIUP = 'qualification_up';							// 洗炼宠物资质增加值
	const ALLBLUE_FARMFISH_QUALIFIFIX = 'qualification_fix';						// 洗炼宠物资质修正值
	const ALLBLUE_FARMFISH_QUALIFIPOW = 'qualification_pow';						// 改变宠物蛮力资质
	const ALLBLUE_FARMFISH_QUALIFISEN = 'qualification_sen';						// 改变宠物灵敏资质
	const ALLBLUE_FARMFISH_QUALIFIINT = 'qualification_int';						// 改变宠物智慧资质
	const ALLBLUE_FARMFISH_QUALIFIPHY = 'qualification_phy';						// 改变宠物体质资质
	const ALLBLUE_FARMFISH_QUALIFINUM = 'rand_qualification_num';					// 随机改变宠物资质数量
	const ALLBLUE_FARMFISH_REFISHINGWEIGHT = 'rand_qualification_weight';			// 鱼苗刷新权重(从10条鱼中选取5条鱼时候的所用权重)
	const ALLBLUE_FARMFISH_ICON = 'rand_fish_icon';									// 喂鱼图标
	const ALLBLUE_FARMFISH_STEALCOMPEN = 'rand_steal_compention';					// 偷鱼获得贝里基础值
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */