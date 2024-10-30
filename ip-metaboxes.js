var ipmb_field = '';

function ipmb_metabox_upload_insert(field) {
	ipmb_field = jQuery(field);
}

function ipmb_metabox_upload_remove(field) {
	ipmb_field = jQuery(field);
	if(jQuery(ipmb_field).hasClass('ipmb-metabox-upload-remove')) 	jQuery(ipmb_field).removeClass('ipmb-metabox-upload-remove').addClass('ipmb-metabox-upload-insert').find('img').remove();
	else 															jQuery(ipmb_field).addClass('insert-media');
	jQuery(ipmb_field).siblings().val('');
}

jQuery(document).ready(function($) {

	$('.ipmb-metabox-datepicker').datepicker({dateFormat: 'mm/dd/yy'});
	$('.ipmb-metabox').sortable({handle: '.ipmb-metabox-move'});
	
	window.ipmb_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html) {
		if(ipmb_field) {
      var attachment = false;
      if(html.match(/mp3="([^"]*)"/)) attachment = html.match(/mp3="([^"]*)"/)[1];
      else if (html.match(/src="([^"]*)"/)) attachment = html.match(/src="([^"]*)"/)[1];
      
			$(ipmb_field).addClass('ipmb-metabox-upload-inprogress');
			$.post(ajaxurl, {action:'ipmb_upload', attachment: attachment}, function(response) {
				$(ipmb_field).removeClass('ipmb-metabox-upload-inprogress ipmb-metabox-upload-insert insert-media')
							 .addClass('ipmb-metabox-upload-remove')
							 .attr('onclick', 'ipmb_metabox_upload_remove(this); return false;')
							 .append(response.split('|')[1])
							 .siblings().val(response.split('|')[0]);
				
				ipmb_field = '';
			});
		} else {
			window.ipmb_send_to_editor(html);
		}
	}
	
	$(document).on('click', '.ipmb-metabox-add', function() {
		var total_metaboxes = $(this).parents('.ipmb-metabox').find('dl').length + 1;
		$(this).parents('.ipmb-metabox').siblings('input').val(total_metaboxes);
		
		var metabox = $(this).parents('.ipmb-metabox').find('dl:first-child').clone();
		$('input[type="text"], select, textarea', $(metabox)).val('');
		$('.ipmb-metabox-datepicker', $(metabox)).removeClass('hasDatepicker').attr('id', null).datepicker({dateFormat: 'mm/dd/yy'});
		$('input[type="checkbox"]', $(metabox)).each(function() {
			$(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '[' + (total_metaboxes - 1) + ']'))
				   .attr('checked', null);
		});
		$('input[type="radio"]', $(metabox)).each(function() {
			$(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '[' + (total_metaboxes - 1) + ']'))
				   .attr('checked', null);
		});
		$('select[multiple="multiple"]', $(metabox)).each(function() {
			$(this).attr('name', $(this).attr('name').replace(/\[\d+\]/, '[' + (total_metaboxes - 1) + ']'))
				   .find('option').attr('selected', null);
		});
		$('.ipmb-metabox-upload', $(metabox)).each(function() {
			$(this).attr('id', $(this).attr('id').replace(/\d+$/, (total_metaboxes - 1)))
				   .removeClass('ipmb-metabox-upload-remove')
				   .addClass('ipmb-metabox-upload-insert insert-media')
				   .find('img').remove();
			$(this).siblings().val('');
		});
		$(this).parents('.ipmb-metabox').append($(metabox));
		
		return false;
	})
	
	.on('click', '.ipmb-metabox-remove', function() {
		$(this).parents('.ipmb-metabox').siblings('input').val(function() {
			return parseInt($(this).val()) - 1;
		});
		$(this).parents('dl').remove();
		
		return false;
	})
	
	.on('click', '.ipmb-metabox-collapse', function() {
		var _this = this;
		var parent = $(_this).parents('dl');
		if($(_this).find('a').text() == 'Collapse') {
			parent.animate({'height': $(parent).find('dd:nth-child(2)').height()}, function() {
				$(_this).html(' &nbsp; | &nbsp; <a href="#">Expand</a>');
			});
		} else {
			parent.animate({'height': '100%'}, function() {
				$(_this).html(' &nbsp; | &nbsp; <a href="#">Collapse</a>');
			});
		}
		
		return false;
	})
	
	.on('click', '.ipmb-metabox-collapse-all', function() {
		$('.ipmb-metabox-collapse').each(function() {
			var _this = this;
			var parent = $(_this).parents('dl');
			$(_this).parents('dl').animate({'height': $(parent).find('dd:nth-child(2)').height()}, function() {
				$(_this).html(' &nbsp; | &nbsp; <a href="#">Expand</a>');
			});
		});
		
		return false;
	})
	
	.on('click', '.ipmb-metabox-expand-all', function() {
		$('.ipmb-metabox-collapse').each(function() {
			var _this = this;
			var parent = $(_this).parents('dl');
			$(_this).parents('dl').animate({'height': '100%'}, function() {
				$(_this).html(' &nbsp; | &nbsp; <a href="#">Collapse</a>');
			});
		});
		
		return false;
	})
	
	.on('click', '.ipmb-delete', function() {
		if(confirm('Do you really want to delete this metabox?')) {
			return true;
		} else {
			return false;
		}
	})
	
	.on('click', '.button-primary', function() {
		if($('#ipmb-name').val() == '') {
			alert('You must enter metabox name');
			return false;
		}
		
		var names = $('.ipmb-fields-name').map(function(){ return $(this).val(); }).get();
		if (names.indexOf('') != -1) {
			if(names.length == 1) alert('You must enter at least a field name');
			else alert('You must enter all field names');
			return false;
		}
		
		var fields_error = false;
		var fields = [];
		$('.ipmb-fields tbody tr').each(function(index) {
			if(names.lastIndexOf($(this).find('.ipmb-fields-name').val()) != index) {
				fields_error = true;
				return;
			}
		
			fields.push({
				'name' 			: $(this).find('.ipmb-fields-name').val(),
				'type'			: $(this).find('.ipmb-fields-type').val(),
				'options'		: $(this).find('.ipmb-fields-options').val(),
				'description' 	: $(this).find('.ipmb-fields-description').val()
			});
		});
		
		if(fields_error) {
			alert('You have duplicated field name');
			return false;
		}
		
		$('.ipmb-fields').val(JSON.stringify(fields));
	})
	
	.on('click', '.ipmb-fields-add', function() {
		var field = $('.ipmb-fields tbody tr:first-child').clone().insertAfter($(this).parents('tr'));
		$('input, select', $(field)).val('');
		$('code', $(field)).html('&nbsp;');
		return false;
	})
	
	.on('click', '.ipmb-fields-remove', function() {
		$(this).parents('tr').remove();
		return false;
	});
});