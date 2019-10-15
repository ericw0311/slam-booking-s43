document.getElementById("query_booking_name").focus();

var periodType = document.getElementById('query_booking_periodType');
var beginningDate = document.getElementById('query_booking_beginningDate');
var endDate = document.getElementById('query_booking_endDate');

var periodTypeValue = periodType.options[periodType.selectedIndex].value;

if (periodTypeValue != "BETWEEN" && periodTypeValue != "AFTER") {
	beginningDate.disabled = true;
}
if (periodTypeValue != "BETWEEN" && periodTypeValue != "BEFORE") {
	endDate.disabled = true;
}

periodType.addEventListener('change', function() {
	var periodTypeValue = periodType.options[periodType.selectedIndex].value;
	if (periodTypeValue == "BETWEEN" || periodTypeValue == "AFTER") {
		beginningDate.disabled = false;
	} else {
		beginningDate.value = "";
		beginningDate.disabled = true;
	}

	if (periodTypeValue == "BETWEEN" || periodTypeValue == "BEFORE") {
		endDate.disabled = false;
	} else {
		endDate.value = "";
		endDate.disabled = true;
	}
});
