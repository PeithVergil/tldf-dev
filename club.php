<?php
/**
* Clubs
*
* @package DatingPro
* @subpackage User Mode
**/

include './include/config.php';
include './common.php';
include './include/config_index.php';
include './include/functions_auth.php';
include './include/functions_index.php';
include './include/class.lang.php';
include './include/class.percent.php';
include './include/class.images.php';

// authentication
$user = auth_index_user();

if (empty($user) || $user == 'err' || empty($user[ AUTH_ID_USER ])) {
	header('location: '.$config['site_root'].'/index.php');
	exit;
}

// check guest
// (handled by permissions)

// check group, period, expiration
RefreshAccount();

// check status
if (!$user[ AUTH_STATUS ]) {
	AlertPage(GetRightModulePath(__FILE__));
}

// check permissions
IsFileAllowed(GetRightModulePath(__FILE__));

// alerts and statistics
if (!$user[ AUTH_GUEST ]) {
	GetAlertsMessage();
	SetModuleStatistic(GetRightModulePath(__FILE__));
}

// active menu item
$smarty->assign('sub_menu_num', '');

unset($_SESSION['return_to_view']);

// user selection
$sel = isset($_POST['sel']) ? $_POST['sel'] : (isset($_GET['sel']) ? $_GET['sel'] : '');

// dispatcher
switch ($sel) {
	case 'create':			CreateForm();					break;
	case 'add':				AddClub();						break;
	case 'list':			ClubList();						break;
	case 'my_club':			ClubList('', 'my_club');		break;
	case 'club':			ClubTable();					break;
	case 'join':			ClubCategories();				break;
	case 'join_club':		JoinClub();						break;
	case 'leave_club':		LeaveClub();					break;
	case 'invite':			InviteUser();					break;
	case 'upload_image':	UploadImage();					break;
	case 'upload_view':		UploadView();					break;
	case 'upload_news':		UploadNews();					break;
	case 'upload_del':		UploadDelete();					break;
	case 'more_users':		ListMore('users');				break;
	case 'more_photos':		ListMore('photos');				break;
	case 'more_news':		ListMore('news');				break;
	case 'search':			ClubList('', 'search');			break;
	case 'delete_club':		DeleteClub();					break;
	case 'delete_new':		DeleteClubNews();				break;
	case 'save_edited_news': SaveNews();					break;
	case 'save_icon':		SaveClubIcon();					break;
	case 'edit_club':		EditForm();						break;
	case 'save_club':		SaveClub();						break;
	default: 				ClubCategories();				break;
}

function ClubCategories($err='')
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "club.php";

	$strSQL = "SELECT DISTINCT id, name FROM ".CLUB_CATEGORIES_TABLE." GROUP BY id ORDER BY name";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		$i = 0;
		$cat_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$cat_arr[$i]["id"] = $row["id"];
			$cat_arr[$i]["name"] = stripslashes($row["name"]);

			$strSQL = "SELECT COUNT(*) FROM ".CLUB_TABLE." WHERE id_category='".$cat_arr[$i]["id"]."' AND is_hidden='0' " ;
			$rs_c = $dbconn->Execute($strSQL);
			$cat_arr[$i]["clubs_num"] = $rs_c->fields[0];
			if ($cat_arr[$i]["clubs_num"]>0){
				$cat_arr[$i]["link"] = $file_name."?sel=list&amp;id_cat=".$cat_arr[$i]["id"];
			}
			$rs->MoveNext();
			$i++;
		}
		$num_cat = sizeof($cat_arr)%2;
		if ($num_cat==0){
			$smarty->assign("half_num_cat",intval(sizeof($cat_arr)/2));
		} else {
			$smarty->assign("half_num_cat",intval(sizeof($cat_arr)/2)+1);
		}
		$smarty->assign("club_categories", $cat_arr);
	}
	$form["club_page"] = 2;
	$form["guest_user"] = $user[ AUTH_GUEST ];

	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["club"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/club_categories_table.tpl");
	exit;
}

