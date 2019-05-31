
var sendForm = function(e) {
	e.preventDefault();
	var t = $(this);
	var data = t.serialize();
	$.ajax({
		url : t.attr('action'),
		type : 'POST',
		data : data,
		dataType : 'html',
		success : function(response) {
			$('.response #block').html(response);
		}
	});
}

$(document).ready(function() {
	$('#apiform').bind('submit', sendForm);
});