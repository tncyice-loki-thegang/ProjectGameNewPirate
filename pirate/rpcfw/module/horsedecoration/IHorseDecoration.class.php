<?php

interface IHorseDecoration
{	
	public function getInfo();

	public function setSuit($decoration_id);
	
	public function reinforce($pos);
	
	public function refresh($decoration_id, $lock_ids);
	
	public function replace($decoration_id);
	
	public function transfer($old_id, $new_id, $type);
}	