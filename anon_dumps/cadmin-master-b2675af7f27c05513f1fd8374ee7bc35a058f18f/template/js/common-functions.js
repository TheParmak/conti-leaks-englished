var closeModalDialog = function(e) {
		if (e.target == e.currentTarget || arguments[1] && arguments[1] == 'yes') {
				$(".modal-bg, .dialog").hide();
				$("body").css("overflow-y", "auto");
				$(".modal-bg").css("overflow-y", "hidden");
		}
};

var showModalDialog = function(target_selector) {
		$(".modal-bg").show();
		$(target_selector).show();
		$("body").css("overflow-y", "hidden");
		$(".modal-bg").css("overflow-y", "auto");
};

var listComment = function (items, dialogs) {
		var data = "<ul>";
		var created_time = "</li>";
		if (items.length) {
				$.each(items, function (i, v) {
					//To not displayed "undefined" when creating comments
					if ( (typeof(v.created_at) != "undefined")  && (v.created_at != "") ){ created_time = " ("+v.created_at+") </li>"; }
					data += "<li class='wrap-word'><b>[" + (v.user || "me") + "]</b>: " + v.msg + created_time;
				});
				data += "</ul>";
				dialogs.notify("Comments list", data, {});
		}
};

var commentListGenerator = function (items, need_reverse, shorted) {
		var com = "";
		var created_time = " \n ";
		if (items.length) {
				if (need_reverse) { items.reverse(); }
				$.each(items, function (i, v) {
					//To not displayed "undefined" when creating comments
					if ( (typeof(v.created_at) != "undefined") && (v.created_at != "") ){ created_time = " ("+v.created_at+") \n "; }
					else { created_time = " \n " }
					if ( v.msg != "" ) { com += "[ " + (v.user || "Me") + " ]: " + v.msg + created_time; }
				});

		};
		if ((typeof shorted != "undefined") && (com.length > shorted+3)) {
			com = com.substring(0, Number(shorted));
			com += "...";
		}
		return com;
};
