<?php
if (! defined ( 'ROOT' ))
{
	define ( 'ROOT', dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) ) );
	define ( 'LIB_ROOT', ROOT . '/lib' );
	define ( 'EXLIB_ROOT', ROOT . '/exlib' );
	define ( 'DEF_ROOT', ROOT . '/def' );
	define ( 'CONF_ROOT', ROOT . '/conf' );
	define ( 'LOG_ROOT', ROOT . '/log' );
	define ( 'MOD_ROOT', ROOT . '/module' );
	define ( 'HOOK_ROOT', ROOT . '/hook' );
}


require_once (DEF_ROOT . '/Define.def.php');

if (file_exists ( DEF_ROOT . '/Classes.def.php' ))
{
	require_once (DEF_ROOT . '/Classes.def.php');

	function __autoload($className)
	{

		$className = strtolower ( $className );
		if (isset ( ClassDef::$ARR_CLASS [$className] ))
		{
			require_once (ROOT . '/' . ClassDef::$ARR_CLASS [$className]);
		}
		else
		{
			trigger_error ( "class $className not found", E_USER_ERROR );
		}
	}
}

$recieverUid = 51710;
$senderUid = 10001;
$subject = "1";
$content = "1";
$itemTemplates = array(
	10001=>5
);

/*MailLogic::sendSysItemMailByTemplate($recieverUid, 0,$subject, $content, $itemTemplates);
MailLogic::sendSysItemMailByTemplate($recieverUid, 0,$subject, $content, $itemTemplates);
MailLogic::sendSysItemMailByTemplate($recieverUid, 0,$subject, $content, $itemTemplates);
MailLogic::sendSysItemMailByTemplate($recieverUid, 0,$subject, $content, $itemTemplates);
MailLogic::sendSysItemMailByTemplate($recieverUid, 0,$subject, $content, $itemTemplates);
 */

MailLogic::sendPlayerMail($senderUid, $recieverUid, $subject, $content);
MailLogic::sendPlayerMail($senderUid, $recieverUid, $subject, $content);
MailLogic::sendPlayerMail($senderUid, $recieverUid, $subject, $content);
MailLogic::sendPlayerMail($senderUid, $recieverUid, $subject, $content);
MailLogic::sendPlayerMail($senderUid, $recieverUid, $subject, $content);