function ClubList($err='', $par='')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "club.php";

	$settings = GetSiteSettings(array('photos_default','club_uploads_folder','thumb_max_width'));

	$page = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}else{	$page=intval($page);}

	switch ($par) {
		case 'my_club':
			$strSQL = " SELECT DISTINCT id_club FROM ".CLUB_USERS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' GROUP BY id_club ";
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->RowCount();
			if ($num_records>0){
				$id_arr = array();
				while(!$rs->EOF){
					array_push($id_arr, $rs->fields[0]);
					$rs->MoveNext();
				}
				$id_str = implode(",", $id_arr);
			}
			$lim_min = ($page-1)*$config_index["clubs_numpage"];
			$lim_max = $config_index["clubs_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if ($num_records > 0) {
				$strSQL = " SELECT DISTINCT
								ct.id, ct.name as club_name, cct.name as category, cut.upload_path, ct.id_creator as leader_id,
								DATE_FORMAT(ct.creation_date,'".$config["date_format"]."') as creation_date, ct.is_open, ct.description
							FROM ".CLUB_TABLE." ct
			                LEFT JOIN ".CLUB_CATEGORIES_TABLE." cct on cct.id=ct.id_category
			                LEFT JOIN ".CLUB_UPLOADS_TABLE." cut ON (cut.id_club=ct.id AND cut.club_icon='1' AND cut.status='1' AND cut.upload_type='f')
			                WHERE ct.id IN (".$id_str.")
			                GROUP BY ct.id ORDER BY ct.id DESC ".$limit_str;
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0){
					$i = 0;
					$clubs = array();
					while(!$rs->EOF){
						$row = $rs->GetRowAssoc(false);
						$clubs[$i]["id"] = $row["id"];
						$clubs[$i]["club_name"] = stripslashes($row["club_name"]);
						$clubs[$i]["category"] = stripslashes($row["category"]);
						if (strlen(utf8_decode($row["description"]))>200){
							$clubs[$i]["description"] = utf8_substr(stripslashes($row["description"]), 0, 200)."...";
						} else {
							$clubs[$i]["description"] = stripslashes($row["description"]);
						}
						$clubs[$i]["creation_date"] = $row["creation_date"];
						$clubs[$i]["is_open"] = $row["is_open"];
						if ($row["leader_id"] == $user[ AUTH_ID_USER ]) {
							$clubs[$i]["user_is_leader"] = 1;
						}

						$icon_path = $row["upload_path"]?$row["upload_path"]:$settings["photos_default"];
						if($icon_path && file_exists($config["site_path"].$settings["club_uploads_folder"]."/thumb_".$icon_path)){
							$clubs[$i]["icon_path"] = $config["site_root"].$settings["club_uploads_folder"]."/thumb_".$icon_path;
						} else {
							$clubs[$i]["icon_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
						}
						$clubs[$i]["link"] = $file_name."?sel=club&id_club=".$clubs[$i]["id"];
						$clubs[$i]["leave_link"] = $file_name."?sel=leave_club&id_club=".$clubs[$i]["id"];

						$strSQL = "SELECT COUNT(*) FROM ".CLUB_USERS_TABLE." WHERE id_club='".$clubs[$i]["id"]."' AND status='1' " ;
						$rs_c = $dbconn->Execute($strSQL);
						$clubs[$i]["members_count"] = $rs_c->fields[0];

						$strSQL = "SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_club='".$clubs[$i]["id"]."' AND id_user='".$user[ AUTH_ID_USER ]."' " ;
						$rs_c = $dbconn->Execute($strSQL);
						if ($rs_c->fields[0]>0){
							$clubs[$i]["user_in_club"] = 1;
						} else {
							$clubs[$i]["user_in_club"] = 0;
						}
						$rs->MoveNext();
						$i++;
					}
					$param = $file_name."?sel=my_club&";
					$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["clubs_numpage"]));
					$smarty->assign("clubs", $clubs);
					$smarty->assign("par", "my");
				}
			} else {
				ClubCategories("you_not_have_clubs");
				exit;
			}
			$form["club_page"] = 1;
			break;
		case "search":
			$smarty->assign("par", "search");
			$keywords = htmlspecialchars(strip_tags(trim($_REQUEST["keywords"])));
			if ($keywords == ''){
				ClubCategories();
				exit;
			}
			$smarty->assign("keywords", $keywords);

			$strSQL = "	SELECT DISTINCT ct.id
						FROM ".CLUB_TABLE." ct
						LEFT JOIN ".CLUB_NEWS_TABLE." cnt ON cnt.id_club=ct.id
						LEFT JOIN ".CLUB_UPLOADS_TABLE." cut ON cut.id_club=ct.id
			 			WHERE
			 				(
			 				(ct.name LIKE '%".$keywords."%' OR ct.description LIKE '%".$keywords."%') OR
			 				(cut.comment LIKE '%".$keywords."%') OR
			 				(cnt.news_name LIKE '%".$keywords."%' OR cnt.news_text LIKE '%".$keywords."%')
			 				)
			 				AND (ct.is_hidden='0')
			 			GROUP BY ct.id ";
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->RowCount();
			// page
			$lim_min = ($page-1)*$config_index["clubs_numpage"];
			$lim_max = $config_index["clubs_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if ($num_records > 0) {
				$strSQL = " SELECT DISTINCT
								ct.id, ct.name as club_name, cct.name as category, cut.upload_path, ct.id_creator as leader_id,
								DATE_FORMAT(ct.creation_date,'".$config["date_format"]."') as creation_date, ct.is_open, ct.description
							FROM ".CLUB_TABLE." ct
							LEFT JOIN ".CLUB_NEWS_TABLE." cnt ON cnt.id_club=ct.id
							LEFT JOIN ".CLUB_UPLOADS_TABLE." cut2 ON cut2.id_club=ct.id
			                LEFT JOIN ".CLUB_CATEGORIES_TABLE." cct on cct.id=ct.id_category
			                LEFT JOIN ".CLUB_UPLOADS_TABLE." cut ON (cut.id_club=ct.id AND cut.club_icon='1' AND cut.status='1' AND cut.upload_type='f')
			                WHERE
			 				(
			 				(ct.name LIKE '%".$keywords."%' OR ct.description LIKE '%".$keywords."%') OR
			 				(cut2.comment LIKE '%".$keywords."%') OR
			 				(cnt.news_name LIKE '%".$keywords."%' OR cnt.news_text LIKE '%".$keywords."%')
			 				)
			 				AND (ct.is_hidden='0')
			                GROUP BY ct.id ORDER BY ct.id  DESC	".$limit_str;
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0){
					$i = 0;
					$clubs = array();
					while(!$rs->EOF){
						$row = $rs->GetRowAssoc(false);
						$clubs[$i]["id"] = $row["id"];
						$clubs[$i]["club_name"] = stripslashes($row["club_name"]);
						$clubs[$i]["category"] = stripslashes($row["category"]);
						if (strlen(utf8_decode($row["description"]))>200){
							$clubs[$i]["description"] = utf8_substr(stripslashes($row["description"]), 0, 200)."...";
						} else {
							$clubs[$i]["description"] = stripslashes($row["description"]);
						}
						$clubs[$i]["creation_date"] = $row["creation_date"];
						$clubs[$i]["is_open"] = $row["is_open"];

						if ($row["leader_id"] == $user[ AUTH_ID_USER ]) {
							$clubs[$i]["user_is_leader"] = 1;
						}
						
						$icon_path = $row["upload_path"]?$row["upload_path"]:$settings["photos_default"];
						if($icon_path && file_exists($config["site_path"].$settings["club_uploads_folder"]."/thumb_".$icon_path)){
							$clubs[$i]["icon_path"] = $config["site_root"].$settings["club_uploads_folder"]."/thumb_".$icon_path;
						} else {
							$clubs[$i]["icon_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
						}
						
						$clubs[$i]["link"] = $file_name."?sel=club&id_club=".$clubs[$i]["id"];
						$clubs[$i]["join_link"] = $file_name."?sel=join_club&id_club=".$clubs[$i]["id"];
						$clubs[$i]["leave_link"] = $file_name."?sel=leave_club&id_club=".$clubs[$i]["id"];

						$strSQL = "SELECT COUNT(*) FROM ".CLUB_USERS_TABLE." WHERE id_club='".$clubs[$i]["id"]."' AND status='1' " ;
						$rs_c = $dbconn->Execute($strSQL);
						$clubs[$i]["members_count"] = $rs_c->fields[0];

						$strSQL = "SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_club='".$clubs[$i]["id"]."' AND id_user='".$user[ AUTH_ID_USER ]."'";
						$rs_c = $dbconn->Execute($strSQL);
						if ($rs_c->fields[0]>0){
							$clubs[$i]["user_in_club"] = 1;
						} else {
							$clubs[$i]["user_in_club"] = 0;
						}
						$rs->MoveNext();
						$i++;
					}
					$category = intval($_REQUEST["id_cat"]);
					$param = $file_name."?sel=list&id_cat=".$category."&";
					$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["clubs_numpage"]));
					$smarty->assign("clubs", $clubs);
				}
			} else {
				ClubCategories("no_clubs_match_your_search");
				exit;
			}
			$form["club_page"] = 2;
			break;
		default:
			$category = intval($_REQUEST["id_cat"]);
			if ($category<1){
				ClubCategories("category_not_selected");
				exit;
			}
			// number of entries
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_TABLE." WHERE is_open='1' AND id_category='".$category."' ";
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->fields[0];
			// page
			$lim_min = ($page-1)*$config_index["clubs_numpage"];
			$lim_max = $config_index["clubs_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;

			if($num_records>0){
				$strSQL = " SELECT DISTINCT
								ct.id, ct.name as club_name, cct.name as category, cut.upload_path, ct.id_creator as leader_id,
								DATE_FORMAT(ct.creation_date,'".$config["date_format"]."') as creation_date, ct.is_open, ct.description
							FROM ".CLUB_TABLE." ct
			                LEFT JOIN ".CLUB_CATEGORIES_TABLE." cct on cct.id=ct.id_category
			                LEFT JOIN ".CLUB_UPLOADS_TABLE." cut ON (cut.id_club=ct.id AND cut.club_icon='1' AND cut.status='1' AND cut.upload_type='f')
			                WHERE ct.is_hidden='0' AND ct.id_category='".$category."'
			                GROUP BY ct.id ORDER BY ct.id  DESC	".$limit_str;
				$rs = $dbconn->Execute($strSQL);
				if ($rs->fields[0]>0){
					$i = 0;
					$clubs = array();
					while(!$rs->EOF){
						$row = $rs->GetRowAssoc(false);
						$clubs[$i]["id"] = $row["id"];
						$clubs[$i]["club_name"] = stripslashes($row["club_name"]);
						$clubs[$i]["category"] = stripslashes($row["category"]);
						if (strlen(utf8_decode($row["description"]))>200){
							$clubs[$i]["description"] = utf8_substr(stripslashes($row["description"]), 0, 200)."...";
						} else {
							$clubs[$i]["description"] = stripslashes($row["description"]);
						}
						$clubs[$i]["creation_date"] = $row["creation_date"];
						$clubs[$i]["is_open"] = $row["is_open"];

						if ($row["leader_id"] == $user[ AUTH_ID_USER ]) {
							$clubs[$i]["user_is_leader"] = 1;
						}
						$icon_path = $row["upload_path"]?$row["upload_path"]:$settings["photos_default"];
						if($icon_path && file_exists($config["site_path"].$settings["club_uploads_folder"]."/thumb_".$icon_path)){
							$clubs[$i]["icon_path"] = $config["site_root"].$settings["club_uploads_folder"]."/thumb_".$icon_path;
						} else {
							$clubs[$i]["icon_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
						}
						$clubs[$i]["link"] = $file_name."?sel=club&id_club=".$clubs[$i]["id"];
						$clubs[$i]["join_link"] = $file_name."?sel=join_club&id_club=".$clubs[$i]["id"];
						$clubs[$i]["leave_link"] = $file_name."?sel=leave_club&id_club=".$clubs[$i]["id"];

						$strSQL = "SELECT COUNT(*) FROM ".CLUB_USERS_TABLE." WHERE id_club='".$clubs[$i]["id"]."' AND status='1' " ;
						$rs_c = $dbconn->Execute($strSQL);
						$clubs[$i]["members_count"] = $rs_c->fields[0];

						$strSQL = "SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_club='".$clubs[$i]["id"]."' AND id_user='".$user[ AUTH_ID_USER ]."' " ;
						$rs_c = $dbconn->Execute($strSQL);
						if ($rs_c->fields[0]>0){
							$clubs[$i]["user_in_club"] = 1;
						} else {
							$clubs[$i]["user_in_club"] = 0;
						}
						$rs->MoveNext();
						$i++;
					}
					$param = $file_name."?sel=list&id_cat=".$category."&";
					$smarty->assign("links", GetLinkArray($num_records, $page, $param, $config_index["clubs_numpage"]));
					$smarty->assign("clubs", $clubs);
				}
			} else {
				ClubCategories("no_clubs_in_category");
				exit;
			}
			$form["club_page"] = 2;
			break;
	}
	$form["guest_user"] = $user[ AUTH_GUEST ];

	$form["icon_max_width"] = $settings["thumb_max_width"];

	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["club"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/club_list_table.tpl");
	exit;
}

function ClubTable($err='', $id_club='', $show_upload_form='', $show_news_form='', $data='', $agreement=0)
{
	global $lang, $config, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();

	if ($id_club !=''){
		$club_id = intval($id_club);
	} else {
		$club_id = intval($_REQUEST["id_club"]);
	}

	if ($club_id<1){
		ClubCategories("club_not_selected");
		exit;
	}
	$strSQL = " SELECT is_hidden FROM ".CLUB_TABLE." WHERE id='".$club_id."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]!=0){
		//hidden club
		$strSQL = " SELECT id FROM ".CLUB_USERS_TABLE." WHERE  id_user='".$user[ AUTH_ID_USER ]."' AND id_club='".$club_id."' AND status='1' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]<1){
			ClubCategories();
			exit;
		}
	}

	$file_name = (isset($_SERVER["PHP_SELF"]))?AfterLastSlash($_SERVER["PHP_SELF"]):"club.php";
	
	GetActiveUserInfo($user);

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','photos_default','club_uploads_folder','thumb_max_width'));

	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	if ($agreement == 0) {

		$strSQL = " SELECT ct.id, ct.name as club_name, cct.name as category, c.name as country, r.name as region, ci.name as city,
					DATE_FORMAT(ct.creation_date,'".$config["date_format"]."') as creation_date, ct.is_open, ct.description,
					ut.login as club_leader, ut.icon_path as leader_icon_path, ut.gender, ct.id_creator as leader_id, cut.upload_path, ct.can_invite,
					ut.date_birthday, ut.id_country, ut.id_city, ut.id_region,
					ct.can_post_images
				FROM ".CLUB_TABLE." ct
				LEFT JOIN ".COUNTRY_SPR_TABLE." c ON c.id=ct.id_country
				LEFT JOIN ".REGION_SPR_TABLE." r ON r.id=ct.id_region
				LEFT JOIN ".CITY_SPR_TABLE." ci ON ci.id=ct.id_city
	            LEFT JOIN ".CLUB_CATEGORIES_TABLE." cct ON cct.id=ct.id_category
	            LEFT JOIN ".USERS_TABLE." ut ON ut.id=ct.id_creator
	            LEFT JOIN ".CLUB_UPLOADS_TABLE." cut ON (cut.id_club=ct.id AND cut.club_icon='1' AND cut.status='1' AND cut.upload_type='f')
	            WHERE ct.id='".$club_id."'
	            ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0){
			$row = $rs->GetRowAssoc(false);
			$club["id"] = $row["id"];
			$club["club_name"] = stripslashes($row["club_name"]);
			$club["category"] = stripslashes($row["category"]);
			$club["country"] = stripslashes($row["country"]);
			$club["region"] = stripslashes($row["region"]);
			$club["city"] = stripslashes($row["city"]);
			$club["creation_date"] = $row["creation_date"];
			$club["is_open"] = $row["is_open"];
			$club["can_invite"] = $row["can_invite"];
			$club["can_post_images"] = $row["can_post_images"];
			$club["description"] = stripslashes($row["description"]);

			$club["club_leader"] = stripslashes($row["club_leader"]);
			$club["leader_id"] = $row["leader_id"];
			$leader_icon_path = $row["leader_icon_path"]?$row["leader_icon_path"]:$default_photos[$row["gender"]];
			if($leader_icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$leader_icon_path)){
				$club["leader_icon"] = $config["site_root"].$settings["icons_folder"]."/".$leader_icon_path;
			} else {
				$club["leader_icon"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
			}
			$club["club_leader_age"] = AgeFromBDate($row["date_birthday"]);
			$club["club_leader_id_country"] = intval($row["id_country"]);
			$club["club_leader_id_region"] = intval($row["id_region"]);
			$club["club_leader_id_city"] = intval($row["id_city"]);
			$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
			$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
			$_LANG_NEED_ID["city"][] = intval($row["id_city"]);
			$club["leader_profile_link"] = "./viewprofile.php?id=".$club["leader_id"];

			$icon_path = $row["upload_path"]?$row["upload_path"]:$settings["photos_default"];
			if($icon_path && file_exists($config["site_path"].$settings["club_uploads_folder"]."/thumb_".$icon_path)){
				$club["icon_path"] = $config["site_root"].$settings["club_uploads_folder"]."/thumb_".$icon_path;
			} else {
				$club["icon_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
			}
			$club["leave_link"] = $file_name."?sel=leave_club&id_club=".$club["id"];
			$club["join_link"] = $file_name."?sel=join_club&id_club=".$club["id"];
			$club["invite_link"] = "hotlist.php?sel=invite&id_club=".$club["id"];
			$club["upload_link"] = "club.php?sel=upload_image&id_club=".$club["id"];
			$club["upload_news_link"] = "club.php?sel=upload_news&id_club=".$club["id"];

			if ($club["leader_id"] == $user[ AUTH_ID_USER ]){
				$club["user_is_leader"] = 1;
				$club["user_in_club"] = 1;
			} else {
				$club["user_is_leader"] = 0;
				$strSQL = "SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_club='".$club["id"]."' AND id_user='".$user[ AUTH_ID_USER ]."' " ;
				$rs_c = $dbconn->Execute($strSQL);
				if ($rs_c->fields[0]>0){
					$club["user_in_club"] = 1;
				} else {
					$club["user_in_club"] = 0;
				}
			}
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_USERS_TABLE." WHERE id_club='".$club["id"]."' AND status='1' " ;
			$rs_c = $dbconn->Execute($strSQL);
			$club["members_count"] = $rs_c->fields[0];

			$strSQL = "	SELECT DISTINCT cut.id_user, ut.login, ut.icon_path, ut.gender,
									ut.date_birthday, ut.id_country, ut.id_city, ut.id_region
					FROM ".CLUB_USERS_TABLE." cut
					LEFT JOIN ".USERS_TABLE." ut ON ut.id=cut.id_user
					WHERE cut.status='1' AND cut.id_club='".$club_id."' AND cut.id_user!='".$club["leader_id"]."' AND cut.id_user!='".$user[ AUTH_ID_USER ]."'
					GROUP BY cut.id_user
					LIMIT 0,5 ";
			$rs_users = $dbconn->Execute($strSQL);
			$club_users = array();
			$_LANG_NEED_ID = array();
			if ($rs_users->fields[0] > 0) {
				$i = 0;
				while (!$rs_users->EOF) {
					$row_users = $rs_users->GetRowAssoc(false);
					$club_users[$i]["id_user"] = $row_users["id_user"];
					$club_users[$i]["login"] = stripslashes($row_users["login"]);
					$club_users[$i]["age"] = AgeFromBDate($row_users["date_birthday"]);
					$club_users[$i]["id_country"] = intval($row_users["id_country"]);
					$club_users[$i]["id_region"] = intval($row_users["id_region"]);
					$club_users[$i]["id_city"] = intval($row_users["id_city"]);
					$_LANG_NEED_ID["country"][] = intval($row_users["id_country"]);
					$_LANG_NEED_ID["region"][] = intval($row_users["id_region"]);
					$_LANG_NEED_ID["city"][] = intval($row_users["id_city"]);

					$icon_path = $row_users["icon_path"]?$row_users["icon_path"]:$default_photos[$row_users["gender"]];
					if($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path)){
						$club_users[$i]["icon"] = $config["server"].$config["site_root"].$settings["icons_folder"]."/".$icon_path;
					} else {
						$club_users[$i]["icon"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
					}
					$club_users[$i]["profile_link"] = "./viewprofile.php?id=".$club_users[$i]["id_user"];
					$rs_users->MoveNext();
					$i++;
				}
				$smarty->assign("club_users_num", sizeof($club_users));
				$smarty->assign("club_users", $club_users);
			}
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_USERS_TABLE." WHERE status='1' AND id_club='".$club_id."' AND id_user!='".$club["leader_id"]."' AND id_user!='".$user[ AUTH_ID_USER ]."' " ;
			$rs_c = $dbconn->Execute($strSQL);
			$club["members_count_2"] = $rs_c->fields[0];
			if ($club["members_count_2"]>sizeof($club_users)){
				$club["link_more_users"] = $file_name."?sel=more_users&id_club=".$club["id"];
			}
			//uploads
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_UPLOADS_TABLE." WHERE id_club='".$club["id"]."' AND status='1' AND club_icon!='1' " ;
			$rs_p = $dbconn->Execute($strSQL);
			$club["photos_count"] = $rs_p->fields[0];

			$strSQL = "	SELECT DISTINCT cut.id, ut.login, cut.upload_path, cut.comment, cut.id_user
					FROM ".CLUB_UPLOADS_TABLE." cut
					LEFT JOIN ".USERS_TABLE." ut ON ut.id=cut.id_user
					WHERE cut.status='1' AND cut.id_club='".$club_id."' AND cut.club_icon!='1'
					GROUP BY cut.id ORDER BY cut.id DESC
					LIMIT 0,5 ";
			$rs_photos = $dbconn->Execute($strSQL);
			$club_photos = array();
			if ($rs_photos->fields[0]>0){
				$i = 0;
				while(!$rs_photos->EOF){
					$row_photos = $rs_photos->GetRowAssoc(false);
					$club_photos[$i]["id"] = $row_photos["id"];
					$club_photos[$i]["id_user"] = $row_photos["id_user"];
					$club_photos[$i]["login"] = stripslashes($row_photos["login"]);
					$club_photos[$i]["comment"] = stripslashes($row_photos["comment"]);

					$icon_path = $row_photos["upload_path"];
					if($icon_path && file_exists($config["site_path"].$settings["club_uploads_folder"]."/".$icon_path)){
						$club_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$icon_path;
						$club_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/thumb_".$icon_path;
						$club_photos[$i]["view_link"] = $file_name."?sel=upload_view&id_file=".$club_photos[$i]["id"];
						$club_photos[$i]["del_link"] = $file_name."?sel=upload_del&id_file=".$club_photos[$i]["id"];
					} else {
						$club_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
						$club_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
					}
					$rs_photos->MoveNext();
					$i++;
				}
				$smarty->assign("club_photos_num", sizeof($club_photos));
				$smarty->assign("club_photos", $club_photos);
			}
			if ($club["photos_count"]>sizeof($club_photos)){
				$club["link_more_photos"] = $file_name."?sel=more_photos&id_club=".$club["id"];
			}
			//news
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_NEWS_TABLE." WHERE id_club='".$club["id"]."' " ;
			$rs_n = $dbconn->Execute($strSQL);
			$club["news_count"] = $rs_n->fields[0];
			$strSQL = "	SELECT DISTINCT cnt.id, cnt.news_name, cnt.news_text, DATE_FORMAT(cnt.creation_date,'".$config["date_format"]."') as creation_date
					FROM ".CLUB_NEWS_TABLE." cnt
					WHERE cnt.id_club='".$club_id."'
					GROUP BY cnt.id ORDER BY cnt.creation_date DESC
					LIMIT 0,5 ";
			$rs_news = $dbconn->Execute($strSQL);
			$club_news = array();
			if ($rs_news->fields[0]>0){
				$i = 0;
				while(!$rs_news->EOF){
					$row_news = $rs_news->GetRowAssoc(false);
					$club_news[$i]["id"] = $row_news["id"];
					$club_news[$i]["news_name"] = stripslashes($row_news["news_name"]);
					$club_news[$i]["news_text"] = stripslashes($row_news["news_text"]);
					$club_news[$i]["creation_date"] = $row_news["creation_date"];
					$rs_news->MoveNext();
					$i++;
				}
				$smarty->assign("club_news_num", sizeof($club_news));
				$smarty->assign("club_news", $club_news);
			}
			if ($club["news_count"]>sizeof($club_news)){
				$club["link_more_news"] = $file_name."?sel=more_news&id_club=".$club["id"];
			}
			$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
			$smarty->assign("club", $club);
		} else {
			ClubCategories();
			exit;
		}
		if ($err !=''){
			$form["err"] = $lang["err"][$err];
		}
		if ($club["user_in_club"] == 1){
			$form["club_page"] = 1;
		} else {
			$form["club_page"] = 2;
		}


		if ($show_news_form == '1'){
			$smarty->assign("show_news_form", 1);
		}
		if ($show_upload_form == '1'){
			$smarty->assign("show_upload_form", 1);
		}
	} else {
		$strSQL = " SELECT agreement_text FROM ".CLUB_TABLE." WHERE id='".$club_id."' ";
		$rs = $dbconn->Execute($strSQL);
		$data['agreement_text'] = stripslashes($rs->fields[0]);
		$data['agree_form'] = 1;
		$data['agree_link'] = "club.php?sel=join_club&from_agree=1&id_club=".$club_id;
	}

	if (isset($data) && is_array($data)){
		$smarty->assign("data", $data);
	}
	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];
	$form["icon_max_width"] = $settings["thumb_max_width"];

	$smarty->assign("form", $form);
	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["club"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/club_table.tpl");
	exit;
}

