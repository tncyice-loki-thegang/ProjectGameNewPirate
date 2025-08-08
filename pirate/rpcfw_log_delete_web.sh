#!/bin/bash
#-------- web_rpcfw log delete------

RPCFW=/home/pirate/rpcfw
RPCFW_LOG=/home/pirate/rpcfw/log
ccDATE=$(date "-d 7 day ago" +%Y%m%d)

cd $RPCFW_LOG

for i in `ls *$ccDATE*`
do
        if [ -e $i ]
        then
                rm -f $i
                echo "Delete $i succeed" >>$RPCFW/rpcfw_web_log_delete.log
        else
                echo "Not found $i file" >>$RPCFW/rpcfw_web_log_delete.log
        fi
done
