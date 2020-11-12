var Alltext = "";

document.addEventListener('DOMContentLoaded', function () {

	var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

	if (isMobile) {
		document.getElementById("navpc").remove();
		document.getElementById("navmobile").style.display = "block";
	};

	if (localStorage.getItem('theme') === "dark") {
		document.body.classList.toggle(localStorage.getItem('theme'));
		$('.ui.checkbox').checkbox('check');
	}
	$('.ui.sticky').sticky();

var cursorX;
var cursorY;
document.onmousemove = function(e){
    cursorX = e.pageX;
    cursorY = e.pageY;
}

function preview(no) {
	var div = document.createElement('div');
	div.id = 'tmp';
	div.style.display = "inline";
	div.style.position = "absolute";
	div.style.top = cursorY  + "px";
	div.style.left = cursorX + "px";
	div.style.zIndex = "-1500";
	document.body.appendChild(div);
	l = location.href;
	l = l.substring(0, l.indexOf('?'));
	$( "#tmp" ).load(  l + "?mode=preview&nbpost=" + no );
	setTimeout(() => {  resize(); }, 350);
}

function resize(){
	var element = document.getElementById("tmp");
	s = element.style.top;
	s = s.substring(0, s.indexOf('p'));
	element.style.top = (s - element.offsetHeight) + "px";
	element.style.zIndex = "999";
}

function removepreview(){
	var element = document.getElementById("tmp");
    element.parentNode.removeChild(element);
}
    
var tag = $('[id^="tag"]');
$( tag ).hover(function(){
	var idStr = this.id;
	idStr = idStr.replace('tag','');
    preview(idStr);
    }, function(){
	removepreview();
});

	const themeSwitcher = document.getElementById('theme-switcher');
	if (themeSwitcher) {
		themeSwitcher.addEventListener('click', function () {
			document.body.classList.toggle('dark');
			localStorage.setItem('theme', document.body.classList);
		})
	}

	var element = document.getElementById("submitbtn");
	if (element) {
		element.onclick = validate;
	}
	$('.ui.checkbox')
		.checkbox()
	;

	const queryString = window.location.search;
	const urlParams = new URLSearchParams(queryString);
	const down = urlParams.get('down')
	if (down =="true"){
		$(document).scrollTop($(document).height());
	}
});

function changeCSS(cssFile, cssLinkIndex) {

	var oldlink = document.getElementsByTagName("link").item(cssLinkIndex);

	var newlink = document.createElement("link");
	newlink.setAttribute("rel", "stylesheet");
	newlink.setAttribute("type", "text/css");
	newlink.setAttribute("href", cssFile);

	document.getElementsByTagName("head").item(0).replaceChild(newlink, oldlink);

}

function mobile(x) {
	document.getElementById("mobile-cacher").classList.toggle("show");
};


function hideform(x) {
	document.getElementById("postarea-hidden").classList.toggle("show");
	const fileInput = document.querySelector('.label-file input[type=file]');
	$( "#draggable" ).draggable();
	fileInput.onchange = () => {
		if (fileInput.files.length > 0) {
			const fileName = document.querySelector('.file-name');
			fileName.textContent = fileInput.files[0].name;
		}
	}

};

function openform(x){
	document.getElementById("postarea-hidden").classList.add("show");
	const fileInput = document.querySelector('.label-file input[type=file]');
	$( "#draggable" ).draggable();
	fileInput.onchange = () => {
		if (fileInput.files.length > 0) {
			const fileName = document.querySelector('.file-name');
			fileName.textContent = fileInput.files[0].name;
		}
	}
	
}

function addref(text) {
	$("#com").val($("#com").val() + text);
}

function styletext(type) {
	var textarea = document.getElementById("com");
	var selection = (textarea.value).substring(textarea.selectionStart,textarea.selectionEnd);
	text = $('#com').val();
	edittext = text.replace(selection, type + selection + type);
	$('#com').val(edittext);
}

function validate(event) {
	event.preventDefault();
	hcaptcha.execute();
}

function onSubmit(token) {
	document.getElementById('form-board').submit();
}



