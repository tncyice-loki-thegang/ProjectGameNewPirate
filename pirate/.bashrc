# .bashrc

# Source global definitions
if [ -f /etc/bashrc ]; then
	. /etc/bashrc
fi

# User specific aliases and functions

alias zkclient='/home/pirate/programs/zookeeper/bin/cli_mt 127.0.0.1:2182'
alias btscript='/home/pirate/programs/php/bin/php /home/pirate/rpcfw/lib/ScriptRunner.php -f'
alias zkmgr='/home/pirate/programs/php/bin/php /home/pirate/rpcfw/lib/ScriptRunner.php -f /home/pirate/rpcfw/script/ZKManager.php'
