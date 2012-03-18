$(document).ready(function () {
    $('input,textarea').bind('focus blur', function (ev) {
	var s = $(this);
	var p = s.parents('.row');
	s.toggleClass('active');
	p.toggleClass('active');
	p.children('.desc').toggle();

	console.log(s.siblings('.desc'));
    });
});