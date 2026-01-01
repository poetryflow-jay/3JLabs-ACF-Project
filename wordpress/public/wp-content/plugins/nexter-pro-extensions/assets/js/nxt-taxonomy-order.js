function nxt_taxonomy_order_change(element) {
    jQuery('#nxt_taxonomy_order_form #cat').val(jQuery("#nxt_taxonomy_order_form #cat option:first").val());
    jQuery('#nxt_taxonomy_order_form').submit();
}

var nxt_array_to_obj_conv = function(array) {
	var element_object = new Object();

	if (typeof array == "object") {
		for (var i in array) {
			var element = nxt_array_to_obj_conv(array[i]);
			element_object[i] = element;
		}
	} else {
		element_object = array;
	}

	return element_object;
}