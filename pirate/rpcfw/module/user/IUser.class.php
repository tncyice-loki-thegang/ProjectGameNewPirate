<?php
/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $$Id: IUser.class.php 36864 2013-01-24 02:47:23Z HongyuLan $$
 *
 **************************************************************************/

/**
 * @file $$HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/user/IUser.class.php $$
 * @author $$Author: HongyuLan $$(lanhongyu@babeltime.com)
 * @date $$Date: 2013-01-24 10:47:23 +0800 (四, 2013-01-24) $$
 * @version $$Revision: 36864 $$
 * @brief
 *
 **/

interface IUser
{
	/**
	 * 玩家登录到游戏服务器
	 * @param $arrReq
	 * @return string ok 成功  full 服务器人太多 timeout 超时 fail 失败 
	 */
	public function login($arrReq);

	/**
	 * 得到玩家所有的用户
	 * @return array 格式如下
	 * <code>{
	 * uid:用户id
	 * utid:用户模版id
	 * name:user名字
	 * dtime:如果非0,表示角色删除时间。
	 * title: title id
	 * guildId: guild id
	 * guildName : guild name
	 * emblemId : 
	 * 'last_place_type',
	 * 'last_place_data,	
	 * 'last_town_id', 
	 * 'last_x',
	 * 'last_y',	
	 * }</code>
	 */
	public function getUsers();

	/**
	 * 创建角色
	 * @param uint $utid 用户模版id
	 * @param string $uname
	 * @return string 返回值如下
	 * ok
	 * invalid_char
	 * sensitive_word
	 * name_used
	 * other
	 */
	public function createUser($utid, $uname);

	/**
	 * 删除用户
	 * @param uint 用户id
	 * @return string
	 * ok
	 * fail
	 * fake
	 */
	public function delUser($uid);

	/**
	 * 取消删除角色
	 * @param string $uid
	 * @return string
	 * ok
	 * fail
	 * user_not_found
	 */
	public function cancelDel($uid);

	/**
	 * 得到随机名字
	 * @param uint $num 返回名字数量，如果大于20,返回20个。
	 * @param uint $gender 0:女 1：男
	 * @return array 名字组成的数组
	 */
	public function getRandomName($num, $gender=0);


	/**
	 * 使用uid用户进入游戏
	 * @param unit $uid 用户id
	 * @return
	 * ok
	 * logined: 已经在其他地方登录过
	 * ban:time:msg
	 * fail
	 */
	public function userLogin($uid);

	/**
	 * 得到用户信息
	 * @see UserDef::$USER_FIELDS
	 * @return array
	 * <code>{
	 * 'uid':'用户id',
	 * 'uname':'用户名字',
	 * 'utid':'用户模版id',
	 * 'birthday':'用户生日',
	 * 'group_id':'阵营',
	 * 'guild_id':'公会',
	 * 'guild_name':'公会名',
	 * 'cur_execution':'当前行动力',
	 * 'execution_time' : 上次恢复行动力时间
	 * 'today_buy_execution_num' : 今天已经购买行动力数量
	 * 'cur_formation':'当前阵型fid',
	 * 'vip':'vip等级',
	 * 'recruit_num':'可以招募英雄的数量，没包括vip的加成',
	 * 'watch_num':'可以观战英雄的数量，没包括vip的加成',
	 * 'belly_num':'贝里当前值',
	 * 'gold_num':'金币RMB',
	 * 'reward_point':'积分',
	 * 'gift_cash':'礼金',
	 * 'prestige_num':'威望',
	 * 'experience_num':'阅历',
	 * 'food_num':'食物',
	 * 'atk_value':attack value,
	 * 'arena_cdtime':arena cd时间,
	 * 'practice_last_time':挂机剩余秒数。-1表示没有开启此项功能
	 * 'title' : 称谓
	 * 'blood_package':'血池',
	 * 'fight_cdtime' : 保护时间
	 * 'protect_cdtime' : 保护时间
	 * 'last_salary_time' : 上次领取工资的时间
	 * 'ban_chat_time' : 禁言结束时间
	 * 'last_town_id', 
	 * 'last_x',
	 * 'last_y',
	 * 'copy_execution' : '副本令'
	 * 'vassal_execution' : 下属令
	 * 'resource_execution': 资源令
	 * 'attack_execution': 港口攻打令
	 * 'online_accum_time' : 在线累计时间
	 * 'login_time' : 登录时间
	 * 'opclient_reward' : 0 没有领取微端奖励， 1 已经领取微端奖励
	 * 'charge_gold' : 充值金币数量
	 * 'can_arena_num': 竞技场能挑战多少次
	 *  'va_user': object
	 * [
	 * 'dress':object
	 * [
	 * 时装模板信息
	 * ]
	 * 
 	 * 'group_info':
	 * [
	 * 'free_transfer' :  玩家免费转移了多少次
	 * 'gold_transfer' : 金币转移了多少次 
	 * ]	 	
	 * 'goodwill' : 
	 * [
	 * 'num_by_gold' : 金币加好感度次数
	 * 'num_free' : 使用了的免费次数
	 * 'time' : 修改num_by_gold的时间，返回给前端的时候会根据时间重置 num_by_gold 和 time
	 * 'heritage' => 
	 * [
	 * 'cfg_gold_num' => N 每天金币传承最大次数
	 * 'num' => 金币传承次数，
	 * 'time' => 金币传承时间
	 * ]
	 * ]
	 * ]
	 * }</code>
	 */
	public function getUser ();

