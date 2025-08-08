<?php

interface IBlood
{
	public function getInfo();
	
	public function create($enemyID, $isAutoStart, $joinLimit);
	
	public function join($teamId);
	
	public function start($teamList);
}

// autoReceiveScore
// buyFailCount
// buyReceiveCount
// buyReceiveCount
// cancelReady
// change
// changeFormation
// create
// endBlood
// enhanceAttr
// enhanceAttrAll
// enter
// formation
// getEnterInfo
// getInfo
// join
// leave
// notify
// ready
// receiveScore
// reChallenge
// serverRank
// start
