<?php
/***************************************************************************
 *
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id: Item.def.php 40182 2013-03-07 02:40:14Z yangwenhai $
 *
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/def/Item.def.php $
 * @author $Author: yangwenhai $(jhd@babeltime.com)
 * @date $Date: 2013-03-07 10:40:14 +0800 (四, 2013-03-07) $
 * @version $Revision: 40182 $
 * @brief
 *
 **/

class ItemDef
{
	//物品属性名
	/***basic***/
	const ITEM_ATTR_NAME_ITEM_TEMPLATE_ID					=			'item_template_id';
	const ITEM_ATTR_NAME_ITEM_NUM							=			'item_num';
	const ITEM_ATTR_NAME_QUALITY							=			'item_quality';
	const ITEM_ATTR_NAME_BIND								=			'bind';
	const ITEM_ATTR_NAME_EXP								=			'exp';

	/***use***/
	const ITEM_ATTR_NAME_USE								=			'use';
	const ITEM_ATTR_NAME_USE_REQ							=			'use_req';
	const ITEM_ATTR_NAME_USE_REQ_ITEMS						=			'use_req_items';
	const ITEM_ATTR_NAME_USE_REQ_BELLY						=			'use_req_belly';
	const ITEM_ATTR_NAME_USE_REQ_GOLD						=			'use_req_gold';
	const ITEM_ATTR_NAME_USE_REQ_DELAYTIME					=			'use_req_delaytime';
	const ITEM_ATTR_NAME_USE_REQ_USER_LEVEL					=			'use_req_user_level';
	const ITEM_ATTR_NAME_USE_INFO							=			'use_info';
	const ITEM_ATTR_NAME_USE_ITEMS							=			'use_items';
	const ITEM_ATTR_NAME_USE_DROP_TEMPLATE_ID				=			'use_drop_template_id';
	const ITEM_ATTR_NAME_USE_ITEM_CHOOSE					=			'use_item_choose';
	const ITEM_ATTR_NAME_USE_BELLY							=			'use_belly';
	const ITEM_ATTR_NAME_USE_GOLD							=			'use_gold';
	const ITEM_ATTR_NAME_USE_BLOOD_PACKAGE					=			'use_blood_package';	//增加血池
	const ITEM_ATTR_NAME_USE_EXECUTION						=			'use_execution';		//增加行动力
	const ITEM_ATTR_NAME_USE_FOOD							=			'use_food';				//增加食物
	const ITEM_ATTR_NAME_USE_PRESTIGE						=			'use_prestige';			//增加威望
	const ITEM_ATTR_NAME_USE_EXPRIENCE						=			'use_exprience';		//增加阅历
	const ITEM_ATTR_NAME_USE_PET_TEMPLATE_ID				=			'use_pet_template_id';
	const ITEM_ATTR_NAME_USE_MOUNT_TEMPLATE_ID				=			'use_mount_template_id';
	const ITEM_ATTR_NAME_USE_DEMON_TEMPLATE_ID				=			'use_demon_template_id';
	const ITEM_ATTR_NAME_USE_TITLE							=			'use_title';			//获得称号
	const ITEM_ATTR_NAME_USE_STAR_STONE						=			'use_star_stone';		//增加星灵石
	const ITEM_ATTR_NAME_USE_HERO							=			'use_hero';				//获得英雄
	const ITEM_ATTR_NAME_USE_TREASURE_PURPLE				=			'use_treasure_purple';	//增加寻宝的紫星
	const ITEM_ATTR_NAME_USE_TREASURE_RED					=			'use_treasure_purple';	//增加寻宝的红星
	const ITEM_ATTR_NAME_USE_EQUIP_PURPLE					=			'use_equip_purple';		//增加装备制作的紫星
	const ITEM_ATTR_NAME_USE_EQUIP_RED						=			'use_equip_red';		//增加装备制作的红星
	const ITEM_ATTR_NAME_USE_ELEMENT						=			'use_element';			//增加元素石
	const ITEM_ATTR_NAME_USE_ENERGY							=			'use_energy';			//增加能量石
	const ITEM_ATTR_NAME_USE_HONOUR							=			'use_honour';			//增加荣誉
	const ITEM_ATTR_NAME_USE_PURPLE_SOUL					=			'use_purple_soul';		//增加紫魂
	const ITEM_ATTR_NAME_USE_HTID_ITEMS						=			'use_htid_items';
	const ITEM_ATTR_NAME_USE_DOMINEER						=			'use_domineer';
	const ITEM_ATTR_NAME_USE_DEMON_KERNEL					=			'use_demon_kernel';
	const ITEM_ATTR_NAME_USE_SEA_SOUL						=			'use_sea_soul';
	const ITEM_ATTR_NAME_USE_GEM_EXP						=			'use_gem_exp';
	const ITEM_ATTR_NAME_USE_GEM_SCORE						=			'use_gem_score';
	const ITEM_ATTR_NAME_USE_GEM_ESSENCE					=			'use_gem_essence';
	const ITEM_ATTR_NAME_USE_IS_MATERAL						=			'use_is_materal';
	const ITEM_ATTR_NAME_USE_ADD_PETSKILL					=			'use_add_petskill';
	const ITEM_ATTR_NAME_USE_PILL_EXP						=			'use_pill_exp';
	const ITEM_ATTR_NAME_USE_GUILD_CONTRIBUTIONS			=			'use_guild_contributions';
	const ITEM_ATTR_NAME_USE_GUILD_EXP						=			'use_guild_exp';
	const ITEM_ATTR_NAME_USE_ALLBLUE_DONATE_SCORE			=			'use_allblue_donate_score';
	const ITEM_ATTR_NAME_USE_ADD_ASSIST_PETSKILL			=			'use_add_assist_petskill';
	const ITEM_ATTR_NAME_USE_CAN_COMPOSITE					=			'use_can_composite';
	const ITEM_ATTR_NAME_USE_ADD_PEAKFIGHT_HONOR			=			'use_add_peakfight_honor';
	const ITEM_ATTR_NAME_USE_ADD_PEAKFIGHT_DONATE			=			'use_add_peakfight_donate';
	const ITEM_ATTR_NAME_USE_DECORATION_CRYSTAL				=			'use_decoration_crystal';
	const ITEM_ATTR_NAME_USE_DECORATION						=			'use_decoration';	
	const ITEM_ATTR_NAME_USE_DAIMONAPPLE_EXP				=			'use_daimonapple_exp';
	