	/**
	 * 设置group, 如果groupId等于0,设置为用户最少的groupId
	 * @param uint $groupId
	 * @return array
	 * <code>
	 * object(
	 * group_id: group id
	 * gold: gold num
	 * )
	 * </code>
	 */
	public function setGroup ($groupId);

	/**
	 * 设置消息
	 * @param string $msg
	 * @return ok
	 */
	public function updateMsg ($msg);

	/**
	 * 根据uname查uid
	 * Enter description here ...
	 * @param unknown_type $uname
	 * @return uint uid
	 */
	public function unameToUid($uname);

	/**
	 *
	 * 攻打别人
	 *
	 * @param int $des_uid
	 *
	 * @return array
	 * <code>
	 * {
	 * 		'error_code':boolean				错误码
	 * 											10000 OK
	 * 											10001 在保护期内,在此情况下target_protect_cdtime数据用于前端刷新
	 * 											10002 在战斗cd中
	 * 											10003 血量不足
	 * 											10004 被攻击方功能没有开启
	 * 											10100 非法(错误应该由前端拦截)
	 * 		'target_protect_cdtime':int			目标目前的保护时间(如果前端数据过旧,则此数据用于刷新前端数据)
	 *		'fight_ret':string					战斗结果
	 *		'cur_hp':array						当前阵型的英雄的血量信息
	 *		[
	 *			heroid:hp
	 *		]
	 *		'blood_package':int					当前的血库血量
	 *		'prestige':int						当前声望
	 *		'protect_cdtime':int				当前的保护时间
	 *		'fight_cdtime':int					当前的战斗cd时间
	 *		'atk_value':int						当前攻击值
	 *		'appraisal':string					战斗评价
	 * }
	 * </code>
	 */
	public function attack($des_uid);

	/**
	 * 得到其他用户的信息
	 * @param unknown_type $uid
	 * @return array
	 * <code>
	 * object(
	 * 'title': 'title',
	 * 'utid' : utid
	 * 'guild_id': guild id,
	 * 'guild_name':'公会名',	
	 * 'atk_value':attack value,
	 * 'arena_position':arena 排名,
	 * 'reward_level': 悬赏等级,
	 * 'port_id':港口
	 * )
	 * </code>
	 */
	//public function getOtherUser($uid);
	