function CreateForm($err='', $data='')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);
	
	$form["club_page"] = 3;
	$strSQL = "SELECT DISTINCT id, name FROM ".COUNTRY_SPR_TABLE." GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$spr_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$spr_arr[$i]["id"] = $row["id"];
		$spr_arr[$i]["name"] = stripslashes($row["name"]);
		if ($data["country"]==$spr_arr[$i]["id"]){
			$spr_arr[$i]["sel"] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("countries", $spr_arr);

	if (isset($data["country"]) && $data["country"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".REGION_SPR_TABLE." WHERE id_country='".$data["country"]."' ORDER BY name";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if (isset($data["region"]) && $data["region"] == $spr_arr[$i]["id"])
			$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("regions", $spr_arr);
	}
	if (isset($data["region"]) && $data["region"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".CITY_SPR_TABLE." WHERE id_region='".$data["region"]."' ORDER BY id";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if (isset($data["city"]) && $data["city"] == $spr_arr[$i]["id"])
			$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("cities", $spr_arr);
	}
	$strSQL = "SELECT DISTINCT id, name FROM ".CLUB_CATEGORIES_TABLE." GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		$i = 0;
		$cat_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$cat_arr[$i]["id"] = $row["id"];
			if (isset($data["club_category"]) && $cat_arr[$i]["id"]==$data["club_category"]){
				$cat_arr[$i]["sel"] = 1;
			}
			$cat_arr[$i]["name"] = stripslashes($row["name"]);
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("club_categories", $cat_arr);
	}

	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["kcaptcha"] = $config["site_root"].$config_index["kcaptcha"];

	if (!isset($data['agreement_text']) || ($data['agreement_text']!='')) {
		$data['agreement_text'] = str_replace("[site_server]", $config['server'], $lang['club']['default_agreement_text']);
	}
	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->assign("data", $data);

	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["club"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/club_create_form.tpl");
	exit;
}

function AddClub()
{
	global $dbconn, $user;
	$data = $_POST;
	
	$file_name = 'club.php';

	if ( (strip_tags(trim($data["club_name"]))) == '' || intval($data["club_category"]) == 0 || (strip_tags(htmlspecialchars($data["description"]))) == '' ||
	(strip_tags(trim($data['agreement_text']))=='' && intval($data['use_agreement'] == 1) ) ) {
		$err = 'empty_fields';
		CreateForm($err, $data);
		return;
	}

	$strSQL = "SELECT name FROM ".CLUB_TABLE." WHERE name='".addslashes(strip_tags(trim($data["club_name"])))."'";
	if ($dbconn->GetOne($strSQL)){
		CreateForm("club_exist", $data);
		return;
	}

	if (BadWordsCont(strip_tags($data["club_name"]), 8)) {
		CreateForm("badword_finding_8_1", $data);
		return;
	}
	if (check_filter(strip_tags($data["club_name"]))) {
		CreateForm("info_finding_1", $data);
		return;
	}
	if (BadWordsCont(strip_tags(htmlspecialchars($data["description"])), 8)) {
		CreateForm("badword_finding_8_2", $data);
		return;
	}
	if (check_filter(strip_tags(htmlspecialchars($data["description"])))) {
		CreateForm("info_finding_1", $data);
		return;
	}
	if (intval($data['use_agreement']) == 1) {
		if (BadWordsCont(strip_tags(htmlspecialchars($data["agreement_text"])), 8)) {
			CreateForm("badword_finding_8_6", $data);
			return;
		}
		if (check_filter(strip_tags(htmlspecialchars($data["agreement_text"])))) {
			CreateForm("info_finding_1", $data);
			return;
		}
	}

	if(!(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['keystring'])) {
		$err = 'invalid_security_code';
		CreateForm($err, $data);
		unset($_SESSION['captcha_keystring']);
		return;
	}
	unset($_SESSION['captcha_keystring']);

	$strSQL = " INSERT INTO ".CLUB_TABLE."
					(name, id_creator, id_category, is_open, is_hidden, can_invite, can_post_images, id_country, id_region, id_city, description, creation_date, use_agreement, agreement_text)
				VALUES
					('".addslashes((strip_tags(trim($data["club_name"]))))."', '".$user[ AUTH_ID_USER ]."', '".intval($data["club_category"])."', '".intval($data["open_join"])."',
					'".intval($data["hidden_club"])."','".intval($data["members_can_invite"])."', '".intval($data["members_can_post_images"])."', '".intval($data["country"])."',
					'".intval($data["region"])."', '".intval($data["city"])."', '".addslashes(htmlspecialchars(strip_tags($data["description"])))."', now(),
					'".intval($data['use_agreement'])."', '".addslashes(htmlspecialchars(strip_tags($data["agreement_text"])))."')
				";
	$rs = $dbconn->Execute($strSQL);
	$rs = $dbconn->Execute("SELECT MAX(id) FROM ".CLUB_TABLE." WHERE id_creator='".$user[ AUTH_ID_USER ]."' ");
	$id_club = $rs->fields[0];
	$dbconn->Execute("INSERT INTO ".CLUB_USERS_TABLE." (id_user, id_club) VALUES ('".$user[ AUTH_ID_USER ]."','".$id_club."') ");

	$upload = $_FILES["upload"];
	$images_obj = new Images($dbconn);
	$images_obj->UploadImages($upload, $user[ AUTH_ID_USER ], 'club', '1', '', $id_club);

	$err = "club_was_created";
	//ClubCategories($err);
	header("location: ".$file_name."?sel=my_club");
	exit;
}

function JoinClub()
{
	global $dbconn, $user;

	if ($_REQUEST["par"] == 'from_invite'){
		$id_invite = intval($_REQUEST["id_invite"]);
		$strSQL = " SELECT id, id_inviter, id_user, id_club FROM ".CLUB_INVITES_TABLE." WHERE id='".$id_invite."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]>0){
			$id_club = $rs->fields[3];
			$id_user = $rs->fields[2];
			if ($id_user != $user[ AUTH_ID_USER ]) {
				ClubCategories();
				exit;
			}
		} else {
			ClubCategories();
			exit;
		}
	} else {
		$id_club = intval($_REQUEST["id_club"]);
		if ($id_club<1){
			ClubCategories();
			exit;
		}
	}

	$strSQL = " SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_club='".$id_club."' AND id_user='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		ClubTable('you_are_in_club', $id_club);
		exit;
	}
	if ($id_invite<1){
		$strSQL = " SELECT is_open FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0]=='0'){
			ClubTable('this_is_private_club', $id_club);
			exit;
		}
	}

	$strSQL = " SELECT use_agreement FROM ".CLUB_TABLE." WHERE id='".$id_club."'  ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]==1 && (intval($_REQUEST['from_agree'])==0)) {
		ClubTable('', $id_club, '0', '0', '', 1);
	} else {
		$rs = $dbconn->Execute(" INSERT INTO ".CLUB_USERS_TABLE." (id_user, id_club, status) VALUES ('".$user[ AUTH_ID_USER ]."', '".$id_club."', '1') ");
		SendJoinedMail($id_club);
		ClubTable('you_joined_to_club', $id_club);
	}
	exit;
}