	/***sell***/
	const ITEM_ATTR_NAME_SELL								=			'sell';
	const ITEM_ATTR_NAME_SELL_PRICE							=			'sell_price';
	const ITEM_ATTR_NAME_SELL_TYPE							=			'sell_type';

	const ITEM_ATTR_NAME_STACKABLE							=			'stackable';
	const ITEM_ATTR_NAME_RANDSKILLLIST						=			'random_skill_list';
	const ITEM_ATTR_NAME_DESTORY							=			'destory';
	const ITEM_ATTR_NAME_TYPE								=			'type';
	const ITEM_ATTR_NAME_DROP_TEMPLATE_ID					=			'drop_template_id';

	/***Arm***/
	const ITEM_ATTR_NAME_ARM_TYPE							=			'arm_type';
	const ITEM_ATTR_NAME_USER_LEVEL							=			'user_level';
	const ITEM_ATTR_NAME_HERO_LEVEL							=			'hero_level';
	const ITEM_ATTR_NAME_HERO_VOCATION						=			'hero_vocation';
	const ITEM_ATTR_NAME_ARM_ENCHANSE						=			'arm_enchanse';
	const ITEM_ATTR_NAME_EXCHANGE_ID						=			'exchange_id';
	const ITEM_ATTR_NAME_REBIRTH_NUM						=			'rebirth_num';
	const ITEM_ATTR_NAME_CAN_GILDING						=			'can_gilding';
	const ITEM_ATTR_NAME_GILDING_RATIO						=			'gilding_ratio';
	const ITEM_ATTR_NAME_MAX_GILDING_LV						=			'max_gilding_lv';
	const ITEM_ATTR_NAME_GILDING_ID							=			'gilding_id';
	const ITEM_ATTR_NAME_ISDARKGOLD							=			'isdarkgold';
	const ITEM_ATTR_NAME_SUITS								=			'suits';
	
	//daimonapple donation
	const ITEM_ATTR_NAME_DAIMONAPPLE_DONATION				=			'daimonapple_donation';

	//reinfore level
	const ITEM_ATTR_NAME_REINFORCE_LEVEL					=			'reinforce_level';
	const ITEM_ATTR_NAME_GILD_LEVEL							=			'gildlevel';

	//potentiality
	const ITEM_ATTR_NAME_POTENTIALITY						=			'potentiality';
	const ITEM_ATTR_NAME_POTENTIALITY_RAND_REFRESH			=			'potentiality_rand_refresh';
	const ITEM_ATTR_NAME_POTENTIALITY_FIXED_REFRESH			=			'potentiality_fixed_refresh';
	const ITEM_ATTR_NAME_REFRESH_RANDPOTENTIALITY_ENABLE	=			'random_refresh_potentiality_able';
	const ITEM_ATTR_NAME_REFRESH_FIXEDPOTENTIALITY_ENABLE	=			'fixed_refresh_potentiality_able';
	const ITEM_ATTR_NAME_RANDPOTENTIALITY					=			'random_potentiality_id';
	const ITEM_ATTR_NAME_FIXED_POTENTIALITY					=			'fixed_potentiality_id';
	const ITEM_ATTR_NAME_RAND_REFRESH_BELLY					=			'random_refresh_belly';
	const ITEM_ATTR_NAME_FIXED_REFRESH_BELLY				=			'fixed_refresh_belly';
	//physical attack
	const ITEM_ATTR_NAME_PHYSICAL_ATTACK					=			'physical_attack';
	const ITEM_ATTR_NAME_PHYSICAL_ATTACK_BASIC				=			'physical_attack_basic';
	const ITEM_ATTR_NAME_PHYSICAL_ATTACK_REINFORCE			=			'reinforce_physical_attack';
	const ITEM_ATTR_NAME_PHYSICAL_ATTRCK_PERCENT			=			'physical_attack_percent';
	//kill attack
	const ITEM_ATTR_NAME_KILL_ATTACK						=			'kill_attack';
	const ITEM_ATTR_NAME_KILL_ATTACK_BASIC					=			'kill_attack_basic';
	const ITEM_ATTR_NAME_KILL_ATTACK_REINFORCE				=			'reinforce_kill_attack';
	const ITEM_ATTR_NAME_KILL_ATTACK_PERCENT				=			'kill_attack_percent';
	//magic attack
	const ITEM_ATTR_NAME_MAGIC_ATTACK						=			'magic_attack';
	const ITEM_ATTR_NAME_MAGIC_ATTACK_BASIC					=			'magic_attack_basic';
	const ITEM_ATTR_NAME_MAGIC_ATTACK_REINFORCE				=			'reinforce_magic_attack';
	const ITEM_ATTR_NAME_MAGIC_ATTACK_PERCENT				=			'magic_attack_percent';
	//physical defence
	const ITEM_ATTR_NAME_PHYSICAL_DEFENCE					=			'physical_defence';
	const ITEM_ATTR_NAME_PHYSICAL_DEFENCE_BASIC				=			'physical_defence_basic';
	const ITEM_ATTR_NAME_PHYSICAL_DEFENCE_REINFORCE			=			'reinforce_physical_defence';
	const ITEM_ATTR_NAME_PHYSICAL_DEFENCE_PERCENT			=			'physical_defence_percent';
	//kill defence
	const ITEM_ATTR_NAME_KILL_DEFENCE						=			'kill_defence';
	const ITEM_ATTR_NAME_KILL_DEFENCE_BASIC					=			'kill_defence_basic';
	const ITEM_ATTR_NAME_KILL_DEFENCE_REINFORCE				=			'reinforce_kill_defence';
	const ITEM_ATTR_NAME_KILL_DEFENCE_PERCENT				=			'kill_defence_percent';
	//magic defence
	const ITEM_ATTR_NAME_MAGIC_DEFENCE						=			'magic_defence';
	const ITEM_ATTR_NAME_MAGIC_DEFENCE_BASIC				=			'magic_defence_basic';
	const ITEM_ATTR_NAME_MAGIC_DEFENCE_REINFORCE			=			'reinforce_magic_defence';
	const ITEM_ATTR_NAME_MAGIC_DEFENCE_PERCENT				=			'magic_defence_percent';
	
