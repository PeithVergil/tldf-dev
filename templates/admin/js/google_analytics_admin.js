// version for admin back end WITHOUT tracking page views
// keep in sync with user front end

function _uGC(l,n,s){
	if (!l||l==""||!n||n==""||!s||s=="") return "-";
	var i,i2,i3,c="-";
	i=l.indexOf(n);
	i3=n.indexOf("=")+1;
	if (i>-1){
		i2=l.indexOf(s,i);
		if (i2<0) {
			i2=l.length;
		}
		c=l.substring((i+i3),i2);
	}
	return c;
}

/* Event Configuration Container */
var _track_event_Array = [ {
	id : "newsignup",
	category : "New Accounts",
	action : "New Signup"
}, {
	id : "trialstarted",
	category : "New Accounts",
	action : "Trial Started"
}, {
	id : "login",
	category : "Engagement",
	action : "Account Login"
}, {
	id : "picupload",
	category : "Engagement",
	action : "Picture Uploaded"
}, {
	id : "emailconfirmed",
	category : "New Accounts",
	action : "Email Confirmed"
}, {
	id : "addtohotlist",
	category : "Engagement",
	action : "Added to Hotlist"
}, {
	id : "sentkiss",
	category : "Engagement",
	action : "Sent a Kiss"
}, {
	id : "connectinvite",
	category : "Engagement",
	action : "Invited to Connect"
}, {
	id : "inviteaccepted",
	category : "Engagement",
	action : "Connect Invite Accepted"
}, {
	id : "eCard sent",
	category : "Engagement",
	action : "Sent eCard"
}, {
	id : "emailsent",
	category : "Engagement",
	action : "Email Sent"
}, {
	id : "emailopened",
	category : "Engagement",
	action : "Email Opened"
}, {
	id : "emailreplied",
	category : "Engagement",
	action : "Email Replied"
}, {
	id : "chatstarted",
	category : "Engagement",
	action : "Chat started"
}, {
	id : "webcamview",
	category : "Engagement",
	action : "Web Cam Views"
}, {
	id : "addtoblacklist",
	category : "Engagement",
	action : "Add to Blacklist"
}, {
	id : "customcreditpurchase",
	category : "Payments",
	action : "Custom Credit Purchase"
}, {
	id : "bronzepackbuy",
	category : "Payments",
	action : "Bronze Pack Purchase"
}, {
	id : "silverpackbuy",
	category : "Payments",
	action : "Silver Pack Purchase"
}, {
	id : "goldpackbuy",
	category : "Payments",
	action : "Gold Pack Purchase"
}, {
	id : "month1",
	category : "Payments",
	action : "1 month time purchase"
}, {
	id : "month3",
	category : "Payments",
	action : "3 month time purchase"
}, {
	id : "month6",
	category : "Payments",
	action : "6 month time purchase"
}, {
	id : "platinummatching",
	category : "Engagement",
	action : "Platinum Matching Applied"
}];

/* Extract Tracking data from HTML fields */
var _track_gender = $("#_track_gender").val();
var _track_memberStatus = $("#_track_memberStatus").val();
var _track_event_Category, _track_event_Action;

/* GA Configuration */
var _gaq = _gaq || [];
_gaq.push([ '_setAccount', 'UA-15603421-2' ]);
_gaq.push([ '_setCustomVar', 5, 'gender', _track_gender, 3 ]);
_gaq.push([ '_setCustomVar', 4, 'memberstatus', _track_memberStatus, 3 ]);

var allcookies = document.cookie;
var utma = allcookies.indexOf("__utma");
if (utma != -1) {
	var a = _uGC(document.cookie, '__utma=', ';');
	var id = a.split(".");
	var visitorId = id[1];
}
_gaq.push([ '_setCustomVar', 3, 'VisitorId', visitorId, 1 ]);
//_gaq.push([ '_trackPageview' ]);

/* Conditional Firing of GA Events */
if ($("#_track_eventCode").length > 0) {
	var _track_eventCode = $("#_track_eventCode").val();
	$.each(_track_event_Array, function(elid, el) {
		if (_track_eventCode == el.id) {
			_track_event_Category = el.category;
			_track_event_Action = el.action;
			_gaq.push([ '_trackEvent', _track_event_Category, _track_event_Action ]);
			return false;
		}
	});
}

(function() {
	var ga = document.createElement('script');
	ga.type = 'text/javascript';
	ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0];
	s.parentNode.insertBefore(ga, s);
})();
