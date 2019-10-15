document.getElementById("user_modify_firstName").focus();

var accountType = document.getElementById('user_modify_accountType');
var uniqueName = document.getElementById('user_modify_uniqueName');

var accountTypeValue = accountType.options[accountType.selectedIndex].value;

if (accountTypeValue == "INDIVIDUAL") {
	uniqueName.disabled = true;
} else {
	uniqueName.disabled = false;
}

accountType.addEventListener('change', function() {
	var accountTypeValue = accountType.options[accountType.selectedIndex].value;
	if (accountTypeValue == "INDIVIDUAL") {
		uniqueName.value = "";
		uniqueName.disabled = true;
	} else {
		uniqueName.disabled = false;
	}
});
