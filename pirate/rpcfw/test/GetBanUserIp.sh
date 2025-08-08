#!/usr/bin/env bash

# Time-stamp: <2012-07-26 15:06:59 Thursday by liuyang>

# @version 1.0
# @author liuyang


#!/bin/sh
#进入日志目录
cd /home/pirate/rpcfw/log

#现阶段被ban的用户, 将其导入到临时文件
# pid 服务器名 用户名
blackList="50904 game002 三哥"
#遍历黑名单
echo "$blackList" | while read sb; 
do 
		#获取pid
		pid=`echo $sb | awk '{print $1}'`
		#获取服务器
		server=`echo $sb | awk '{print $2}'`
		#获取用户名
		uname=`echo $sb | awk '{print $3}'`
		#获取登陆的所有日志
		loginLog=`grep $pid rpc.log.2* | grep NOTICE | grep user.userLogin | grep $server`
		#查看这一次登陆
		echo "$loginLog" | while read once; 
		do 
			ip=`echo "$once" | cut -d] -f4 | cut -d: -f2`
			loginTime=`echo "$once" | cut -d] -f1 | cut -d[ -f2`

			echo $loginTime $ip $uname >> banUserIp.log
		done

done

#################################################  End of File  #######################################################
