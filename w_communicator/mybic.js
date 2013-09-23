// GLOBALS
var mybic_failed_requests = 0;                  // a placeholder to count the number of failed requests to the server

/**
* Constructor for MyBIC's ajax object
* This only needs to be instantiated once in your script and you'll be able to re-use the same object for all your requests
*@param string The URL of the page you're going to connect to on the server, by default mybic_server.php
*@param string The function that will be called when ajax responses come back from the server, by default mybic handles this for you
*/
function XMLHTTP(server_url, readyStateFunction)
{
        this.version = '0.6.5';
        this.server_url = server_url;           // the SERVER page URL to connect to IE ajax_server.php
        this.async = true;                                      // whether we're in syncronous mode or async (default)
        this.debug=0;                                           // debug turned off by default, to see your request/response do ajaxObj.debug=1
        this.throttle=1;                                        // this enables throttling by default so all your requests are in the proper order
        this.method = "POST";                           // by default all requests are sent via POST to override just use ajaxObj.method="GET"; before your call
        this.req = null;                                        // the xmlhttprequest variable, starts off as null
        this.headers = new Array();                     // array of optional headers you may pass in
        this.callBack = '';                                     // the callback function, when the ajax request is sent, this is the function that will be called
        this.format = "JSON";                           // by default JSON encoding is the expected format, to override: ajaxObj.format = "XML"; or ajaxObj.format="TEXT";
        this.net_down_func = this.down;         // the function that will be called if mybic cannot make a request to the server (server down)
        this.abort_timeout = 5000;                      // the number of seconds to wait before calling network down function, set to -1  disable feature, defaults to 5 seconds
        this.failed_threshold = 3;                      // the number of failed requests before mybic is disabled -> prevents repeated error notifications
        this.ignoreCall = 0;                            // set this to 1 if you have a polling object and you don't want to see debug msgs every second or activity indicators
        this.stopRequest = 0;                           // set this to one in your client code if you want to make sure your callback functions completes before making another ajax call in the queue
        this.readyStateFunction = (readyStateFunction) ? readyStateFunction : this.responseHandler;
        
        /*-- SYSTEM PROPERTIES - no need to change the properites below --*/
        this.debugID = 0;                                       // used for showing expandable visual debug data
        this.errors = new Array();                      // array of errors generated
        this.queue = new Array();                       // the queue that will handle the throttling requests to keep your stuff in sync
        this.queue_in_process = 0;                      // the current index for the throttling array
        this.currentCallIgnore=0;                       // system uses this to see what calls to ignore from the queue
}

/**
* Method to create an XMLHTTP object
*@access private
*/
XMLHTTP.prototype.getXMLHTTP = function()
{
        // moz XMLHTTPRequest object
        if (window.XMLHttpRequest) {
                this.req = new XMLHttpRequest();
        }
        // IE/Windows ActiveX version
        else if (window.ActiveXObject){
                this.req = new ActiveXObject("Microsoft.XMLHTTP");
        } else {
                if(this.debug == 1) {
                        this.showDebug("<BR>FATAL ERROR: Could not create XMLHTTPRequest Object!<BR>"); 
                }
                return false;
        }
        return this.req;
}

