StarOutUrl= 'images/others/star_out.gif';
StarOverUrl= 'images/others/star_over.gif';
StarBaseId= 'Star';
NbStar= 6;
bool_init = 0;
LgtStarBaseId=StarBaseId.lastIndexOf('');
function is_child_of(parent, child) {
	if( child != null ) {			
		while( child.parentNode ) {
			if( (child = child.parentNode) == parent ) {
				return true;
			}
		}
	}
	return false;
}
function old_rating(element, event, rating) {
	var current_mouse_target = null;
	if( event.toElement ) {				
		current_mouse_target 			 = event.toElement;
	} else if( event.relatedTarget ) {				
		current_mouse_target 			 = event.relatedTarget;
	}
	if( !is_child_of(element, current_mouse_target) && element != current_mouse_target ) {
		for (i=1;i<NbStar+1;i++) {
			if (i <= rating)
				document.getElementById('Star'+i).src =StarOverUrl;
			else
				document.getElementById('Star'+i).src =StarOutUrl;
		}
		bool_init = 0;
	}
}
function NotationSystem(lien) {
	for (i=1;i<NbStar+1;i++) {
		var img =document.getElementById('Star'+i);
		img.onclick =function() {window.location.href = lien+'&rating='+Name2Nb(this.id);};
		img.onmouseover =function() {StarOver(this.id);};
		img.onmouseout =function() {StarOut(this.id);};
	}
}
function init_stars() {
	if (bool_init == 0){
		bool_init = 1;
		for (i=1;i<NbStar+1;i++) {
			document.getElementById('Star'+i).src =StarOutUrl;
		}
	}
}
function StarOver(Star) {
	document.body.style.cursor='pointer';
	StarNb=Name2Nb(Star);
	for (i=1;i<(StarNb*1)+1;i++) {
		document.getElementById('Star'+i).src=StarOverUrl;
	}
}
function StarOut(Star) {
	document.body.style.cursor='default';
	StarNb=Name2Nb(Star);
	for (i=1;i<(StarNb*1)+1;i++) {
		document.getElementById('Star'+i).src=StarOutUrl;
	}
}
function Name2Nb(Star) {
	StarNb=Star.slice(LgtStarBaseId);
	return(StarNb);
}