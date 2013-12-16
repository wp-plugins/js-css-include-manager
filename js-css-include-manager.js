jQuery(document).ready(function($) {

	// location change
	$(document).on("click", "input[class=location_radio]", function() {
		var $RadioList = $(this).parent().parent().parent();
		$RadioList.children("li").each( function() {
			$(this).find("input[type=text]").addClass("disabled");
			$(this).find("input[type=text]").prop("disabled", true);
		});
		$(this).parent().parent().find("input[type=text]").removeClass("disabled");
		$(this).parent().parent().find("input[type=text]").prop("disabled", false);
	});
	
	// show description of condition
	$(document).on("click", ".wrap_jcim .condition_desc_show", function() {
		$(this).parent().parent().find(".condition_desc").slideToggle();
		return false;
	});

	// delete
	$(document).on("click", "a.delete.button", function() {

		if( confirm( js_css_include_manager.delete_cofirm ) ){

			var $DeleteTr = $(this).parent().parent();
			var DeleteID = $DeleteTr.prop("id").replace( "data_" , "" );

			$DeleteTr.find(".spinner").css("display", "inline");
			$DeleteTr.css("background-color", "#E6E6E6");
			var PostData = {
				action: 'jcim_delete_line',
				nonce: js_css_include_manager.nonce,
				data: {
					delete_id: DeleteID
				}
			};

			$.post( js_css_include_manager.ajax_url , PostData , function( response ) {
				if( typeof( response ) == 'object' && response.success ) {
					$DeleteTr.fadeOut( "middle" , function() {
						$DeleteTr.remove();
					});
				}
			});

			return false;

		} else {

			return false;

		}

	});

	// edit
	$(document).on("click", "a.edit", function() {
		var $EditTr = $(this).parent().parent();
		var EditID = $EditTr.prop("id").replace( "data_" , "" );
		
		$EditTr.find(".spinner").css("display", "inline");

		var PostData = {
			action: 'jcim_get_edit_line',
			nonce: js_css_include_manager.nonce,
			data: {
				edit_id: EditID,
				alternate: $EditTr.hasClass("alternate")
			}
		};

		$.post( js_css_include_manager.ajax_url , PostData , function( response ) {
			$EditTr.find(".spinner").css("display", "none");
			$EditTr.hide();
			$EditTr.after( response );
		});

		return false;
	});

	// cancel
	$(document).on("click", "a.cancel", function() {
		var $CancelTr = $(this).parent().parent().parent().parent();
		var CancelID = $CancelTr.prop("id").replace( "data_inline_" , "" );
		
		$CancelTr.remove();
		$("tr#data_" + CancelID ).show();

		return false;
	});

	// update
	$(document).on("submit", "form[name=update_row]", function() {

		var $UpdateTr = $(this).parent().parent();
		var UpdateID = $UpdateTr.prop("id").replace( "data_inline_" , "" );
		
		$UpdateTr.find(".spinner").css("display", "inline");

		var PostData = {
			action: 'jcim_update_line',
			nonce: js_css_include_manager.nonce,
			data: {
				update_id: UpdateID,
				data_ver: $(this).find("input[name=data_ver]").val(),
				use: $(this).find("select[name=use] :selected").val(),
				filetype: $(this).find("select[name=filetype] :selected").val(),
				output: $(this).find("select[name=output] :selected").val(),
				condition: $(this).find("select[name=condition] :selected").val(),
				location_num: $(this).find("input[name=location_num]:checked").val(),
			}
		};
		PostData.data.location_name = $(".inline-edit-col-right .inline-edit-col ul li:eq(" + ( PostData.data.location_num - 1 ) + ")", $(this)).find("input[name=location_name]").val();
		
		$.post( js_css_include_manager.ajax_url , PostData , function( response ) {
			$UpdateTr.remove();
			$("tr#data_" + UpdateID).replaceWith( response );
			$("tr#data_" + UpdateID).fadeIn();
		});

		return false;

	});

	// donation width
	function donation_toggle_set( s ) {
		if( s ) {
			$(".columns-2").addClass('full-width');
		} else {
			$(".columns-2").removeClass('full-width');
		}
	}

	// donation toggle
	$(document).on("click", "#about_plugin .toggle-plugin a", function() {
		
		if( $(".columns-2").hasClass('full-width') ) {
			donation_toggle_set( false );
			$.post(js_css_include_manager.ajax_url, {
				'action': 'jcim_set_donation_toggle',
				'f': 0,
			});

		} else {
			donation_toggle_set( true );
			$.post(js_css_include_manager.ajax_url, {
				'action': 'jcim_set_donation_toggle',
				'f': 1,
			});
		}

		return false;
	});

});