/**
* Main API method to use for AJAX requests
* example: ajaxObj.call("action=loadComments&id=1", myCallBackFunction);
*@access public
*@param string A url encoded string of data to send to the server
*@param string A callback function that the server will launch when the response is generated
*@param string Used by the response handler function to send back throttled requests, you won't need to worry about this param
*/
XMLHTTP.prototype.call = function(queryVars, userCallback, queue_request)
{
        // test for too many failed requests
        if(mybic_failed_requests >= this.failed_threshold) {
                // call network down method with instruction to notify of mybic being disabled
                this.net_down_func('disable');
                return false;
        } else {
                var currentVars;
                var callback;
                this.fullUrl = '';
        
                if(this.throttle == 1 && queue_request != 'queue' || this.stopRequest == 1) {           // throttling keeps your requests in sync, so things aren't out of order
                        this.add2Queue(queryVars, userCallback);        
                }
                
                if(this.queue_in_process == 0)
                {
                        // get XMLHTTPRequest Object
                        if(!this.getXMLHTTP())
                        {
                                return false;
                        }
                        
                        if(this.throttle == 1) {
                                this.queue_in_process = 1;
                                var currentCall = this.queue.shift();   // get the current call to make
                                currentVars = currentCall.queryVars;
                                callback = currentCall.userCallback;
                                this.format = currentCall.format;
                                this.method = currentCall.method;
                                this.abort_timeout = currentCall.abortTimeout;
                                this.currentCallIgnore = currentCall.ignoreCall;
                        } else {
                                currentVars = queryVars;
                                callback = userCallback;
                                var ignoreCall=0;
                                
                        }
                        this.callBack = callback;
                        
                        
                        
                        // set response handler, if none is set, use our default one
                        this.req.onreadystatechange =  this.readyStateFunction;
                
                        // check for JSON encoding
                        if(this.format != 'JSON') {
                                currentVars = currentVars+'&json=false';
                        }
                                
                        // if get is used, append the query variables to the url string 
                        this.full_url = (this.method == "POST") ? this.server_url : this.server_url + '?'+ currentVars;
                        
                        if(this.debug == 1 && this.currentCallIgnore != 1) {
                                try {
                                var matches = currentVars.match(/action=(\w+)&?/);
                                this.showDebug('new', 'MYBIC - CALLING: '+matches[1]);
                                this.showDebug("Server Page: "+this.server_url+"<BR>HTTP Method: "+this.method+"<BR>Encoding Format: "+this.format+"<BR>Query String: "+currentVars+"<BR>");
                                }catch(e){}
                        }
                        
                        // open connection
                        this.req.open(this.method, this.full_url, this.async);
                        
                        // set any optional headers
                        if(this.headers){
                                for(var i in this.headers) {
                                        if(i != '') {
                                                try {
                                                        this.req.setRequestHeader( i, this.headers[i]);
                                                        if(this.debug == 1) { this.showDebug('Setting Custom Header: '+this.headers[i]+'<br>');}
                                                } catch(e) {}
                                        }
                                }
                        }
                        // START TIMER TO ABORT REQUEST
                        if(this.abort_timeout != -1) {
                                this.end_timer = setInterval('ajaxObj.endCall()', this.abort_timeout);
                        }
                        // send request
                        if(this.method == 'POST') {
                                this.req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                                this.request = currentVars;
                                this.req.send(currentVars);
                        } else {
                                this.req.send(null);
                        }
                        
                }
        }
        
}

/**
* Default method for parsing the response from the server. It will try to eval the obj.method_to_call property and pass the native JS object
*/
XMLHTTP.prototype.responseHandler = function()
{
        if(ajaxObj.req)
        {
                try
                {
                // only if req shows "complete"
                if (ajaxObj.req.readyState == 4) {
                        // only if "OK" 
                        if (ajaxObj.req.status && ajaxObj.req.status == 200) {

                                if(ajaxObj.req.responseText.indexOf('ajax_msg_failed') != -1) {
                                        ajaxObj.showDebug("Fatal Error: mybic_server sent back ajax_msg_failed! - MSG: "+ajaxObj.req.responseText+"<br/>");
                                        if(ajaxObj.req.responseText.indexOf('notauth') != -1) { 
                                                if(ajaxObj.abort_timeout != -1) { clearInterval(ajaxObj.end_timer); }
                                                ajaxObj.callBack('notauthorized'); 
                                        } else {
                                                ajaxObj.callBack(false); 
                                        }
                                } else {
                                        // clear out network down timer
                                        if(ajaxObj.abort_timeout != -1) {
                                                clearInterval(ajaxObj.end_timer);
                                        }
                                        if(ajaxObj.format == "JSON") {
                                        try {
                                                var myObject = JSON.parse(ajaxObj.req.responseText);
                                        // callback function we passed to the server to process the results
                                        
                                        if(document.getElementById(ajaxObj.callBack)) {
                                                document.getElementById(ajaxObj.callBack).innerHTML = myObject;
                                        } else {
                                                ajaxObj.callBack(myObject);
                                        }
                                        
                                        } catch(e) {
                                                        alert('an error occurred in your response function, not mybic related. Error Name: ' + e.name + '  Message:' + e.message);
                                                        if(document.getElementById(ajaxObj.callBack)) { } else {
                                                                ajaxObj.callBack(false);
                                                        }
                                        }
                                        } else if(ajaxObj.format == "XML") {
                                                // send the raw xml data to the callback function
                                                ajaxObj.callBack(ajaxObj.req.responseXML);      
                                        } else {
                                                if(document.getElementById(ajaxObj.callBack)) {
                                                document.getElementById(ajaxObj.callBack).innerHTML = ajaxObj.req.responseText;
                                                } else {
                                                        ajaxObj.callBack(ajaxObj.req.responseText);
                                                }

                                        }
                                }
                                mybic_failed_requests = 0; // reset failed requests back to 0
                        } else {
                                // server is came back with a bad status
                                try{
                                ajaxObj.showDebug("Fatal Error: MSG: "+ajaxObj.req.responseText+" StatusText: "+ajaxObj.req.statusText+"<br/>");
                                } catch(e){}
                                ajaxObj.endCall();      
                        }
                        if(ajaxObj.debug == 1 && ajaxObj.currentCallIgnore != 1) {
                                        // STRIP HTML
                                        var str = ajaxObj.req.responseText.replace(/(\<)/gi, '&lt;');
                                        var str = str.replace(/(\>)/gi, '&gt;');
                                        ajaxObj.showDebug("HTTP Server Response:<br/> "+str+"<br>");
                                        
                                }
                        // reset the method, format, etc back to class defaults
                        ajaxObj.restoreDefaults();
                        
                        // reset our queue and call
                        ajaxObj.queue_in_process = 0;
                        if(ajaxObj.queue.length > 0) {
                                
                                ajaxObj.call('','','queue');
                        }
                }
                } catch(e) { 
                /*network is down*/}
        }
        
}

/**
* This method will allow you to lazy load javascript source files for on-demand javascript processing
* Just pass in the url of the javascript file you wish to pull in and you're all set, its not ajaxified so you 
* can pull in scripts from any URL you want! You can also use this function to remove scripts from the DOM as well
* Just pass in 'remove' as the 2nd parameter with the 1st parameter being the file you want to remove
* Removal is good for memory heavy desktop type apps you might be creating
*@param string The URL of the script you wish to load
*@param string OPTIONAL If you wish to remove a javascript file from the DOM then just pass in 'remove' as the 2nd parameter
*/
XMLHTTP.prototype.loadScript = function(url, remove)
{
        try{
                // lets check to see if the script is already loaded, if so lets remove it and add a new one
                var scripts = document.getElementsByTagName('script');
                s_len = scripts.length;
                for(var i=0;i<s_len;i++){
                        var reg = new RegExp(url+"$");
                        if (reg.test(scripts[i].src)) {
                                var p2 = scripts[i];
                                p2.parentNode.removeChild(p2);
                                break;
                        }       
                }
                if(remove != 'remove') {
                        newScript = document.createElement("script");
                        newScript.setAttribute("type", "text/javascript");
                        newScript.setAttribute("src", url);
                        document.getElementsByTagName('head')[0].appendChild(newScript);
                }
        } catch(e) {
                this.showDebug("MyBIC - loadScript failed URL: "+url+" ErrName: "+e.name+" Msg: "+e.message);   
        }

}

/**
* This method will allow you to create a "command queue" so ajax requests are sent in order they were fired. 
* You will be able to keep your request/responses in order they were sent
*/
XMLHTTP.prototype.add2Queue = function(queryVars, userCallback)
{
        var addAjax = new Array();
        addAjax['queryVars'] = queryVars;
        addAjax['userCallback'] = userCallback;
        addAjax['ignoreCall'] = this.ignoreCall;
        addAjax['abortTimeout'] = this.abort_timeout;
        addAjax['format'] = this.format;
        addAjax['method'] = this.method;
        
        this.ignoreCall=0;      // reset back to original state
        this.queue.push(addAjax);
}


