#!/bin/sh

module=lcserver

function usage()
{
    echo "sh $0 start|stop|restar|args group"
    echo "  start: start $module only if $module not running"
    echo "  stop: stop $module only if $module is running"
    echo "  restart: restart $module only if $module is running, supervisor will not be restarted"
    echo "  force_restart: first stop then start"
    echo "  args: show start arguments"
}

ulimit -n 65535

script=$0
if [ -L $script ]; then
        script=`ls -l $script|awk '{print $11}'`
fi

ROOT=`dirname $script`

op=$1
if [ "$op" = "" ]; then
    usage
    exit 0
fi

name=$2
if [ "$name" = "" ]; then
    usage
    exit 0
fi


if [ ! -d /home/pirate/rpcfw/conf/gsc/$name ]; then
    echo "/home/pirate/rpcfw/conf/gsc/$name does not exists"
    exit 0
fi

if [ ! -e $ROOT/conf/$name.xml ]; then
    echo "$ROOT/conf/$name.xml does not exist"
    exit 0
fi


args="-c conf/$name.xml"

function op_start()
{
    count=`ps -ef|grep supervisor.$module|grep $name|wc -l`
    if [ $count -eq 1 ]; then
        echo "start $module $name failed: already started"
        return
    fi
    bin/supervisor.$module bin/$module $args> $ROOT/log/$name.supervisor.log 2>&1 &
    echo "start $module ok"
}

function op_stop()
{
    count=`ps -ef|grep supervisor.$module|grep $name|wc -l`
    if [ $count -lt 1 ]; then
        echo "stop $module $name failed: already stopped"
        return
    fi
    
    supervisor_pid=`ps -ef|grep supervisor.$module|grep $name|awk '{print $2}'`
    if [ "$supervisor_pid" != "" ]; then
        kill -9 $supervisor_pid
    fi
    
    pid=`ps -ef|grep -v supervisor|grep $module|grep $name|awk '{print $2}'`
    if [ "$pid" != "" ]; then
        kill -9 $pid
    fi
    
    echo "stop $module $name ok"
}


function op_restart()
{
    count=`ps -ef|grep -v supervisor|grep $module|grep $name|wc -l`
    if [ $count -lt 1 ]; then
        echo "restart $module $name failed: service stopped"
        return
    fi
    
    pid=`ps -ef|grep -v supervisor|grep $module|grep $name|awk '{print $2}'`
    if [ "$pid" != "" ]; then
        kill -9 $pid
    fi
    
    echo "restart $module $name ok"
}


cd $ROOT

case $op in
    start)
    op_start
    ;;
    stop)
    op_stop
    ;;
    restart)
    op_restart
    ;;
    force_restart)
    op_stop
    sleep 1
    op_start
    ;;
    *)
    usage
    ;;
esac
