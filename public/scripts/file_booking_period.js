var before = document.getElementById('file_booking_period_before');
var beforeNumber = document.getElementById('file_booking_period_beforeNumber');
var beforeType = document.getElementById('file_booking_period_beforeType');

var after = document.getElementById('file_booking_period_after');
var afterNumber = document.getElementById('file_booking_period_afterNumber');
var afterType = document.getElementById('file_booking_period_afterType');

if (before.checked) {
	beforeNumber.disabled = false;
	beforeType.disabled = false;
} else {
	beforeNumber.disabled = true;
	beforeType.disabled = true;
}

if (after.checked) {
	afterNumber.disabled = false;
	afterType.disabled = false;
} else {
	afterNumber.disabled = true;
	afterType.disabled = true;
}

before.addEventListener('change', function() {
	// alert(before.checked);
	if (before.checked) {
		beforeNumber.disabled = false;
		beforeType.disabled = false;
	} else {
		beforeNumber.disabled = true;
		beforeType.disabled = true;
	}
});

after.addEventListener('change', function() {
	// alert(after.checked);
	if (after.checked) {
		afterNumber.disabled = false;
		afterType.disabled = false;
	} else {
		afterNumber.disabled = true;
		afterType.disabled = true;
	}
});
