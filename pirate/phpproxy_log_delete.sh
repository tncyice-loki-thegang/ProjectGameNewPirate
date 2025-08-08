#!/bin/bash
#--------phpproxy log delete------

PHPPROXY=/home/pirate/phpproxy
PHPPROXY_LOG=/home/pirate/phpproxy/log
ccDATE=$(date "-d 7 day ago" +%Y%m%d)

cd $PHPPROXY_LOG

for i in `ls phpproxy.log.$ccDATE*`
do
        if [ -e $i ]
        then
                rm -f $i
                echo "Delete $i succeed" >>$PHPPROXY/phpproxy_log_delete.log
        else
                echo "Not found $i file" >>$PHPPROXY/phpproxy_log_delete.log
        fi
done

for h in `ls phpproxy.log.wf.$ccDATE*`
do
        if [ -e $h ]
        then
                rm -f $h
                echo "Delete $h succeed" >>$PHPPROXY/phpproxy_log_delete.log
        else
                echo "Not found $h file" >>$PHPPROXY/phpproxy_log_delete.log
        fi
done
