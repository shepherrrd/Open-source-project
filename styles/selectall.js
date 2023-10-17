function selectAll(selectBox,selectAll) {
	if (typeof selectBox == "string") {
		selectBox = document.getElementById(selectBox);
	}
	if (selectBox.type == "select-multiple") {
		for (var i = 0; i < selectBox.options.length; i++) {
			selectBox.options[i].selected = selectAll;
		}
	}
}
function disabled_select(selectBox,valeur) {
	selectBox = document.getElementById(selectBox);
	selectBox.disabled = valeur;
}
function disabled_text_input(val1,val2,val3,val4) {
	input1 = document.getElementById('id_access');
	input1.disabled = val1;
	input2 = document.getElementById('ip_access');
	input2.disabled = val2;
	input3 = document.getElementById('date_1');
	input3.disabled = val3;
	input4 = document.getElementById('date_2');
	input4.disabled = val4;
}
