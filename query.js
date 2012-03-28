function checked(name) {
    var a = [];

    $("input[name='" + name + "']:checked").each(function() {
	a.push($(this).val());
    });
    return a;
}

function read_num(name) {
    return parseInt($("input[name='" + name + "']").val());
}

function read_atoms(name) {
    var a = $("input[name='" + name + "']").val().split(/ *, */);
    return $.grep(a, function (n) {  return n; });
}

function form_value() {
    return {
	num_exercises: read_num('num_exercises') * 1,
	num_students: 1,
	atoms: read_atoms('atoms'),
	compl_min: read_num('compl_min'),
	compl_max: read_num('compl_max'),
	num_premises: read_num('num_premises'),
	//message: $("input[textarea=message]"),
	conectives: checked('conectives'),
	restrictions: checked('restrictions')
    };
}

var test;

$(document).ready(function () {
     $('input,textarea').bind('focus blur', function (ev) {
	var s = $(this);
	var p = s.parents('.row');
	s.toggleClass('active');
	p.toggleClass('active');
	p.children('.desc').toggle();
     }); 

    $("a#send").hover(function (ev) {
	$(this).toggleClass('hover');
    });

    $("a#send").click(function (ev) {
	console.log(form_value());

	$.ajax({
	    url:"generate.php",
	    type: "POST",
	    contentType: "application/json; charset=utf-8",
	    dataType: "json",
	    data: JSON.stringify(form_value()),
	    error: function(obj, status) {
			alert("Erro ao enviar os dados: " + status);
	    },
	    success: function(data) {
		console.log($('#results'));
		$('#results').show().append('<pre>' + JSON.stringify(data, null, 4));
		console.log(data);

		test = data;
	    }
	});
    });

    $('input[name=num_exercises]').bind('blur', function (ev) {
	var v = $(this).val() || 0;
	$(this).val(v);
	$('span#num_total').text($(this).val() * 1);
    });
});
