(function ($) {
    var galleryDefaults = {
        csrfToken: null,
        csrfTokenName: null,
		labelCreate: '',
		labelUpdate: '',
        labelAttributes: [],
        textareaAttributes: [],
        editorAttributes: [],
        inputAttributes: [],
        multilingual: true,
       
		createUrl: '',
        deleteUrl: '',
        updateUrl: '',
        arrangeUrl: '',
        getUrl: '',
 
		items: [],
        languages: [],
        language: 'ru',
		modelName: '',
		fullModelName: '',
		modelAttribute: 'owner_id'
    };
	
	$('body').on('click', '.js-btn-rotate', function (){
		var _img = $(this).next();
		var angle = _img.data('angle');
		var attr  = $(this).data('attr');
		
		angle = (angle + 90) % 360;
		_img.attr('class', "rotate" + angle);
		_img.data('angle', angle);
		
		$('#'+$(this).data('id')).val(angle);
		
		$('#vstab_'+attr).val($('#vstab_'+attr+'_base64').val());
	})	

    function vstabManager(el, options) {
		//Extending options:
        var opts = $.extend({}, galleryDefaults, options);
		
        var csrfParams = opts.csrfToken ? '&' + opts.csrfTokenName + '=' + opts.csrfToken : '';
        var items = {}; // photo elements by id
        var $vstab = $(el);

        var $sorterVsTab = $('.sorter', $vstab);
        var $itemsVsTab = $('.images', $sorterVsTab);
        var $editorModal = $('.editor-modal', $vstab);
        var $progressOverlay = $('.progress-overlay', $vstab);
        var $uploadProgress = $('.upload-progress', $progressOverlay);
        var $editorForm = $('.form', $editorModal);

        function htmlEscape(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

		
        function createEditorElement(mode, params) {
			
            model_id = (typeof(params['id']) != 'undefined') ? params['id'] : 0;
			
			var ckeditorIds 	= [];
			var chosenIds 		= [];
			var selectValues 	= [];
			var c_class = '';
			var f_class = '';
			var ft_class = '';
			
			var html = '<ul id="tab-vs'+model_id+'" class="nav nav-tabs">' +
                '<li class="active"><a href="#tab-vs-'+opts.language+'-'+model_id+'" data-toggle="tab" aria-expanded="false">'+opts.labelItem+'</a></li>';
				
				if (opts.multilingual) {				
					$.each(opts.languages, function(i, n){
						html += '<li><a href="#tab-vs-'+i+'-'+model_id+'" data-toggle="tab" aria-expanded="false">'+i+'</a></li>';
					})
				}
				
				html += '</ul>';
				html += '<div class="tab-content gallery-form" style="overflow: visible;">';
						
				html  +='<div id="tab-vs-'+opts.language+'-'+model_id+'" class="tab-pane active">';
	
					
					$.each(opts.selectAttributes, function(attr, selectParams){
						 value 		= (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
						 s_multiple = (typeof(selectParams.multiple) != 'undefined' && selectParams.multiple) ? true : false;	
						 s_chosen = (typeof(selectParams.chosen) != 'undefined' && selectParams.chosen) ? true : false;	
						 c_class = (typeof(selectParams.c_class) != 'undefined' && selectParams.c_class) ? selectParams.c_class : '';	
						 f_class = (typeof(selectParams.f_class) != 'undefined' && selectParams.f_class) ? selectParams.f_class : '';	
						 ft_class = (typeof(selectParams.ft_class) != 'undefined' && selectParams.ft_class) ? selectParams.ft_class : '';	
						 s_id       = 'vstab_'+attr;
							
						 s_multiple_arr = s_multiple ? '[]':'';
						 s_multiple_attr = s_multiple ? 'multiple="multiple"':'';
							
						 if (s_chosen) {
							 chosenIds.push(s_id);
						 }		
							 
						 html  +='<div class="'+c_class+'"><label for="'+s_id+'">'+opts.labelAttributes[attr]+':</label>' +
								 '<select data-toogle-class="'+ft_class+'" class="'+f_class+'" name="'+options.modelName+'['+attr+']'+s_multiple_arr+'" '+s_multiple_attr+'  style="min-width: 480px" value="' + htmlEscape(value) + '" id="'+s_id+'">';
								
								if (!s_chosen) {
									html += '<option value="">-</option>';	
								}
								
								selectValues.push({'id':s_id, 'value':value});
								
								$.each(selectParams.options, function(k, v){
									html += '<option value="'+k+'">'+v+'</option></div>';
								})
						
						html    +='</select></div>';		 
					})			


					$.each(opts.inputAttributes, function(attr, attr_data){
						 value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	

						 c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
						 
						 html  +='<div class="'+c_class+'"><label for="vstab_'+attr+'">'+opts.labelAttributes[attr]+':</label>' +
								 '<input type="text" name="'+options.modelName+'['+attr+']"  style="min-width: 480px" value="' + htmlEscape(value) + '" id="vstab_'+attr+'"/></div>';
					})			
					
					$.each(opts.imageAttributes, function(attr, attr_data){
						 var value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
						 var base64 = (typeof(params[attr+'_base64']) != 'undefined') ? params[attr+'_base64'] : '';	
						 c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
							 
						 html  +='<div class="'+c_class+'"><label for="vstab_'+attr+'">'+opts.labelAttributes[attr]+':</label>' +
								 '<input name="'+options.modelName+'['+attr+']" id="vstab_'+attr+'"" type="text" style="display: none;" />' + 
								 '<input name="'+options.modelName+'['+attr+'_rotate]" id="vstab_'+attr+'_rotate" value="" type="hidden"/>' + 
								 '<input name="'+options.modelName+'['+attr+'_base64]" id="vstab_'+attr+'_base64" value="'+base64+'" type="hidden"/>' + 
					 			 '<input data-id="img_vstab_'+attr+'" type="file" data-name="'+attr+'" class="js-image-bs-tab" />' +
								 '<div class="image-container"><input type="button" data-attr="'+attr+'" data-id="vstab_'+attr+'_rotate" class="js-btn-rotate" value="Rotate" /><img data-angle="0" width="480" id="img_vstab_'+attr+'" src="' + value + '" /></div></div>';
					})		

					
					$.each(opts.filesAttributes, function(attr, attr_data){
						 var value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
						 var file_url = (typeof(params[attr+'_url']) != 'undefined') ? params[attr+'_url'] : '';	
						 c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
							 
						 html  +='<div class="'+c_class+'"><label for="vstab_'+attr+'">'+opts.labelAttributes[attr]+':</label>' +
								 '<input name="'+options.modelName+'['+attr+']" id="vstab_'+attr+'"" type="text" style="display: none;" />' + 
								 '<input name="'+options.modelName+'['+attr+'_base64]" id="vstab_'+attr+'_base64" value="" type="hidden"/>' + 
								 '<input name="'+options.modelName+'['+attr+'_ext]" id="vstab_'+attr+'_ext" value="" type="hidden"/>' + 
								 '<input name="'+options.modelName+'['+attr+'_name]" id="vstab_'+attr+'_name" value="" type="hidden"/>' + 
					 			 '<input data-id="img_vstab_'+attr+'" type="file" data-name="'+attr+'" class="js-file-bs-tab" />'; 
								
						if (file_url != '') {
							html += '<a href="'+file_url+'" target="_blank">скачать</a>';
						}
								
						html += '</div>';
					})		

					
					$.each(opts.textareaAttributes, function(attr, attr_data){
						 value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
						 c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
	 
						 html  +='<div class="'+c_class+'"><label for="vstab_'+attr+'">'+opts.labelAttributes[attr]+':</label>' +
								 '<textarea name="'+options.modelName+'['+attr+']"  style="min-width: 480px" id="vstab_'+attr+'">' + htmlEscape(value) + '</textarea></div>';
					})			
					
					$.each(opts.editorAttributes, function(attr, attr_data){
						 value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
						 c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
	 
							 
						 html  +='<div class="'+c_class+'"><label for="vstab_'+attr+'">'+opts.labelAttributes[attr]+':</label>' +
								 '<textarea name="'+options.modelName+'['+attr+']" id="vstab_'+attr+'" class="ckeditor">' + htmlEscape(value) + '</textarea></div>';
								 
						ckeditorIds.push('vstab_'+attr);		 
					})			
					
								
					html  += '</div>';
						
					if (opts.multilingual) {	
						$.each(opts.languages, function(language, i){
							html += '<div id="tab-vs-'+language+'-'+model_id+'" class="tab-pane">';

							$.each(opts.inputAttributes, function(attr, attr_data){
								 attr  = attr + '_' + language;
								
								 value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
								 c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
					
									
								 html  +='<div class="'+c_class+'"><label for="vstab_'+attr+'">'+opts.labelAttributes[attr]+':</label>' +
										 '<input type="text" name="'+options.modelName+'['+attr+']"  style="min-width: 480px" value="' + htmlEscape(value) + '" id="vstab_'+attr+'"/></div>';
							})			

							$.each(opts.textareaAttributes, function(attr, attr_data){
								 attr  = attr + '_' + language;
								
								 value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
							     c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
		
								 html  +='<div class="'+c_class+'"><label for="vstab_'+attr+'">'+opts.labelAttributes[attr]+':</label>' +
										 '<textarea name="'+options.modelName+'['+attr+']"  style="min-width: 480px" id="vstab_'+attr+'">' + htmlEscape(value) + '</textarea></div>';
							})			
							
							$.each(opts.editorAttributes, function(attr, attr_data){
								 lang_attr  = attr + '_' + language;
								
								 value = (typeof(params[attr]) != 'undefined') ? params[attr] : '';	
							     c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
 		
								 
								 html  +='<div class="'+c_class+'"><label for="vstab_'+lang_attr+'">'+opts.labelAttributes[attr]+':</label>' +
										 '<textarea name="'+options.modelName+'['+lang_attr+']" id="vstab_'+lang_attr+'" class="ckeditor">' + htmlEscape(value) + '</textarea></div>';
								
								ckeditorIds.push('vstab_'+lang_attr);		 
							})			

							
							$.each(opts.filesAttributes, function(attr, attr_data){
								 lang_attr  = attr + '_' + language;
								 lang_attr_url  = attr + '_url_' + language;
								 var file_url = (typeof(params[lang_attr_url]) != 'undefined') ? params[lang_attr_url] : '';	
								 c_class = (typeof(attr_data.c_class) != 'undefined' && attr_data.c_class) ? attr_data.c_class : '';	
									 
								 html  +='<div class="'+c_class+'"><label for="vstab_'+lang_attr+'">'+opts.labelAttributes[attr]+':</label>' +
										 '<input name="'+options.modelName+'['+lang_attr+']" id="vstab_'+lang_attr+'"" type="text" style="display: none;" />' + 
										 '<input name="'+options.modelName+'['+lang_attr+'_base64]" id="vstab_'+lang_attr+'_base64" value="" type="hidden"/>' + 
										 '<input name="'+options.modelName+'['+lang_attr+'_ext]" id="vstab_'+lang_attr+'_ext" value="" type="hidden"/>' + 
										 '<input name="'+options.modelName+'['+lang_attr+'_name]" id="vstab_'+lang_attr+'_name" value="" type="hidden"/>' + 
										 '<input data-id="img_vstab_'+lang_attr+'" type="file" data-name="'+lang_attr+'" class="js-file-bs-tab" />'; 
										
								if (file_url != '') {
									html += '<a href="'+file_url+'" target="_blank">скачать</a>';
								}
										
								html += '</div>';
							})		
							
							html += '</div>';
						});	
					}
					
				html+= '</div><br/><br/>';
				
            var form = $editorForm.empty();
            
			action = (mode == 'create') ? opts.createUrl : opts.updateUrl+'&id='+params.id;
			label = (mode == 'create') ? opts.labelCreate : opts.labelUpdate;
			form.attr('action', action);
			
			form.append(html);
        
			//$editorModal.css('width', '1000px');

			$editorModal.modal('show');
            $editorModal.find('h3').text(label);
			
			
			
			$.each(ckeditorIds, function(i, attr){
				$('#'+attr).ckeditor();
			})

			console.log(selectValues);

			$.each(selectValues, function(i, k){
				$('#'+k.id).val(k.value);
			})

			
			$.each(chosenIds, function(i, attr){
				$('#'+attr).chosen({
					"disable_search_threshold":10,
					"placeholder_text_single":"Select an option",
					"placeholder_text_multiple":"Select some options",
					"no_results_text":"No results match"
				});				
			})
			
			triggerToogleSelectRelations();
			
            return $(html);
        }	
		
        var photoTemplate = '<div class="photo">' + '<div class="image-preview"><img src=""/></div><div class="caption">';
        
		photoTemplate += '<p class="title"></p>';
		photoTemplate += '</div><div class="actions">';
        photoTemplate += '<span class="editPhoto btn btn-apply"><i class="icon-pencil icon-white"></i></span> ';
        photoTemplate += '<span class="deletePhoto btn btn-danger"><i class="icon-remove icon-white"></i></span>' +
            '</div><input type="checkbox" class="photo-select"/></div>';

        function addItem(data) {
            var photo = $(photoTemplate);
            
			if (!$('.photo[data-id="'+data['id']+'"]', $itemsVsTab).size()) {
 			
				items[data['id']] = photo;
				photo.attr('data-id', data['id']);
				photo.data('rank', data['rank']);

				$('.caption p', photo).html(data['title']);
				
				if (typeof(data['preview']) != 'undefined') {
					$('img', photo).attr('src', data['preview']);
				}
				
				$itemsVsTab.append(photo);
			} else {
				photo = $('.photo[data-id="'+data['id']+'"]');
				$('.caption p', photo).html(data['title']);
				if (typeof(data['preview']) != 'undefined') {
					$('img', photo).attr('src', data['preview']);
				}
				
			}
			
            return photo;
        }		

        function removeItems(ids) {
            $.ajax({
                type: 'POST',
                url: opts.deleteUrl+'&fullModelName='+opts.fullModelName + '&multilingual=' + opts.multilingual,
                data: 'id[]=' + ids.join('&id[]=') + csrfParams + '&fullModelName='+opts.fullModelName,
                success: function (t) {
                    if (t == 'OK') {
                        for (var i = 0, l = ids.length; i < l; i++) {
                            items[ids[i]].remove();
                            delete items[ids[i]];
                        }
                    }
                }});
        }


        function deleteClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            removeItems([id]);
            return false;
        }

        function editClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            
            $.post(opts.getUrl + '&id='+ id + csrfParams + '&fullModelName='+opts.fullModelName + '&modelAttribute='+opts.modelAttribute + '&multilingual=' + opts.multilingual, function (response) {
                createEditorElement('update', response);			
            }, 'json');
        }

        function updateButtons() {
            var selectedCount = $('.photo.selected', $sorterVsTab).length;
            $('.select_all', $vstab).prop('checked', $('.photo', $sorterVsTab).length == selectedCount);
            if (selectedCount == 0) {
                $('.edit_selected, .remove_selected', $vstab).addClass('disabled');
            } else {
                $('.edit_selected, .remove_selected', $vstab).removeClass('disabled');
            }
        }

        function selectChanged() {
            var $this = $(this);
            if ($this.is(':checked'))
                $this.closest('.photo').addClass('selected');
            else
                $this.closest('.photo').removeClass('selected');
            updateButtons();
        }

        $itemsVsTab
            .on('click', '.photo .deletePhoto', deleteClick)
            .on('click', '.photo .editPhoto', editClick)
            .on('click', '.photo .photo-select', selectChanged);


        $('.images', $sorterVsTab).sortable({ tolerance: "pointer" }).disableSelection().bind("sortstop", function () {
            var data = [];
            $('.photo', $sorterVsTab).each(function () {
                var t = $(this);
                data.push('order[' + t.data('id') + ']=' + t.data('rank'));
            });
            $.ajax({
                type: 'GET',
                url: opts.arrangeUrl+'&fullModelName='+opts.fullModelName + '&multilingual=' + opts.multilingual,
                data: data.join('&') + csrfParams + '&fullModelName='+opts.fullModelName,
                dataType: "json"
            }).done(function (data) {
                    for (var id in data[id]) {
                        items[id].data('rank', data[id]);
                    }
                    // order saved!
                    // we can inform user that order saved
                });
        });

        $('.save-changes', $editorModal).click(function (e) {
            e.preventDefault();
			
            $.post($('.form', $editorModal).attr('action'), $('input, textarea, select', $editorForm).serialize() + csrfParams + '&modelName=' + opts.modelName + '&fullModelName='+opts.fullModelName + '&modelAttribute='+opts.modelAttribute + '&multilingual=' + opts.multilingual, function (response) {
                $('.error', $editorModal).removeClass('error');
				
				if (response.success) {
					
					addItem(response);
					
					$editorModal.modal('hide');
					//deselect all items after editing
					$('.photo.selected', $sorterVsTab).each(function () {
						$('.photo-select', this).prop('checked', false)
					}).removeClass('selected');
					
					$('.select_all', $vstab).prop('checked', false);
					updateButtons();
					
				} else {
					$.each(response.errors, function(k, v){
						field = $('#vstab_'+k);
						field.addClass('error');
					})					
				}
								
            }, 'json');

        });

        $('.remove_selected', $vstab).click(function (e) {
            e.preventDefault();
            var ids = [];
            $('.photo.selected', $sorterVsTab).each(function () {
                ids.push($(this).data('id'));
            });
            removeItems(ids);

        });

        $('.js-vstab-create', $vstab).click(function (e) {
            e.preventDefault();
			createEditorElement('create', []);
        });

        $('.select_all', $vstab).change(function () {
            if ($(this).prop('checked')) {
                $('.photo', $sorterVsTab).each(function () {
                    $('.photo-select', this).prop('checked', true)
                }).addClass('selected');
            } else {
                $('.photo.selected', $sorterVsTab).each(function () {
                    $('.photo-select', this).prop('checked', false)
                }).removeClass('selected');
            }
            updateButtons();
        });
		
		l = opts.items.length;
		for (var i = 0; i < l; i++) {
            var resp = opts.items[i];
			addItem(resp);
        }
    }

    // The actual plugin
    $.fn.vstabManager = function (options) {
		
        if (this.length) {
            this.each(function () {
	            vstabManager(this, options);
            });
        }
    };
		
})(jQuery);

