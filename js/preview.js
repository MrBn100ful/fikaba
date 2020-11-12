document.addEventListener('DOMContentLoaded', function () {


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
	div.style.top = cursorY + "px";
	div.style.left = cursorX + "px";
	document.body.appendChild(div);
	l = location.href;
	l = l.substring(0, l.indexOf('?'));
	$( "#tmp" ).load(  l + "?mode=preview&nbpost=" + no );
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

});