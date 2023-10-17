function getElementsByClassName(className, tag, elm){
	var testClass = new RegExp("(^|s)" + className + "(s|$)");
	var tag = tag || "*";
	var elm = elm || document;
	var elements = (tag == "*" && elm.all)? elm.all : elm.getElementsByTagName(tag);
	var returnElements = [];
	var current;
	var length = elements.length;
	for(var i=0; i<length; i++){
		current = elements[i ];
		if(testClass.test(current.className)){
			returnElements.push(current);
		}
	}
	return returnElements;
}

function DisplayHide(clas, id){
var i;
	var lay=document.getElementById(id);
	var list_lay=getElementsByClassName(clas,'div');
	lay.style.display='block';
	for(i=0;i<list_lay.length;i++)
		list_lay[i].style.display='none';
	lay.style.display='block';
}
function DisplayHideDivImg(id_div){
	var lay=document.getElementById(id_div);
	var img_open = "images/others/open.png";
	var img_close = "images/others/close.png";
	if(lay.style.display=='none'){
		lay.style.display='block';
		document.getElementById('open_close_'+id_div).src = img_close;
	}
	else{
		lay.style.display='none';
		document.getElementById('open_close_'+id_div).src = img_open;
	}
}
