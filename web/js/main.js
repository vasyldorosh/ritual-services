$(function() {

	$('.mask-phone').mask("+38(999) 999-99-99");	
	
	$('#form-contact').submit(function(e){
	  e.preventDefault();
		
	   var _form = $(this);
	   $.post('/?r=site/contact&lang='+$('html').attr('lang'), _form.serialize(), function(response){
			
			$('.error', _form).removeClass('error');
		
			if (response.success) {
				_form.html(response.message);
			} else {
				$.each(response.errors, function(i,v) {
					field = $('#contact-'+i);
					field.addClass('error').attr('placeholder', v);	
					field.data('value', field.val());
					field.val('');
				})			
			}
		}, 'json');	
		
	})
	
	$('#form-contact input').focus(function(){
		if ($(this).hasClass('error')) {
			$(this).removeClass('error'); 
			$(this).attr('placeholder', $(this).data('placeholder')); 
			$(this).val($(this).data('value')); 
		}
	})
	
})