#!/bin/sh

group=$1

if [ "$group" = "" ]; then
    "Usage: sh init.sh group" 
    exit 0
fi

echo -n "check if db is ok, press enter to start"
read p
btscript $group CheckDB.php

echo -n "init boss, press enter to start" 
read p
btscript $group BossInit.class.php

echo -n "init artificer, press enter to start" 
read p
btscript $group ArtificerLeaveTimeInit.php

echo -n "init world resource, press enter to start" 
read p
btscript $group WorldResourceInit.class.php