function LeaveClub()
{
	global $dbconn, $user;
	$id_club = intval($_REQUEST["id_club"]);
	if ($id_club<1) {
		ClubCategories();
		exit;
	}
	$strSQL = " SELECT id_creator FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] == $user[ AUTH_ID_USER ]) {
		ClubTable('leader_cant_leave', $id_club);
	}
	$dbconn->Execute("DELETE FROM ".CLUB_USERS_TABLE." WHERE id_club='".$id_club."' AND id_user='".$user[ AUTH_ID_USER ]."' ");
	ClubCategories();
	exit;
}

function InviteUser()
{
	global $lang, $dbconn, $user;
	$id_club = intval($_REQUEST["id_club"]);
	$id_user = intval($_REQUEST["id_user"]);

	if ($id_club<1 || $id_user<1){
		ClubCategories();
		exit;
	}
	$strSQL = " SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_user='".$id_user."' AND id_club='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>1) {
		ClubTable('user_already_club_member', $id_club);
		exit;
	}
	$strSQL = " SELECT id_creator, can_invite FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not club leader
		if ($rs->fields[1] != '1') {//club is closed and only moderator can invite other users
			ClubTable('you_cant_invite', $id_club);
			exit;
		} else {
			$strSQL = " SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' AND id_club='".$id_club."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {//user isn't in club
				ClubTable('you_cant_invite', $id_club);
				exit;
			}
		}
	}

	$rs = $dbconn->Execute(" INSERT INTO ".CLUB_INVITES_TABLE." (id_inviter, id_user, id_club) VALUES ('".$user[ AUTH_ID_USER ]."', '".$id_user."', '".$id_club."') ");
	$invite_id = $dbconn->Insert_Id();//to mailbox and check it while joining the club

	$body = $lang["club"]["invite_mail_body"];

	$strSQL = " SELECT login FROM ".USERS_TABLE." WHERE id='".$id_user."' ";
	$rs = $dbconn->Execute($strSQL);
	$body = str_replace("[user_login]", stripslashes($rs->fields[0]), $body);
	$sub = str_replace("[user_login]", stripslashes($user[ AUTH_LOGIN ]), $lang["club"]["invite_mail_sub"]);

	$strSQL = " SELECT name, description FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	$body = str_replace("[club_name]", stripslashes($rs->fields[0]), $body);
	$body = str_replace("[club_description]", stripslashes($rs->fields[1]), $body);
	$body .= "<br><a href='club.php?sel=join_club&par=from_invite&id_invite=".$invite_id."'>".$lang["club"]["invite_mail_join_link"]."</a><br>".$sub;

	$strSQL = " INSERT INTO ".MAILBOX_TABLE." (id_from, id_to, subject, body, date_creation, was_read, deleted_from, deleted_to)
				VALUES ('".$user[ AUTH_ID_USER ]."', '".$id_user."', '".$lang["club"]["invite_mail_subject"]."', '".addslashes($body)."', now(),'0','0','0') ";
	$rs = $dbconn->Execute($strSQL);
	ClubTable('user_was_invited', $id_club);
	exit;
}

