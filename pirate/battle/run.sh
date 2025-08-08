#!/bin/sh

module=battle
args=""

function usage()
{
	echo "sh $0 start|stop|restart|force_restart"
	echo "  start: start $module only if $module not running"
	echo "  stop: stop $module only if $module is running"
	echo "  restart: restart $module only if $module is running, supervisor will not be restarted"
	echo "  force_restart: first stop then start"
}

function op_start()
{
        count=`ps -ef|grep supervisor.$module|wc -l`
        if [ $count -gt 1 ]; then
                echo "start $module failed: already started"
                return
        fi
        bin/supervisor.$module bin/$module $args> $ROOT/log/supervisor.log 2>&1 &
        echo "start $module ok"
}

function op_stop()
{
        count=`ps -ef|grep supervisor.$module|wc -l`
        if [ $count -lt 2 ]; then
                echo "stop $module failed: already stopped"
                return
        fi
        killall -9 supervisor.$module $module
        echo "stop $module ok"
}

function op_restart()
{

        count=`ps -ef|grep supervisor.$module|wc -l`
        if [ $count -lt 2 ]; then
                echo "restart $module failed: service stopped"
                return
        fi
        killall -9 $module
        echo "restart $module ok"
}

set -e

#ulimit -n 65535

script=$0
if [ -L $script ]; then
        script=`ls -l $script|awk '{print $11}'`
fi

ROOT=`dirname $script`

cd $ROOT

op=$1
if [ "$op" = "" ]; then
        usage
        exit 0
fi

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
