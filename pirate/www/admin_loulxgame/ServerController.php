<?php
	require_once 'function/Utils.php';
	require_once 'function/GoToPage.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<!--===============================================================================================-->
		<link type="text/css" rel="stylesheet" href="css/main_admin_cp.css" />
		<link type="text/css" rel="stylesheet" href="css/main_admin_cp_send_item_with_mail.css" />
		<link type="text/css" rel="stylesheet" href="css/style-nlct-new.css" />
	<!--===============================================================================================-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<?php
			$titlePage = getLanguageByKey('WEB_TITLE');
			echo "<title>{$titlePage}</title>";
		?>
		<link rel="shortcut icon" href="images/icons/admincp-ico.ico" />
		<meta http-equiv="content-type" content="text/html" />
	</head>

	<body>
		<div class="Wrapper">
			<div class="headerNav t-gray-scale">
				<div class="containerMain">
					<div class="NavTop">
						<?php
							$homePageLinkConfig = $GLOBALS['config']['homePageLink'];
							$titlePage = getLanguageByKey($homePageLinkConfig['title']);
							echo "<a href='{$homePageLinkConfig['url']}' title='{$titlePage}' class='LogoCT'>";
								echo "<img style='width:295px; height:350px' src='images/logo.png' alt='{$titlePage}'>";
							echo "</a>";
						?>
					</div>
				</div>
			</div>

			<div class="containerMain">
				<div class="MainContent">
					<div class="containerMain">
						<div id="banner">
							<div class="Banner_Top"></div>
							<div class="Banner_Top" style="left: 150px"></div>
							<div class="Banner_Top" style="left: 300px"></div>
							<div class="Banner_Top" style="left: 500px"></div>
							<div class="Banner_Top" style="left: 650px"></div>
							<div class="Banner_Top" style="left: 800px"></div>
							<div class="Banner_Top" style="left: 900px"></div>
							<div class="Banner_Top_Center"></div>
							<div class="Banner_Bottom" style="bottom: 0px;"></div>
							<div class="Banner_Bottom" style="bottom: 0px; left: 200px"></div>
							<div class="Banner_Bottom" style="bottom: 0px; left: 400px"></div>
							<div class="Banner_Bottom" style="bottom: 0px; left: 600px"></div>
							<div class="Banner_Bottom" style="bottom: 0px; left: 800px"></div>
							<div class="Banner_Bottom" style="bottom: 0px; left: 850px"></div>
							<div class="Banner_Left" style="top: 200px"></div>
							<div class="Banner_Left" style="top: 400px"></div>
							<div class="Banner_Left" style="top: 500px"></div>
							<div class="Banner_Right" style="top: 200px"></div>
							<div class="Banner_Right" style="top: 400px"></div>
							<div class="Banner_Right" style="top: 500px"></div>
							<div class="Banner_Left_Top"></div>
							<div class="Banner_Left_Bottom" style="top: 603px"></div>
							<div class="Banner_Right_Top"></div>
							<div class="Banner_Right_Bottom" style="top: 603px"></div>
							<div style="width: 1000px; height:850px">
								<FORM id="formAdmin" name="formAdmin" action="" method="post">
									<div class="special">
										<br/>
										<?php
											require_once 'function/ShowGoBackButton.php';
											require_once 'function/ServerControllerInfo.php';
										?>
									</div>
								</FORM>
							</div>
						</div>
					</div>
				</div>
				<div class="footer">
					<?php
						require_once 'function/ShowFooter.php';
					?>
				</div>
			</div>
		</div>
	</body>
</html>