	//伤害倍率
	const ITEM_ATTR_NAME_PYH_ATT_GIFT						=           'phyAttgift';	//物理伤害倍率
	const ITEM_ATTR_NAME_PYH_DEF_GIFT						=           'phyDefgift';	//物理免伤倍率
	const ITEM_ATTR_NAME_KILL_ATT_GIFT						=           'kilAttgift';	//必杀伤害倍率
	const ITEM_ATTR_NAME_KILL_DEF_GIFT						=           'kilDefgift';	//必杀免伤倍率
	const ITEM_ATTR_NAME_MAG_ATT_GIFT						=           'magAttgift';	//魔法伤害倍率
	const ITEM_ATTR_NAME_MAG_DEF_GIFT						=           'magDefgift';	//魔法免伤倍率
	
	const ITEM_ATTR_NAME_NORMAL_ATT_RATIO					=           'normalAttRatio';//调整普通攻击伤害比
	const ITEM_ATTR_NAME_NORMAL_DEF_RATIO					=			'normalDefRatio';//调整普通攻击免伤比
	const ITEM_ATTR_NAME_RAGER_ATT_RATIO					=			'ragerAttRatio'; //调整怒气攻击伤害比
	const ITEM_ATTR_NAME_RAGER_DEF_RATIO					=			'ragerDefRatio'; //调整怒气攻击免伤比
	const ITEM_ATTR_NAME_TREAT_RATIO						=			'treatRatio';	 //调整治疗比率
	const ITEM_ATTR_NAME_TREATED_RATIO						=			'treatedRatio';  //调整被治疗比率
	
	//life
	const ITEM_ATTR_NAME_HP									=			'hp';
	const ITEM_ATTR_NAME_HP_BASIC							=			'hp_basic';
	const ITEM_ATTR_NAME_HP_REINFORCE						=			'reinforce_hp';
	const ITEM_ATTR_NAME_HP_PERCENT							=			'hp_percent';
	//arm basic
	//strength
	const ITEM_ATTR_NAME_STRENGTH							=			'strength';
	const ITEM_ATTR_NAME_STRENGTH_PERCENT					=			'strength_percent';
	//agility
	const ITEM_ATTR_NAME_AGILITY							=			'agility';
	const ITEM_ATTR_NAME_AGILITY_PERCENT					=			'agility_percent';
	//intelligence
	const ITEM_ATTR_NAME_INTELLIGENCE						=			'intelligence';
	const ITEM_ATTR_NAME_INTELLIGENCE_PERCENT				=			'intelligence_percent';
	//hit rating
	const ITEM_ATTR_NAME_HIT_RATING							=			'hit_rating';
	const ITEM_ATTR_NAME_HIT_RATING_BASIC					=			'hit_rating_basic';
	const ITEM_ATTR_NAME_HIT_RATING_REINFORCE				=			'hit_rating_reinforce';
	//fatal
	const ITEM_ATTR_NAME_FATAL								=			'fatal';
	const ITEM_ATTR_NAME_FATAL_BASIC						=			'fatal_basic';
	const ITEM_ATTR_NAME_FATAL_REINFORCE					=			'fatal_reinforce';
	//dodge
	const ITEM_ATTR_NAME_DODGE								=			'dodge';
	const ITEM_ATTR_NAME_DODGE_BASIC						=			'dodge_basic';
	const ITEM_ATTR_NAME_DODGE_REINFORCE					=			'dodge_reinforce';
	//other
	const ITEM_ATTR_NAME_PARRY								=			'parry';
	const ITEM_ATTR_NAME_WIND_ATTACK						=			'wind_attack';
	const ITEM_ATTR_NAME_THUNDER_ATTACK						=			'thunder_attack';
	const ITEM_ATTR_NAME_WATER_ATTACK						=			'water_attack';
	const ITEM_ATTR_NAME_FIRE_ATTACK						=			'fire_attack';
	//arm resistance
	const ITEM_ATTR_NAME_WIND_RESISTANCE					=			'wind_resistance';
	const ITEM_ATTR_NAME_THUNDER_RESISTANCE					=			'thunder_resistance';
	const ITEM_ATTR_NAME_WATER_RESISTANCE					=			'water_resistance';
	const ITEM_ATTR_NAME_FIRE_RESISTANCE					=			'fire_resistance';
	
	const ITEM_ATTR_NAME_RAGE								=			'rage';
	
	//arm damage
	const ITEM_ATTR_NAME_DAMAGE								=			'damage';
	const ITEM_ATTR_NAME_AVOID_DAMAGE						=			'avoid_damage';
	//arm reinforce
	const ITEM_ATTR_NAME_REINFORCE_FEE						=			'reinforce_fee';
	const ITEM_ATTR_NAME_REINFORCE_INC_TIME					=			'reinforce_inc_time';
	const ITEM_ATTR_NAME_REINFORCE_ENABLE					=			'reinforce_enable';
	const ITEM_ATTR_NAME_INIT_REINFORCE_LEVEL				=			'init_reinforce_level';
	//arm enchase
	const ITEM_ATTR_NAME_ENCHASE_REQ						=			'enchase_req';

