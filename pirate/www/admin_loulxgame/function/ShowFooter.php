<?php
	showFooter();

	function showFooter() {
		$footerPageConfig = $GLOBALS['config']['footerPage'];
		$footerTitle = getLanguageByKey($footerPageConfig['title']);
		$footerPullLeft = getLanguageByKey($footerPageConfig['pullLeft']);
		echo "<a href='{$footerPageConfig['url']}' target='_blank' title='{$footerTitle}' class='logocmn'>{$footerTitle}</a>";
		echo "<p class='pull-left'>® <strong>{$footerPullLeft}</strong></p>";
	}
?>