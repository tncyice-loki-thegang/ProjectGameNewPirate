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

class MapSciHero
{
	static $mapSciHero = array(
1	=> CreatureInfoKey::hp  , //调整生命基础值	生命基础值
2	=> CreatureInfoKey::hpRatio  , //调整生命值百分比	生命值百分比
3	=> CreatureInfoKey::strength  , //调整力量值点数	力量
4	=> CreatureInfoKey::agile  , //调整敏捷值点数	敏捷
5	=> CreatureInfoKey::intelligence  , //调整智慧值点数	智慧
6	=> CreatureInfoKey::phyAttack  , //调整物理攻击基础值	物理攻击基础值
7	=> CreatureInfoKey::killAttack  , //调整必杀攻击基础值	必杀攻击基础值
8	=> CreatureInfoKey::mgcAttack  , //调整魔法攻击基础值	魔法攻击基础值
9	=> CreatureInfoKey::phyDefend  , //调整物理防御基础值	物理防御基础值
10	=> CreatureInfoKey::killDefend  , //调整必杀防御基础值	必杀防御基础值
11	=> CreatureInfoKey::mgcDefend  , //调整魔法防御基础值	魔法防御基础值
12	=> CreatureInfoKey::rage  , //调整怒气值点数	怒气值点数
13	=> CreatureInfoKey::hitRatingRatio  , //调整命中率	命中率
14	=> CreatureInfoKey::ftlAtkRatio  , //调整致命一击率	致命一击率
15	=> CreatureInfoKey::pryRatio  , //调整格挡率	格挡率
16	=> CreatureInfoKey::dgeRatio  , //调整闪避率	闪避率
17	=> CreatureInfoKey::windAttack  , //调整风属性攻击基础值	风属性攻击基础值
18	=> CreatureInfoKey::thdAttack  , //调整雷属性攻击基础值	雷属性攻击基础值
19	=> CreatureInfoKey::wtrAttack  , //调整水属性攻击基础值	水属性攻击基础值
20	=> CreatureInfoKey::fireAttack  , //调整火属性攻击基础值	火属性攻击基础值
21	=> CreatureInfoKey::windResistance  , //调整风属性抗性	风属性抗性
22	=> CreatureInfoKey::thdResistance  , //调整雷属性抗性	雷属性抗性
23	=> CreatureInfoKey::wtrResistance  , //调整水属性抗性	水属性抗性
24	=> CreatureInfoKey::fireResistance  , //调整火属性抗性	火属性抗性
25	=> CreatureInfoKey::phyAtkRatio  , //调整物理攻击百分比	物理攻击百分比
26	=> CreatureInfoKey::phyDfsRatio  , //调整物理防御百分比	物理防御百分比
27	=> CreatureInfoKey::killAtkRatio  , //调整必杀攻击百分比	必杀攻击百分比
28	=> CreatureInfoKey::killDfsRatio  , //调整必杀防御百分比	必杀防御百分比
29	=> CreatureInfoKey::mgcAtkRatio  , //调整魔法攻击百分比	魔法攻击百分比
30	=> CreatureInfoKey::mgcDfsRatio  , //调整魔法防御百分比	魔法防御百分比
31	=> CreatureInfoKey::windAtkRatio  , //调整风属性攻击百分比	风属性攻击百分比
32	=> CreatureInfoKey::thdAtkRatio  , //调整雷属性攻击百分比	雷属性攻击百分比
33	=> CreatureInfoKey::wtrAtkRatio  , //调整水属性攻击百分比	水属性攻击百分比
34	=> CreatureInfoKey::fireAtkRatio  , //调整火属性攻击百分比	火属性攻击百分比
35	=> CreatureInfoKey::phyFDmgRatio  , //调整物理伤害倍率	物理伤害倍率
36	=> CreatureInfoKey::phyFEptRatio  , //调整物理免伤倍率	物理免伤倍率
37	=> CreatureInfoKey::killFDmgRatio  , //调整必杀伤害倍率	必杀伤害倍率
38	=> CreatureInfoKey::killFEptRatio  , //调整必杀免伤倍率	必杀免伤倍率
39	=> CreatureInfoKey::mgcFDmgRatio  , //调整魔法伤害倍率	魔法伤害倍率
40	=> CreatureInfoKey::mgcFEptRatio  , //调整魔法免伤倍率	魔法免伤倍率
41	=> CreatureInfoKey::absoluteDamage  , //调整最终伤害	最终伤害
42	=> CreatureInfoKey::absoluteDefend  , //调整减免最终伤害	减免最终伤害
43	=> CreatureInfoKey::stgRatio  , //调整力量值百分比	力量值百分比
44	=> CreatureInfoKey::aglRatio  , //调整敏捷值百分比	敏捷值百分比
45	=> CreatureInfoKey::itgRatio  , //调整智慧值百分比	智慧值百分比
46  => CreatureInfoKey::hpFinal , //调整最终生命
47	=> CreatureInfoKey::phyAtkFinal     ,  //最终物理攻击基础值	最终物理攻击基础值
48	=> CreatureInfoKey::killAtkFinal     ,  //最终必杀攻击基础值	最终必杀攻击基础值
49	=> CreatureInfoKey::mgcAtkFinal     ,  //最终魔法攻击基础值	最终魔法攻击基础值
50	=> CreatureInfoKey::phyDfsFinal     ,  //最终物理防御基础值	最终物理防御基础
51	=> CreatureInfoKey::killDfsFinal     ,  //最终必杀防御基础值	最终必杀防御基础值
52	=> CreatureInfoKey::mgcDfsFinal     ,  //最终魔法防御基础值	最终魔法防御基础值					
	);
	
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */