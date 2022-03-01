///////////////////////////////////////////////////////////////////////////////
// Validate functions
//
var isExist = function(val){
	if ((typeof(val) === "undefined") || (val == null)){ return false; }
	else { return true; }
}

var isNumeric = function(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

var closeModalDialog = function(e) {
	let parent_bg;
	let current_target = e.target || e.currentTarget;
	if (!($(current_target).hasClass("modal-bg"))){
		current_target = $(current_target).parents(".dialog");
		parent_bg = current_target.parents(".modal-bg");
	}
	if (e.target == e.currentTarget || arguments[1] && arguments[1] == 'yes') {
		if ($(current_target).hasClass("modal-bg")){ $(".modal-bg, .dialog").hide(); }
		else {
			current_target.hide();
			parent_bg.hide();
		}
		$("body").css("overflow-y", "auto");
		$(".modal-bg").css("overflow-y", "hidden");
	}
};

var showModalDialog = function(target_selector) {
		let parent_bg;
		let current_target = $(target_selector);
		if (current_target.parent(".modal-bg").length > 0){
			parent_bg = current_target.parent(".modal-bg");
		}
		else {
			parent_bg = current_target.parent(".container").parent(".modal-bg");
		}
		parent_bg.show();
		current_target.show();
		$("body").css("overflow-y", "hidden");
		$(".modal-bg").css("overflow-y", "auto");
};
