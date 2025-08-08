<?php
/*
 *	Class:	GuildWar
 *	Author:	RiosS
 *	Email:	riossvn@gmail.com
 *	Date:	08/05/2017
 */

class GuildWar implements IGuildWar {
	public function getUserGuildWarInfo() {
		$ret = array('session' => 1, 'cheer_time' => 0,
				'worship_times' => 0, 'worship_time' => 0, 'sign_time' => 0, 'world_prize_id' => 0, 'update_fmt_time' => 0, 'world_prize_time' => 0,
				'is_player' => 1, 'cheer_guild_id' => 0, 'cheer_guild_server_id' => 0, 'fight_force' => 0, 'max_win_times' => 0, 'max_win_time' => 0, 'send_prize_time' => 0);
		return $ret;
	}
	
	public function getGuildWarInfoByID() {
		return array();
	}
}
