<?php
	require_once 'function/Utils.php';
	require_once 'function/GoToPage.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<!--===============================================================================================-->
		<link type="text/css" rel="stylesheet" href="css/util.css">
		<link type="text/css" rel="stylesheet" href="css/main_table_column.css">
		<link type="text/css" rel="stylesheet" href="css/main_admin_cp.css" />
		<link type="text/css" rel="stylesheet" href="css/style-nlct-new.css" />
		<link type="text/css" rel="stylesheet" href="css/main_admin_cp_add_item.css" />
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
							<div class="Banner_Left" style="top: 800px"></div>
							<div class="Banner_Right" style="top: 200px"></div>
							<div class="Banner_Right" style="top: 400px"></div>
							<div class="Banner_Right" style="top: 600px"></div>
							<div class="Banner_Right" style="top: 800px"></div>
							<div class="Banner_Left_Top"></div>
							<div class="Banner_Left_Bottom" style="top: 912px"></div>
							<div class="Banner_Right_Top"></div>
							<div class="Banner_Right_Bottom" style="top: 912px"></div>
							<div style="width: 1000px; height:1159px">
								<FORM id="formAdmin" name="formAdmin" action="" method="post">
									<?php
										require_once 'function/AccountAdmin.php';
									?>
									<div class="special">
										<?php
											require_once 'function/ShowNote.php';
											require_once 'function/ShowGoBackButton.php';
											require_once 'function/AccountLogin.php';
										?>
										<div class='login'>
											<div style='width:71%'>
												<div class='lineTop'>
												</div>
												<div class='loginBottom'>
													<div class='bgInput'>
														<input type="number" name="itemID" id="itemID" value="" valign="center" placeholder="ID Vật Phẩm">
													</div>
												</div>
											</div>
										</div>
										<br/>
										<div class="btn-group" style="width:50%">
											<button style="width:100%; background-color:blue" type="submit" class="brk-btn" name="addItem" value="addItem">
												<?php
													$addItemButton = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_ADD_ITEM');
													echo "{$addItemButton}";
												?>
											</button>
										</div>
										<br/>
										<div class="btn-group" style="width:95%">
											<button style="width:50%" type="submit" class="brk-btn" name="getAllItemsFruit" value="getAllItemsFruit">
												<?php
													$showAllItemsFruit = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_FRUIT');
													echo "{$showAllItemsFruit}";
												?>
											</button>
											<button style="width:50%" type="submit" class="brk-btn" name="getAllItemsDress" value="getAllItemsDress">
												<?php
													$showAllItemsDress = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_DRESS');
													echo "{$showAllItemsDress}";
												?>
											</button>
										</div>
										<div class="btn-group" style="width:95%">
											<button style="width:50%" type="submit" class="brk-btn" name="getAllItemsPetEgg" value="getAllItemsPetEgg">
												<?php
													$showAllItemsPetEgg = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_PET_EGG');
													echo "{$showAllItemsPetEgg}";
												?>
											</button>
											<button style="width:50%" type="submit" class="brk-btn" name="getAllItemsMounts" value="getAllItemsMounts">
												<?php
													$showAllItemsMounts = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS_MOUNTS');
													echo "{$showAllItemsMounts}";
												?>
											</button>
										</div>
										<div class="btn-group" style="width:95%">
											<button style="width:100%" type="submit" class="brk-btn" name="getAllItems" value="getAllItems">
												<?php
													$showAllItems = getLanguageByKey('ADD_ITEM_TO_ACCOUNT_BUTTON_SHOW_ALL_ITEMS');
													echo "{$showAllItems}";
												?>
											</button>
										</div>
										<?php
											require_once 'function/DatabaseManagerAddItemsToAccount.php';
										?>
									</div>
								</FORM>
							</div>
						</div>
					</div>
				</div>
				<div class="footer" style="padding: 0px; margin-top: -5px">
					<?php
						require_once 'function/ShowFooter.php';
					?>
				</div>
			</div>
		</div>
	</body>
</html>