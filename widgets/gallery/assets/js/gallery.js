(function ($) {
	
    var galleryDefaults = {
        csrfToken: null,
        csrfTokenName: null,

        nameLabel: 'Заголовок',
        descriptionLabel: 'Описание',
       
        hasTitle: true,
        hasDescription: true,

        uploadUrl: '',
        deleteUrl: '',
        updateUrl: '',
        arrangeUrl: '',
        photos: [],
        languages: [],
        language: 'ru',
		photoModelName: '',
		photoFullModelName: '',
		modelAttribute: 'gallery_id'
    };

    function galleryManager(el, options) {
		//Extending options:
        var opts = $.extend({}, galleryDefaults, options);
		
        var csrfParams = opts.csrfToken ? '&' + opts.csrfTokenName + '=' + opts.csrfToken : '';
        var photos = {}; // photo elements by id
        var $gallery = $(el);
        if (!opts.hasTitle) {
            if (!opts.hasDescription) {
                $gallery.addClass('no-name-no-desc');
                $('.edit_selected',$gallery).hide();
            }
            else $gallery.addClass('no-name');

        } else if (!opts.hasDescription)
            $gallery.addClass('no-desc');

        var $sorter = $('.sorter', $gallery);
        var $images = $('.images', $sorter);
        var $editorModal = $('.editor-modal', $gallery);
        var $progressOverlay = $('.progress-overlay', $gallery);
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

		
        function createEditorElement(params) {
           
			var html = '<div class="photo-editor">' +
                '<div class="preview"><img src="' + htmlEscape(params.src) + '" alt=""/></div>' +
                '<div>';
			
				html+= '<div id="tab_photo_'+params.id+'" class="simple_tabs ui-tabs ui-widget ui-widget-content ui-corner-all">'+
						'<ul class="tabs ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">'+
							'<li id="li_'+opts.language+'_'+params.id+'" class="ui-state-default ui-corner-top ui-state-active"><a rel="li_'+opts.language+'_'+params.id+'" class="js-tab-photo" href="#phototab_'+opts.language+'_'+params.id+'">'+opts.language+'</a></li>';
								
							$.each(opts.languages, function(i, n){
								html+='<li id="li_'+i+'_'+params.id+'" class="ui-state-default ui-corner-top"><a rel="li_'+i+'_'+params.id+'" class="js-tab-photo" href="#phototab_'+i+'_'+params.id+'">'+i+'</a></li>';
							});	
						html+='</ul>'+
						
						'<div class="content ui-tabs-panel ui-widget-content ui-corner-bottom" id="phototab_'+opts.language+'_'+params.id+'">'+
								(opts.hasTitle
									? '<label for="photo_name_'+params.id+'">' + opts.nameLabel + ':</label>' +
									'<input type="text" name="photo['+params.id+'][name]" class="input-xlarge" value="' + htmlEscape(params.name) + '" id="photo_name_'+params.id+'"/>'
									: '') +
								(opts.hasDescription
									? '<label for="photo_description_'+params.id+'">' + opts.descriptionLabel + ':</label>' +
									'<textarea name="photo['+params.id+'][description]" rows="3" cols="40" class="input-xlarge" id="photo_description_'+params.id+'">' + htmlEscape(params.description) + '</textarea>'
									: '')+
						'</div>';
						
						$.each(opts.languages, function(language, i){
						html+='<div style="display:none" class="content ui-tabs-panel ui-widget-content ui-corner-bottom" id="phototab_'+language+'_'+params.id+'">'+
								(opts.hasTitle
									? '<label for="photo_name_'+language+'_'+params.id+'">' + opts.nameLabel + ':</label>' +
									'<input type="text" name="photo['+params.id+'][name_'+language+']" class="input-xlarge" value="' + htmlEscape(params["name_"+language]) + '" id="photo_name_'+language+'_'+params.id+'"/>'
									: '') +
								(opts.hasDescription
									? '<label for="photo_description_'+language+'_'+params.id+'">' + opts.descriptionLabel + ':</label>' +
									'<textarea name="photo['+params.id+'][description_'+language+']" rows="3" cols="40" class="input-xlarge" id="photo_description_'+language+'_'+params.id+'">' + htmlEscape(params["description_"+language]) + '</textarea>'
									: '')+
						'</div>';
						});	

				html+= '</div>';	

                html+='</div>' +
                '</div>';
				
						
				
            return $(html);
        }	
		


        var photoTemplate = '<div class="photo">' + '<div class="image-preview"><img src=""/></div><div class="caption">';
        if (opts.hasTitle) {
			photoTemplate += '<h5 class="'+opts.language+'"></h5>';
			$.each(opts.languages, function(i, n){
				photoTemplate += '<h5 style="display:none;" class="'+i+'"></h5>';
			})				
		}	
			
        if (opts.hasDescription) {
			photoTemplate += '<p class="'+opts.language+'"></p>';
			$.each(opts.languages, function(i, n){
				photoTemplate += '<p style="display:none;" class="'+i+'"></p>';
			})				
		}	
			
        photoTemplate += '</div><div class="actions">';
        if (opts.hasTitle || opts.hasDescription)photoTemplate += '<span class="editPhoto btn btn-apply"><i class="icon-pencil icon-white"></i></span> ';
        photoTemplate += '<span class="deletePhoto btn btn-danger"><i class="icon-remove icon-white"></i></span>' +
            '</div><input type="checkbox" class="photo-select"/></div>';


        function addPhoto(resp) {
            var photo = $(photoTemplate);
            photos[resp['id']] = photo;
            photo.data('id', resp['id']);
            photo.data('rank', resp['rank']);

            $('img', photo).attr('src', resp['preview']);
            if (opts.hasTitle) {
				$('.caption h5.'+opts.language, photo).text(resp['name']);
				$.each(opts.languages, function(i, n){
					$('.caption h5.'+i, photo).text(resp["name_"+i]);
				})									
			}
						
            if (opts.hasDescription) {
				$('.caption p.'+opts.language, photo).text(resp['description']);
				$.each(opts.languages, function(i, n){
					$('.caption p.'+i, photo).text(resp["description_"+i]);
				})					
			}

            $images.append(photo);
            return photo;
        }		

        function editPhotos(ids) {
            var l = ids.length;
            var form = $editorForm.empty();
            for (var i = 0; i < l; i++) {
                var id = ids[i];
				var photo = photos[id];
 
				params = {};	
				params.link = $('.caption h4.link', photo).text();	
				params.name = $('.caption h5.'+opts.language, photo).text();	
				params.description = $('.caption p.'+opts.language, photo).text();
				params.src = $('img', photo).attr('src');
				params.id = id;
					
				$.each(opts.languages, function(i, n){
					params["name_"+i] =  $('.caption h5.'+i, photo).text();	;
					params["description_"+i] = $('.caption p.'+i, photo).text();;
				})	
					
                form.append(createEditorElement(params));
            }
            if (l > 0)$editorModal.modal('show');
        }

        function removePhotos(ids) {
            $.ajax({
                type: 'POST',
                url: opts.deleteUrl+'&photoFullModelName='+opts.photoFullModelName,
                data: 'id[]=' + ids.join('&id[]=') + csrfParams + '&photoFullModelName='+opts.photoFullModelName,
                success: function (t) {
                    if (t == 'OK') {
                        for (var i = 0, l = ids.length; i < l; i++) {
                            photos[ids[i]].remove();
                            delete photos[ids[i]];
                        }
                    } else alert(t);
                }});
        }


        function deleteClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            // here can be question to confirm delete
            // if (!confirm(deleteConfirmation)) return false;
            removePhotos([id]);
            return false;
        }

        function editClick(e) {
            e.preventDefault();
            var photo = $(this).closest('.photo');
            var id = photo.data('id');
            editPhotos([id]);
            return false;
        }

        function updateButtons() {
            var selectedCount = $('.photo.selected', $sorter).length;
            $('.select_all', $gallery).prop('checked', $('.photo', $sorter).length == selectedCount);
            if (selectedCount == 0) {
                $('.edit_selected, .remove_selected', $gallery).addClass('disabled');
            } else {
                $('.edit_selected, .remove_selected', $gallery).removeClass('disabled');
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

        $images
            .on('click', '.photo .deletePhoto', deleteClick)
            .on('click', '.photo .editPhoto', editClick)
            .on('click', '.photo .photo-select', selectChanged);


        $('.images', $sorter).sortable({ tolerance: "pointer" }).disableSelection().bind("sortstop", function () {
            var data = [];
            $('.photo', $sorter).each(function () {
                var t = $(this);
                data.push('order[' + t.data('id') + ']=' + t.data('rank'));
            });
            $.ajax({
                type: 'POST',
                url: opts.arrangeUrl+'&photoFullModelName='+opts.photoFullModelName,
                data: data.join('&') + csrfParams + '&photoFullModelName='+opts.photoFullModelName,
                dataType: "json"
            }).done(function (data) {
                    for (var id in data[id]) {
                        photos[id].data('rank', data[id]);
                    }
                    // order saved!
                    // we can inform user that order saved
                });
        });

        if (window.FormData !== undefined) { // if XHR2 available
            var uploadFileName = $('.afile', $gallery).attr('name');

            function multiUpload(files) {
                $progressOverlay.show();
                $uploadProgress.css('width', '5%');
                var filesCount = files.length;
                var uploadedCount = 0;
                var ids = [];
                for (var i = 0; i < filesCount; i++) {
                    var fd = new FormData();

                    fd.append(uploadFileName, files[i]);
                    if (opts.csrfToken) {
                        fd.append(opts.csrfTokenName, opts.csrfToken);
                    }

					var xhr = new XMLHttpRequest();
                    xhr.open('POST', opts.uploadUrl+'&photoFullModelName='+opts.photoFullModelName+'&modelAttribute='+opts.modelAttribute, true);
                    xhr.onload = function () {
                        uploadedCount++;
                        if (this.status == 200) {
                            var resp = JSON.parse(this.response);
                            addPhoto(resp);
                            ids.push(resp['id']);
                        } else {
                            // exception !!!
                        }
                        $uploadProgress.css('width', '' + (5 + 95 * uploadedCount / filesCount) + '%');
                        if (uploadedCount === filesCount) {
                            $uploadProgress.css('width', '100%');
                            $progressOverlay.hide();
                            if (opts.hasTitle || opts.hasDescription) editPhotos(ids);
                        }
                    };
                    xhr.send(fd);
                }

            }

            (function () { // add drag and drop
                var el = $gallery[0];
                var isOver = false;
                var lastIsOver = false;

                setInterval(function () {
                    if (isOver != lastIsOver) {
                        if (isOver) el.classList.add('over');
                        else el.classList.remove('over');
                        lastIsOver = isOver
                    }
                }, 30);

                function handleDragOver(e) {
                    e.preventDefault();
                    isOver = true;
                    return false;
                }

                function handleDragLeave() {
                    isOver = false;
                    return false;
                }

                function handleDrop(e) {
                    e.preventDefault();
                    e.stopPropagation();


                    var files = e.dataTransfer.files;
                    multiUpload(files);

                    isOver = false;
                    return false;
                }

                function handleDragEnd() {
                    isOver = false;
                }


                el.addEventListener('dragover', handleDragOver, false);
                el.addEventListener('dragleave', handleDragLeave, false);
                el.addEventListener('drop', handleDrop, false);
                el.addEventListener('dragend', handleDragEnd, false);
            })();

            $('.afile', $gallery).attr('multiple', 'true').on('change', function (e) {
                e.preventDefault();
                multiUpload(this.files);
            });
        } else {
            $('.afile', $gallery).on('change', function (e) {
                e.preventDefault();
                var ids = [];
                $progressOverlay.show();
                $uploadProgress.css('width', '5%');

                var data = {};
                if (opts.csrfToken)
                    data[opts.csrfTokenName] = opts.csrfToken + '&photoFullModelName='+opts.photoFullModelName+'&modelAttribute='+opts.modelAttribute;
                
				$.ajax({
                    type: 'POST',
                    url: opts.uploadUrl,
                    data: data,
                    files: $(this),
                    iframe: true,
                    processData: false,
                    dataType: "json"
                }).done(function (resp) {
                        addPhoto(resp);
                        ids.push(resp['id']);
                        $uploadProgress.css('width', '100%');
                        $progressOverlay.hide();
                        if (opts.hasTitle || opts.hasDescription) editPhotos(ids);
                    });
            });
        }

        $('.save-changes', $editorModal).click(function (e) {
            e.preventDefault();
            $.post(opts.updateUrl, $('input, textarea', $editorForm).serialize() + csrfParams + '&photoModelName='+opts.photoModelName, function (data) {
                var count = data.length;
                for (var key = 0; key < count; key++) {
                    var p = data[key];
                    var photo = photos[p.id];
                    $('img', photo).attr('src', p['src']);
                    if (opts.hasTitle) {
                        $('.caption h5.'+opts.language, photo).text(p['name']);
						$.each(opts.languages, function(i, n){
							 $('.caption h5.'+i, photo).text(p['name_'+i]);
						})							
					}	
										
                    if (opts.hasDescription) {
                        $('.caption p.'+opts.language, photo).text(p['description']);
						$.each(opts.languages, function(i, n){
							 $('.caption p.'+i, photo).text(p['description_'+i]);
						})							
					}
                }
                $editorModal.modal('hide');
                //deselect all items after editing
                $('.photo.selected', $sorter).each(function () {
                    $('.photo-select', this).prop('checked', false)
                }).removeClass('selected');
                $('.select_all', $gallery).prop('checked', false);
                updateButtons();
            }, 'json');

        });

        $('.edit_selected', $gallery).click(function (e) {
            e.preventDefault();
            var ids = [];
            $('.photo.selected', $sorter).each(function () {
                ids.push($(this).data('id'));
            });
            editPhotos(ids);
            return false;
        });

        $('.remove_selected', $gallery).click(function (e) {
            e.preventDefault();
            var ids = [];
            $('.photo.selected', $sorter).each(function () {
                ids.push($(this).data('id'));
            });
            removePhotos(ids);

        });

        $('.select_all', $gallery).change(function () {
            if ($(this).prop('checked')) {
                $('.photo', $sorter).each(function () {
                    $('.photo-select', this).prop('checked', true)
                }).addClass('selected');
            } else {
                $('.photo.selected', $sorter).each(function () {
                    $('.photo-select', this).prop('checked', false)
                }).removeClass('selected');
            }
            updateButtons();
        });
		
        for (var i = 0, l = opts.photos.length; i < l; i++) {
            var resp = opts.photos[i];
            addPhoto(resp);
        }
    }

    // The actual plugin
    $.fn.galleryManager = function (options) {
		
        if (this.length) {
            this.each(function () {
                galleryManager(this, options);
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
})