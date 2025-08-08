<?php

class Pachinko implements IPachinko
{
	public function getUserPachinkoInfo()
	{
		$ret = array(
						'coins' => 1,
						'gold_play_times' => 2,
						'inner_circle' => 3
		);
		return $ret;
	}
	
	public function play()
	{
		$ret = array(
						'bag' => array(),
						'id' => 2
		);
		return $ret;

	}
	
	public function showHand()
	{
		$ret = array(
						'bag' => array(),
						'ids' => array(),
						'inner_circle' => 1
		);
		return $ret;

	}

}
