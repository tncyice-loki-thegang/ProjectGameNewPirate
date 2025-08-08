<?php

/***************************************************************************
 *
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IGuild.class.php 34823 2013-01-08 08:44:09Z HaidongJia $
 *
 **************************************************************************/

/**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/guild/IGuild.class.php $
 * @author $Author: HaidongJia $(hoping@babeltime.com)
 * @date $Date: 2013-01-08 16:44:09 +0800 (二, 2013-01-08) $
 * @version $Revision: 34823 $
 * @brief
 *
 **/
interface IGuild
{

	/**
	 * 弹劾会长
	 * @return string
	 * <code>
	 * ok表示成功
	 * time表示时间未到3天
	 * gold表示金币不足
	 * self表示不能弹劾自己
	 * </code>
	 */
	function impeach();

	/**
	 * 创建公会
	 * @param string $name 公会名称
	 * @param string $post 公会公告
	 * @param string $slogan 公会宣言
	 * @param string $passwd 公会会长密码
	 * @return array
	 * <code>
	 * {
	 * err:创建情况，harmony代表公会名称有敏感词，exceed代表创建公会超过上限，used代表公会名称已经被使用，lack_money代表游戏币不足,blank表示含有空格，ok代表成功
	 * guildId:新创建的公会id
	 * cost:花费的belly
	 * }
	 * <code>
	 */
	function create($name, $post, $slogan, $passwd = "");

	/**
	 * 用户加入公会
	 * @param int $uid
	 */
	function update($uid, $join);

	/**
	 * 申请某个工会
	 * @param int $guildId 要申请的公会id
	 * @return string 处理结果
	 * <code>
	 * ok 处理成功
	 * exceed 超过申请上限
	 * diff_group 不同阵营
	 * </code>
	 */
	function apply($guildId);

	/**
	 * 同意某个人的申请
	 * @param int $uid
	 * @return string 处理结果
	 * <code>
	 * ok 处理成功
	 * exceed 工会人数超过上限
	 * dispatched 申请已经处理过
	 * </code>
	 */
	function agree($uid);

	/**
	 * 拒绝某个人的申请
	 * @param int $uid
	 * @return string 处理结果
	 * <code>
	 * ok 处理成功
	 * dispatched 申请已经处理
	 * </code>
	 */
	function refuse($uid);

	/**
	 * 取消某个申请记录
	 * @param int $guildId
	 * @return string 处理结果
	 * <code>
	 * ok 处理成功
	 * </code>
	 */
	function cancel($guildId);

	/**
	 * 获取用户所在公会的申请列表
	 * @param int $offset 分页位置
	 * @param int $limit 每页大小
	 * @return array 申请记录
	 * <code>
	 * {
	 * count:总数
	 * offset:回传offset
	 * data:[{
	 * uid:用户uid
	 * uname:用户uname
	 * level:等级
	 * vip:vip等级
	 * last_login_time:最后一次登录时间
	 * status:状态
	 * contribute_data:贡献值
	 * }]
	 * }
	 * </code>
	 * @see UserDef
	 */
	function getGuildApplyList($offset, $limit);

	/**
	 * 获取单个用户的申请记录
	 * @return array 个人申请记录
	 * [{
	 * guild_id:分会id
	 * name:公会名称
	 * guild_level:公会等级
	 * current_emblem_id:当前会微
	 * }]
	 */
	function getPersonalApplyList();

	/**
	 * 获取成员列表
	 * @param int $offset
	 * @param int $limit
	 * @return array 获取成员列表
	 * <code>
	 * {
	 * count:成员总数
	 * offset:回传给前端
	 * data:[{
	 * uid:用户uid
	 * uname:用户name
	 * status:状态
	 * last_login_time:最后一次登录时间
	 * level:等级
	 * contribute_data:贡献值
	 * rank:用户排名
	 * official:官职
	 * role_type:职位
	 * }]
	 * }
	 * </code>
	 * @see GuildOfficialType
	 * @see GuildRoleType
	 */
	function getMemberList($offset, $limit);

	/**
	 * 获取成员列表
	 * @param int $offset
	 * @param int $limit
	 * @return array 获取成员列表，多了一个position, vip字段
	 * @see IGuild::getMemberList()
	 */
	function getMemberArenaList($offset, $limit);

	/**
	 * 获取自己的成员信息
	 * @return array
	 * <code>
	 * {
	 * official:官职
	 * role_type:职位
	 * guild_id:公会id,
	 * uid:用户uid,
	 * contribute_data:贡献值,
	 * day_belly_num:今日贡献belly数,
	 * last_belly_time:最后一次贡献belly时间,
	 * last_gold_time:最后一次贡献金币时间,
	 * last_banquet_time:最后一次参加宴会时间,
	 * status:用户状态 ,
	 * va_info:其他信息
	 * }
	 * </code>
	 * @see GuildRoleType
	 * @see GuildOfficialType
	 */
	function getMemberInfo();

	/**
	 * 退出当前工会
	 * @return string ok表示成功
	 */
	function quit();

	/**
	 * 得到全世界的工会列表
	 * @param int $offset
	 * @param int $limit
	 * @param bool $exclude 是否排除已经申请的公会
	 * @return array 查询结果
	 * <code>
	 * {
	 * count:总记录数
	 * offset:回传
	 * data:[{
	 * guild_id:公会id
	 * name:工会名称
	 * guild_level:工会等级
	 * current_emblem_id:当前会徽
	 * group_id:阵营id
	 * }]
	 * }
	 * </code>
	 */
	function getWorldList($offset, $limit, $exclude = true);

	/**
	 * 根据名称查询对应公会
	 * @param string $name 工会名称
	 * @return array 公会信息
	 * <code>
	 * {
	 * guild_id:公会名称
	 * }
	 * </code>
	 * @see IGuild::getWorldList()
	 */
	function getGuildByName($name, $offset, $limit);

	/**
	 * 得到自己阵营的工会列表
	 * @param int $offset
	 * @param int $limit
	 * @param bool $exclude 是否排除已经申请的公会
	 * @return array
	 * <code>
	 * {
	 * count:总公会数
	 * offset:回传
	 * data:[{
	 * guild_id:公会id
	 * name:工会名称
	 * guild_level:工会等级
	 * current_emblem_id:当前会徽
	 * }]
	 * }
	 * </code>
	 */
	function getGroupList($offset, $limit, $exclude = true);

	/**
	 * 贡献工会
	 * @param int $bellyNum
	 * @return string 贡献结果
	 * <code>
	 * {
	 * err:ok表示成功，tech_full科技已升满，lack_belly缺少游戏币,exceed超过当日贡献,overflow所有科技已经升满
	 * prestige:增加的声望
	 * }
	 * </code>
	 */
	function contributeBelly($bellyNum);

	/**
	 * 金币贡献
	 * @param int $goldNum
	 * @return array 贡献结果
	 * <code>
	 * {
	 * err:ok 贡献成功， exceed 超过上限
	 * prestige:增加的声望
	 * }
	 * </code>
	 */
	function contributeGold($goldNum);

	/**
	 * 更新宣言
	 * @param string $slogan
	 * @return array 更新结果
	 * <code>
	 * {
	 * err:ok表示成功，其他表示失败
	 * slogan:更新后的slogan
	 * }
	 * </code>
	 */
	function updateSlogan($slogan);

	/**
	 * 更新公告
	 * @param string $post
	 * @return array 更新结果
	 * <code>
	 * {
	 * err:ok表示成功，其他表示失败
	 * post:最终的post
	 * }
	 * </code>
	 */
	function updatePost($post);

	/**
	 * 升级宴会科技
	 * @return string 升级结果
	 * <code>
	 * {
	 * ok 升级成功
	 * lack_reward_point 积分不足
	 * banquet_full 宴会等级已满
	 * }
	 * </code>
	 */
	function upgradeBanquet();

	/**
	 * 举办宴会
	 * @param int $time
	 * <code>
	 * ok 举办成功
	 * hold 已经举办
	 * lack_reward_point 缺少积分
	 * </code>
	 */
	function holdBanquet($time);

	/**
	 * 刷新宴会
	 * @return array
	 * <code>
	 * {
	 * err:retake表示已经参数过，不用再过这个命令上来，ok表示成功
	 * experience:总增加的阅历
	 * }
	 * </code>
	 */
	function refreshBanquet();

	/**
	 * 进入俱乐部
	 * @param int $x 进入的x坐标
	 * @param int $y 进入的y坐标
	 * @return array
	 * <code>
	 * {
	 * experience:当前阅历
	 * last_banquet_time:宴会时间
	 * user_banquet_time:用户最后一次宴会时间
	 * }
	 * </code>
	 */
	function enterClub($x, $y);

	/**
	 * 离开俱乐部
	 */
	function leaveClub();

	/**
	 * 最终奖励
	 * @param int $guildId
	 * @param int $time
	 */
	function finalReward($guildId, $time);

	/**
	 * 获取本人所在工会的信息
	 * @return array 工会信息
	 * <code>
	 * {
	 * guild_id:工会id
	 * last_banquet_time:最后一次宴会时间
	 * default_tech:当前的默认科技
	 * guild_level:工会等级科技
	 * guild_data:工会等级经验值
	 * exp_level:经验科技等级
	 * exp_data:经验科技经验
	 * experience_level:阅历等级
	 * experience_data:阅历经验
	 * resource_level:资源等级
	 * resource_data:资源经验
	 * banquet_level:宴会等级
	 * reward_point:工会积分
	 * week_contribute_data:本周工会总积分
	 * gold_member_num:当前金币购买次数
	 * va_info:{
	 * arrEmblem:当前已经购买的会徽
	 * post:
	 * slogan:
	 * }
	 * }
	 * </code>
	 * @see GuildTech
	 * @see IGuild::getGuildInfoById()
	 */
	function getGuildInfo();

	/**
	 * 获取公会以及成员信息
	 * @return array
	 * <code>
	 * {
	 * user_banquet_time:成员参加宴会时间
	 * va_guild_info:对应getGuildInfo中的va_info
	 * }
	 * </code>
	 * @see IGuild::getGuildInfo()
	 * @see IGuild::getMemberInfo()
	 */
	function getGuildAndMemberInfo();

	/**
	 * 获取某个工会的信息
	 * @param int $guildId
	 * @return array
	 * <code>
	 * {
	 * guild_id:工会id
	 * name:公会名称
	 * va_info:{
	 * post:公告
	 * slogan:宣言
	 * }
	 * worldRank:世界排名
	 * groupRank:阵营排名
	 * presidentUname:会长名称
	 * president_uid:会长id
	 * guild_level:公会等级
	 * currMemberNum:当前人数
	 * maxMemberNum:最大人数
	 * group_id:所属阵营
	 * current_emblem_id:工会会徽
	 * }
	 * </code>
	 */
	function getGuildInfoById($guildId);

	/**
	 * 设置副会长
	 * @param int $uid
	 * @return string
	 * <code>
	 * {
	 * ok 表示成功
	 * full 表示副会长已经满员
	 * no_member 表示不是公会成员
	 * }
	 * </code>
	 */
	function setVicePresident($uid);

	/**
	 * 取消副会长
	 * @param int $uid
	 */
	function unsetVicePresident($uid);

	/**
	 * 获取当前登录用户的公会buffer信息
	 * @return array
	 * <code>
	 * {
	 * battleExpAddition:战斗经验百分比
	 * battleExperienceAddition:战斗阅历百分比
	 * resourceAddition:产量百分比
	 * }
	 * </code>
	 */
	function getBuffer();

	/**
	 * 买成员数量
	 * @param int $goldNum
	 * @return string
	 * <code>
	 * ok 购买成功
	 * full 已经买到上限
	 * no_gold 金币不足
	 * </code>
	 */
	function buyMemberNum($goldNum);

	/**
	 * 购买会徽
	 * @param int $emblemId
	 * @return string
	 * <code>
	 * ok 购买成功
	 * bought 已经购买过
	 * lack_reward_point 缺少积分
	 * </code>
	 */
	function buyEmblem($emblemId);

	/**
	 * 设置会徽
	 * @return string
	 * <code>
	 * ok 表示成功
	 * </code>
	 */
	function setEmblem($emblemId);

	/**
	 * 设置默认科技
	 * @param int $defaultTech
	 * @see GuildTech
	 */
	function setDefaultTech($defaultTech);

	/**
	 * 获取贡献记录信息
	 * @return array
	 * <code>
	 * [{
	 * uid:用户uid
	 * name:用户名
	 * contribute_type:贡献类型
	 * contribute_data:贡献数量
	 * contribute_tech:贡献科技
	 * contribute_time:贡献时间
	 * }]
	 * </code>
	 * @see GuildContributeType
	 * @see GuildTech
	 */
	function getRecordList();

	/**
	 * 将目标用户从公会中踢出
	 * @param int $targetUid
	 * @return string ok表示成功，fail表示失败
	 */
	function kickMember($targetUid);

	/**
	 * 转让会长
	 * @param int $targetUid
	 * @return string ok表示成功,no_member表示不是成员,err_passwd表示密码错误
	 */
	function transPresident($targetUid, $passwd = "");

	/**
	 * 鼓舞
	 * @param bool $isGold 是否金币鼓舞
	 * @return string 含意如下
	 * ok 成功
	 * full 鼓舞满
	 * no_inspire 鼓舞失败
	 * no_flag 鼓舞成功但是没有旗子
	 */
	function inspire($isGold);

	/**
	 * 开启一个新旗子
	 * @return string 含意如下
	 * ok 成功
	 * lack_cost 金币不足
	 */
	function openFlag();

	/**
	 * 解散公会
	 * @return string ok表示解散成功，member表示还有成员, err_passwd表示密码错误
	 */
	function dismiss($passwd = "");

	/**
	 *
	 * 修改会长密码
	 *
	 * @param string $oPasswd
	 * @param string $nPasswd
	 *
	 * @return string 含意如下
	 * ok 成功
	 * err 其他错误,权限不足，就密码不对
	 */
	function modifyPasswd($oPasswd, $nPasswd);
	
	function holdBanquetGuildBoss();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */