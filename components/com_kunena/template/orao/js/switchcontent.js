// -------------------------------------------------------------------
// Switch Content Script- By Dynamic Drive, available at: http://www.dynamicdrive.com
// Created: Jan 5th, 2007
// April 5th, 07: Added ability to persist content states by x days versus just session only
// March 27th, 08': Added ability for certain headers to get its contents remotely from an external file via Ajax (2 variables below to customize)
// -------------------------------------------------------------------

var switchcontent_ajax_msg='<em>Loading Ajax content...</em>' //Customize message to show while fetching Ajax content (if applicable)
var switchcontent_ajax_bustcache=true //Bust cache and refresh fetched Ajax contents when page is reloaded/ viewed again?

function switchcontent(className, filtertag){
	this.className=className
	this.collapsePrev=false //Default: Collapse previous content each time
	this.persistType="none" //Default: Disable persistence
	//Limit type of element to scan for on page for switch contents if 2nd function parameter is defined, for efficiency sake (ie: "div")
	this.filter_content_tag=(typeof filtertag!="undefined")? filtertag.toLowerCase() : ""
	this.ajaxheaders={} //object to hold path to ajax content for corresponding header (ie: ajaxheaders["header1"]='external.htm')
}

switchcontent.prototype.setStatus=function(openHTML, closeHTML){ //PUBLIC: Set open/ closing HTML indicator. Optional
	this.statusOpen=openHTML
	this.statusClosed=closeHTML
}

switchcontent.prototype.setColor=function(openColor, closeColor){ //PUBLIC: Set open/ closing color of switch header. Optional
	this.colorOpen=openColor
	this.colorClosed=closeColor
}

switchcontent.prototype.setPersist=function(bool, days){ //PUBLIC: Enable/ disable persistence. Default is false.
	if (bool==true){ //if enable persistence
		if (typeof days=="undefined") //if session only
			this.persistType="session"
		else{ //else if non session persistent
			this.persistType="days"
			this.persistDays=parseInt(days)
		}
	}
	else
		this.persistType="none"
}

switchcontent.prototype.collapsePrevious=function(bool){ //PUBLIC: Enable/ disable collapse previous content. Default is false.
	this.collapsePrev=bool
}

switchcontent.prototype.setContent=function(index, filepath){ //PUBLIC: Set path to ajax content for corresponding header based on header index
	this.ajaxheaders["header"+index]=filepath
}

switchcontent.prototype.sweepToggle=function(setting){ //PUBLIC: Expand/ contract all contents method. (Values: "contract"|"expand")
	if (typeof this.headers!="undefined" && this.headers.length>0){ //if there are switch contents defined on the page
		for (var i=0; i<this.headers.length; i++){
			if (setting=="expand")
				this.expandcontent(this.headers[i]) //expand each content
			else if (setting=="contract")
				this.contractcontent(this.headers[i]) //contract each content
		}
	}
}


switchcontent.prototype.defaultExpanded=function(){ //PUBLIC: Set contents that should be expanded by default when the page loads (ie: defaultExpanded(0,2,3)). Persistence if enabled overrides this setting.
	var expandedindices=[] //Array to hold indices (position) of content to be expanded by default
	//Loop through function arguments, and store each one within array
	//Two test conditions: 1) End of Arguments array, or 2) If "collapsePrev" is enabled, only the first entered index (as only 1 content can be expanded at any time)
	for (var i=0; (!this.collapsePrev && i<arguments.length) || (this.collapsePrev && i==0); i++)
		expandedindices[expandedindices.length]=arguments[i]
	this.expandedindices=expandedindices.join(",") //convert array into a string of the format: "0,2,3" for later parsing by script
}


//PRIVATE: Sets color of switch header.

switchcontent.prototype.togglecolor=function(header, status){
	if (typeof this.colorOpen!="undefined")
		header.style.color=status
}


//PRIVATE: Sets status indicator HTML of switch header.

switchcontent.prototype.togglestatus=function(header, status){
	if (typeof this.statusOpen!="undefined")
		header.firstChild.innerHTML=status
}


//PRIVATE: Contracts a content based on its corresponding header entered

switchcontent.prototype.contractcontent=function(header){
	var innercontent=document.getElementById(header.id.replace("-title", "")) //Reference content container for this header
	innercontent.style.display="none"
	this.togglestatus(header, this.statusClosed)
	this.togglecolor(header, this.colorClosed)
}


//PRIVATE: Expands a content based on its corresponding header entered

switchcontent.prototype.expandcontent=function(header){
	var innercontent=document.getElementById(header.id.replace("-title", ""))
	if (header.ajaxstatus=="waiting"){//if this is an Ajax header AND remote content hasn't already been fetched
		switchcontent.connect(header.ajaxfile, header)
	}
	innercontent.style.display="block"
	this.togglestatus(header, this.statusOpen)
	this.togglecolor(header, this.colorOpen)
}

// -------------------------------------------------------------------
// PRIVATE: toggledisplay(header)- Toggles between a content being expanded or contracted
// If "Collapse Previous" is enabled, contracts previous open content before expanding current
// -------------------------------------------------------------------

switchcontent.prototype.toggledisplay=function(header){
	var innercontent=document.getElementById(header.id.replace("-title", "")) //Reference content container for this header
	if (innercontent.style.display=="block")
		this.contractcontent(header)
	else{
		this.expandcontent(header)
		if (this.collapsePrev && typeof this.prevHeader!="undefined" && this.prevHeader.id!=header.id) // If "Collapse Previous" is enabled and there's a previous open content
			this.contractcontent(this.prevHeader) //Contract that content first
	}
	if (this.collapsePrev)
		this.prevHeader=header //Set current expanded content as the next "Previous Content"
}


// -------------------------------------------------------------------
// PRIVATE: collectElementbyClass()- Searches and stores all switch contents (based on shared class name) and their headers in two arrays
// Each content should carry an unique ID, and for its header, an ID equal to "CONTENTID-TITLE"
// -------------------------------------------------------------------

switchcontent.prototype.collectElementbyClass=function(classname){ //Returns an array containing DIVs with specified classname
	var classnameRE=new RegExp("(^|\\s+)"+classname+"($|\\s+)", "i") //regular expression to screen for classname within element
	this.headers=[], this.innercontents=[]
	if (this.filter_content_tag!="") //If user defined limit type of element to scan for to a certain element (ie: "div" only)
		var allelements=document.getElementsByTagName(this.filter_content_tag)
	else //else, scan all elements on the page!
		var allelements=document.all? document.all : document.getElementsByTagName("*")
	for (var i=0; i<allelements.length; i++){
		if (typeof allelements[i].className=="string" && allelements[i].className.search(classnameRE)!=-1){
			if (document.getElementById(allelements[i].id+"-title")!=null){ //if header exists for this inner content
				this.headers[this.headers.length]=document.getElementById(allelements[i].id+"-title") //store reference to header intended for this inner content
				this.innercontents[this.innercontents.length]=allelements[i] //store reference to this inner content
			}
		}
	}
}


//PRIVATE: init()- Initializes Switch Content function (collapse contents by default unless exception is found)

switchcontent.prototype.init=function(){
	var instanceOf=this
	this.collectElementbyClass(this.className) //Get all headers and its corresponding content based on shared class name of contents
	if (this.headers.length==0) //If no headers are present (no contents to switch), just exit
		return
	//If admin has changed number of days to persist from current cookie records, reset persistence by deleting cookie
	if (this.persistType=="days" && (parseInt(switchcontent.getCookie(this.className+"_dtrack"))!=this.persistDays))
		switchcontent.setCookie(this.className+"_d", "", -1) //delete cookie
	// Get ids of open contents below. Four possible scenerios:
	// 1) Session only persistence is enabled AND corresponding cookie contains a non blank ("") string
	// 2) Regular (in days) persistence is enabled AND corresponding cookie contains a non blank ("") string
	// 3) If there are contents that should be enabled by default (even if persistence is enabled and this IS the first page load)
	// 4) Default to no contents should be expanded on page load ("" value)
	var opencontents_ids=(this.persistType=="session" && switchcontent.getCookie(this.className)!="")? ','+switchcontent.getCookie(this.className)+',' : (this.persistType=="days" && switchcontent.getCookie(this.className+"_d")!="")? ','+switchcontent.getCookie(this.className+"_d")+',' : (this.expandedindices)? ','+this.expandedindices+',' : ""
	for (var i=0; i<this.headers.length; i++){ //BEGIN FOR LOOP
		if (typeof this.ajaxheaders["header"+i]!="undefined"){ //if this is an Ajax header
			this.headers[i].ajaxstatus='waiting' //two possible statuses: "waiting" and "loaded"
			this.headers[i].ajaxfile=this.ajaxheaders["header"+i]
		}
		if (typeof this.statusOpen!="undefined") //If open/ closing HTML indicator is enabled/ set
			this.headers[i].innerHTML='<span class="status"></span>'+this.headers[i].innerHTML //Add a span element to original HTML to store indicator
		if (opencontents_ids.indexOf(','+i+',')!=-1){ //if index "i" exists within cookie string or default-enabled string (i=position of the content to expand)
			this.expandcontent(this.headers[i]) //Expand each content per stored indices (if ""Collapse Previous" is set, only one content)
			if (this.collapsePrev) //If "Collapse Previous" set
			this.prevHeader=this.headers[i]  //Indicate the expanded content's corresponding header as the last clicked on header (for logic purpose)
		}
		else //else if no indices found in stored string
			this.contractcontent(this.headers[i]) //Contract each content by default
		this.headers[i].onclick=function(){instanceOf.toggledisplay(this)}
	} //END FOR LOOP
	switchcontent.dotask(window, function(){instanceOf.rememberpluscleanup()}, "unload") //Call persistence method onunload
}


// -------------------------------------------------------------------
// PRIVATE: rememberpluscleanup()- Stores the indices of content that are expanded inside session only cookie
// If "Collapse Previous" is enabled, only 1st expanded content index is stored
// -------------------------------------------------------------------

//Function to store index of opened ULs relative to other ULs in Tree into cookie:
switchcontent.prototype.rememberpluscleanup=function(){
	//Define array to hold ids of open content that should be persisted
	//Default to just "none" to account for the case where no contents are open when user leaves the page (and persist that):
	var opencontents=new Array("none")
	for (var i=0; i<this.innercontents.length; i++){
		//If persistence enabled, content in question is expanded, and either "Collapse Previous" is disabled, or if enabled, this is the first expanded content
		if (this.persistType!="none" && this.innercontents[i].style.display=="block" && (!this.collapsePrev || (this.collapsePrev && opencontents.length<2)))
			opencontents[opencontents.length]=i //save the index of the opened UL (relative to the entire list of ULs) as an array element
		this.headers[i].onclick=null //Cleanup code
	}
	if (opencontents.length>1) //If there exists open content to be persisted
		opencontents.shift() //Boot the "none" value from the array, so all it contains are the ids of the open contents
	if (typeof this.statusOpen!="undefined")
		this.statusOpen=this.statusClosed=null //Cleanup code
	if (this.persistType=="session") //if session only cookie set
		switchcontent.setCookie(this.className, opencontents.join(",")) //populate cookie with indices of open contents: classname=1,2,3,etc
	else if (this.persistType=="days" && typeof this.persistDays=="number"){ //if persistent cookie set instead
		switchcontent.setCookie(this.className+"_d", opencontents.join(","), this.persistDays) //populate cookie with indices of open contents
		switchcontent.setCookie(this.className+"_dtrack", this.persistDays, this.persistDays) //also remember number of days to persist (int)
	}
}


// -------------------------------------------------------------------
// A few utility functions below:
// -------------------------------------------------------------------


switchcontent.dotask=function(target, functionref, tasktype){ //assign a function to execute to an event handler (ie: onunload)
	var tasktype=(window.addEventListener)? tasktype : "on"+tasktype
	if (target.addEventListener)
		target.addEventListener(tasktype, functionref, false)
	else if (target.attachEvent)
		target.attachEvent(tasktype, functionref)
}

switchcontent.connect=function(pageurl, header){
	var page_request = false
	var bustcacheparameter=""
	if (window.ActiveXObject){ //Test for support for ActiveXObject in IE first (as XMLHttpRequest in IE7 is broken)
		try {
		page_request = new ActiveXObject("Msxml2.XMLHTTP")
		} 
		catch (e){
			try{
			page_request = new ActiveXObject("Microsoft.XMLHTTP")
			}
			catch (e){}
		}
	}
	else if (window.XMLHttpRequest) // if Mozilla, Safari etc
		page_request = new XMLHttpRequest()
	else
		return false
	page_request.onreadystatechange=function(){switchcontent.loadpage(page_request, header)}
	if (switchcontent_ajax_bustcache) //if bust caching of external page
		bustcacheparameter=(pageurl.indexOf("?")!=-1)? "&"+new Date().getTime() : "?"+new Date().getTime()
	page_request.open('GET', pageurl+bustcacheparameter, true)
	page_request.send(null)
}

switchcontent.loadpage=function(page_request, header){
	var innercontent=document.getElementById(header.id.replace("-title", "")) //Reference content container for this header
	innercontent.innerHTML=switchcontent_ajax_msg //Display "fetching page message"
	if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)){
		innercontent.innerHTML=page_request.responseText
		header.ajaxstatus="loaded"
	}
}

switchcontent.getCookie=function(Name){ 
	var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
	if (document.cookie.match(re)) //if cookie found
		return document.cookie.match(re)[0].split("=")[1] //return its value
	return ""
}

switchcontent.setCookie=function(name, value, days){
	if (typeof days!="undefined"){ //if set persistent cookie
		var expireDate = new Date()
		var expstring=expireDate.setDate(expireDate.getDate()+days)
		document.cookie = name+"="+value+"; expires="+expireDate.toGMTString()
	}
	else //else if this is a session only cookie
		document.cookie = name+"="+value
}