function UploadImage()
{
	global $dbconn, $user;
	$id_club = intval($_REQUEST["id_club"]);
	if ($id_club<1){
		ClubCategories();
		exit;
	}
	if (BadWordsCont($_REQUEST["comment_to_upload"], 8)) {
		ClubTable("badword_finding_8_3", $id_club,'1', '', $_REQUEST);
		return;
	}
	if (check_filter($_REQUEST["comment_to_upload"])) {
		ClubTable("info_finding_1", $id_club, '1','', $_REQUEST);
		return;
	}
	$strSQL = " SELECT id_creator, can_post_images FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not club leader
		if ($rs->fields[1] != '1') {//club is closed and only moderator can_post_images
			ClubTable('you_cant_post_images', $id_club);
			exit;
		} else {
			$strSQL = " SELECT id FROM ".CLUB_USERS_TABLE." WHERE id_user='".$user[ AUTH_ID_USER ]."' ";
			$rs = $dbconn->Execute($strSQL);
			if ($rs->fields[0]<1) {//user isn't in club
				ClubTable('you_cant_post_images', $id_club);
				exit;
			}
		}
	}
	$upload = $_FILES["upload"];
	$images_obj = new Images($dbconn);
	$err_upload = $images_obj->UploadImages($upload, $user[ AUTH_ID_USER ], 'club', '0', strip_tags(htmlspecialchars($_REQUEST["comment_to_upload"])), $id_club);
	if ($err_upload){
		$err = 'upload_err';
	} else {
		$err = 'club_photo_uploaded';
	}
	ClubTable($err, $id_club);
	exit;
}

function UploadView()
{
	global $smarty, $dbconn, $config, $lang;
	IndexHomePage();
	$id_file = intval($_REQUEST["id_file"]);
	$rs = $dbconn->Execute("	SELECT cut.upload_path, ut.login, cut.comment
								FROM ".CLUB_UPLOADS_TABLE." cut
								LEFT JOIN ".USERS_TABLE." ut ON cut.id_user = ut.id
								WHERE cut.id='".$id_file."'");
	$data["comment"] = stripslashes($rs->fields[2]);
	$data["login"] = stripslashes($rs->fields[1]);
	$data["file_name"] = $rs->fields[0];
	$folder = GetSiteSettings("club_uploads_folder");
	$data["file_path"] = $config["server"].$config["site_root"]."/".$folder."/".$data["file_name"];
	$smarty->assign("data", $data);
	$smarty->assign("button", $lang["button"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/club_view_table.tpl");
	exit;
}

function UploadNews()
{
	global $dbconn, $user;
	$data["news_name"] = $_REQUEST["news_name"];
	$data["news_text"] = $_REQUEST["news_text"];
	$id_club = intval($_REQUEST["id_club"]);
	if ($id_club<1){
		ClubCategories();
		exit;
	}
	if ((strip_tags(trim($data["news_name"])) == '') || (strip_tags($data["news_text"]) == '')){
		ClubTable("empty_fields", $id_club, '', '1', $data);
		return;
	}
	if (BadWordsCont($_REQUEST["news_name"], 8)) {
		ClubTable("badword_finding_8_4", $id_club, '', '1', $data);
		return;
	}
	if (check_filter($_REQUEST["news_name"])) {
		ClubTable("info_finding_1", $id_club, '', '1', $data);
		return;
	}
	if (BadWordsCont($_REQUEST["news_text"], 8)) {
		ClubTable("badword_finding_8_5", $id_club, '', '1', $data);
		return;
	}
	if (check_filter($_REQUEST["news_text"])) {
		ClubTable("info_finding_1", $id_club, '', '1', $data);
		return;
	}
	$strSQL = " SELECT id_creator FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not club leader
		ClubTable('you_cant_post_news', $id_club);
		return;
	}
	$strSQL = " INSERT INTO ".CLUB_NEWS_TABLE." (id_club, news_name, news_text, creation_date)
				VALUES ('".$id_club."','".addslashes(strip_tags(trim($data["news_name"])))."','".addslashes(strip_tags($data["news_text"]))."', now() ) ";
	$rs = $dbconn->Execute($strSQL);
	ClubTable('news_was_added', $id_club);
	exit;
}

function UploadDelete()
{
	global $config, $dbconn, $user;
	
	$id_file = intval($_REQUEST["id_file"]);
	if ($id_file<1){
		ClubCategories();
		exit;
	}
	$strSQL = " SELECT id_club, upload_path FROM ".CLUB_UPLOADS_TABLE." WHERE id='".$id_file."' ";
	$rs = $dbconn->Execute($strSQL);
	$id_club = $rs->fields[0];
	if ($id_club<1){
		ClubCategories();
		exit;
	}
	$upload_path = $rs->fields[1];

	$strSQL = " SELECT id_creator FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not club leader
		ClubTable('you_cant_del_uploads', $id_club);
		return;
	}
	$folder = GetSiteSettings("club_uploads_folder");
	unlink($config["site_path"].$folder."/".$upload_path);
	unlink($config["site_path"].$folder."/thumb_".$upload_path);
	$dbconn->Execute(" DELETE FROM ".CLUB_UPLOADS_TABLE." WHERE id='".$id_file."' ");
	ClubTable('club_upload_deleted', $id_club);
	exit;
}

function ListMore($par, $err = '')
{
	global $lang, $config, $config_index, $smarty, $dbconn, $user;

	$id_club = intval($_REQUEST["id_club"]);
	if ($id_club<1){
		ClubCategories();
		exit;
	}
	
	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$file_name = "club.php";

	$settings = GetSiteSettings(array('icon_male_default','icon_female_default','icons_folder','show_users_connection_str','show_users_comments','show_users_group_str','club_uploads_folder','thumb_max_width'));
	$default_photos['1'] = $settings['icon_male_default'];
	$default_photos['2'] = $settings['icon_female_default'];

	$percent = new Percent($user[ AUTH_ID_USER ]);
	
	$form["club_page"] = 2;
	$form["guest_user"] = $user[ AUTH_GUEST ];
	$form["show_users_connection_str"] = $settings["show_users_connection_str"];
	$form["show_users_comments"] = $settings["show_users_comments"];
	$form["show_users_group_str"] = $settings["show_users_group_str"];

	$page = $_GET["page"]?$_GET["page"]:$_POST["page"];
	if( (strval($page) == "") || (strval($page) == "0")){ $page = 1;}else{	$page=intval($page);}

	$strSQL = " SELECT name, id_creator FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	$smarty->assign('club_name', stripslashes($rs->fields[0]));
	$smarty->assign('id_club', $id_club);
	$smarty->assign('back_link', $file_name."?sel=club&id_club=".$id_club);
	$id_creator = $rs->fields[1];
	$smarty->assign('par', $par);
	switch ($par) {
		case "users":
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_USERS_TABLE." WHERE id_club='".$id_club."' AND status='1' AND id_user!='".$user[ AUTH_ID_USER ]."' " ;
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->fields[0];
			// page
			$lim_min = ($page-1)*$config_index["search_numpage"];
			$lim_max = $config_index["search_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if($num_records>0){
				$strSQL = "	SELECT DISTINCT cut.id_user, ut.login, ut.icon_path, ut.gender,
									ut.date_birthday, ut.id_country, ut.id_city, ut.id_region,
									SUBSTRING(ut.comment,1,165) as comment, DATE_FORMAT(ut.date_last_seen,'".$config["date_format"]."')  as date_last_login
								FROM ".CLUB_USERS_TABLE." cut
								LEFT JOIN ".USERS_TABLE." ut ON ut.id=cut.id_user
								WHERE cut.status='1' AND cut.id_club='".$id_club."' AND cut.id_user!='".$user[ AUTH_ID_USER ]."'
								GROUP BY cut.id_user ORDER BY cut.id
								".$limit_str;
				$rs_users = $dbconn->Execute($strSQL);
				$i = 0;
				$search = array();
				$_LANG_NEED_ID = array();
				while (!$rs_users->EOF) {
					$row = $rs_users->GetRowAssoc(false);
					$search[$i]["id"] = $row["id_user"];
					if ($id_creator == $row["id_user"]) {
						$search[$i]["club_leader"] = 1;
					}
					$search[$i]["name"] = $row["login"];
					$search[$i]["number"] = ($page-1)*$config_index["search_numpage"]+($i+1);
					$search[$i]["age"] = AgeFromBDate($row["date_birthday"]);
					$search[$i]["id_country"] = intval($row["id_country"]);
					$search[$i]["id_region"] = intval($row["id_region"]);
					$search[$i]["id_city"] = intval($row["id_city"]);

					$_LANG_NEED_ID["country"][] = intval($row["id_country"]);
					$_LANG_NEED_ID["region"][] = intval($row["id_region"]);
					$_LANG_NEED_ID["city"][] = intval($row["id_city"]);

					$icon_path = $row["icon_path"]?$row["icon_path"]:$default_photos[$row["gender"]];
					$icon_image= (strlen($row["icon_path"]))?1:0;
					if ($icon_path && file_exists($config["site_path"].$settings["icons_folder"]."/".$icon_path)) {
						$search[$i]["icon_path"] = $config["site_root"].$settings["icons_folder"]."/".$icon_path;
					}
					
					$strSQL = "SELECT COUNT(*) FROM ".USER_UPLOAD_TABLE."  WHERE  id_user='".$row["id_user"]."' AND upload_type='f' AND status='1' AND allow in ('1', '2')";
					$rs_sub = $dbconn->Execute($strSQL);
					$search[$i]["photo_count"] = intval($rs_sub->fields[0])+$icon_image;

					$search[$i]["profile_link"] = "./viewprofile.php?id=".$row["id_user"]."&search_type=club&page=".$page;

					$search[$i]["completion"] = $percent->GetAllPercentForUser($row["id_user"]);
					$search[$i]["annonce"] = stripslashes($row["comment"]);
					$search[$i]["last_login"] = $row["date_last_login"];

					/// get groups
					$sub_strSQL = "Select a.name from ".USER_GROUP_TABLE." b left join ".GROUPS_TABLE." a on a.id=b.id_group where b.id_user='".$row["id_user"]."'";
					$sub_rs = $dbconn->Execute($sub_strSQL);
					$groups_arr = array();
					while(!$sub_rs->EOF){
						$groups_arr[] = $sub_rs->fields[0];
						$sub_rs->MoveNext();
					}
					if (is_array($groups_arr) && count($groups_arr) > 0) {
						$search[$i]["group"] = implode(",", $groups_arr);
					}
					
					// get status
					$sub_rs = $dbconn->Execute('SELECT COUNT(*) FROM '.ACTIVE_SESSIONS_TABLE.' WHERE id_user = ?', array($row['id_user']));
					$search[$i]["status"] = intval($sub_rs->fields[0])?$lang["status"]["on"]:$lang["status"]["off"];

					/// get user search params
					$sub_strSQL = "SELECT gender as gender_search, age_max, age_min FROM ".USER_MATCH_TABLE." WHERE id_user='".$row["id_user"]."' ";
					$sub_rs = $dbconn->Execute($sub_strSQL);
					$sub_row = $sub_rs->GetRowAssoc(false);
					$search[$i]["age_max"] = $sub_row["age_max"];
					$search[$i]["age_min"] = $sub_row["age_min"];
					$search[$i]["gender_search"] = $lang["gender_search"][$sub_row["gender_search"]];

					//links
					$search[$i]["email_link"] = "./mailbox.php?sel=fs&id=".$row["id_user"]."&search_type=club";
					$search[$i]["im_link"] = $config["use_pilot_module_webmessenger"] ? "./webmessenger/wm.php?strDestinationUserID=".$row["id_user"] : "./w_communicator/flash_im.php?send_user=".$row["id_user"];
					$search[$i]["gift_link"] = "./giftshop.php?sel=users_add&id_user=".$row["id_user"];
					$rs_users->MoveNext();
					$i++;
				}
				$param = $file_name."?sel=more_users&id_club=".$id_club."&";
				$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["search_numpage"]));
				$smarty->assign("search_res", $search);
				if ($err != '') {
					$form["err"] = $lang["err"][$err];
				}
				$smarty->assign("base_lang", GetBaseLang($_LANG_NEED_ID));
				$smarty->assign("form", $form);
				$smarty->assign("header", $lang["homepage"]);
				$smarty->assign("header_s", $lang["club"]);
				$smarty->display(TrimSlash($config["index_theme_path"])."/club_users_table.tpl");
			} else {
				ClubTable('',$id_club);
			}
			break;
		case "photos":
			if ($id_creator == $user[ AUTH_ID_USER ]) {
				$smarty->assign("user_is_leader", 1);
			}
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_UPLOADS_TABLE." WHERE id_club='".$id_club."' AND status='1' AND club_icon!='1' " ;
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->fields[0];
			// page
			$lim_min = ($page-1)*$config_index["club_uploads_numpage"];
			$lim_max = $config_index["club_uploads_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if($num_records>0){
				$strSQL = "	SELECT DISTINCT cut.id, ut.login, cut.upload_path, cut.comment, cut.id_user
								FROM ".CLUB_UPLOADS_TABLE." cut
								LEFT JOIN ".USERS_TABLE." ut ON ut.id=cut.id_user
								WHERE cut.status='1' AND cut.id_club='".$id_club."' AND cut.club_icon!='1'
								GROUP BY cut.id ORDER BY cut.id DESC
								".$limit_str;
				$rs_photos = $dbconn->Execute($strSQL);
				$i = 0;
				$club_photos = array();
				while(!$rs_photos->EOF){
					$row_photos = $rs_photos->GetRowAssoc(false);
					$club_photos[$i]["id"] = $row_photos["id"];
					$club_photos[$i]["id_user"] = $row_photos["id_user"];
					$club_photos[$i]["login"] = stripslashes($row_photos["login"]);
					$club_photos[$i]["comment"] = stripslashes($row_photos["comment"]);
					$icon_path = $row_photos["upload_path"];
					if($icon_path && file_exists($config["site_path"].$settings["club_uploads_folder"]."/".$icon_path)){
						$club_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$icon_path;
						$club_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/thumb_".$icon_path;
						$club_photos[$i]["view_link"] = $file_name."?sel=upload_view&id_file=".$club_photos[$i]["id"];
						$club_photos[$i]["del_link"] = $file_name."?sel=upload_del&id_file=".$club_photos[$i]["id"];
					} else {
						$club_photos[$i]["upload_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
						$club_photos[$i]["upload_thumb_path"] = $config["server"].$config["site_root"].$settings["club_uploads_folder"]."/".$settings["photos_default"];
					}
					$rs_photos->MoveNext();
					$i++;
				}
				$param = $file_name."?sel=more_photos&id_club=".$id_club."&";
				$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["club_uploads_numpage"]));
				$smarty->assign("club_photos", $club_photos);
				if ($err !=''){
					$form["err"] = $lang["err"][$err];
				}
				$smarty->assign("form", $form);
				$smarty->assign("header", $lang["homepage"]);
				$smarty->assign("header_s", $lang["club"]);
				$smarty->display(TrimSlash($config["index_theme_path"])."/club_photos_news_table.tpl");
			} else {
				ClubTable('',$id_club);
			}
			break;
		case "news":
			//news
			if ($id_creator == $user[ AUTH_ID_USER ]) {
				$smarty->assign("user_is_leader", 1);
			}
			$strSQL = "SELECT COUNT(*) FROM ".CLUB_NEWS_TABLE." WHERE id_club='".$id_club."' " ;
			$rs = $dbconn->Execute($strSQL);
			$num_records = $rs->fields[0];
			// page
			$lim_min = ($page-1)*$config_index["club_news_numpage"];
			$lim_max = $config_index["club_news_numpage"];
			$limit_str = " LIMIT ".$lim_min.", ".$lim_max;
			if ($num_records>0) {
				$strSQL = "	SELECT DISTINCT cnt.id, cnt.news_name, cnt.news_text, DATE_FORMAT(cnt.creation_date,'".$config["date_format"]."') as creation_date
							FROM ".CLUB_NEWS_TABLE." cnt
							WHERE cnt.id_club='".$id_club."'
							GROUP BY cnt.id ORDER BY cnt.creation_date DESC
							".$limit_str;
				$rs_news = $dbconn->Execute($strSQL);
				$i = 0;
				$club_news = array();
				while(!$rs_news->EOF){
					$row_news = $rs_news->GetRowAssoc(false);
					$club_news[$i]["id"] = $row_news["id"];
					$club_news[$i]["news_name"] = stripslashes($row_news["news_name"]);
					$club_news[$i]["news_text"] = stripslashes($row_news["news_text"]);
					$club_news[$i]["creation_date"] = $row_news["creation_date"];
					$rs_news->MoveNext();
					$i++;
				}
				$param = $file_name."?sel=more_news&id_club=".$id_club."&";
				$smarty->assign("links", GetLinkArray($num_records,$page,$param,$config_index["club_news_numpage"]));
				$smarty->assign("club_news", $club_news);
				if ($err !=''){
					$form["err"] = $lang["err"][$err];
				}
				$smarty->assign("form", $form);
				$smarty->assign("header", $lang["homepage"]);
				$smarty->assign("header_s", $lang["club"]);
				$smarty->display(TrimSlash($config["index_theme_path"])."/club_photos_news_table.tpl");
			} else {
				ClubTable('',$id_club);
			}
			break;
		default: ClubCategories(); break;
	}
	exit;
}

function DeleteClub()
{
	global $config, $dbconn, $user;
	$id_club = intval($_GET['id_club']);
	$strSQL = " SELECT id FROM ".CLUB_TABLE." WHERE id='".$id_club."' AND id_creator='".$user[ AUTH_ID_USER ]."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$folder = GetSiteSettings('club_uploads_folder');
		$dbconn->Execute("DELETE FROM ".CLUB_TABLE." WHERE id='".$id_club."' ");
		$dbconn->Execute("DELETE FROM ".CLUB_USERS_TABLE." WHERE id_club='".$id_club."' ");
		$dbconn->Execute("DELETE FROM ".CLUB_INVITES_TABLE." WHERE id_club='".$id_club."' ");
		$dbconn->Execute("DELETE FROM ".CLUB_NEWS_TABLE." WHERE id_club='".$id_club."' ");
		$rs = $dbconn->Execute(" SELECT DISTINCT id, upload_path FROM ".CLUB_UPLOADS_TABLE." WHERE id_club='".$id_club."' GROUP BY id ");
		if ($rs->fields[0]>0) {
			while(!$rs->EOF){
				unlink($config['site_path'].$folder."/".$rs->fields[1]);
				unlink($config['site_path'].$folder."/thumb_".$rs->fields[1]);
				$rs->MoveNext();
			}
		}
		$dbconn->Execute("DELETE FROM ".CLUB_UPLOADS_TABLE." WHERE id_club='".$id_club."' ");
	}
	ClubCategories();
	exit;
}

function SendJoinedMail($id_club)
{
	global $lang, $dbconn, $user;
	$strSQL = " SELECT ct.name, ct.description, ut.login, ct.id_creator
				FROM ".CLUB_TABLE." ct
				LEFT JOIN ".USERS_TABLE." ut ON ut.id=ct.id_creator
				WHERE ct.id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	$club_name = stripslashes($rs->fields[0]);
	$creator_login = stripslashes($rs->fields[2]);
	$creator_id = $rs->fields[3];

	$body = $lang["club"]['welcome_to_club_mess_body'];
	$body = str_replace("[user_login]", "<b>".stripslashes($user[ AUTH_LOGIN ])."</b>", $body);
	$body = str_replace("[club_name]", stripslashes($club_name), $body);
	$sub = str_replace("[user_login]", stripslashes($creator_login), $lang["club"]["welcome_to_club_mess_sub"]);

	$body = $body."<br>".$sub;

	$strSQL = " INSERT INTO ".MAILBOX_TABLE." (id_from, id_to, subject, body, date_creation, was_read, deleted_from, deleted_to)
				VALUES ('".$creator_id."', '".$user[ AUTH_ID_USER ]."', '".$lang["club"]["welcome_to_club_mess_subject"]."', '".addslashes($body)."', now(),'0','0','0') ";
	$rs = $dbconn->Execute($strSQL);
	return ;
}

function DeleteClubNews()
{
	global $dbconn, $user;
	$id_news = (isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) ? intval($_REQUEST['id']) : null;
	if ($id_news == null) {
		ClubCategories();
		return;
	}
	$strSQL = "SELECT id, id_club FROM ".CLUB_NEWS_TABLE." WHERE id='".$id_news."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$id_club = $rs->fields[1];
		$strSQL = " SELECT id_creator FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not club leader
			ClubTable('you_cant_delete_news', $id_club);
			return;
		}
		$strSQL = " DELETE FROM ".CLUB_NEWS_TABLE." WHERE id='".$id_news."' ";
		$dbconn->Execute($strSQL);
		ClubTable('news_was_deleted', $id_club);
	} else {
		ClubCategories();
	}
	return;
}

function SaveNews()
{
	global $dbconn, $user;
	$id_news = (isset($_REQUEST['id_new']) && intval($_REQUEST['id_new'])>0) ? intval($_REQUEST['id_new']) : null;
	if ($id_news == null) {
		ClubCategories();
		return;
	}
	$strSQL = "SELECT id, id_club FROM ".CLUB_NEWS_TABLE." WHERE id='".$id_news."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0) {
		$id_club = $rs->fields[1];
		$strSQL = " SELECT id_creator FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
		$rs = $dbconn->Execute($strSQL);
		if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not club leader
			ClubTable('you_cant_post_news', $id_club);
			return;
		}
		$news_name = isset($_REQUEST["news_name"]) ? strip_tags($_REQUEST["news_name"]) : "";
		$news_text = isset($_REQUEST["news_text"]) ? strip_tags($_REQUEST["news_text"]) : "";
		if ($news_name == "" || $news_text== "") {
			ClubTable('empty_fields', $id_club);
			return;
		}
		$strSQL = " UPDATE ".CLUB_NEWS_TABLE." SET news_name='".addslashes($news_name)."', news_text='".addslashes($news_text)."' WHERE id='".$id_news."' ";
		$dbconn->Execute($strSQL);
		ClubTable('news_was_edited', $id_club);
	} else {
		ClubCategories();
	}
	return ;
}