	/**
	 * 得到其他用户和已招募英雄的信息
	 * @param unknown_type $uid
	 * @return array
	 * <code>
	 * object(
	 * 'level':'level',
	 * 'title': title id,
	 * 'utid' : utid
	 * 'uname' : uname
	 * 'group_id' : 阵营id
	 * 'master_htid' : 主角英雄的htid
	 * 'talent_ast_id': 天赋星盘id， 没有则为0
	 * 'transferNum' : 转职次数
	 * 'guild_id': guild id,
	 * 'guild_name':'公会名',
	 * 'emblemId' : 公会会徽id	
	 * 'atk_value':attack value,
	 * 'arena_position':arena 排名,
	 * 'reward_level': 悬赏等级,
	 * 'cur_elite_copy_id' : 当前精英副本id
	 * 'archieve_point' : 成就点数
	 * 'port_id':港口
	 * 'show_achieve' : 展示的成就id数组
	 * 'recruit_heroes' => array()
	 * 'fight_force' => 战斗力
	 * )
	 * 	recruit hero 信息 @see IHero::getRecruitHeroes
	 * </code>
	 */
	public function getOtherUserRctHeroes($uid);
	
	public function getUserInfoFromCache();
	
	/**
	 * 花费belly买血
	 * @param unknown_type $num
	 * @return ok
	 */
	public function buyBloodPackage($num);
	
	/**
	 * 购买行动力
	 * Enter description here ...
	 * @param unknown_type $num
	 * @return overflow: 超出最大值 ok:suc
	 */
	public function buyExecution($num);
	
	/**
	 * 加金币
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @param uint $orderId 订单号
	 * @param uint $gold 人民币，分为单位
	 */
	public function addGold4BBpay($uid, $orderId, $gold);
	
	/**
	 * 设置vip等级。
	 * 在数据库插入一个订单号，订单中的金币设置为相应的vip所需要的金币.
	 * 不给用户加金币。
	 * Enter description here ...
	 * @param uint $uid
	 * @param uint $vip
	 * @param string $orderId
	 */
	public function setVip4BBpay($uid, $vip, $orderId);
	
	/**
	 * 功能是否开启
	 * @return array 数组的内容为功能开启的项
	 */
	public function getSwitch();
	
	/**
	 * 功能开启点奖励.
	 * @return array 数组的内容为已经领取奖励的功能节点
	 * array(1,3,4) 表示功能点1,3,4的奖励已经领取
	 */
	public function getSwitchRewardInfo();
	
	/**
	 * 领取功能开启点奖励
	 * @param $type 功能点
	 * @return array
	 * <code>
	 * object(
	 * 'ret':ok, bag_full
	 * 'res': object
	 * (
	 * 'belly': belly
	 * 'experience': 阅历
	 * 'grid' : 背包信息
	 * )
	 * )
	 * </code>
	 */
	public function switchReward($type);
	
	/**
	 * 得到最高排名
	 * @param string $type 排名种类 'level', 'arena', 'prestige', 'achieve' 'copy'
	 * @param uint $offset 开始位置， 从0开始
	 * @param uint $limit 返回数据量大小  $limit + $offset 必须小于100
	 * @return array
	 * <code>
	 * array(
	 * object(
	 * uid => uid,
	 * uname => uname,
	 * guild_id => 公会id,
	 * guild_name => 公会名称,
	 * group_id => 阵营
	 * level => 主角英雄的等级
	 * 根据type，可能有以下属性
	 * [
	 * 'level' => 等级
	 * ]
	 * [
	 * 'position' => 竞技场排名
	 * ]
	 * [
	 * 'prestige_num' => 威望
	 * ]
	 * [
	 * 'copy_id' => 副本id
	 * ]
	 * [
	 * 'achieve_point' => 悬赏值
	 * ]
	 * )
	 * )
	 * </code>
	 */
	public function getTop($type, $offset, $limit);
	
	/**
	 * 得到自己的排名
	 * @param string $type @see getTop
	 * @return uint 如果没有值返回0
	 */
	public function getSelfOrder($type);
	
	/**
	 * 查看排行榜用户信息
	 * @param unknown_type $uid
	 * @return array
	 * <code>
	 * 'transferNum':转生次数	
	 * 'title': 称号Id
	 * 'armingInfo': array(itemInfo) ,主角英雄
	 * 'master_htid': htid
	 * 'fight_force': 战斗力
	 * 'dress': array(itemInfo) ,如果用户展示时装有此字段
	 * </code>
	 */
	public function getTopUserInfo($uid);
	
	/**
	 * 得到用户简单信息
	 * Enter description here ...
	 * @param unknown_type $uid
	 * @return array
	 * <code>
	 * object(
	 * utid : utid
	 * uanme : uname
	 * level : level
	 * )
	 * </code>
	 */
	public function getSimpleInfo($uid);
	
