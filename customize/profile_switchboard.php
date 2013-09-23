<?php

$check_email_domain = 0;					// disable for offline testing

// bitmask
// 000 = 0 = do not use at all
// 001 = 1 = use on registration form
// 010 = 2 = use on profile editing form
// 011 = 3 = use on registration and profile editing form
// 100 = 4 = use on platinum application form

if (!defined('SB_REGISTRATION')) {
	define('SB_REGISTRATION', 1);
	define('SB_EDIT', 2);
}

$use_field = array
(
	'fname'						=> 3,
	'sname'						=> 3,
	'background_check'			=> 0,
	'marital_status_check'		=> 0,
	'work_history_check'		=> 0,
	'mm_nickname'				=> 2,
	'gender'					=> 3,
	'couple'					=> 0,
	'mm_marital_status'			=> 2,
	'date_birthday'				=> 3,
	'mm_place_of_birth'			=> 0,
	'id_nationality'			=> 2,
	'mm_id_number'				=> 2,
	'email'						=> 3,
	'reemail'					=> 3,
	'mm_contact_phone_number'	=> 2,
	'mm_contact_mobile_number'	=> 2,
	'best_call_time'			=> 2,
	'gender_search'				=> 0,	// gender_search is oposite of gender
	'age_min'					=> 2,
	'age_max'					=> 2,
	'couple_search'				=> 0,
	'id_relationship'			=> 0,	// modify search for this, maybe use it later for marriage, long-time relationship, etc.
	'id_country'				=> 2,
	'id_region'					=> 2,	// modify search for this, only for ladies
	'id_city'					=> 0,	// we use text input in mm_city, modify search for this
	'mm_city'					=> 2,
	'zipcode'					=> 0,
	'mm_address_1'				=> 0,
	'mm_address_2'				=> 0,
	'mm_address_3'				=> 0,
	'id_language_1'				=> 2,
	'id_language_2'				=> 2,
	'id_language_3'				=> 2,
	'mm_level_of_english'		=> 2,
	'site_language'				=> 0,	// we need it, but do not let the user make a selection
	'mm_employment_status'		=> 0,
	'mm_business_name'			=> 0,
	'mm_employer_name'			=> 0,
	'mm_job_position'			=> 0,
	'mm_work_address'			=> 0,
	'mm_work_phone_number'		=> 0,
	'mm_ref_1_first_name'		=> 0,
	'mm_ref_1_last_name'		=> 0,
	'mm_ref_1_relationship'		=> 0,
	'mm_ref_1_phone_number'		=> 0,
	'mm_ref_2_first_name'		=> 0,
	'mm_ref_2_last_name'		=> 0,
	'mm_ref_2_relationship'		=> 0,
	'mm_ref_2_phone_number'		=> 0,
	'headline'					=> 0,
	'subscribes'				=> 0,
	'notification'				=> 0,
	'privacy_settings'			=> 2,
	'online_privacy'			=> 2,
	'privacy_female'			=> 2,
	'privacy_male'				=> 2,
	'promotion'					=> 2,
	'promote_no'				=> 2,
	'promote_within'			=> 2,
	'promote_prospective'		=> 2,
	'biography'					=> 2,
	'about_me'					=> 2,
	'what_i_do'					=> 2,
	'my_idea'					=> 2,
	'hoping_to_find'			=> 2,
	'id_height'					=> 2,
	'id_weight'					=> 2
);

// bitmask
// 000 = 0 = not mandatory
// 001 = 1 = mandatory on registration form
// 010 = 2 = mandatory on profile verification and profile editing form
// 011 = 3 = mandatory on registration, profile verification and profile editing form
// 100 = 4 = mandatory on platinum application

$mandatory = array
(
	'fname'						=> 3,
	'sname'						=> 3,
	'background_check'			=> 0,
	'marital_status_check'		=> 0,
	'work_history_check'		=> 0,
	'mm_nickname'				=> 0,
	'gender'					=> 3,
	'couple'					=> 0,
	'mm_marital_status'			=> 4,
	'date_birthday'				=> 3,
	'mm_place_of_birth'			=> 0,
	'id_nationality'			=> 2,
	'mm_id_number'				=> 0,
	'email'						=> 3,
	'reemail'					=> 3,
	'mm_contact_phone_number'	=> 4,
	'mm_contact_mobile_number'	=> 4,
	'best_call_time'			=> 0,
	'gender_search'				=> 3,
	'age_min'					=> 2,
	'age_max'					=> 2,
	'couple_search'				=> 0,
	'id_relationship'			=> 0,
	'id_country'				=> 2,
	'id_region'					=> 4,
	'id_city'					=> 0,
	'mm_city'					=> 4,
	'zipcode'					=> 0,
	'mm_address_1'				=> 0,
	'mm_address_2'				=> 0,
	'mm_address_3'				=> 0,
	'id_language_1'				=> 4,
	'id_language_2'				=> 0,
	'id_language_3'				=> 0,
	'mm_level_of_english'		=> 4,
	'site_language'				=> 3,
	'mm_employment_status'		=> 0,
	'mm_business_name'			=> 0,
	'mm_employer_name'			=> 0,
	'mm_job_position'			=> 0,
	'mm_work_address'			=> 0,
	'mm_work_phone_number'		=> 0,
	'mm_ref_1_first_name'		=> 0,
	'mm_ref_1_last_name'		=> 0,
	'mm_ref_1_relationship'		=> 0,
	'mm_ref_1_phone_number'		=> 0,
	'mm_ref_2_first_name'		=> 0,
	'mm_ref_2_last_name'		=> 0,
	'mm_ref_2_relationship'		=> 0,
	'mm_ref_2_phone_number'		=> 0,
	'headline'					=> 0,
	'subscribes'				=> 0,
	'notification'				=> 0,
	'online_privacy'			=> 0,
	'privacy_female'			=> 0,
	'privacy_male'				=> 0,
	'promote_no'				=> 0,
	'promote_within'			=> 0,
	'promote_prospective'		=> 0,
	'biography'					=> 0,
	'about_me'					=> 0,
	'what_i_do'					=> 0,
	'my_idea'					=> 0,
	'hoping_to_find'			=> 0,
);

?>