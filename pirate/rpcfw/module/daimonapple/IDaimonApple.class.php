<?php

interface IDaimonApple
{
	public function getInfo();
	
	public function transfer($src, $des);
	
	public function composite($item_id1, $item_id2, $composite_id);
	
	public function split($item_id);

}