	/***Gem***/
	const ITEM_ATTR_NAME_GEM_ARM_TYPE						=			'gem_arm_type';
	const ITEM_ATTR_NAME_GEM_ATTR_NUM						=			'gem_attr_num';
	const ITEM_ATTR_NAME_GEM_ATTR							=			'gem_attr';
	const ITEM_ATTR_NAME_GEM_ENCHASE_BELLY					=			'gem_enchase_belly';
	const ITEM_ATTR_NAME_GEM_ENCHASE_GOLD					=			'gem_enchase_gold';
	const ITEM_ATTR_NAME_GEM_SPLIT_BELLY					=			'gem_split_belly';
	const ITEM_ATTR_NAME_GEM_SPLIT_GOLD						=			'gem_split_gold';
	const ITEM_ATTR_NAME_GEM_MAX_LEVEL						=			'gem_max_level';
	const ITEM_ATTR_NAME_GEM_LEVEL_TABLE					=			'gem_level_table';
	const ITEM_ATTR_NAME_GEM_ATTR_REINFORCE					=			'gem_attr_reinforce';
	const ITEM_ATTR_NAME_GEM_EXP							=			'gem_exp';
	const ITEM_ATTR_NAME_GEM_IMPRINT_LEVEL					=			'gem_imprint_level';
	const ITEM_ATTR_NAME_GEM_QUALITY_ID						=			'gem_quality_id';
	const ITEM_ATTR_NAME_GEM_IMPRINT_COST					=			'gem_imprint_cost';
	const ITEM_ATTR_NAME_GEM_PRINT_LEVEL					=			'gem_imprint_lv';
	const ITEM_ATTR_NAME_GEM_GOLD_SMITH_COST				=			'gem_gold_smith_cost';
	const ITEM_ATTR_NAME_GEM_ESSENCE_SMITH_COST				=			'gem_essence_smith_cost';
	const ITEM_ATTR_NAME_GEM_ITEM_SMITH_COST				=			'gem_item_smith_cost';
	
	const ITEM_ATTR_GEM_XILIAN_IDS_							=			'xilian_ids_';			//第X层可洗炼属性ID组
	const ITEM_ATTR_GEM_XILIAN_RATES_						=			'xilian_rates_';		//第X层洗炼权重组		
	const ITEM_GEM_ALL_ARM_TYPE								=			10;
	const ITEM_GEM_MIN_LEVEL								=			1;
	const ITEM_ATTR_GEM_TEXT_SEAL							=			'seal';					//封印属性数据
	const ITEM_ATTR_GEM_TEXT_FRESH							=			'seal_general_fixed';	//封印属性洗练数据


	/**Gift**/
	const ITEM_ATTR_NAME_GIFT_ITEMS							=			'gift_items';
	const ITEM_ATTR_NAME_GIFT_ITEM_NUM						=			'gift_item_num';
	
	/**Fish**/
	const ITEM_ATTR_NAME_FISH_FEED_GET_VALUE				=			'fish_feed_get_value';
	const ITEM_ATTR_NAME_FISH_FEED_GET_BELLY				=			'fish_feed_get_belly';
	const ITEM_ATTR_NAME_FISH_RIPE_TIME						=			'fish_ripe_time';
	const ITEM_ATTR_NAME_FISH_GET_NUM						=			'fish_get_num';
	const ITEM_ATTR_NAME_FISH_TYPE							=			'fish_type';

	/**DeamonApple**/
	const ITEM_ATTR_NAME_DAIMONAPPLE_ATTRS					=			'daimonapple_attrs';
	const ITEM_ATTR_NAME_DAIMONAPPLE_SKILLS					=			'daimonapple_skills';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE				=			'daimonapple_erasure';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_BELLY			=			'daimonapple_erasure_belly';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_GOLD			=			'daimonapple_erasure_gold';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ERASURE_ITEMS			=			'daimonapple_erasure_items';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_REINFORCE			=			'daimonapple_attr_reinforce';
	const ITEM_ATTR_NAME_DAIMONAPPLE_CAN_UP					=			'can_up';
	const ITEM_ATTR_NAME_DAIMONAPPLE_LEVEL_TABLE			=			'level_table';
	const ITEM_ATTR_NAME_DAIMONAPPLE_MAX_LEVEL				=			'max_level';
	const ITEM_ATTR_NAME_DAIMONAPPLE_DECOMPOSE_NEED_PRESTIGE=			'decompose_need_prestige';
	const ITEM_ATTR_NAME_DAIMONAPPLE_DECOMPOSE_ITEM			=			'decompose_item';
	const ITEM_ATTR_NAME_DAIMONAPPLE_CAN_FINING				=			'can_fining';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE				=			'attr_fine';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_FINE_REINFORCE	=			'attr_fine_reinforce';
	const ITEM_ATTR_NAME_DAIMONAPPLE_FINE_MAX_LEVEL			=			'fine_max_level';
	const ITEM_ATTR_NAME_DAIMONAPPLE_FINE_COST				=			'fine_cost';
	const ITEM_ATTR_NAME_DAIMONAPPLE_MIST_LEVEL_LIMIT		=			'mist_level_limit';
	const ITEM_ATTR_NAME_DAIMONAPPLE_ATTR_MIST				=			'attr_mist';
	const ITEM_DAIMONAPPLE_MIN_LEVEL						=			0;
	
	const ITEM_DAIMONAPPLE_GILD_LEVEL						=			'evo_level';
	
	/**FashionDress 时装**/
	const ITEM_ATTR_NAME_FASHION_EQUIP_TYPE					=			'equipType';			//装备类型1,时装（2，3，4预留 给翅膀 光环 坐骑之类的其他部位的时装）
	const ITEM_ATTR_NAME_FASHION_DRESS_TYPE					=			'dressType';			//时装类型 1只有主角可以穿2伙伴类时装
	const ITEM_ATTR_NAME_FASHION_DRESS_HERO_IDS				=			'canDressHero';			//可装备英雄ID组
	const ITEM_ATTR_NAME_FASHION_DRESS_HERO_METERIAL_ID		=			'heroMaterialId';		//模型ID
	const ITEM_ATTR_NAME_FASHION_DRESS_LVLIMIT				=			'lvLimit';				//装备等级
	const ITEM_ATTR_NAME_FASHION_DRESS_PROPERTY				=			'improveProperty';		//增加的属性ID数值组
	const ITEM_ATTR_NAME_FASHION_DRESS_SPLITID				=			'splitID';				//装备兑换表ID
	const ITEM_ATTR_NAME_FASHION_DRESS_SHOWEVER				=			'showEver';				// 城镇永不隐藏
	const ITEM_ATTR_NAME_FASHION_DRESS_GROWNUP				=			'grownUp';
	const ITEM_ATTR_NAME_FASHION_DRESS_STRENGTH_ID			=			'strengthId';
	const ITEM_ATTR_NAME_FASHION_DRESS_CANSPLIT				=			'canSplit';
	
