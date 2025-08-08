#!/bin/bash

ip=$1
db=$2
us=`whoami`
if [ "$us" == "pirate" ];then
        continue
else
        echo 'must use pirate'
        exit 0
fi

if [ $# -eq 2 ];then
        continue
else
        echo '''
                usage: sh db_init_check.sh [mdbip] [dbname]
                '''
fi

echo '-------------------------------'
echo $ip  $db

dir=/home/pirate/programs/mysql


arena=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_arena'"|sed -n 2p`
user=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_user'"|sed -n 2p`
boss=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_boss'"|sed -n 2p`
olympic=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_olympic'"|sed -n 2p`
world=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_world_resource'"|sed -n 2p`
port=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_port_resource'"|sed -n 2p`
groupwar=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_group_war_resource'"|sed -n 2p`
npcboat=`ssh $ip "cd $dir && bin/mysql -u root -e 'use $db;select count(*) from t_treasure_npc'"|sed -n 2p`
if [ $arena -ge 5 ];then
        echo 'ok'
else
        echo 'error！检查表t_arena'
        echo $arena
fi
if [ $user  -ge  5 ];then 
        echo 'ok'
else
        echo 'error！检查t_user表'
        echo $user
fi

if [ $boss -ge 2 ];then
        echo 'ok'
else
        echo 'error!检查t_boss表'
        echo $boss
fi

if [ $olympic -ge 32 ];then
        echo 'ok'
else
        echo 'error! 检查t_olympic表'
        echo $olympic
fi

if [ $world -ge 5 ];then
        echo 'ok'
else
        echo 'error!检查t_world_resource表'
        echo $world
fi


if [ $port -ge 3952 ];then
        echo 'ok'
else
        echo 'error!检查t_port_resource表'
        echo $port
fi


if [ $groupwar -ge 2 ];then
        echo 'ok'
else
        echo 'error!检查t_group_war_resource表'
        echo $groupwar
fi


if [ $npcboat -ge 10 ]; then
        echo 'ok'
else
        echo 'error!检查t_treasure_npc表'
        echo $npcboat
fi