function SaveClubIcon()
{
	global $config, $dbconn, $user;
	$id_club = (isset($_REQUEST['id_club']) && intval($_REQUEST['id_club'])>0) ? intval($_REQUEST['id_club']) : null;
	if ($id_club == null) {
		ClubCategories();
		return;
	}

	$strSQL = " SELECT id_creator FROM ".CLUB_TABLE." WHERE id='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] != $user[ AUTH_ID_USER ]) {//not club leader
		ClubTable('you_cant_post_images', $id_club);
		return;
	}
	$folder = GetSiteSettings('club_uploads_folder');
	$strSQL = " SELECT id, upload_path FROM ".CLUB_UPLOADS_TABLE." WHERE club_icon='1' AND id_club='".$id_club."' ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0] > 0) {
		unlink($config['site_path'].$folder."/".$rs->fields[1]);
		unlink($config['site_path'].$folder."/thumb_".$rs->fields[1]);
	}
	$upload = $_FILES["club_icon"];
	$images_obj = new Images($dbconn);
	$images_obj->UploadImages($upload, $user[ AUTH_ID_USER ], 'club', 'change', '', $id_club);
	ClubTable('', $id_club);
	return ;
}

function EditForm($err='', $data='',$id_club='')
{
	global $lang, $config, $smarty, $dbconn, $user;

	if (!$id_club){
		$id_club = intval($_GET["id_club"]);
	}

	$strSQL = "SELECT id FROM ".CLUB_TABLE." WHERE id='".$id_club."' and id_creator='".$user[ AUTH_ID_USER ]."' ";
	$can_edit = $dbconn->GetOne($strSQL);

	if (!$id_club || !$can_edit){
		ClubCategories("no access");
		exit;
	}

	Banners(GetRightModulePath(__FILE__));
	IndexHomePage();
	GetActiveUserInfo($user);

	$form["club_page"] = 3;

	$strSQL = "SELECT id, name, id_category, is_open, is_hidden, can_invite, can_post_images, id_country, id_region, id_city, description, use_agreement, agreement_text
							FROM ".CLUB_TABLE." WHERE id='".$id_club."'";
	$rs = $dbconn->Execute($strSQL);
	$data = $rs->GetRowAssoc(false);

	$strSQL = "SELECT DISTINCT id, name FROM ".COUNTRY_SPR_TABLE." GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	$i = 0;
	$spr_arr = array();
	while(!$rs->EOF){
		$row = $rs->GetRowAssoc(false);
		$spr_arr[$i]["id"] = $row["id"];
		$spr_arr[$i]["name"] = stripslashes($row["name"]);
		if ($data["id_country"]==$spr_arr[$i]["id"]){
			$spr_arr[$i]["sel"] = 1;
		}
		$rs->MoveNext();
		$i++;
	}
	$smarty->assign("countries", $spr_arr);
	if (isset($data["id_country"]) && $data["id_country"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".REGION_SPR_TABLE." WHERE id_country='".$data["id_country"]."' ORDER BY name";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if (intval($data["id_region"]) && $data["id_region"] == $spr_arr[$i]["id"])
			$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("regions", $spr_arr);
	}
	if (isset($data["id_region"]) && $data["id_region"]){
		$strSQL = "SELECT DISTINCT id, name FROM ".CITY_SPR_TABLE." WHERE id_region='".$data["id_region"]."' ORDER BY id";
		$rs = $dbconn->Execute($strSQL);
		$i = 0;
		$spr_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$spr_arr[$i]["id"] = $row["id"];
			$spr_arr[$i]["name"] = stripslashes($row["name"]);
			if (intval($data["id_city"]) && $data["id_city"] == $spr_arr[$i]["id"])
			$spr_arr[$i]["sel"] = 1;
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("cities", $spr_arr);
	}
	$strSQL = "SELECT DISTINCT id, name FROM ".CLUB_CATEGORIES_TABLE." GROUP BY id ORDER BY name ";
	$rs = $dbconn->Execute($strSQL);
	if ($rs->fields[0]>0){
		$i = 0;
		$cat_arr = array();
		while(!$rs->EOF){
			$row = $rs->GetRowAssoc(false);
			$cat_arr[$i]["id"] = $row["id"];
			if (isset($data["id_category"]) && $cat_arr[$i]["id"]==$data["id_category"]){
				$cat_arr[$i]["sel"] = 1;
			}
			$cat_arr[$i]["name"] = stripslashes($row["name"]);
			$rs->MoveNext();
			$i++;
		}
		$smarty->assign("club_categories", $cat_arr);
	}

	if (!isset($data['agreement_text']) || ($data['agreement_text']=='')) {
		$data['agreement_text'] = str_replace("[site_server]", $config['server'], $lang['club']['default_agreement_text']);
	}
	if ($err !=''){
		$form["err"] = $lang["err"][$err];
	}
	$smarty->assign("form", $form);
	$smarty->assign("data", $data);

	$smarty->assign("header", $lang["homepage"]);
	$smarty->assign("header_s", $lang["club"]);
	$smarty->display(TrimSlash($config["index_theme_path"])."/club_edit_form.tpl");
	exit;
}

function SaveClub()
{
	global $dbconn, $user;

	$data = $_POST;
	$id_club = intval($_POST["id_club"]);

	$strSQL = "SELECT id FROM ".CLUB_TABLE." WHERE id='".$id_club."' and id_creator='".$user[ AUTH_ID_USER ]."' ";
	$can_edit = $dbconn->GetOne($strSQL);
	if (!$id_club || !$can_edit){
		ClubCategories("no access");
	}
	if ( (strip_tags(trim($data["club_name"]))) == '' || intval($data["club_category"]) == 0 || (strip_tags(htmlspecialchars($data["description"]))) == '' ||
	(strip_tags(trim($data['agreement_text']))=='' && intval($data['use_agreement'] == 1) ) ) {
		$err = 'empty_fields';
		EditForm($err, $data, $id_club);
		return;
	}

	$strSQL = "SELECT id FROM ".CLUB_TABLE." WHERE id!='".$id_club."' AND name='".addslashes((strip_tags(trim($data["club_name"]))))."'";
	if ($dbconn->GetOne($strSQL)){
		EditForm("club_exist", $data, $id_club);
		return;
	}

	if (BadWordsCont(strip_tags($data["club_name"]), 8)) {
		EditForm("badword_finding_8_1", $data, $id_club);
		return;
	}
	if (check_filter(strip_tags($data["club_name"]))) {
		EditForm("info_finding_1", $data, $id_club);
		return;
	}

	if (BadWordsCont(strip_tags(htmlspecialchars($data["description"])), 8)) {
		EditForm("badword_finding_8_2", $data, $id_club);
		return;
	}

	if (check_filter(strip_tags(htmlspecialchars($data["description"])))) {
		EditForm("info_finding_1", $data, $id_club);
		return;
	}
	if (intval($data['use_agreement']) == 1) {
		$agreement_text_str = ", agreement_text='".addslashes(htmlspecialchars(strip_tags($data["agreement_text"])))."'";
		if (BadWordsCont(strip_tags(htmlspecialchars($data["agreement_text"])), 8)) {
			EditForm("badword_finding_8_6", $data, $id_club);
			return;
		}
		if (check_filter(strip_tags(htmlspecialchars($data["agreement_text"])))) {
			EditForm("info_finding_1", $data, $id_club);
			return;
		}
	}

	$strSQL = " UPDATE ".CLUB_TABLE." SET
					name='".addslashes((strip_tags(trim($data["club_name"]))))."', id_creator='".$user[ AUTH_ID_USER ]."', id_category='".intval($data["club_category"])."', is_open='".intval($data["open_join"])."', is_hidden='".intval($data["hidden_club"])."', can_invite='".intval($data["members_can_invite"])."', can_post_images='".intval($data["members_can_post_images"])."', id_country='".intval($data["country"])."', id_region='".intval($data["region"])."', id_city='".intval($data["city"])."', description='".addslashes(htmlspecialchars(strip_tags($data["description"])))."', use_agreement='".intval($data['use_agreement'])."' ".$agreement_text_str."
					WHERE id='".$id_club."' and id_creator='".$user[ AUTH_ID_USER ]."'
				";
	$dbconn->Execute($strSQL);

	$upload = $_FILES["upload"];
	if (intval($upload["size"])) {
		DelClubIcon($id_club);
	}
	$images_obj = new Images($dbconn);
	$images_obj->UploadImages($upload, $user[ AUTH_ID_USER ], 'club', 'change', '', $id_club);

	ClubTable($err, $id_club);
	exit;
}

function DelClubIcon($id_club){
	global $user, $config, $dbconn;

	$clubs_folder = $config["site_path"].GetSiteSettings('club_uploads_folder');

	$strSQL = "SELECT upload_path FROM ".CLUB_UPLOADS_TABLE." WHERE id_club='".$id_club."' and id_user='".$user[ AUTH_ID_USER ]."' and club_icon='1'";
	$icon_file = $dbconn->GetOne($strSQL);
	unlink($clubs_folder."/thumb_".$icon_file);
	unlink($clubs_folder."/".$icon_file);

	$strSQL = "DELETE FROM ".CLUB_UPLOADS_TABLE." WHERE id_club='".$id_club."' and id_user='".$user[ AUTH_ID_USER ]."' and club_icon='1'";
	$dbconn->Execute($strSQL);
	return;
}

?>