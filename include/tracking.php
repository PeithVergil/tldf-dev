<?php

// $_SESSION['ga_gender']        : male, female
// $_SESSION['ga_member_status'] : Signup, Trial, Regular, Platinum, Guest
// $_SESSION['ga_event_code']    : see below

/* Tracking Information is held in a session object which must be filled when a user takes certain actions.
 * When the user first logs in, two parameters have to be populated - gender and subscription status
 * Following that, other 'events' will need to be recorded in the trackingObject as per our measurement requriements
 * 
 * Below is a list of events that need to be recorded
 * 
 * The event object will contain a list of all event codes as below and will be populated from serverside when a particular
 * event occurs. So for example, when a new signup takes place and the user lands on homepage, the following code 
 * should be triggerd
 * $_SESSION['trackingObject']['eventCode'] = 'newsignup';

List of events and corresponding codes

Event ---> eventCode
------------------------
New Signup --> newsignup						X
Trial Started --> trialstarted					X
Login -->login									X
Emaill address confirmed ---> emailconfirmed	X
Photo uploaded ----> picupload					X
Added to Hotlist --> addtohotlist				X
Send a Kiss ---> sentkiss						X
Invite to connect ----> connectinvite			X
Accept Invite to connect ---> inviteaccepted	X
Send an eCard ---->eCard sent					X
Email sent ----> emailsent						X
Email opened -----> emailopened					X
Email replied to ----->emailreplied				X
Started Chat ----> chatstarted					X
Started Webcam ----> webcamview					X
Added to Blacklist ----> addtoblacklist			X
Buy a Point --> customcreditpurchase			paypal, offline payment
Bronze Pack --> bronzepackbuy					paypal, offline payment
Silver Pack --> silverpackbuy					paypal, offline payment
Gold Pack --> goldpackbuy						paypal, offline payment
1 month time purchase --> month1				handled in AssignUserGroup(...), so it works with paypal and offline payment
3 months --> month3								handled in AssignUserGroup(...), so it works with paypal and offline payment
6 months --> month6								handled in AssignUserGroup(...), so it works with paypal and offline payment
Platinum Matching Applied--> platinummatching	form submit, paypal, offline payment
*/

echo '<input type="hidden" name="_track_gender" id="_track_gender" value="'.$_SESSION['ga_gender'].'" />';
echo '<input type="hidden" name="_track_memberStatus" id="_track_memberStatus" value="'.$_SESSION['ga_member_status'].'" />';

if (isset($_SESSION['ga_event_code'])) {
	echo '<input type="hidden" name="_track_eventCode" id="_track_eventCode" value="'.$_SESSION['ga_event_code'].'" />';
	unset($_SESSION['ga_event_code']);
}

?>