	/**宝物表*/
	const ITEM_ATTR_JEWELRY_EQUIP_TYPE						=			'equipType';			//装备类型
	const ITEM_ATTR_JEWELRY_JOBLIMIT						=			'jobLimit';          	//职业限制
	const ITEM_ATTR_JEWELRY_BASELIFE						=			'baseLife';				//宝物本体生命
	const ITEM_ATTR_JEWELRY_BASEPHYATT						=			'basePhyAtt';			//宝物本体物理攻击
	const ITEM_ATTR_JEWELRY_BASEKILLATT						=			'baseKilAtt';			//宝物本体必杀攻击
	const ITEM_ATTR_JEWELRY_BASEMAGATT						=			'baseMagAtt';			//宝物本体魔法攻击
	const ITEM_ATTR_JEWELRY_BASEPHYDEF						=			'basePhyDef';			//宝物本体物理防御
	const ITEM_ATTR_JEWELRY_BASEKILLDEF						=			'baseKilDef';			//宝物本体必杀防御
	const ITEM_ATTR_JEWELRY_BASEMAGDEF						=			'baseMagDef';			//宝物本体魔法防御
	const ITEM_ATTR_JEWELRY_LIFEPL							=			'lifePL';				//宝物生命成长
	const ITEM_ATTR_JEWELRY_PHYATTPL						=			'phyAttPL';				//宝物物理攻击成长
	const ITEM_ATTR_JEWELRY_KILLATTPL						=			'kilAttPL';				//宝物必杀攻击成长
	const ITEM_ATTR_JEWELRY_MAGATTPL						=			'magAttPL';				//宝物魔法攻击成长
	const ITEM_ATTR_JEWELRY_PHYDEFPL						=			'phyDefPL';				//宝物物理防御成长
	const ITEM_ATTR_JEWELRY_KILLDEFPL						=			'kilDefPL';				//宝物必杀防御成长
	const ITEM_ATTR_JEWELRY_MAGDEFPL						=			'magDefPL';				//宝物魔法防御成长
	const ITEM_ATTR_JEWELRY_NOUN_SCORE						=			'noun_score';			//宝物本体评分
	const ITEM_ATTR_JEWELRY_NOUN_SCOREUP					=			'noun_scoreUp';			//宝物本体强化评分成长
	const ITEM_ATTR_JEWELRY_BASE_DECOM_VAL					=			'base_decom_val';		//宝物基础分解价值
	const ITEM_ATTR_JEWELRY_BASE_SEAL_VAL					=			'base_seal_val';		//宝物基础封印价值
	const ITEM_ATTR_JEWELRY_DESEAL_VAL_ARY					=			'deseal_val_ary';		//宝物解封封印价值数组
	const ITEM_ATTR_JEWELRY_MAX_SEALLAYER					=			'max_sealLayer';		//宝物最高封印层数
	const ITEM_ATTR_JEWELRY_OPENSEALNEEDREINFORCE_LV		=			'openSealNeedReinforce_lv';	//宝物封印开启需要强化等级组
	const ITEM_ATTR_JEWELRY_XILIAN_IDS_						=			'xilian_ids_';			//第X层可洗炼属性ID组
	const ITEM_ATTR_JEWELRY_XILIAN_RATES_					=			'xilian_rates_';		//第X层洗炼权重组
	const ITEM_ATTR_JEWELRY_GOLDSMITHCOST					=			'goldSmithCost';		//金币与贝里每层花费
	const ITEM_ATTR_JEWELRY_ENERGYSMITHCOSET				=			'energySmithCost';		//能量与贝里每层花费
	const ITEM_ATTR_JEWELRY_ITEMSMITHCOSET					=			'itemSmithCost';		//洗练石洗练第1到20层洗练需要物品ID及个数
	const ITEM_ATTR_JEWELRY_WAKENEEDSEALOPENNUM				=			'wakeNeedSealOpenNum ';	//觉醒属性开启需要封印开启层数数组
	const ITEM_ATTR_JEWELRY_WAKEPROPERTIES					=			'wakeProperties';		//觉醒属性ID组
	const ITEM_ATTR_JEWELRY_INITMAXRANDSEALATTRNUM			=			'initmaxrandsealattrnum';//初始最大随机封印属性个数
	const ITEM_ATTR_JEWELRY_INITATTRRATES					=			'initattrrates';		//初始属性随机权重
	const ITEM_ATTR_JEWELRY_STRENTHPROPERTY					=			'strengthProperty';		//宝物强化
	const ITEM_ATTR_JEWELRY_STRENTHSPACE					=			'strengthSpace';		//装备强化等级间隔
	const ITEM_ATTR_JEWELRY_LAYERSTARMAX					=			'layerStarMax';			//宝物每层星级上限
	const ITEM_ATTR_JEWELRY_CANSPLIT						=			'canSplit';				//是否可放到礼品屋里面分解
	const ITEM_ATTR_JEWELRY_EXPERIENCE						=			'yueli';				//同礼品屋拆分装备消耗阅历功能
	const ITEM_ATTR_JEWELRY_REINFORCE_VAL_RATE				=			'reinforce_val_rate';	//宝物强化价值比率
	const ITEM_ATTR_JEWELRY_SEAL_VAL_REATE					=			'seal_val_rate';		//宝物封印价值比率

	const ITEM_ATTR_JEWELRY_XILIAN_ID_MAXNUM				=			20;						//可洗炼属性ID组上限
	const ITEM_ATTR_JEWELRY_XILIAN_RATE_MAXNUM				=			20;						//可洗炼权重组上限
	const ITEM_ATTR_JEWELRY_TEXT_SEAL						=			'seal';					//封印属性数据
	const ITEM_ATTR_JEWELRY_TEXT_FRESH						=			'seal_general_fixed';	//封印属性洗练数据
	
