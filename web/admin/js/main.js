$('document').ready(function(){
	$('body').on('change', '.js-grid-checkbox', function() {
		toogleGridActions($(this).closest('.grid-view'))
	})
	
	$('body').on('click', '._blank', function(e) {
		e.preventDefault();
		window.location = $(this).attr('href');
	})
	
	$('body').on('change', '.select-on-check-all', function() {
		toogleGridActions($(this).closest('.grid-view'))
	})
	
	$('body').on('change', '#event-event_id', function() {
		var t = $('#event-event_id :selected').text();
		if ($.trim($('#event-subject').val()) == '') {
			$('#event-subject').val(t);
		}
	})
	
	$('body').on('click', '.grid-actions .btn', function() {
		if ($(this).hasClass('disabled')) {
			return;
		}
		
		var _btn = $(this);
		
		var ids = [];
		$('.js-grid-checkbox').each(function() {
			if ($(this).is(':checked')) {
				ids.push($(this).val());
			}
		})		
		
		$.ajax({
			url: _btn.data('url'), 
			data: {'ids': ids}, 
			dataType: 'json', 
			statusCode: {
				404: function() {
					alert("page not found: " + _btn.data('url'));
				},
				500: function() {
					alert("Доступ запрещен");
				}
			},			
			success: function(response){
				if (response.success) {
					$("#" + _btn.data('grid-id')).yiiGridView("applyFilter");
				} else {
					alert(response.error);
				}
			}
		});
		
	})
	
	deleteFilePrevHidden();
	
	$('body').on('click', '.js-delete-image-attribute', function(e){
		e.preventDefault();
		_self = $(this);
		
		var data = {
			'model':  $(this).data('model'),
			'attribute':  $(this).data('attribute'),
			'size':  $(this).data('size'),
			'id':  $(this).data('id'),
			'i18n':  $(this).data('i18n'),
			'lang':  $(this).data('lang'),
		};
		
		$.post('/index.php?r=image-attribute/delete', data, function(response) {
			if (response.success) {
				_self.parent().html('');
			} else {
				alert(response.error);
			}			
		}, 'json');
	})
	
	$(".js-upload-image").change(function (e) {
		if(this.disabled) return alert('File upload not supported!');
		var F = this.files;
		if(F && F[0]) for(var i=0; i<F.length; i++) readImageAdaptive(F[i], $(this));
	});	
	
	
	$('input.js-file-image-upload').each(function(i,v){
		$('#yt'+$(this).attr('id')).attr('name', 'no-name');
	})	

	CKEDITOR.on('instanceReady', function (ev) {
		ev.editor.on('change', function () {
			if (!ev.editor.unsaved && ev.editor.checkDirty()) {
				$('#'+ev.editor.name).val(ev.editor.getData());
			}
		});
	});
	
})

function deleteFilePrevHidden() {
	$('input[type="file"]').each(function(i,v) {
		if ($(this).prev().attr('type') == 'hidden') {
			$(this).prev().remove();
		}
	})
}

function toogleGridActions(grid) {
	if ($('.js-grid-checkbox', grid).filter(":checked").length) {
		$('.grid-actions button', grid).removeClass('disabled');
	} else {
		$('.grid-actions button', grid).addClass('disabled');
	}
}


function readImageAdaptive(file, field) {
  
    var reader = new FileReader();
    var image  = new Image();
  
    reader.readAsDataURL(file);  
    reader.onload = function(_file) {
        image.src    = _file.target.result;              // url.createObjectURL(file);
        image.onload = function() {
			field.parent().find('.js-file-binary-data').val(this.src);
			field.parent().parent().find('.image-preview').html('<br/><img style="height:50px;" src="'+ this.src +'">');
			field.closest('.control-group').find('.js-filename').val(field.val().split('\\').pop());			
        };
        image.onerror= function() {
            alert('Invalid file type: '+ file.type);
        };      
    };   
}