$('document').ready(function(){
	$('body').on('click', '.js-tab-photo', function(e){
		e.preventDefault();
		id = $(this).parent().parent().parent().attr('id');
		$('#'+id+' ul li').removeClass('ui-state-active');
		$('#'+id+' .ui-tabs-panel').hide();
		$('#'+$(this).attr('rel')).addClass('ui-state-active');
		$($(this).attr('href')).show();
	})
	
	$("body").on("change", ".js-image-bs-tab", function (e) {
		if(this.disabled) return alert('File upload not supported!');
		var F = this.files;
		if(F && F[0]) for(var i=0; i<F.length; i++) {
			readImageVsTab(F[i], $(this).data('name'));
		}
	});	

	$("body").on("change", ".js-file-bs-tab", function (e) {
		if(this.disabled) return alert('File upload not supported!');
		var F = this.files;
		if(F && F[0]) for(var i=0; i<F.length; i++) {
			readFileVsTab(F[i], $(this).data('name'));
			var sp = $(this).val().toString().split('.');
			var sa = $(this).val().toString().split('\\');
			
			$('#vstab_'+$(this).data('name')+'_ext').val(sp[sp.length-1]);
			$('#vstab_'+$(this).data('name')+'_name').val(sa[sa.length-1]);
		}
	});	

	$("body").on("change", ".js-select-toogle", function (e) {
		toogleSelectRelations($(this));
	});	
})

function toogleSelectRelations(_select) {
	var _tclass = _select.data('toogle-class');
	$('.'+_tclass).hide();
	$('.'+_tclass+'-'+_select.val()).show();
}

function triggerToogleSelectRelations() {
	$(".js-select-toogle").each(function(){
		toogleSelectRelations($(this));
	})
}

function readImageVsTab(file, attr) {
    var reader = new FileReader();
    var image  = new Image();
  
    reader.readAsDataURL(file);  
    reader.onload = function(_file) {
        image.src    = _file.target.result;  
        image.onload = function() {
			$('#img_vstab_'+attr).attr('src', this.src).show();
			$('#img_vstab_'+attr).attr('class', '');
			$('#vstab_'+attr).val(this.src);
			$('#vstab_'+attr+'_base64').val(this.src);	
	    };
        image.onerror= function() {
            alert('Invalid file type: '+ file.type);
        };      
    };   
}

function readFileVsTab(file, attr) {
	var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () {
		console.log(reader);
		
		$('#vstab_'+attr).val(reader.result);	
		$('#vstab_'+attr+'_base64').val(reader.result);	
	}   
}