	/**宝物的封印属性表*/
	const ITEM_ATTR_JEWELRYSEAL_ID							=			'id';					//属性ID
	const ITEM_ATTR_JEWELRYSEAL_AFFIXID						=			'affixID';				//增加的属性ID
	const ITEM_ATTR_JEWELRYSEAL_AFFIXVALUE					=			'affixValue ';			//增加的属性数值
	const ITEM_ATTR_JEWELRYSEAL_STAR_LV						=			'star_lv ';				//属性评级
	const ITEM_ATTR_JEWELRYSEAL_SCORE_PROPERTY				=			'score_property ';		//属性评分
	const ITEM_ATTR_JEWELRYSEAL_PROPERTY_RATE				=			'property_rate';		//属性权重
	const ITEM_ATTR_JEWELRYSEAL_BINDHERO					=			'bindHero';				//绑定英雄ID
	const ITEM_ATTR_JEWELRYSEAL_NOUN_ADDRATE				=			'noun_addRate';			//宝物本体属性加成百分比
	const ITEM_ATTR_JEWELRYSEAL_DECOM_VAL					=			'decom_val';			//宝物单层分音属性分解封印价值
	
	/**PetEgg**/
	const ITEM_ATTR_NAME_PET_TEMPLATE_ID					=			'pet_template_id';
	const ITEM_ATTR_NAME_PET_EGG_TYPE						=			'petEggType';
	const ITEM_ATTR_NAME_PET_LEVEL_LIMIT					=			'lvLimit';
	
	/**Mount**/
	const ITEM_ATTR_NAME_MOUNT_TEMPLATE_ID					=			'mount_template_id';
	const ITEM_ATTR_NAME_MOUNT_SPLIT_COST					=			'split_cost';
	
	/**Decoration**/
	const ITEM_ATTR_NAME_DECORATION_SPLIT_COST				=			'decoration_split_code';
	
	/**Card**/
	const ITEM_ATTR_NAME_CARD_ID							=			'cardid';
	
	/**Element**/
	const ITEM_ATTR_NAME_ELEMENT_ATTR_NUM					=			'element_attr_num';	
	const ITEM_ATTR_NAME_ELEMENT_ATTRS						=			'element_attr';
	const ITEM_ATTR_NAME_ELEMENT_ATTR_REINFORCE				=			'element_attr_reinforce';
	const ITEM_ATTR_NAME_ELEMENT_SKILLS						=			'element_skill';	
	const ITEM_ATTR_NAME_ELEMENT_CAN_UP						=			'can_up';
	const ITEM_ATTR_NAME_ELEMENT_LEVEL_TABLE				=			'level_table';
	const ITEM_ATTR_NAME_ELEMENT_MAX_LEVEL					=			'max_level';
	const ITEM_ATTR_NAME_ELEMENT_PROPERTY_VALUE				=			'property_value';
	const ITEM_ATTR_NAME_ELEMENT_SAME_EFFECT_ID				=			'same_effect_id';
	const ITEM_ATTR_NAME_ELEMENT_TYPE						=			'element_type';
	const ITEM_ATTR_NAME_ELEMENT_EFFECT_TYPE				=			'effect_type';
	const ITEM_ELEMENT_MIN_LEVEL							=			1;


	/**Demon**/
	const ITEM_ATTR_NAME_DEMON_TEMPLATE_ID					=			'demonid';
	const ITEM_ATTR_NAME_DEMON_LEVEL_LIMIT					=			'lvLimit';

	/**good will**/
	const ITEM_ATTR_NAME_GOOD_WILL							=			'good_will';
	const ITEM_ATTR_NAME_GOOD_WILL_TYPE						=			'good_will_type';

	/**arm reinforce**/
	const REINFORCE_FEE_BELLY								=			'reinforce_fee_belly';
	const REINFORCE_FEE_GOLD								=			'reinforce_fee_gold';
	const REINFORCE_FEE_ITEMS								=			'reinforce_fee_items';
	const REINFORCE_FEE_PROBABILITY							=			'reinforce_fee_probability';

	//物品分类
	const ITEM_ARM											=			1;
	const ITEM_GEM											=			2;
	const ITEM_CARD											=			3;
	const ITEM_GIFT											=			4;
	const ITEM_RANDGIFT										=			5;
	const ITEM_DAIMONAPPLE									=			6;
	const ITEM_SHIPBLUEPRINT								=			7;
	const ITEM_MISSION										=			8;
	const ITEM_DIRECT										=			9;
	const ITEM_BOATARM										=			10;
	const ITEM_PETEGG										=			11;
	const ITEM_NORMAL										=			12;
	const ITEM_FRAGMENT										=			13;
	const ITEM_GOODWILL										=			14;
	const ITEM_FISH											=			15;
	const ITEM_FASHION_DRESS								=			16;
	const ITEM_JEWELRY										=			17;
	const ITEM_MOUNT										=			18;
	const ITEM_TIMELIMIT									=			19;
	const ITEM_ELEMENT										=			20;
	const ITEM_DEMON										=			21;
	const ITEM_PARTNERDRESS									=			22;

	//装备分类
	//武器
	const ITEM_ARM_ARM						=			1;
	//戒指
	const ITEM_ARM_RING						=			2;
	//书
	const ITEM_ARM_BOOK						=			3;
	//衣服
	const ITEM_ARM_CLOTHING					=			4;
	//帽子
	const ITEM_ARM_HAT						=			5;
	//披风
	const ITEM_ARM_MANTLE					=			6;
	//项链
	const ITEM_ARM_NECKLACE					=			7;
	//耳环
	const ITEM_ARM_EARRING					=			8;
	//船首火炮
	const ITEM_BOAT_ARM_CANNON				=			101;
	//舷炮
	const ITEM_BOAT_ARM_WALLPIECE			=			102;
	//甲板
	const ITEM_BOAT_ARM_ARMOUR				=			103;
	//风帆
	const ITEM_BOAT_ARM_SAILS				=			104;
	//船首像
	const ITEM_BOAT_ARM_FIGUREHEAD			=			105;
	
