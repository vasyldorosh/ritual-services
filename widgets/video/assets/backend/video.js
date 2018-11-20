(function ($) {
	
	var videoDefaults = {
        csrfToken: null,
        csrfTokenName: null,

        nameLabel: 'Заголовок',
        descriptionLabel: 'Описание',
       
        hasTitle: true,
        hasDescription: true,

        createUrl: '',
        deleteUrl: '',
        updateUrl: '',
        orderUrl: '',
        videos: [],
        languages: [],
        language: 'ru',
		videoModelName: '',
		videoFullModelName: '',
		modelAttribute: 'video_id',
		model_id: ''
    };

    function videoManager(el, options) {
		//Extending options:
        var opts = $.extend({}, videoDefaults, options);
		
        var csrfParams = opts.csrfToken ? '&' + opts.csrfTokenName + '=' + opts.csrfToken : '';
        var videos = {}; // video elements by id
        var $video = $(el);
        if (!opts.hasTitle) {
            if (!opts.hasDescription) {
                $video.addClass('no-name-no-desc');
                $('.edit_selected',$video).hide();
            }
            else $video.addClass('no-name');

        } else if (!opts.hasDescription)
            $video.addClass('no-desc');

        var $sorter 	= $('.sorter', $video);
        var $videos 	= $('.videos', $sorter);
        var $editorModal= $('.editor-modal', $video);
        var $editorForm = $('.form', $editorModal);

        function htmlEscape(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
		
		$('.js-btn-add-video', $video).click(function (e) {
			e.preventDefault();
			
        	video_url = $('.js-input-video-url', $video).val();

			url = opts.createUrl;
			url+= '&videoFullModelName=' + opts.videoFullModelName;
			url+= '&modelAttribute=' + opts.modelAttribute;
			url+= '&gallery_id=' + opts.model_id;
			url+= '&url=' + video_url;
			
			$.ajax({
                type: 'GET',
                url: url,
				dataType: 'json',
                success: function (response) {
					if (response.success) {
						addVideo(response);
					} else {
						error = '';
						$.each(response.errors, function(k, v){
							error += v + "\n";
						})	
						alert(error);
					}
 				}
			});
		})
				
        function createEditorElement(params) {
           
			var html = '<div class="video-editor">' +
                '<div class="preview"><img src="' + htmlEscape(params.src) + '" alt=""/></div>' +
                '<div>';
			
				html+= '<div id="tab_video_'+params.id+'" class="simple_tabs ui-tabs ui-widget ui-widget-content ui-corner-all">'+
						'<ul class="tabs ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">'+
							'<li id="li_'+opts.language+'_'+params.id+'" class="ui-state-default ui-corner-top ui-state-active"><a rel="li_'+opts.language+'_'+params.id+'" class="js-tab-video" href="#videotab_'+opts.language+'_'+params.id+'">'+opts.language+'</a></li>';
								
							$.each(opts.languages, function(i, n){
								html+='<li id="li_'+i+'_'+params.id+'" class="ui-state-default ui-corner-top"><a rel="li_'+i+'_'+params.id+'" class="js-tab-video" href="#videotab_'+i+'_'+params.id+'">'+i+'</a></li>';
							});	
						html+='</ul>'+
						
						'<div class="content ui-tabs-panel ui-widget-content ui-corner-bottom" id="videotab_'+opts.language+'_'+params.id+'">'+
								(opts.hasTitle
									? '<label for="video_name_'+params.id+'">' + opts.nameLabel + ':</label>' +
									'<input type="text" name="video['+params.id+'][name]" class="input-xlarge" value="' + htmlEscape(params.name) + '" id="video_name_'+params.id+'"/>'
									: '') +
								(opts.hasDescription
									? '<label for="video_description_'+params.id+'">' + opts.descriptionLabel + ':</label>' +
									'<textarea name="video['+params.id+'][description]" rows="3" cols="40" class="input-xlarge" id="video_description_'+params.id+'">' + htmlEscape(params.description) + '</textarea>'
									: '')+
						'</div>';
						
						$.each(opts.languages, function(language, i){
						html+='<div style="display:none" class="content ui-tabs-panel ui-widget-content ui-corner-bottom" id="videotab_'+language+'_'+params.id+'">'+
								(opts.hasTitle
									? '<label for="video_name_'+language+'_'+params.id+'">' + opts.nameLabel + ':</label>' +
									'<input type="text" name="video['+params.id+'][name_'+language+']" class="input-xlarge" value="' + htmlEscape(params["name_"+language]) + '" id="video_name_'+language+'_'+params.id+'"/>'
									: '') +
								(opts.hasDescription
									? '<label for="video_description_'+language+'_'+params.id+'">' + opts.descriptionLabel + ':</label>' +
									'<textarea name="video['+params.id+'][description_'+language+']" rows="3" cols="40" class="input-xlarge" id="video_description_'+language+'_'+params.id+'">' + htmlEscape(params["description_"+language]) + '</textarea>'
									: '')+
						'</div>';
						});	

				html+= '</div>';	

                html+='</div>' +
                '</div>';
				
						
				
            return $(html);
        }	
		


        var videoTemplate = '<div class="video">' + '<div class="video-preview"><img src=""/></div><div class="caption">';
        if (opts.hasTitle) {
			videoTemplate += '<h5 class="'+opts.language+'"></h5>';
			$.each(opts.languages, function(i, n){
				videoTemplate += '<h5 style="display:none;" class="'+i+'"></h5>';
			})				
		}	
			
        if (opts.hasDescription) {
			videoTemplate += '<p class="'+opts.language+'"></p>';
			$.each(opts.languages, function(i, n){
				videoTemplate += '<p style="display:none;" class="'+i+'"></p>';
			})				
		}	
			
        videoTemplate += '</div><div class="actions">';
        if (opts.hasTitle || opts.hasDescription)videoTemplate += '<span class="editVideo btn btn-apply"><i class="icon-pencil icon-white"></i></span> ';
        videoTemplate += '<span class="deleteVideo btn btn-danger"><i class="icon-remove icon-white"></i></span>' +
            '</div><input type="checkbox" class="video-select"/></div>';

			
        function addVideo(response) {
	        var video = $(videoTemplate);
            videos[response['id']] = video;
            video.data('id', response['id']);
            video.data('rank', response['rank']);
			
            $('img', video).attr('src', response['preview']);
            
			/*
			if (opts.hasTitle) {
				$('.caption h5.'+opts.language, video).text(response['name']);
				$.each(opts.languages, function(i, n){
					$('.caption h5.'+i, video).text(response["name_"+i]);
				})									
			}
						
            if (opts.hasDescription) {
				$('.caption p.'+opts.language, video).text(response['description']);
				$.each(opts.languages, function(i, n){
					$('.caption p.'+i, video).text(response["description_"+i]);
				})					
			}
			*/

            $videos.append(video);
            return video;
        }			
			
		function editVideos(ids) {
            var l = ids.length;
            var form = $editorForm.empty();
            for (var i = 0; i < l; i++) {
                var id = ids[i];
				var video = videos[id];
 
				params = {};	
				params.link = $('.caption h4.link', video).text();	
				params.name = $('.caption h5.'+opts.language, video).text();	
				params.description = $('.caption p.'+opts.language, video).text();
				params.src = $('img', video).attr('src');
				params.id = id;
					
				$.each(opts.languages, function(i, n){
					params["name_"+i] =  $('.caption h5.'+i, video).text();	;
					params["description_"+i] = $('.caption p.'+i, video).text();;
				})	
					
                form.append(createEditorElement(params));
            }
            if (l > 0)$editorModal.modal('show');
        }

        function removeVideos(ids) {
            $.ajax({
                type: 'POST',
                url: opts.deleteUrl+'&videoFullModelName='+opts.videoFullModelName,
                data: 'id[]=' + ids.join('&id[]=') + csrfParams + '&videoFullModelName='+opts.videoFullModelName,
                success: function (t) {
                    if (t == 'OK') {
                        for (var i = 0, l = ids.length; i < l; i++) {
                            videos[ids[i]].remove();
                            delete videos[ids[i]];
                        }
                    } else alert(t);
                }});
        }


        function deleteClick(e) {
            e.preventDefault();
            var video = $(this).closest('.video');
            var id = video.data('id');
            // here can be question to confirm delete
            // if (!confirm(deleteConfirmation)) return false;
            removeVideos([id]);
            return false;
        }

        function editClick(e) {
            e.preventDefault();
            var video = $(this).closest('.video');
            var id = video.data('id');
            editVideos([id]);
            return false;
        }

        function updateButtons() {
            var selectedCount = $('.video.selected', $sorter).length;
            $('.select_all', $video).prop('checked', $('.video', $sorter).length == selectedCount);
            if (selectedCount == 0) {
                $('.edit_selected, .remove_selected', $video).addClass('disabled');
            } else {
                $('.edit_selected, .remove_selected', $video).removeClass('disabled');
            }
        }

        function selectChanged() {
            var $this = $(this);
            if ($this.is(':checked'))
                $this.closest('.video').addClass('selected');
            else
                $this.closest('.video').removeClass('selected');
            updateButtons();
        }

        $videos
            .on('click', '.video .deleteVideo', deleteClick)
            .on('click', '.video .editVideo', editClick)
            .on('click', '.video .video-select', selectChanged);


        $('.videos', $sorter).sortable({ tolerance: "pointer" }).disableSelection().bind("sortstop", function () {
            var data = [];
            $('.video', $sorter).each(function () {
                var t = $(this);
                data.push('order[' + t.data('id') + ']=' + t.data('rank'));
            });
            $.ajax({
                type: 'GET',
                url: opts.orderUrl+'&videoFullModelName='+opts.videoFullModelName+'&'+data.join('&'),
                dataType: "json"
            }).done(function (data) {
                for (var id in data[id]) {
					videos[id].data('rank', data[id]);
				}
            });
        });

        $('.save-changes', $editorModal).click(function (e) {
            e.preventDefault();
            $.post(opts.updateUrl, $('input, textarea', $editorForm).serialize() + csrfParams + '&videoModelName='+opts.videoModelName, function (data) {
                var count = data.length;
                for (var key = 0; key < count; key++) {
                    var p = data[key];
                    var video = videos[p.id];
                    $('img', video).attr('src', p['src']);
                    if (opts.hasTitle) {
                        $('.caption h5.'+opts.language, video).text(p['name']);
						$.each(opts.languages, function(i, n){
							 $('.caption h5.'+i, video).text(p['name_'+i]);
						})							
					}	
										
                    if (opts.hasDescription) {
                        $('.caption p.'+opts.language, video).text(p['description']);
						$.each(opts.languages, function(i, n){
							 $('.caption p.'+i, video).text(p['description_'+i]);
						})							
					}
                }
                $editorModal.modal('hide');
                //deselect all items after editing
                $('.video.selected', $sorter).each(function () {
                    $('.video-select', this).prop('checked', false)
                }).removeClass('selected');
                $('.select_all', $video).prop('checked', false);
                updateButtons();
            }, 'json');

        });

        $('.edit_selected', $video).click(function (e) {
            e.preventDefault();
            var ids = [];
            $('.video.selected', $sorter).each(function () {
                ids.push($(this).data('id'));
            });
            editVideos(ids);
            return false;
        });

        $('.remove_selected', $video).click(function (e) {
            e.preventDefault();
            var ids = [];
            $('.video.selected', $sorter).each(function () {
                ids.push($(this).data('id'));
            });
            removeVideos(ids);

        });

        $('.select_all', $video).change(function () {
            if ($(this).prop('checked')) {
                $('.video', $sorter).each(function () {
                    $('.video-select', this).prop('checked', true)
                }).addClass('selected');
            } else {
                $('.video.selected', $sorter).each(function () {
                    $('.video-select', this).prop('checked', false)
                }).removeClass('selected');
            }
            updateButtons();
        });
		
        for (var i = 0, l = opts.videos.length; i < l; i++) {
            var resp = opts.videos[i];
            addVideo(resp);
        }
    }

    // The actual plugin
    $.fn.videoManager = function (options) {

        if (this.length) {
            this.each(function () {
                videoManager(this, options);
            });
        }
    };
		
})(jQuery);

$('document').ready(function(){
	$('body').on('click', '.js-tab-video', function(e){
		e.preventDefault();
		id = $(this).parent().parent().parent().attr('id');
		$('#'+id+' ul li').removeClass('ui-state-active');
		$('#'+id+' .ui-tabs-panel').hide();
		$('#'+$(this).attr('rel')).addClass('ui-state-active');
		$($(this).attr('href')).show();
	})
})