/**
* Method called after callback function is called to return the class to a default state
*/
XMLHTTP.prototype.restoreDefaults = function()
{
        this.method = "POST";
        this.format = "JSON";   
        this.callback = "";
        this.abort_timeout = 5000;
        this.failed_threshold = 3;      
        
}

/**
* This method will allow you to get all your form fields in ONE magical step!
* right before your ajaxObj.call statement all you have to do is: var form_vars = ajaxObj.getForm('formid');
* that will loop through the form id you pass, and put all the form variables into your query string!
*@param string The ID of the form you wish to submit
*@return string An encoded query string, ready to send to the server
*/

XMLHTTP.prototype.getForm = function(formid)
{
        var formobj = document.getElementById(formid);
        var fields = new Array();
        var form_len = formobj.elements.length;
        for (var x = 0; x < form_len; x++) {
        switch(formobj.elements[x].type) {
           case 'select-one':
        
                fields.push(encodeURIComponent(formobj.elements[x].name)+'='+encodeURIComponent(formobj.elements[x].options[formobj.elements[x].selectedIndex].value));
                break;
                case 'select-multiple':
                var obj = formobj.elements[x];
                        for(var y=0; y < formobj.elements[x].options.length; y++) {
                           if(formobj.elements[x].options[y].selected) {
                                                if(formobj.elements[x].options[y].value == ''){
                                                        fields.push(encodeURIComponent(formobj.elements[x].name)+'='+encodeURIComponent(formobj.elements[x].options[y].text));
                                                } else {
                                                        fields.push(encodeURIComponent(formobj.elements[x].name)+'='+encodeURIComponent(formobj.elements[x].options[y].value));
                                                }
                           }
                        }
                break;
                case 'radio':
                           if(formobj.elements[x].checked) {
                                           fields.push(encodeURIComponent(formobj.elements[x].name)+'='+encodeURIComponent(formobj.elements[x].value));
                           }
        break;
                case 'checkbox':
                        if(formobj.elements[x].checked) {
                                fields.push(encodeURIComponent(formobj.elements[x].name)+'='+encodeURIComponent(formobj.elements[x].value));
                        }
                break;
                default:
                // text, password, textarea, etc
                fields.push(encodeURIComponent(formobj.elements[x].name)+'='+encodeURIComponent(formobj.elements[x].value));
                break;
        }
        }
        var new_qstring = '&' + fields.join('&');
        return new_qstring;
}

/**
* This method will check the request call after x number of seconds to see if the network call is still hanging
* You will want to use this when your server might be down and after 5 seconds you'll want to call a function that lets the 
* user know you're having server issues
*/
XMLHTTP.prototype.endCall = function()
{
        try{
                ajaxObj.net_down_func();
                ajaxObj.req.abort();
                ajaxObj.req = null;
                clearInterval(ajaxObj.end_timer);
                // increase failed requests variable
                mybic_failed_requests++;
                // reset our queue and call
                ajaxObj.queue_in_process = 0;
                if(ajaxObj.queue.length > 0) {
                        
                        ajaxObj.call('','','queue');
                }
                if(ajaxObj.debug == 1) { ajaxObj.showDebug("Request Failed - Network Down! Current Failed Attempts: "+mybic_failed_requests+"<br>");}
        } catch(e) {
                // server is completely down, call netdown function
                clearInterval(ajaxObj.end_timer);
                ajaxObj.net_down_func('disable');
        }
}

/**
* Default method that will be called when mybic experiences a network down situation, or is disabled
* This method will pop up a div alerting the user of a general network error
* You should define your own method to properly fit in with your page layout
*/
XMLHTTP.prototype.down = function(status)
{
        var notif_div = '<div id="mybic_notification" style="text-align:center;padding:20px;position:absolute;top:100px;left:100px;width:300px;border:thin solid black;background-color:#F8F021;">';
        notif_div       +=      '<span id="mybic_notif_msg"> MSGHERE </span> <br><br><input type="button" value="OK" onclick="document.getElementById(\'mybic_notification\').style.display=\'none\';"></div>';
        if(status == 'disable') {
                
                var notif = 'A network issue has disabled network connections for this page. Please reload this page or contact the site administrator';
        } else {
                        
                var notif = 'A network issue has occurred which canceled your last request';
        }
        // lets try and find an existing error message or use the current one in the DOM
        try{
                if(document.getElementById('mybic_notification')) {
                        document.getElementById('mybic_notification').style.display='block';
                } else {
                        var new_div = document.createElement('div');
                        new_div.innerHTML = notif_div;
                        document.body.appendChild(new_div);
                }
                document.getElementById('mybic_notif_msg').innerHTML = notif;
        } catch(e) { 
                alert('Network Unavailable: Please re-load page or contact the site administrator');
        }
}
/**
* This method will let developers view debug information on the screen from not only the system calls but also let them tap into it as well
*@access public
*@param string The Message you wish to push to debugging or pass in 'break' and the UI will break a new expandable column for you
*@param string OPTIONAL: If you pass in break, also send in a string that will show what the break label should be
*@param int OPTIONAL: If you pass in break, if you pass in a 1 to the function it will allow you have your section autoexpanded
*/
XMLHTTP.prototype.showDebug = function(msg, label, expand)
{
        if(ajaxObj.debug == 1) {
                if(!document.getElementById('mybic_debug')) {
                        var errs = document.createElement('div');
                        errs.id = 'mybic_errs';
                        var deb = document.createElement('div');
                        deb.id = 'mybic_debug';
                        deb.style.border = "thick solid black";
                        deb.style.backgroundColor = "#eeeeee";
                        deb.style.padding = "10px";     
                        deb.style.margin = '75px 10px 10px 10px';
                        deb.style.width = '90%';
                        deb.style.position = 'absolute';
                        deb.style.zIndex = '999';
                        deb.innerHTML += 'MyBic Debugger: <a href="#" onclick="document.getElementById(\'mybic_errs\').style.display = (document.getElementById(\'mybic_errs\').style.display==\'none\') ? \'\':\'none\'; return false;" >hide/show me!</a>';
                        deb.innerHTML += '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="document.getElementById(\'mybic_errs\').innerHTML = \'\'; return false;">Clear</a>';
                        deb.innerHTML += '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="ajaxObj.debug_expand(\'block\');return false;">Expand All</a>';
                        deb.innerHTML += '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="ajaxObj.debug_expand(\'none\'); return false;">Contract All</a><br><br>';
                        deb.appendChild(errs);
                        if(document.body) {
                                document.body.appendChild(deb);
                        } else {
                                document.lastChild.appendChild(deb);
                        }
                }
                var deb = document.getElementById('mybic_errs');
                if(msg ==  'new') {
                        ajaxObj.debugID++;
                        var dimg = '<a style="color:white;font-size:1.1em;text-decoration:none" href="#" onclick="ajaxObj.debug_expand(this);return false;">+</a>';
                        deb.innerHTML += '<div id="mybiclabel_'+ajaxObj.debugID+'" style="display:block;border:thin solid #999999;padding:2px;background-color:#cccccc;">'+dimg+' label'+ajaxObj.debugID+': '+label+'</div>';
                } else {
                        deb.innerHTML +='<div class="mybic_debug'+ajaxObj.debugID+'" style="padding:5px;display:none; border:thin solid white;">'+msg+'</div>';
                }       
        }               
}

/**
* This method is used by MyBic to visually show the debug data you've passed in
*@private
*@param object The image element that triggers the show/hide functionality
*/
XMLHTTP.prototype.debug_expand = function(el)
{
        var deb = document.getElementById('mybic_errs');
        var deb_len = deb.childNodes.length;
        if(el == 'none' || el == 'block') {
                var label = "mybic_debug";
                var links = deb.getElementsByTagName('a');
                var links_len = links.length;
                for(var q=0;q<links_len;q++) {
                        links[q].innerHTML = (el == 'none') ? '+' : '>';        
                }
        } else {
                var label = el.parentNode.id;
                label = label.split('_');
                label = "mybic_debug"+label[1];
        }
        for(var i=0; i<deb_len; i++) {
                // loop through and show the elements with the right classname
                try {
                if(deb.childNodes[i].className.match(new RegExp("(^"+ label + ".*$)"))) {
                        if(el == 'none' || el == 'block') {
                                deb.childNodes[i].style.display = el;
                        } else {
                                if(deb.childNodes[i].style.display == 'block') {
                                        el.innerHTML = '+';
                                        deb.childNodes[i].style.display = 'none';
                                } else {
                                        el.innerHTML = '>';
                                        deb.childNodes[i].style.display = 'block';
                                }
                        }
                }
                } catch(e) {}
        }
}

/**
* This method is a wrapper for when you have a callback funciton that has to finish executing before any other ajax calls are made
* You may be in this situation when ajax calls rely on each other IE don't update this data until I finish processing the other data
*/
XMLHTTP.prototype.restart = function() 
{
        ajaxObj.stopRequest=0;
        ajaxObj.call('','','queue');
}
/****************************************************/
// INCLUDE JSON.ORG's JSON CLIENT SIDE SERIALIZER
/*
Copyright (c) 2005 JSON.org
*/

/*
    The global object JSON contains two methods.

    JSON.stringify(value) takes a JavaScript value and produces a JSON text.
    The value must not be cyclical.

    JSON.parse(text) takes a JSON text and produces a JavaScript value. It will
    return false if there is an error.
*/
var JSON = function () {
    var m = {
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        s = {
            'boolean': function (x) {
                return String(x);
            },
            number: function (x) {
                return isFinite(x) ? String(x) : 'null';
            },
            string: function (x) {
                if (/["\\\x00-\x1f]/.test(x)) {
                    x = x.replace(/([\x00-\x1f\\"])/g, function(a, b) {
                        var c = m[b];
                        if (c) {
                            return c;
                        }
                        c = b.charCodeAt();
                        return '\\u00' +
                            Math.floor(c / 16).toString(16) +
                            (c % 16).toString(16);
                    });
                }
                return '"' + x + '"';
            },
            object: function (x) {
                if (x) {
                    var a = [], b, f, i, l, v;
                    if (x instanceof Array) {
                        a[0] = '[';
                        l = x.length;
                        for (i = 0; i < l; i += 1) {
                            v = x[i];
                            f = s[typeof v];
                            if (f) {
                                v = f(v);
                                if (typeof v == 'string') {
                                    if (b) {
                                        a[a.length] = ',';
                                    }
                                    a[a.length] = v;
                                    b = true;
                                }
                            }
                        }
                        a[a.length] = ']';
                    } else if (x instanceof Object) {
                        a[0] = '{';
                        for (i in x) {
                            v = x[i];
                            f = s[typeof v];
                            if (f) {
                                v = f(v);
                                if (typeof v == 'string') {
                                    if (b) {
                                        a[a.length] = ',';
                                    }
                                    a.push(s.string(i), ':', v);
                                    b = true;
                                }
                            }
                        }
                        a[a.length] = '}';
                    } else {
                        return;
                    }
                    return a.join('');
                }
                return 'null';
            }
        };
    return {
        copyright: '(c)2005 JSON.org',
        license: 'http://www.JSON.org/license.html',
/*
    Stringify a JavaScript value, producing a JSON text.
*/
        stringify: function (v) {
            var f = s[typeof v];
            if (f) {
                v = f(v);
                if (typeof v == 'string') {
                    return v;
                }
            }
            return null;
        },
/*
    Parse a JSON text, producing a JavaScript value.
    It returns false if there is a syntax error.
*/
        parse: function (text) {
            try {
                return !(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(
                        text.replace(/"(\\.|[^"\\])*"/g, ''))) &&
                    eval('(' + text + ')');
            } catch (e) {
                return false;
            }
        }
    };
}();
