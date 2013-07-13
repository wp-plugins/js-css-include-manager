jQuery(document).ready(function($) {

	var $Form = $("#js_css_include_manager_form");
	
	// create submit
	$("p.submit input:button").click(function() {
		$Form.submit();
	});

	// location change
	var $Location = $Form.children("div").children("table").children("tbody").children("tr").children("td").children("ul").children("li");
	$Location.children("label").children("input[type=radio]").click(function() {
		$(this).parent().parent().parent().children("li").each(function() {
			$(this).children("input[type=text]").addClass("disabled");
			$(this).children("input[type=text]").attr("disabled", "disabled");
		});
		$(this).parent().parent().children("input[type=text]").removeClass("disabled");
		$(this).parent().parent().children("input[type=text]").attr("disabled", false);
	});

	// update
	var $UpdateTr = $Form.children("div#update").children("table").children("tbody").children("tr");
	$UpdateTr.children("td.use").children("select").hide();
	$UpdateTr.children("td.filetype").children("select").hide();
	$UpdateTr.children("td.output").children("select").hide();
	$UpdateTr.children("td.condition").children("select").hide();
	$UpdateTr.children("td.location").children("ul").hide();
	$UpdateTr.children("td.operation").children("p.submit").hide();

	$UpdateTr.children("td.operation").children("span").children("a.edit").click(function() {
		var $ParentTr = $(this).parent().parent().parent();
		$ParentTr.children("td.use").children("span").hide();
		$ParentTr.children("td.use").children("select").show();
		$ParentTr.children("td.filetype").children("span").hide();
		$ParentTr.children("td.filetype").children("select").show();
		$ParentTr.children("td.output").children("span").hide();
		$ParentTr.children("td.output").children("select").show();
		$ParentTr.children("td.condition").children("span").hide();
		$ParentTr.children("td.condition").children("select").show();
		$ParentTr.children("td.location").children("span").hide();
		$ParentTr.children("td.location").children("ul").show();
		$(this).parent().hide();
		$(this).parent().parent().children("p.submit").show();
		
		return false;
	});

});
