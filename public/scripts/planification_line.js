var activate_MON = document.getElementById('planification_lines_ndb_activate_MON');
var activate_TUE = document.getElementById('planification_lines_ndb_activate_TUE');
var activate_WED = document.getElementById('planification_lines_ndb_activate_WED');
var activate_THU = document.getElementById('planification_lines_ndb_activate_THU');
var activate_FRI = document.getElementById('planification_lines_ndb_activate_FRI');
var activate_SAT = document.getElementById('planification_lines_ndb_activate_SAT');
var activate_SUN = document.getElementById('planification_lines_ndb_activate_SUN');

var timetable_MON = document.getElementById('planification_lines_ndb_timetable_MON');
var timetable_TUE = document.getElementById('planification_lines_ndb_timetable_TUE');
var timetable_WED = document.getElementById('planification_lines_ndb_timetable_WED');
var timetable_THU = document.getElementById('planification_lines_ndb_timetable_THU');
var timetable_FRI = document.getElementById('planification_lines_ndb_timetable_FRI');
var timetable_SAT = document.getElementById('planification_lines_ndb_timetable_SAT');
var timetable_SUN = document.getElementById('planification_lines_ndb_timetable_SUN');

if (activate_MON.checked) {
	timetable_MON.style.visibility = "visible";
} else {
	timetable_MON.style.visibility = "hidden";
}

if (activate_TUE.checked) {
	timetable_TUE.style.visibility = "visible";
} else {
	timetable_TUE.style.visibility = "hidden";
}

if (activate_WED.checked) {
	timetable_WED.style.visibility = "visible";
} else {
	timetable_WED.style.visibility = "hidden";
}

if (activate_THU.checked) {
	timetable_THU.style.visibility = "visible";
} else {
	timetable_THU.style.visibility = "hidden";
}

if (activate_FRI.checked) {
	timetable_FRI.style.visibility = "visible";
} else {
	timetable_FRI.style.visibility = "hidden";
}

if (activate_SAT.checked) {
	timetable_SAT.style.visibility = "visible";
} else {
	timetable_SAT.style.visibility = "hidden";
}

if (activate_SUN.checked) {
	timetable_SUN.style.visibility = "visible";
} else {
	timetable_SUN.style.visibility = "hidden";
}

activate_MON.addEventListener('change', function() {
	if (activate_MON.checked) {
		timetable_MON.style.visibility = "visible";
	} else {
		timetable_MON.style.visibility = "hidden";
	}
});

activate_TUE.addEventListener('change', function() {
	if (activate_TUE.checked) {
		timetable_TUE.style.visibility = "visible";
	} else {
		timetable_TUE.style.visibility = "hidden";
	}
});

activate_WED.addEventListener('change', function() {
	if (activate_WED.checked) {
		timetable_WED.style.visibility = "visible";
	} else {
		timetable_WED.style.visibility = "hidden";
	}
});

activate_THU.addEventListener('change', function() {
	if (activate_THU.checked) {
		timetable_THU.style.visibility = "visible";
	} else {
		timetable_THU.style.visibility = "hidden";
	}
});

activate_FRI.addEventListener('change', function() {
	if (activate_FRI.checked) {
		timetable_FRI.style.visibility = "visible";
	} else {
		timetable_FRI.style.visibility = "hidden";
	}
});

activate_SAT.addEventListener('change', function() {
	if (activate_SAT.checked) {
		timetable_SAT.style.visibility = "visible";
	} else {
		timetable_SAT.style.visibility = "hidden";
	}
});

activate_SUN.addEventListener('change', function() {
	if (activate_SUN.checked) {
		timetable_SUN.style.visibility = "visible";
	} else {
		timetable_SUN.style.visibility = "hidden";
	}
});
