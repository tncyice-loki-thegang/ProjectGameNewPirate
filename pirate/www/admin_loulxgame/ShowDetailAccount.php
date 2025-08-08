<?php
	require_once 'function/Utils.php';
	require_once 'function/GoToPage.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" type="image/ico" href="images/icons/admincp-ico.ico"/>
<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
		<link rel="stylesheet" type="text/css" href="css/util.css">
		<link rel="stylesheet" type="text/css" href="css/main_table_detail.css">
		<link type="text/css" rel="stylesheet" href="css/main_admin_cp.css" />
		<link type="text/css" rel="stylesheet" href="css/main_admin_cp_show_detail_account.css" />
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
							<div class="Banner_Left" style="top: 600px"></div>
							<div class="Banner_Right" style="top: 200px"></div>
							<div class="Banner_Right" style="top: 400px"></div>
							<div class="Banner_Right" style="top: 600px"></div>
							<div class="Banner_Left_Top"></div>
							<div class="Banner_Left_Bottom" style="top: 653px"></div>
							<div class="Banner_Right_Top"></div>
							<div class="Banner_Right_Bottom" style="top: 653px"></div>
							<div style="width: 1000px; height:900px">
								<FORM id="formAdmin" name="formAdmin" action="" method="post">
									<div class="special">
										<?php
											echo "<br/>";
											require_once 'function/ShowGoBackButton.php';
											echo "<br/>";
											require_once 'function/AccountLogin.php';
											echo "<br/>";
										?>
										<div class="btn-group" style="width:50%">
											<button style="width:100%" type="submit" class="brk-btn" name="showDetail" value="showDetail">
												<?php
													$showDetailButton = getLanguageByKey('SHOW_DETAIL_ACCOUNT_SHOW_DETAIL_BUTTON');
													echo "{$showDetailButton}";
												?>
											</button>
										</div>
										<br/>
										<br/>
										
										<div class="frameInfo">
											<div class="frameBannerTop" style="left: 45px"></div>
											<div class="frameBannerTop" style="left: 150px"></div>
											<div class="frameBannerTop" style="left: 300px"></div>
											<div class="frameBannerTop" style="left: 500px"></div>
											<div class="frameBannerTop" style="left: 650px"></div>
											<div class="frameBannerTop" style="left: 800px"></div>
											<div class="frameBannerTopCenter" style="left: 400px"></div>
											<div class="frameBannerBottom" style="bottom: -30px; left: 45px"></div>
											<div class="frameBannerBottom" style="bottom: -30px; left: 200px"></div>
											<div class="frameBannerBottom" style="bottom: -30px; left: 400px"></div>
											<div class="frameBannerBottom" style="bottom: -30px; left: 600px"></div>
											<div class="frameBannerBottom" style="bottom: -30px; left: 750px"></div>
											<div class="frameBannerLeft" style="left: 11px; top: 200px"></div>
											<div class="frameBannerRight" style="right: 5px; top: 200px"></div>
											<div class="frameBannerLeftTop"style="left: 11px"></div>
											<div class="frameBannerLeftBottom" style="left: 11px; top: 383px"></div>
											<div class="frameBannerRightTop"style="right: 5px;"></div>
											<div class="frameBannerRightBottom" style="right: 5px; top: 383px"></div>
											<div style="width: 900px; height:600px">
												<div style="height:5px"></div>
												<?php
													require_once 'function/DatabaseManagerShowDetailAccount.php';
												?>
											</div>
										</div>
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
<!--===============================================================================================-->	
		<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
		<script src="vendor/bootstrap/js/popper.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
		<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
		<script src="js/main_table_detail.js"></script>
    </body>
</html>