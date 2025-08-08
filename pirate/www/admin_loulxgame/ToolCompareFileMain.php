<?php
	require_once 'function/Utils.php';
	require_once 'function/GoToPage.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<!--===============================================================================================-->
		<link type="text/css" rel="stylesheet" href="css/main_admin_cp.css" />
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
							<div class="Banner_Left" style="top: 700px"></div>
							<div class="Banner_Right" style="top: 200px"></div>
							<div class="Banner_Right" style="top: 400px"></div>
							<div class="Banner_Right" style="top: 600px"></div>
							<div class="Banner_Right" style="top: 700px"></div>
							<div class="Banner_Left_Top"></div>
							<div class="Banner_Left_Bottom" style="top: 853px"></div>
							<div class="Banner_Right_Top"></div>
							<div class="Banner_Right_Bottom" style="top: 853px"></div>
							<div style="width: 1000px; height:1100px">
								<FORM id="formAdmin" name="formAdmin" action="" method="post">
									<?php
										require_once 'function/AccountAdmin.php';
									?>
									<div class="special">
										<?php
											require_once 'function/ShowGoBackButton.php';
											echo "<div class='toolCampare'>";
												echo "<div style='width:71%'>";
													echo "<div class='title'>";
														$noteLanguage = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_NOTE');
														echo "<p>{$noteLanguage}</p>";
													echo "</div>";
													echo "<div class='noteToolCompareBottom'>";
														echo "<div style='height:3px'></div>";
														$noteLanguage1 = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_NOTE_1');
														echo "{$noteLanguage1}";
														echo "<br/>";
														$noteLanguage2 = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_NOTE_2');
														echo "{$noteLanguage2}";
														echo "<br/>";
														$noteLanguage3 = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_NOTE_3');
														echo "{$noteLanguage3}";
														echo "<br/>";
														$noteLanguage4 = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_NOTE_4');
														echo "{$noteLanguage4}";
														echo "<br/>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
										?>
										<br/>
										<div class="btn-group" style="width:50%">
											<button style="width:100%" type="submit" class="brk-btn" name="runToolMain" value="runToolMain">
												<?php
													$runToolMain = getLanguageByKey('TOOL_COMPARE_FILE_MAIN_BTN_RUN');
													echo "{$runToolMain}";
												?>
											</button>
										</div>
										<br/>
										<div class="frameInfo">
											<div class="frameBannerTop" style="left: 45px"></div>
											<div class="frameBannerTop" style="left: 150px"></div>
											<div class="frameBannerTop" style="left: 300px"></div>
											<div class="frameBannerTop" style="left: 500px"></div>
											<div class="frameBannerTop" style="left: 650px"></div>
											<div class="frameBannerTop" style="left: 800px"></div>
											<div class="frameBannerTopCenter" style="left: 400px"></div>
											<div class="frameBannerBottom" style="bottom: -20px; left: 45px"></div>
											<div class="frameBannerBottom" style="bottom: -20px; left: 200px"></div>
											<div class="frameBannerBottom" style="bottom: -20px; left: 400px"></div>
											<div class="frameBannerBottom" style="bottom: -20px; left: 600px"></div>
											<div class="frameBannerBottom" style="bottom: -20px; left: 780px"></div>
											<div class="frameBannerRight" style="right: -30px; top: 100px"></div>
											<div class="frameBannerLeft" style="left: -25px; top: 100px"></div>
											<div class="frameBannerRightBottom" style="right: -30px; top: 292px"></div>
											<div class="frameBannerLeftBottom" style="left: -25px; top: 292px"></div>
											<div class="frameBannerLeftTop" style="left: -25px"></div>
											<div class="frameBannerRightTop" style="right: -30px;"></div>
											<div style="width: 1000px; height:520px">
												<div style='height:8px'></div>
												<div class='scroll' style='width:90%'>
													<?php
														require_once 'function/ToolCompareFileMainData.php';
													?>
												</div>
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
	</body>
</html>