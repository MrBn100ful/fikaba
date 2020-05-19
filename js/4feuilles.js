var Alltext = "";
$(function() {
	
var value = readCookie("theme");
	
if (value == null) {
		changeCSS("https://4feuilles.org/css/4feuilles_claire.css", 0);
} else {
        changeCSS(value, 0);
}
	
var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

if (isMobile) {
  			changeCSS('https://4feuilles.org/css/4feuilles_mobile.css',0);
			document.cookie='theme=https://4feuilles.org/css/4feuilles_mobile.css';
};
});

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function changeCSS(cssFile, cssLinkIndex) {

    var oldlink = document.getElementsByTagName("link").item(cssLinkIndex);

    var newlink = document.createElement("link");
    newlink.setAttribute("rel", "stylesheet");
    newlink.setAttribute("type", "text/css");
    newlink.setAttribute("href", cssFile);

    document.getElementsByTagName("head").item(0).replaceChild(newlink, oldlink);
    
    document.cookie='theme='+cssFile+';domain=.4feuilles.org;path=/';
}

function getCookie(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}


function mobile(x) {
  document.getElementById("mobile-cacher").classList.toggle("show");
};


function hideform(x) {
  document.getElementById("postarea-hidden").classList.toggle("show");
  const fileInput = document.querySelector('.label-file input[type=file]');
  fileInput.onchange = () => {
    if (fileInput.files.length > 0) {
      const fileName = document.querySelector('.file-name');
      fileName.textContent = fileInput.files[0].name;
    }
  }

};


function addref(text) {
    Alltext += text;
    document.getElementById("com").value = Alltext;
}