	/**
	 * 得到用户的设置信息
	 * Enter description here ...
	 * @return array
	 * <code>
	 * mute: 1 静音 0 有声音
	 * visible_type : 城镇显示用户  0-4
	 * </code>
	 */
	public function getSettings();
	
	/**
	 * 得到用户配置信息
	 * @return array
	 * object
	 *  [
	 *   配置信息， 通过IUser.saveVaConfig保存的key/value对。
	 *  ]
	 * Enter description here ...
	 */
	public function getVaConfig();
	
	/**
	 * 保存设置
	 * @param $vaConfig 数组
	 * @return 'ok'
	 */
	public function setVaConfig($vaConfig);
	
	/**
	 * 前端保存设置用。 
	 * 因为前端把setVaConfig用来做成保存消费提示了，据说还不能修改，只能又提供了这个函数给他们保存设置。
	 * @param unknown_type $arrConfig
	 * @return 'ok'
	 */
	public function setArrConfig($key, $value);
	
	/**
	 *  返回所有的配置
	 *  @return array
	 *  <code>
	 *  key => value
	 *  </code>
	 */
	public function getArrConfig();
	
	/**
	 * 设置静音
	 * Enter description here ...
	 * @param unknown_type $isMute 1 静音 0 有声音
	 * @return 'ok'
	 */
	public function setMute($isMute);
	
	/**
	 * 设置城镇显示用户数量
	 * Enter description here ...
	 * @param unknown_type $visibleType 0-5 对应策划配置的6个值， 目前为0,30,60,100,10000(全部), 15
	 * @return 'ok'
	 */
	public function setVisibleCount($visibleType);
	
	/**
	 * 返回能招募英雄的数量
	 * 默认值 + 开启的扩展位置数量
	 * Enter description here ...
	 * @return int
	 */
	public function getCanRecruitHeroNum();
	
	/**
	 * 开启招募英雄扩展位置
	 * Enter description here ...
	 * @param unknown_type $pos 从0开始
	 * @return ok
	 */
	public function openHeroPos($pos);
	
	/**
	 * 是否充值过
	 * @return 0没有 1有充值 2表示有老福利账号充值
	 */
	public function isPay();
	
	/**
	 * 是否领了充值奖励
	 * Enter description here ...
	 * @return array
	 * <code>
	 * is_pay: 0没有充值 1 充值了
	 * reward: 0没有领奖 1领过了
	 * </code>
	 */
	public function isGetPayReward();
	
	/**
	 * 得到充值奖励 
	 * Enter description here ...
	 * @return array
	 * <code>
	 * 'ret':ok
	 * 'grid':背包格子信息
	 * </code>
	 */
	public function getPayReward();
	
	/**
	 * 防沉迷踢下线
	 * Enter description here ...
	 */
	public function wallowKick();
	
	/**
	 * 微端登录奖励
	 * Enter description here ...
	 * @return array
	 * <code>
	 * ret: 'ok'
	 * grid: 奖励物品， 背包格子信息
	 * </code>
	 * 
	 */
	public function getOPClientReward();
	
	/**
	 * 金币购买宝石经验
	 * @return 'ok'
	 */
	public function buyGemExp($id);
	
	/**
	 * 使用金币转移阵营（免费转移也使用次接口）
	 * @param unknown_type $groupId
	 * @return 'ok'  'boss'--boss 期间禁止转  'olympic'--擂台赛禁止转 
	 */
	public function groupTransferByGold($groupId, $gold);
	
	/**
	 * 使用物品转移阵营
	 * @param unit $groupId
	 * @return array
	 * <code>
	 * ret:ok, 'boss'--boss 期间禁止转  'olympic'--擂台赛禁止转 
	 * grid: 背包格子信息
	 * </code>
	 */
	public function groupTransferByItem($groupId);
	
	/**
	 * 显示时装
	 * @param unknown_type $isShow  0 不显示 1 显示
	 * @return ok
	 */
	public function showDress($isShow);
	
	public function getSecondPayInfo();
	
	public function showVip();
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */
