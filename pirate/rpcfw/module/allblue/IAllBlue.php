<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IAllBlue.php 40022 2013-03-06 04:10:37Z ZhichaoJiang $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/allblue/IAllBlue.php $
 * @author $Author: ZhichaoJiang $(jiangzhichao@babeltime.com)
 * @date $Date: 2013-03-06 12:10:37 +0800 (三, 2013-03-06) $
 * @version $Revision: 40022 $
 * @brief 
 *  
 **/

interface IAllBlue
{
    /**
     * 获取采集区信息
     * 
     * @return array
     * {
     *    'ret' => string                             'ok'可以获取采集信息,'err'不可
     *  'bellyTimes' => array                         5个区域贝里、金币开采次数
     *                 <code>
     *                 {
     *                  '1' => int                    区域1可开采次数
     *                  '2' => int                    区域1可开采次数
     *                  '3' => int                    区域1可开采次数
     *                  '4' => int                    区域1可开采次数
     *                  '5' => int                    区域1可开采次数
     *              }
     *              </code>
     *  'aliveTimes' => int                           攻击海怪剩余次数
     *  'monster_id' => int                           怪物id 
     * }
     */
    function getAllBlueInfo(); 
    
    /**
     * 开始采集
     * 
     * @param int $type                               采集类型
     * @param boolean $isGold                         true:金币采集,false:贝里采集
     * @param int $collectLevel                       1:一档 2:二档
     * @return array
     * {
     *    'ret' => string                             'ok'可以采集,
     *                                                'noTimes'没有采集次数,
     *                                                'noMoney'贝里不足,
     *                                                'noGold'金币不足,
     *                                                'err'其他错误 
     *    'res' => array
     *            <code>
     *            {
     *                 'items' => array
     *                         <code>
     *                         [{
     *                             'item_template_id' => int    采集物品模板ID
     *                             'item_num' => int            采集物品数量
     *                         }]
     *                         <code>
     *                'monster_id' => int             海怪部队ID(0没有遇到怪物,非0遇到怪物)
     *                'bag' => @see IBag::receiveItem
     *            }
     *            <code>
     * }
     */
    function collectAllBule($type, $isGold, $collectLevel);
    
    /**
     * 攻打海王类
     * 
     * @param int $monsterId                           海怪部队ID
     * @return array
     * {
     *        'ret'                                    是否成功,ok表示成功,noTimes没有攻击次数,err表示失败并且下列数据无效
     *        'fightRet'                               战斗模块返回值
     *        'cd'                                     CD 时间
     *        'reward' => array                        战斗后的奖励
     *                     <code>
     *                    {
     *                     'belly'                     贝利
     *                     'exp'                       经验
     *                     'experience'                阅历
     *                     'prestige'                  声望
     *                     'items' => array
     *                     <code>
     *                     [{
     *                         'item_template_id' => int    采集物品模板ID
     *                         'item_num' => int            采集物品数量
     *                     }]
     *                     <code>
     *                     'bag' => @see IBag::receiveItem    包裹信息
     *                     'aliveTimes' => int         还怪复活次数
     *                     }
     *                     <code>
     *        'appraisal'                              战斗评价(数字表示)
     * }
     */
    function atkSeaMonster($monsterId); 

    /**
     * 获取养鱼的信息
     * 
     * @return array
     * {
     *        'ret'                                    ok表示成功,err表示失败并且下列数据无效
     *        'fftimes'                                今日剩余养殖次数
     *        'tftimes'                                今日剩余偷鱼次数
     *        'wftimes'                                今日剩余祝福次数
     *        'wfdtimes'                               今日剩余被祝福次数
     *        'fishqueue' => array                     养鱼队列信息(key默认从0开始)
     *                     <code>
     *                     {
     *                            array
     *                             <code>
     *                             [{
     *                                'qstatus' => int   队列的状态 0:未开通,1:已开通
     *                                'fstatus' => int   养鱼的状态 0:空闲,1:养殖中,2:成熟
     *                                'btime' => string  养鱼开始时间
     *                                'etime' => string  养鱼成熟时间
     *                                'fishid' => int            该队列所养的鱼的ID
     *                                'isboot' => int    保护罩状态 0:未开启 1:开启
     *                                'tfcount' => int   该队列被偷的次数
     *                                'wfcount' => int   该队列被祝福的次数
     *                                'krillid' => int   准备养殖的鱼苗 0:没有鱼
     *                                'krillinfo' => array(fishid)   鱼苗信息
     *                                'fishnum' => int   剩余的鱼的数量
     *                            }]
     *                           <code>
     *                     }
     *                     <code>
     * }
     */
    function farmFishInfo(); 

    /**
     * 开启保护罩
     * 
     * @param int $queueId                             队列ID(队列组从0开始)
     * @return string ret                              是否成功,ok表示成功,noGold金币不足,'noFish'没有鱼,err表示失败并且下列数据无效
     * 
     */
    function openBoot($queueId); 
    
    /**
     * 获取已成熟的鱼
     * 
     * @param int $queueId                             队列ID(队列组从0开始)
     * @return array
     * {
     *    'ret' => string                              'ok'可以捕获,'noFish'没有鱼,'noRipe'没有成熟,'noBag'包裹满,'err'其他错误 
     *    'item' => array
     *             <code>
     *                 {
     *                     'item_template_id' => int   物品模板ID(成熟的鱼)
     *                     'item_num' => int           物品数量(成熟的鱼)
     *                 }
     *             <code>
     *    'bag' => @see IBag::receiveItem
     * }
     * 
     */
    function fishing($queueId); 
    
    /**
     * 开通养鱼队列
     * 
     * @param int $queueId                             队列ID(队列组从0开始)
     * @return 'ret' => string                         'ok'开通成功,'noGold'金币不足,'noQueue'没有改队列,'err'其他错误 
     * 
     */
    function openFishQueue($queueId); 
    
    /**
     * 获得鱼缸信息
     * 
     * @param int $queueId                             队列ID(队列组从0开始)
     * @return array
     * {
     *        'ret'                                    ok表示成功,err表示失败并且下列数据无效
     *        'krillinfo' => array
     *              <code>
     *              {
     *              'reTimes' => int                   已经捞了几次
     *                'fish' => int                    养殖处放着的鱼 0:没有,非0:鱼的ID
     *                'fishinfo' => array              鱼缸里面放着的鱼苗(前一次鱼苗的信息)
     *                  <code>
     *                  {
     *                        int                      鱼苗id
     *                  }
     *                  <code>
     *                 }
     *                 <code>
     * }
     * 
     */
    function krillInfo($queueId);

    /**
     * 捞鱼苗(5条)
     * 
     * @param int $queueId                             队列ID(队列组从0开始)
     * @return array
     * {
     *        'ret'                                    ok表示成功,err表示失败并且下列数据无效
     *        'fish' => array                          随即出来的鱼苗(5条)
     *              <code>
     *              {
     *                    int                          鱼苗id
     *              }
     *              <code>
     * }
     * 
     */
    function catchKrills($queueId);

     /**
     * 捞鱼(1条)
     * 
     * @return array
     * {
     *        'ret'                                    ok表示成功,noGold金币不足,noFish没有鱼苗,err表示失败并且下列数据无效
     *        'fish' => int                            5条玉面中随即给1条鱼苗
     * }
     * 
     */
    function catchKrill($queueId);
    
    /**
     * 刷新(换一批鱼苗)
     * 
     * @param int $queueId                             队列ID(队列组从0开始)
     * @return array
     * {
     *        'ret'                                    ok表示成功,err表示失败并且下列数据无效
     *        'fish' => array                          随即出来的鱼苗(5条)
     *              <code>
     *              {
     *                    int                          鱼苗ID
     *              }
     *              <code>
     * }
     * 
     */
    function refreshKrill($queueId);
    
    /**
     * 养殖
     * 
     * @param int $queueId                             队列ID(队列组从0开始)
     * @return array
     * {
     *        'ret'                                    ok表示成功,noKrill没有准备要养殖的鱼苗,noTimes没有养殖次数,err表示失败并且下列数据无效
     *        'fishqueue' => array                     养鱼队列信息(key默认从0开始)
     *                     <code>
     *                     {
     *                         'qstatus' => int        队列的状态 0:未开通,1:已开通
     *                         'fstatus' => int        养鱼的状态 0:空闲,1:养殖中,2:成熟
     *                         'btime' => string       养鱼开始时间
     *                         'etime' => string       养鱼成熟时间
     *                         'fishid' => int         该队列所养的鱼的ID
     *                         'isboot' => int         保护罩状态 0:未开启 1:开启
     *                         'tfcount' => int        该队列被偷的次数
     *                         'wfcount' => int        该队列被祝福的次数
     *                         'krillid' => int        准备养殖的鱼苗 0:没有鱼
     *                         'krillinfo' => array(fishid)   鱼苗信息
     *                         'krillinfo' => int      可以收获鱼的数量
     *                     }
     * }
     * 
     */
    function farmFish($queueId);
    
    /**
     * 获得好友列表
     * 
     * @param int $offset 分页位置
     * @param int $limit 每页大小
     * @return array
     * {
     *         'userinfo' => array
     *         <code>
     *         [{
     *             uid:好友id
     *             uname:好友姓名
     *             htid:用户模板id
     *             level:用户等级
     *             isthief:是否可以偷鱼 0:不可 1:可
     *             iswish:是否可以祝福 0:不可 1:可
     *             isconquer:是不是可以被征服0:放,1:征,2:其他
     *         }]
     *         </code>
     *         'pagenum' => int
     * }
     * 
     */
    function getFriendList($offset, $limit);
    
    /**
     * 获得好友的养鱼信息(迁移到好友的鱼池)
     * 
     * @param int $fuid 所选的好友uid
     * @return array
     * {
     *        'ret'                                    ok表示成功,err表示失败并且下列数据无效
     *        
     *        'myself' => array
     *                {
     *                    'tftimes'                    今日剩余偷鱼次数
     *                    'wftimes'                    今日祝福次数
     *                    'wdftimes'                   今日被祝福次数
     *                }
     *        'myfrend' => array
     *                {
     *                    'fishqueue' => array         养鱼队列信息(key默认从0开始)
     *                  <code>
     *                  {
     *                        array
     *                         <code>
     *                         [{
     *                            'qstatus' => int     队列的状态 0:未开通,1:已开通
     *                            'fstatus' => int     养鱼的状态 0:空闲,1:养殖中,2:成熟
     *                            'btime' => string    养鱼开始时间
     *                            'etime' => string    养鱼成熟时间
     *                            'fishid' => int      该队列所养的鱼的ID
     *                            'isboot' => int      保护罩状态 0:未开启 1:开启
     *                            'tfcount' => int     该队列被偷的次数
     *                            'wfcount' => int     该队列被祝福的次数
     *                            'krillid' => int     准备养殖的鱼苗 0:没有鱼
     *                            'krillinfo' => array(fishid)   鱼苗信息
     *                            'wisher' => int  	        是否已经祝福过鱼0:未祝福,1:已祝福
     *                            'thief' => int   	        是否已经偷过鱼0:未偷,1:已偷
     *                            
     *                        }]
     *                       <code>
     *                  }
     *                }
     *        'issubord' => int                        是不是自己的下属0:不是,1:是
     * }
     */
    function goFriendFishpond($fuid);

    /**
     * 偷好友的鱼
     * 
     * @param int $fuid                                所选的好友fuid
     * @param int $queueId                             所选的好友的养鱼队列id(从0开始)
     * @return array
     * {
     *    'ret'                                        ok表示成功,noBag背包满了,noRipe没有偷鱼次数,noTfTimes没有偷鱼次数,qStolen该队列你已经偷过了
     *                                                 fNoTfTimes好友被偷次数用完,booting有保护罩,err表示失败并且下列数据无效
     *    'item' => array
     *             <code>
     *                 {
     *                     'item_template_id' => int   物品模板ID(成熟的鱼)
     *                     'item_num' => int           物品数量(成熟的鱼)
     *                 }
     *             <code>
     *    'queueInfo' => array()                       队列信息
     *    'bag' => @see IBag::receiveItem
     * }
     */
    function thiefFish($fuid, $queueId);

    /**
     * 祝福好友的鱼
     * 
     * @param int $fuid                                所选的好友fuid
     * @param int $queueId                             所选的好友的养鱼队列id(从0开始)
     * @return array
     *             {
     *                 'ret' => string                 ok表示成功,nowfTimes没有祝福次数,fNoWfTimes好友的该鱼苗可祝福次数已用完,
     *                                                 qWished该队列已经祝福过了,noFish没有鱼,ripe鱼已经熟了,err表示失败并且下列数据无效
     *                 'queueInfo' => array()          队列信息
     *             }
     */
    function wishFish($fuid, $queueId);
    
    /**
     * 
     * @see IAllBlue::farmFishInfo()
     */
    function getFarmFishInfo();
    
    /**
     * 
     * 获得下属列表
     */
    function getSubordinateList();
    
	/**
     * 获取下属养殖列表
     * 
     * @return array
     *        <code>
     *             {
     *                 'uid' => array
     *                 'uname' => string  下属名字
     *                 'fishinfo' => 
     *                          array
     *                           <code>
     *                               {
     *                                 'fishid' => int,    鱼id
     *                                 'beginTime' => int  养鱼开始时间
     *                               }
     *                            <code>
     *             }
     *        <code>
     */
    function getSubordinateFishList();
	
	function getAllBlueLevelInfo();
	
	function donateByItem();
	
	function donateByGold();
	
	function buyDonateItemTimes();
	
	function catchFish();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */