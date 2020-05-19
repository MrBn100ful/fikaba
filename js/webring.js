function createEL (tag, options, children) {
	let theElement = document.createElement(tag);
	
	if (options) {
		for (let o in options) {
			switch (o) {
				case "event": {
					theElement.addEventListener(options.event[0], options.event[1], (options.event[2])?true:false);
					break;
				}
				case "events": {
					for (let e of options.events) {
						theElement.addEventListener(e[0], e[1], (e[2])?true:false);
					}
					break;
				}
				case "dataset": {
					for (let d in options.dataset) {
						theElement.dataset[d] = options.dataset[d];
					}
					break;
				}
				case "style": {
					if (typeof options.style === "string") {
						theElement.style = options.style;
					}
					else {
						for (let s in options.style) {
							theElement.style[s] = options.style[s];
						}
					}
					break;
				}
				case "attribute": {
					theElement.setAttribute(options.attribute[0], options.attribute[1]);
					break;
				}
				case "attributes": {
					for (let a of options.attributes) {
						theElement.setAttribute(a[0], a[1]);
					}
					break;
				}
				default: {
					theElement[o] = options[o];
				}
			}
		}
	}
	if (children) {
		for (let c of children) {
			theElement.appendChild(c);
		}
	}
	
	return theElement;
}
let webring = {
	websites: [], // loaded from webring jsons, object for the current website is first
	jsonsLoaded: 0, // timestamp when the webring data was last loaded
	sortType: 'lastPostTimestamp', // empty = old grouped list, use any property of the json data for boards in order to sort by it, for example "uniqueUsers"
	
	// loads webring.jsons and adds the websites to webring.websites. callback is optional.
	loadWebringJsons: function(callbackWhenAllHaveLoaded) {
		let websites = [];
		let thingsToLoad = 2;
		function getWebringList(webringJsonUrl) {
			let request = new XMLHttpRequest();
			request.open("GET", webringJsonUrl, true);
			request.addEventListener("load", function () {
				function addSite(site) {
					let currentWebsite = (window.location.href.indexOf(site.url)>=0) ? true : false;
					// add extra data to boards to help sort them and stuff
					for (let board of site.boards) {
						board.websiteUrl = site.url;
						board.websiteName = site.name;
						if (window.location.href.includes(board.path)) board.isCurrentBoard = true;
						if (currentWebsite) board.isCurrentWebsite = true;
					}
					// add current website as first item
					if (currentWebsite) {
						site.isCurrentWebsite = true;
						websites.splice(0,0,site);
					}
					else {
						websites.push(site);
					}
				}
				
				try {
					let j = JSON.parse(this.response);
					if (Array.isArray(j)) {
						for (let site of j) {
							addSite(site);
						}
					}
					else {
						addSite(j);
					}
				}
				catch (err) {
					console.log("Failed to parse webring json", err);
				}
				// when all files have loaded, call callback (if provided)
				thingsToLoad --;
				if (thingsToLoad === 0) {
					webring.websites = websites;
					webring.jsonsLoaded = Date.now();
					if (callbackWhenAllHaveLoaded) callbackWhenAllHaveLoaded();
				}
			});
			request.send();
		}
		
		getWebringList("https://planches.4feuilles.org/webring.json");
		getWebringList("https://planches.4feuilles.org/compiled_webring.json");
	},
	// returns a sorted version of given boardList array
	sortBoardList: function(boardList, sortType) {
		if (!boardList.length) return [];
		
		let typeIsNumber = false; // numbers need a different comparison because 25 is above 10, but "10" is above "25"
			switch (sortType) {case "uniqueUsers": case "postsPerHour": case "totalPosts": typeIsNumber=true;break;}
		let typeIsTimestamp = false;
			switch (sortType) {case "lastPostTimestamp": typeIsTimestamp=true;break;}
		
		boardList.sort(function(a, b){
			if (typeIsNumber) {
				if (Number(a[sortType]) < Number(b[sortType])) {
					return -1;
				}
			} else if (typeIsTimestamp) {
				let time = new Date(a[sortType]).getTime() || 0;
				let refTime = new Date(b[sortType]).getTime() || 0;
				if (time < refTime) {
					return -1;
				}
			} else {
				if (a[sortType].toLowerCase() > b[sortType].toLowerCase()) {
					return -1;
				}
			}
			return 1;
	    });
		
		// always put current website first when sorting by website
		if (sortType === "websiteName") {
			let localBoards = [];
			for (let b=0; b<boardList.length; b++) {
				let board = boardList[b];
				if (board.isCurrentWebsite) {
					localBoards.push(board);
					boardList.splice(b, 1);
					b --;
				}
			}
			boardList = boardList.concat(localBoards);
		}
		
		return boardList;
	},
	// creates the board list depending on webring.sortType
	createWindowHtml: function() {
		if (!webring.jsonsLoaded) {
			// webring data has not been loaded yet; abort, load data, then call this function again
			webring.loadWebringJsons(webring.createWindowHtml);
			return;
		}
		
		let con = webring.getWindow();
		if (!con.classList.contains("open")) return; // window is not open, abort
		let inner = con.getElementsByClassName("webringlist")[0];
		inner.innerHTML = "";
		
		// for website folding/hiding, use localstorage to remember which sites are folded
		let folded;
			try { folded = JSON.parse(localStorage.getItem("webring_foldedsites") || "[]"); } catch (err) {}
			if (!Array.isArray(folded)) folded = [];
		
		// not sorted, create old grouped list
		if (!webring.sortType) {
			inner.classList.add("unsorted");
			inner.classList.remove("sorted");
			
			function foldSite () { // just for remembering what's folded
				if (this.parentNode.open) {
					folded.push(this.dataset.foldtarget);
				}
				else {
					folded.splice(folded.indexOf(this.dataset.foldtarget), 1);
				}
				localStorage.setItem("webring_foldedsites", JSON.stringify(folded));
			}
			for (let site of webring.websites) {
				// main container and header
				let container = createEL("details", {open:!folded.includes(site.name)}, [
					createEL("summary", {dataset:{foldtarget:site.name}, event:["click", foldSite]}, [
						createEL("span", {textContent:site.name+' '}),
						createEL("a", {textContent:"[→]", attribute:["href",site.url]})
					])
				]);
				if (site.isCurrentWebsite) container.classList.add("current");
				// boards
				if (site.boards) {
					let boardsDiv = createEL("div", {className:"webringboards"});
					container.appendChild(boardsDiv);

					for (let board of site.boards) {
						let bLink = createEL("a", {attributes:[["href",board.path],["title",board.subtitle]]}, [
							createEL("span", {textContent:'/'+board.uri+'/'}),
							createEL("small", {textContent:' - '+board.title})
						]);
						if (board.isCurrentBoard) bLink.classList.add("current");
						boardsDiv.appendChild(bLink);
					}
				}
				inner.appendChild(container);
			}
		}
		// sorted, create table list
		else {
			inner.classList.add("sorted");
			inner.classList.remove("unsorted");
			
			let topButtonsDiv = inner.appendChild(createEL("div", {className:"webringtopbuttons"}));
			// refresh button
			topButtonsDiv.appendChild(createEL("button", {type:"button", className:"webringrefreshbutton", textContent:"Rafraîchir la liste", event:["click", function(){
				this.textContent = "Chargement...";
				webring.loadWebringJsons(webring.createWindowHtml);
			}]}));
			// create buttons for hidden boards
			if (folded.length) {
				function unhideBoard () {
					folded.splice(folded.indexOf(this.dataset.target), 1);
					localStorage.setItem("webring_foldedsites", JSON.stringify(folded));
					webring.createWindowHtml();
				}
				topButtonsDiv.appendChild(document.createTextNode("Hidden: "));
				for (let name of folded) {
					topButtonsDiv.appendChild(
						createEL("a", {textContent:name, dataset:{target:name}, event:["click",unhideBoard]})
					);
				}
			}
			
			// create sorted board list array
			let boardList = [];
			for (let site of webring.websites) {
				for (let board of site.boards) {
					// do not include folded boards
					if (!folded.includes(site.name)) {
						boardList.push(board);
					}
				}
			}
			let uidSortedList = webring.sortBoardList(boardList, 'uniqueUsers'); // presort by UIDs so that in the activity tab sites not using the current schema are still in a sensible order
			let sortedList = webring.sortBoardList(uidSortedList, webring.sortType);
			
			// make board list table
			let table = inner.appendChild(createEL("table"));
			table = table.appendChild(createEL("tbody"));
			
			// add table header row
			table.appendChild(
				createEL("tr", null, [
					createEL("th", {textContent:'Site'}),
					createEL("th", {textContent:'Board'}),
					createEL("th", {textContent:'Subtitle'}),
					createEL("th", {textContent:'PPH'}),
					createEL("th", {textContent:'Users'}),
					createEL("th", {textContent:'Posts'}),
					createEL("th", {textContent:'Last activity'}),
				])
			);
			// add boards
			let nowTime = Date.now();
			for (let i=sortedList.length-1; i>=0; i--) {
				let board = sortedList[i];
				
				let activeTime = new Date(board.lastPostTimestamp).getTime() || 0; // new Date() should simply return as NaN if the given value is invalid or malformed somehow
				let timeText = "No data";
				let color = "rgb(100, 100, 100)";
				if (activeTime>0) {
					let msAgo = Math.max(0, nowTime-activeTime);
					let minutesAgo = msAgo/1000/60;
					let hoursAgo = minutesAgo/60;
					
					let minutes = Math.floor(minutesAgo);
					let hours = Math.floor(minutes/60);
					let days = Math.floor(hours/24);
					hours -= days*24;
					minutes -= hours*60;
					
					timeText = "";
					if (days) timeText += days+"d ";
					if (hours||days) timeText += hours+"h ";
					if (!days&&!hours) timeText += "<1h ";
					timeText += "ago";
					
					let r=0, g=0, b=0;
					if (hoursAgo < 1) { // less than hour ago, blue
						g = 1;
						b = 1;
					}
					else if (hoursAgo < 18) { // 1-18 hours ago, green to yellow
						let normal = (hoursAgo-1)/18;
						g = 1;
						r = normal;
					}
					else if (hoursAgo < 24*5) { // 18hours-5days ago, yellow to red
						let normal = (hoursAgo-18)/(24*5);
						r = 1;
						g = 1-normal;
					}
					else if (hoursAgo < 24*14) { // 5days-2weeks ago, red to black
						let normal = (hoursAgo-24*5)/(24*14);
						r = 1-normal;
					}
					g *= 0.7; // this is just so the text is always readable when white.
					r *= 0.8;
					color = "rgb("+Math.floor(r*255)+","+Math.floor(g*255)+","+Math.floor(b*255)+")";
				}
				
				let tr = createEL("tr", null, [
					createEL("td", null, [
						createEL("a", {className:"webringsite", attribute:["href",board.websiteUrl], textContent:board.websiteName})
					]),
					createEL("td", null, [
						createEL("a", {className:"webringboard", attributes:[["href",board.path],["title",board.title]]}, [
							createEL("span", {textContent:'/'+board.uri+'/'}),
							createEL("small", {textContent:' - '+board.title})
						])
					]),
					createEL("td", null, [
						createEL("small", {className:"webringsubtitle", textContent:board.subtitle||"", attribute:["title",board.subtitle||""]})
					]),
					createEL("td", null, [
						createEL("span", {className:"webringpph", textContent:board.postsPerHour||0})
					]),
					createEL("td", null, [
						createEL("span", {className:"webringusers", textContent:board.uniqueUsers||0})
					]),
					createEL("td", null, [
						createEL("span", {className:"webringposts", textContent:board.totalPosts||0})
					]),
					createEL("td", null, [
						createEL("small", {className:"webringactivity", style:(color)?"background:"+color:"",textContent:timeText})
					])
				]);
				if (window.location.href.includes(board.websiteUrl)) tr.classList.add("currentsite");
				if (board.isCurrentBoard) tr.classList.add("currentboard");
				
				table.appendChild(tr);
			}
		}
	},
	// get webring window html element, and create it if it doesn't yet exist
	getWindow: function() {
		let con = document.getElementById("webringwindow");
		
		if (!con) {
			// first create header with buttons
			// get sort order. Empty string becomes old grouped list
			webring.sortType = sessionStorage.getItem("webring_sort") || webring.sortType;
			// get sort order switching buttons
			let windowHeader;
			function selectSort() {
				webring.sortType = this.dataset.type;
				sessionStorage.setItem("webring_sort", webring.sortType);
				
				let current = windowHeader.getElementsByClassName("current")[0]; if (current) current.classList.remove("current");
				this.classList.add("current");
				
				webring.createWindowHtml();
			}
			windowHeader = createEL("div", {className:"webringheader"}, [
    			createEL("a", {textContent:"Activité", dataset:{type:"lastPostTimestamp"}, event:["click", selectSort]}),
				createEL("a", {textContent:"A-Z", dataset:{type:"uri"}, event:["click", selectSort]}),
				createEL("a", {textContent:"Site", dataset:{type:""}, event:["click", selectSort]}),
				// close button
				createEL("button", {type:"button", className:"webringclosebutton", textContent:"X", event:[
					"click", function(){
						con.classList.remove("open");
					}
				]})
			]);
			// add class to the current sort type button
			for (let button of windowHeader.getElementsByTagName("a")) {
				if (button.dataset.type === webring.sortType) {
					button.classList.add("current");
				}
			}
			
			// create container
			con = createEL("div", {id:"webringwindow"}, [
				windowHeader,
				// inner container, making this scrollable instead of webringwindow will prevent the close button from scrolling as well
				createEL("div", {className:"webringlist", events:[["mouseover",function(){this.dataset.mouse=1;}],["mouseout",function(){delete this.dataset.mouse;}]]})
			]);
			document.body.appendChild(con);
		}
		
		return con;
	},
	// open webring window at a given position, and make sure it fits the screen
	openWindow: function(x, y, belowButton) {
		let con = webring.getWindow();
		con.classList.add("open");
		let inner = con.getElementsByClassName("webringlist")[0];
		
		webring.createWindowHtml();
		
		// reset positioning so we can get the container's natural size
		con.style.left = "0px";
		con.style.top = "0px";
		inner.style.maxWidth = "";
		inner.style.maxHeight = "";
		let minWidth = con.offsetWidth;
		let minHeight = con.offsetHeight;
		
		// set window position and make sure it fits to the browser window
		x = Math.min(x, window.innerWidth-minWidth);
		if (!belowButton || y > 50) { // avoid overlapping the button if it's close to the top of the page.
			y = Math.min(y, window.innerHeight-minHeight);
		}
		x = Math.floor(Math.max(0, x));
		y = Math.floor(Math.max(0, y));
		
		let headerHeight = con.getElementsByClassName("webringheader")[0].offsetHeight;
		con.style.left = x+"px";
		con.style.top = y+"px";
		inner.style.maxWidth = "calc(100vw - "+x+"px)";
		inner.style.maxHeight = "calc(100vh - "+y+"px - "+headerHeight+"px)";
	},
	// create a button that opens webring window
	createOpenButton: function() {
		let button = createEL("button", {type:"button", className:"webringopenbutton", event:["click", function(){
			let con = webring.getWindow();
			
			if (!con.classList.contains("open")) {
				// figure out whether to try to place the window above or below the button, depending on where the button is
				if (this.getBoundingClientRect().top < window.innerHeight/2) {
					webring.openWindow(this.getBoundingClientRect().left, this.getBoundingClientRect().bottom, true);
				}
				else {
					webring.openWindow(this.getBoundingClientRect().left, this.getBoundingClientRect().top-con.offsetHeight, false);
				}
			}
			else {
				con.classList.remove("open");
			}
		}]});
		
		// get current board name into the button
		let boardName = window.location.pathname.split('/')[1];
			button.textContent = 'Webring';
		
		return button;
	},
	// loop for updating the list, only call this once.
	update: function() {
		let updateInterval = 1000*60; // repeat every minute
		
		let con = webring.getWindow();
		// if webring hasn't been loaded for 10 minutes, reload it. Otherwise just rebuild the list to update timers.
		if (webring.jsonsLoaded+1000*60*10 < Date.now()) {
			let inner = con.getElementsByClassName("webringlist")[0];
			function rebuild() {
				// before rebuilding, wait until mouse is off the window, otherwise the list order may change as the user is about to click something
				if (inner.dataset.mouse) {
					requestAnimationFrame(rebuild);
				}
				else {
					webring.createWindowHtml();
					setTimeout(webring.update, updateInterval);
				}
			}
			webring.loadWebringJsons(rebuild);
		}
		else {
			webring.createWindowHtml();
			setTimeout(webring.update, updateInterval);
		}
	},
	// initialize, modify this however you want
	init: function() {
		// create webring open buttons
		for (let boardlist of document.getElementsByClassName('webring')){
			boardlist.appendChild(document.createTextNode('\u00A0'));
			boardlist.appendChild(webring.createOpenButton());
		}
		// webring.loadWebringJsons(); // optional: pre-load webring data at startup
		webring.update(); // optional: start auto-refresh loop (also pre-loads webring data)
	}
};
document.addEventListener('DOMContentLoaded', webring.init);
