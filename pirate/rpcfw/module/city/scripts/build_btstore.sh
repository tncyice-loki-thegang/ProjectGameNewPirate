#!/bin/bash

#env
export LANG=en_US.UTF-8


#constant
SVN_RPCFW_PATH="/home/pirate/rpcfw"
SVN_DOCS_PATH="/home/pirate/rpcfw_docs/data"
MYSQLHOST='192.168.1.201'
MYSQLUSER='pirate'
MYSQLPASSWD='admin'
MYSQLDB='pirate'

SCRIPT_TOWN_PATH="$SVN_RPCFW_PATH/module/city/scripts"
SCRIPT_PORT_PATH="$SVN_RPCFW_PATH/module/port/scripts"
SCRIPT_WR_PATH="$SVN_RPCFW_PATH/module/worldResource/scripts"
SCRIPT_BOSS_PATH="$SVN_RPCFW_PATH/module/boss/scripts"
SQL_PORT_PATH="$SVN_RPCFW_PATH/module/port/doc"
SQL_WR_PATH="$SVN_RPCFW_PATH/module/worldResource/doc"
DATA_RPCFW_PATH="$SVN_RPCFW_PATH/data/btstore"
PHP='/home/pirate/bin/php'
MYSQL='/home/pirate/bin/mysql'
ICONV='/usr/bin/iconv -c -f GB2312 -t UTF-8'
SVN='/usr/bin/svn'

SVN_USERNAME=HaidongJia
SVN_PASSWORD=19860918

#svn update
cd $SVN_RPCFW_PATH && $SVN up --username $SVN_USERNAME --password $SVN_PASSWORD --no-auth-cache
cd $SVN_DOCS_PATH && $SVN up --username $SVN_USERNAME --password $SVN_PASSWORD --no-auth-cache

#execute
#town
$PHP $SCRIPT_TOWN_PATH/town.script.php \
	$SVN_DOCS_PATH/town.csv $DATA_RPCFW_PATH/TOWN

#port
$PHP $SCRIPT_PORT_PATH/port.script.php \
	$SVN_DOCS_PATH/port.csv $DATA_RPCFW_PATH/PORT

#port resource
$PHP $SCRIPT_PORT_PATH/resource.script.php \
	$SVN_DOCS_PATH/res.csv $DATA_RPCFW_PATH/PORTRESOURCE

#port resource excavate
$PHP $SCRIPT_PORT_PATH/excavate.script.php \
	$SVN_DOCS_PATH/taojin.csv $DATA_RPCFW_PATH/EXCAVATE

#iconv world resource file
$ICONV $SVN_DOCS_PATH/worldres.csv > $SVN_DOCS_PATH/worldres.csv.iconv
	
#world resource
$PHP $SCRIPT_WR_PATH/worldResource.script.php \
	$SVN_DOCS_PATH/worldres.csv.iconv $DATA_RPCFW_PATH/WORLDRESOURCE
	
#boss
$PHP $SCRIPT_BOSS_PATH/boss.script.php \
	$SVN_DOCS_PATH/worldboss.csv $DATA_RPCFW_PATH/BOSS
	
#boss reward
$PHP $SCRIPT_BOSS_PATH/boss_reward.script.php \
	$SVN_DOCS_PATH/boss_reward.csv $DATA_RPCFW_PATH/BOSS_REWARD
			
#init port 
$PHP $SCRIPT_PORT_PATH/initPort.scripts.php \
	$DATA_RPCFW_PATH/PORT $SQL_PORT_PATH/init_port.sql

#init port resource
$PHP $SCRIPT_PORT_PATH/initPortResource.script.php \
	$DATA_RPCFW_PATH/PORT $DATA_RPCFW_PATH/PORTRESOURCE \
	$SQL_PORT_PATH/init_port_resource.sql
	
#init world resource
$PHP $SCRIPT_WR_PATH/initWorldResource.scripts.php \
	$DATA_RPCFW_PATH/WORLDRESOURCE $SQL_WR_PATH/init_world_resource.sql
	
#import sql 
$MYSQL -u$MYSQLUSER -p$MYSQLPASSWD -h$MYSQLHOST -A $MYSQLDB < $SQL_PORT_PATH/init_port.sql
$MYSQL -u$MYSQLUSER -p$MYSQLPASSWD -h$MYSQLHOST -A $MYSQLDB < $SQL_PORT_PATH/init_port_resource.sql
$MYSQL -u$MYSQLUSER -p$MYSQLPASSWD -h$MYSQLHOST -A $MYSQLDB < $SQL_WR_PATH/init_world_resource.sql