	//时装分类
	//衣服
	const ITEM_FASHION_CLOTHES				=			1;
	
	//宝物分类
	const ITEM_JEWELRY_TYPE_1				=			1;
	const ITEM_JEWELRY_TYPE_2				=			2;
	const ITEM_JEWELRY_TYPE_3				=			3;
	const ITEM_JEWELRY_TYPE_4				=			4;
	const ITEM_JEWELRY_TYPE_5				=			5;
	const ITEM_JEWELRY_TYPE_6				=			6;

	//元素分类
	const ITEM_ELEMENT_ATTACK				=			1;
	const ITEM_ELEMENT_WIND_RESISTANCE		=			2;
	const ITEM_ELEMENT_THUNDER_RESISTANCE	=			3;
	const ITEM_ELEMENT_WATER_RESISTANCE		=			4;
	const ITEM_ELEMENT_FIRE_RESISTANCE		=			5;
	const ITEM_ELEMENT_HP					=			6;
	const ITEM_ELEMENT_DEFENSE				=			7;
	const ITEM_ELEMENT_TREATED				=			8;

	
	//valid arm types
	public static $ITEM_VALID_ARM_TYPES		=			array (
		self::ITEM_ARM_ARM,
		self::ITEM_ARM_RING,
		self::ITEM_ARM_BOOK,
		self::ITEM_ARM_CLOTHING,
		self::ITEM_ARM_HAT,
		self::ITEM_ARM_MANTLE,
		self::ITEM_ARM_NECKLACE,
		self::ITEM_ARM_EARRING,
	);

	//物品品质常数
	//白色品质
	const ITEM_QUALITY_WHITE				=			1;
	//绿色品质
	const ITEM_QUALITY_GREEN				=			2;
	//蓝色品质
	const ITEM_QUALITY_BLUE					=			3;
	//橙色品质
	const ITEM_QUALITY_ORANGE				=			4;
	//红色品质
	const ITEM_QUALITY_RED					=			5;
	//紫色品质
	const ITEM_QUALITY_PURPLE				=			6;
	//金色品质
	const ITEM_QUALITY_GOLD					=			7;

	//物品常数
	//物品不可出售
	const ITEM_CAN_NOT_SELL									=			0;

	//物品不可叠加常数
	const ITEM_CAN_NOT_STACKABLE							=			1;

	//物品可摧毁
	const ITEM_CAN_DESTORY									=			1;

	//默认物品强化等级
	const ITEM_REINFORCE_LEVEL_DEFAULT						=			0;
	
	//默认物品镀金等级
	const ITEM_GILD_LEVEL_DEFAULT							=			0;

	//可以随机洗潜能
	const ITEM_CAN_RANDOM_REFRESH_POTENTIALITY				=			1;

	//可以固定洗潜能
	const ITEM_CAN_FIXED_REFRESH_POTENTIALITY				=			1;

	//没有潜能
	const ITEM_INVALID_POTENTIALITY_ID						=			0;

	//物品可使用
	const ITEM_CAN_USE										=			1;

	//no item
	const ITEM_ID_NO_ITEM									=			0;

	//TODO

	//装备会改变的人物属性
	public static $ITEM_ARM_ATTRS_CALC						=
				array(
					ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK => array (
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK_BASIC,
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK_REINFORCE
					),
					ItemDef::ITEM_ATTR_NAME_KILL_ATTACK => array (
						ItemDef::ITEM_ATTR_NAME_KILL_ATTACK_BASIC,
						ItemDef::ITEM_ATTR_NAME_KILL_ATTACK_REINFORCE
					),
					ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK => array (
						ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK_BASIC,
						ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK_REINFORCE
					),
					ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE => array (
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE_BASIC,
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE_REINFORCE,
					),
					ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE => array (
						ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE_BASIC,
						ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE_REINFORCE
					),
					ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE => array (
						ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE_BASIC,
						ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE_REINFORCE
					),
					ItemDef::ITEM_ATTR_NAME_HP => array (
						ItemDef::ITEM_ATTR_NAME_HP_BASIC,
						ItemDef::ITEM_ATTR_NAME_HP_REINFORCE
					),
				);
	public static $ITEM_ARM_ATTRS_NO_CALC					=
				array(
					ItemDef::ITEM_ATTR_NAME_STRENGTH,
					ItemDef::ITEM_ATTR_NAME_AGILITY,
					ItemDef::ITEM_ATTR_NAME_INTELLIGENCE,
					ItemDef::ITEM_ATTR_NAME_HIT_RATING,
					ItemDef::ITEM_ATTR_NAME_FATAL,
					ItemDef::ITEM_ATTR_NAME_PARRY,
					ItemDef::ITEM_ATTR_NAME_DODGE,
					ItemDef::ITEM_ATTR_NAME_WIND_ATTACK,
					ItemDef::ITEM_ATTR_NAME_THUNDER_ATTACK,
					ItemDef::ITEM_ATTR_NAME_WATER_ATTACK,
					ItemDef::ITEM_ATTR_NAME_FIRE_ATTACK,
					ItemDef::ITEM_ATTR_NAME_WIND_RESISTANCE,
					ItemDef::ITEM_ATTR_NAME_THUNDER_RESISTANCE,
					ItemDef::ITEM_ATTR_NAME_WATER_RESISTANCE,
					ItemDef::ITEM_ATTR_NAME_FIRE_RESISTANCE,
				);

