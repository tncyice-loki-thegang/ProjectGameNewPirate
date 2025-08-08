<?php
/***************************************************************************
 * 
 * Copyright (c) 2011 babeltime.com, Inc. All Rights Reserved
 * $Id$
 * 
 **************************************************************************/

 /**
 * @file $HeadURL$
 * @author $Author$(lanhongyu@babeltime.com)
 * @date $Date$
 * @version $Revision$
 * @brief 
 *  
 **/


require_once LIB_ROOT . '/Util.class.php';

class RefreshDateTask extends BaseScript
{
	/* (non-PHPdoc)
	 * @see BaseScript::executeScript()
	 */
	protected function executeScript ($arrOption)
	{
		RPCContext::getInstance()->sendMsg(array(0), 'task.checkDateTask', array());		
	}

	
}

/* vim: set ts=4 sw=4 sts=4 tw=100 noet: */