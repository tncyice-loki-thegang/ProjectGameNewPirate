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
							<div class="Banner_Left" style="top: 500px"></div>
							<div class="Banner_Right" style="top: 200px"></div>
							<div class="Banner_Right" style="top: 400px"></div>
							<div class="Banner_Right" style="top: 500px"></div>
							<div class="Banner_Left_Top"></div>
							<div class="Banner_Left_Bottom" style="top: 683px"></div>
							<div class="Banner_Right_Top"></div>
							<div class="Banner_Right_Bottom" style="top: 683px"></div>
							<div style="width: 1000px; height:930px">
								<FORM id="formAdmin" name="formAdmin" action="" method="post">
									<?php
										require_once 'function/AccountAdmin.php';
										require_once 'function/DatabaseManagerQueryDatabase.php';
									?>
									<div class="special">
										<?php
											require_once 'function/ShowNote.php';
											require_once 'function/ShowGoBackButton.php';
										?>
										<div class='note'>
											<div style='width:71%'>
												<div class='lineTop'>
												</div>
												<div class='noteBottom'>
													<br/>
													<span style='color:blue'>
														<font size='4'>
															<?php
																$noteLanguageBig = getLanguageByKey('QUERY_DATEBASE_NOTE_BIG');
																echo "{$noteLanguageBig}";
															?>
														</font>
													</span>
												</div>
											</div>
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
											<div class="frameBannerRightBottom" style="right: -30px; top: 27px"></div>
											<div class="frameBannerLeftBottom" style="left: -25px; top: 27px"></div>
											<div class="frameBannerLeftTop" style="left: -25px"></div>
											<div class="frameBannerRightTop" style="right: -30px;"></div>
											<div style="width: 1000px; height:254px">
												<div style="height:5px"></div>
												<textarea type="text" name="query" id="query" value="" valign="center" placeholder="Query" rows="12" style="width:95%"></textarea>
											</div>
										</div>
										<br/>
										<div class="btn-group" style="width:50%">
											<button style="width:100%" type="submit" class="brk-btn" name="runSQL" value="runSQL">
												<?php
													$runQueryButton = getLanguageByKey('QUERY_DATEBASE_RUN_QUERY_BUTTON');
													echo "{$runQueryButton}";
												?>
											</button>
										</div>
										<div class='titleMain'>
											<div style='width:71%'>
												<div class='title'>
													<?php
														$listFunctionTitle = getLanguageByKey('QUERY_DATEBASE_LIST_FUNCTION_TITLE');
														echo "<p>{$listFunctionTitle}</p>";
													?>
												</div>
											</div>
										</div>
										<div class="btn-group" style="width:50%">
											<button style="width:100%" type="submit" class="brk-btn" name="clearAllAccount" value="clearAllAccount">
												<?php
													$clearAllAccountButton = getLanguageByKey('QUERY_DATEBASE_CLEAR_ALL_ACCOUNT');
													echo "{$clearAllAccountButton}";
												?>
											</button>
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