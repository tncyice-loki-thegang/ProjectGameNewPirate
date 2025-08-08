<?php

interface IGrowUpPlan
{
	public function activation();
	
	public function getInfo();
	
	public function fetchPrize($pos);
}