	//主船装备可以改变主船的属性
	public static $ITEM_BOAT_ARM_ATTRS_CALC = array(
					ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK => array (
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK_BASIC,
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK_REINFORCE
					),
					ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE => array (
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK_BASIC,
						ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE_REINFORCE,
					),
					Itemdef::ITEM_ATTR_NAME_HP => array (
						ItemDef::ITEM_ATTR_NAME_HP_BASIC,
						ItemDef::ITEM_ATTR_NAME_HP_REINFORCE,
					),
					ItemDef::ITEM_ATTR_NAME_HIT_RATING => array (
						ItemDef::ITEM_ATTR_NAME_HIT_RATING_BASIC,
						ItemDef::ITEM_ATTR_NAME_HIT_RATING_REINFORCE,
					),
					ItemDef::ITEM_ATTR_NAME_FATAL => array (
						ItemDef::ITEM_ATTR_NAME_FATAL_BASIC,
						ItemDef::ITEM_ATTR_NAME_FATAL_REINFORCE
					),
					ItemDef::ITEM_ATTR_NAME_DODGE => array (
						ItemDef::ITEM_ATTR_NAME_DODGE_BASIC,
						ItemDef::ITEM_ATTR_NAME_DODGE_REINFORCE,
					)
				);

	//item attr id
	public static $ITEM_ATTR_IDS = array (
				1			=>			ItemDef::ITEM_ATTR_NAME_HP,
				2			=>			ItemDef::ITEM_ATTR_NAME_HP_PERCENT,
				3			=>			ItemDef::ITEM_ATTR_NAME_STRENGTH,
				4			=>			ItemDef::ITEM_ATTR_NAME_AGILITY,
				5			=>			ItemDef::ITEM_ATTR_NAME_INTELLIGENCE,
				6			=>			ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTACK,
				7			=>			ItemDef::ITEM_ATTR_NAME_KILL_ATTACK,
				8			=>			ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK,
				9			=>			ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE,
				10			=>			ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE,
				11			=>			ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE,
				13			=>			ItemDef::ITEM_ATTR_NAME_HIT_RATING,
				14			=>			ItemDef::ITEM_ATTR_NAME_FATAL,
				15			=>			ItemDef::ITEM_ATTR_NAME_DODGE,
				16			=>			ItemDef::ITEM_ATTR_NAME_PARRY,
				17			=>			ItemDef::ITEM_ATTR_NAME_WIND_ATTACK,
				18			=>			ItemDef::ITEM_ATTR_NAME_THUNDER_ATTACK,
				19			=>			ItemDef::ITEM_ATTR_NAME_WATER_ATTACK,
				20			=>			ItemDef::ITEM_ATTR_NAME_FIRE_ATTACK,
				21			=>			ItemDef::ITEM_ATTR_NAME_WIND_RESISTANCE,
				22			=>			ItemDef::ITEM_ATTR_NAME_THUNDER_RESISTANCE,
				23			=>			ItemDef::ITEM_ATTR_NAME_WATER_RESISTANCE,
				24			=>			ItemDef::ITEM_ATTR_NAME_FIRE_RESISTANCE,
				25			=>			ItemDef::ITEM_ATTR_NAME_PHYSICAL_ATTRCK_PERCENT,
				26			=>			ItemDef::ITEM_ATTR_NAME_PHYSICAL_DEFENCE_PERCENT,
				27			=>			ItemDef::ITEM_ATTR_NAME_KILL_ATTACK_PERCENT,
				28			=>			ItemDef::ITEM_ATTR_NAME_KILL_DEFENCE_PERCENT,
				29			=>			ItemDef::ITEM_ATTR_NAME_MAGIC_ATTACK_PERCENT,
				30			=>			ItemDef::ITEM_ATTR_NAME_MAGIC_DEFENCE_PERCENT,
				35			=>          ItemDef::ITEM_ATTR_NAME_PYH_ATT_GIFT,	//物理伤害倍率
				36			=>          ItemDef::ITEM_ATTR_NAME_PYH_DEF_GIFT,	//物理免伤倍率
				37			=>          ItemDef::ITEM_ATTR_NAME_KILL_ATT_GIFT,	//必杀伤害倍率
				38			=>          ItemDef::ITEM_ATTR_NAME_KILL_DEF_GIFT,	//必杀免伤倍率
				39			=>          ItemDef::ITEM_ATTR_NAME_MAG_ATT_GIFT,	//魔法伤害倍率
				40			=>          ItemDef::ITEM_ATTR_NAME_MAG_DEF_GIFT,	//魔法免伤倍率
				41			=>			ItemDef::ITEM_ATTR_NAME_DAMAGE,
				42			=>			ItemDef::ITEM_ATTR_NAME_AVOID_DAMAGE,
				43			=>			ItemDef::ITEM_ATTR_NAME_STRENGTH_PERCENT,
				44			=>			ItemDef::ITEM_ATTR_NAME_AGILITY_PERCENT,
				45			=>			ItemDef::ITEM_ATTR_NAME_INTELLIGENCE_PERCENT,
				53			=>			ItemDef::ITEM_ATTR_NAME_NORMAL_ATT_RATIO,	//调整普通攻击伤害比
				54			=>			ItemDef::ITEM_ATTR_NAME_NORMAL_DEF_RATIO,	//调整普通攻击免伤比
				55			=>			ItemDef::ITEM_ATTR_NAME_RAGER_ATT_RATIO	,	//调整怒气攻击伤害比
				56			=>			ItemDef::ITEM_ATTR_NAME_RAGER_DEF_RATIO	,	//调整怒气攻击免伤比
				57			=>			ItemDef::ITEM_ATTR_NAME_TREAT_RATIO	,		//调整治疗比率
				58			=>			ItemDef::ITEM_ATTR_NAME_TREATED_RATIO,		//调整被治疗比率
				59			=>			ItemDef::ITEM_ATTR_NAME_RAGE,
	);

	//SQL
	public static $ITEM_ALLOW_UPDATE_COL					=			array(ItemDef::ITEM_SQL_ITEM_NUM, ItemDef::ITEM_SQL_ITEM_TEXT);

	//SQL attribute name
	const ITEM_TABLE_NAME				=			't_item';
	const ITEM_SQL_ITEM_ID				=			'item_id';
	const ITEM_SQL_ITEM_TEMPLATE_ID		=			'item_template_id';
	const ITEM_SQL_ITEM_NUM				=			'item_num';
	const ITEM_SQL_ITEM_TIME			=			'item_time';
	const ITEM_SQL_ITEM_DELETED			=			'item_deleted';
	const ITEM_SQL_ITEM_TEXT			=			'va_item_text';
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */