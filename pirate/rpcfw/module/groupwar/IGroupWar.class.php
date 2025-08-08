<?php
/***************************************************************************
 * 
 * Copyright (c) 2010 babeltime.com, Inc. All Rights Reserved
 * $Id: IGroupWar.class.php 36163 2013-01-16 09:37:19Z wuqilin $
 * 
 **************************************************************************/

 /**
 * @file $HeadURL: svn://192.168.1.80:3698/C/tags/pirate/rpcfw/rpcfw_1-0-21-57/module/groupwar/IGroupWar.class.php $
 * @author $Author: wuqilin $(wuqilin@babeltime.com)
 * @date $Date: 2013-01-16 17:37:19 +0800 (三, 2013-01-16) $
 * @version $Revision: 36163 $
 * @brief 
 *  
 **/


interface IGroupWar
{
	/**
	 * 获取阵营战整体数据
	 * 
	 * @return mix 失败返回false，成功返回下面的数据
	 * 
	 * <code>
	 * {
	 * 		groupList:		//各阵营信息
	 * 		{
	 * 			1:		//A
	 * 			{
	 * 				initResource:1000
	 * 				curResource:500
	 * 			}
	 * 			2:		//B
	 * 			{
	 * 				initResource:1000
	 * 				curResource:500
	 * 			}
	 * 		}
	 * 		battleList: 
	 * 		[
	 * 				//有多场战斗，每场分上半场、下半场。例如id=1，2的战斗是第一场战斗的上下半场
	 * 				{
	 * 					id:1			//战斗ID
	 * 					attacker:1		//攻击方为西海
	 * 					defender:2
	 * 					startTime:1350015567
	 * 					state:1			//状态 0：还未开始，1：可进入， 2：已经结束
	 * 				}
 	 * 				{
 	 * 
 	 * 					id:2
	 * 					attacker:2		
	 * 					defender:1
	 * 					startTime:1350015567
	 * 					state:0			
	 * 				}
	 * 				{
	 * 					id:3
	 * 					attacker:1		
	 * 					defender:2
	 * 					startTime:1350015567
	 * 					state:0			
	 * 				}
 	 * 				{
 	 * 					id:4
	 * 					attacker:2		
	 * 					defender:1
	 * 					startTime:1350015567
	 * 					state:0			
	 * 				}
	 * 		]
	 * 		userInfo
	 * 		{ 
	 * 			killNum:0
	 * 			score:100
	 * 		}
	 * }
	 */
	public function groupBattleInfo();

	/**
	 *
	 * 参加阵营战
	 *
	 * @param int $battleId
	 *
	 * @return 
	 * ret=
	 * ok: 
	 * reenter:已经在该阵营战中
	 * not_found: 没有找到这个战斗
	 * full:战场人满
	 * 
	 * res = ""
	 * </code>
	 */
	public function enter($battleId = 0);
	
	/**
	 *
	 * 获取进入战场后的初始数据
	 * @param int $battleId
	 *
	 * @return
	 * ret=
	 * ok:
	 *
	 * res =
	 * <code>
	 * {
	 * battlteId:1
	 * refreshMs:1000;	//刷新周期,单位 ms
	 * attacker:
	 * {
	 * 		groupId:1
	 * 		resource:1000
	 * }
	 * defender:
	 * {
	 * 		groupId:2
	 * 		resource:1000
	 * }
	 *
	 * user:
	 * {
	 * 		attackLevel:10
	 * 		defendLevel:10
	 * 		groupId:1		//告诉你这个用户被分到了哪个组
	 * 		canJoinTime:
	 * 		canInspreTime
	 * 		readyTime：	//准备好的时间戳
	 * 		winStreak:0
	 *		extra
	 *		{
	 *			info:
	 *			{
	 *				score:1
	 *				honour:1
	 *				resource:1
	 *				removeJoinCd:1
	 *			}
	 *			topN:
	 *			{
	 *				{uid,uname,score},
	 *				{uid,uname,score}
	 *			}
	 *		}
	 * }
	 *
	 * field
	 * {
	 * 		endTime:120000//毫秒
	 * 		transfer:[1,3,0,4,6,1]  //每个传送阵上的人数，传送阵标号按照从从上向下，从攻方到守方的顺序，从0开始
	 * 		roadLength:100
	 * 		roadState：2/1		//2表示采用大通道数，1表示采用小通道数
	 * 		road:		//通道标号从上到下
	 * 		[
	 *
	 * 			{
	 * 				id:1
	 * 				name:"玩家1"
	 * 				tid:1		// 显示模板ID（对于用户来说就是主角的htid）
	 * 				type:0/1 		//0：用户  1：npc
	 * 				curHp:50
	 * 				maxHp:100
	 * 				transferId:1
	 * 				roadIds:[0]	//玩家都占据一条通道，npc有可能占据多条通道
	 * 				roadX:10	//在通道上的位置
	 * 				stopX:12	//预测单位可能会停止的位置
	 * 				speed:1
	 *				winStreak:1
	 * 			}
	 * 		]
	 *
	 * }
	 * </code>
	 */
	public function getEnterInfo($battleId);
	
	
	
	/**
	 * 退出战场
	 *
	 * @param int $battleId
	 *
	 * @return NULL
	*/
	public function leave($battleId);
	
	
	/**
	 * 参战
	 *
	 * @param int $transferId 传送阵id
	 *
	 * @return array
	 * <code>
	 * 		ret :  (string 取下面之一)
	 * 			ok
	 * 			cdtime	冷却中
	 * 			waitTime 等待中
	 * 			full		队列满
	 * 			battling 战斗中
	 * 			lack_hp	伙伴生命不满
	 * 		outTime:13400000出阵时间戳
	 * 		reward:{score,honour}		//奖励
	 * 		topN 
	 *		{
	 *			{uid,uname,score},
	 *			{uid,uname,score}
	 *		}
	 * </code>
	 *
	*/
	public function join($battleId, $transferId);
	
	/**
	 * 鼓舞
	 *
	 * @param bool $isGold 是否金币鼓舞
	 *
	 * @return string
	 * ret=
	 * 		ok 成功
	 * 		cdtime 
	 * 		full 鼓舞满
	 * 		no_inspire 鼓舞失败
	 * res=
	 * 		attackLevel	=> 1
	 * 		defendLevel => 0
	 * 		cost：1		//鼓舞花费，可能是金币或者阅历
	*/
	function inspire($isGold);
	
	/**
	 * 秒除参战冷却时间
	 *
	 * @return string
	 * ret = ok 成功
	 * res = 5 实际花费的金币数
	*/
	public function removeJoinCd();
	
	
	
	/*******************以下是后端推送给后端的数据****************************/
	/*
	[1]sc.groupwar.refresh
		attacker:
		{
			groupId:1
			resource:1000
		}
		defender:
		{
			groupId:2
			resource:1000
		}
		field
		{
			transfer:[1,3,0,4,6,1]  //每个传送阵上的人数，传送阵标号按照从从上向下，从攻方到守方的顺序，从0开始
			roadLength:100
			roadState：1/2	//1:较少通道，2：较多通道
			road:		//包含所有在通道上的单位
			[ 
				{	//以下数据只有在有更新的时候才会发送，但是id,type是每次都会发送的
					id:1
					type:0/1 		//0：用户  1：npc
										
					//以下数据在两种情况下会有：[1]需要的信息的用户刚刚进入战场；[2]当前单位刚刚进入通道
					name:"玩家1"					
					tid:1
					transferId:1		
					roadIds:[0]	//玩家都占据一条通道，npc有可能占据多条通道, 通道标号从上到下
					speed:1
					maxHp:100
					
					//以下数据在三种情况下会有：[1]需要的信息的用户刚刚进入战场；[2]当前单位刚刚进入通道；[3]当前单位血量发生改变
					curHp:50					
					winStreak:1
					
					//以下数据在三种情况下会有：[1]需要的信息的用户刚刚进入战场；[2]当前单位刚刚进入通道；[3]当前单位发生移动
					roadX:10	//在通道上的位置
					stopX:12	//预测单位可能会停止的位置				 
				}
			]
			touchdown		//这个周期达阵的用户uid
			[
				{
					id:20119,
					type:0
				}
				{
					id:20122,
					type:0
				}				 
			]
			leave
			[
				{
					id:20119,
					type:0
				}
				{
					id:20122,
					type:0
				}
			]
		}

		
	[2]sc.groupwar.fightResult
		winnerUid
		loserUid
		winnerName
		loserName
		winnerType
		loserType
		winStreak	//胜利者连胜次数
		loseStreak	//失败者在此次失败之前的连胜次数
		brid
		
	[3]sc.groupwar.fightWin
		reward:
	 	{
	 		score:1
	 		plunderScore:1 // 掠夺的积分
			honour:1
			belly => 1,
			experience => 1
			prestige =>1	
	 	}
	 	extra
	 	{
	 		loserName:***
	 	}
	 	topN:
	 	{
	 		{uid,uname,score},
	 		{uid,uname,score}
	 	}
	[3]sc.groupwar.fightLose
		reward:
	 	{
	 		score:-1	
	 		plunderScore:1  //被 掠夺的积分
	 	}
	 	extra
	 	{
	 		winnerName:***
	 	}
	 	topN:
	 	{
	 		{uid,uname,score},
	 		{uid,uname,score}
	 	}
	
	[4]sc.groupwar.touchDown
		reward:
	 	{
	 		score:1
			honour:1
	 	}
	 	topN:
	 	{
	 		{uid,uname,score},
	 		{uid,uname,score}
	 	}

		
	[5]sc.groupwar.battleEnd
		ret='ok'
		
		
	[6]sc.groupwar.reckon
		roundEnd=0/1   //0表示是上半场， 1表示是下半场
	    groupResource=1	//所在阵营资源数
	 	winGroup = 0/1/2		//胜利阵营 0：平局，  1：阵营1
	 	rank=1			
	 	score = 1
	 	killNum = 1

	*/	
	
	public function getAutoJoin();
	
	public function setAutoJoin();
}
/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */