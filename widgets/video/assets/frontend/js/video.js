(function( $ ){

  $.fn.videoManager = function( options ) {  

    // Создаём настройки по-умолчанию, расширяя их с помощью параметров, которые были переданы
    var settings = options;
	
	/*
	$.extend( {
		attributeDeletedIds: 'deleted_videos_ids',
		modelName: 'Video',
		attributeVideos: 'post_videos'
    }, options);
	*/

	var deletedUploadedVideoIds = [];  
	
    return this.each(function() {        

		$('.js-btn-add-video').on('click', function(event) {
			event.preventDefault();
			
			var url = $('.js-url-video-result').val();
			var video_url = '';
			var src = '';

			if (url.indexOf('youtu') > 0) {
				regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
				match = url.match(regExp);
				if (match && match[2].length == 11) {
				  src = 'http://img.youtube.com/vi/'+ match[2] +'/0.jpg';
				  video_url = 'https://www.youtube.com/watch?v='+match[2];
				}
			} else if (url.indexOf('vimeo') > 0) {
				regExp = /https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/;
				match = url.match(regExp);
				if (match) {
					video_url = 'https://player.vimeo.com/video/'+ match[3];
					$.ajax({
					  url: '/?r=ajax/vimeo-image&id='+match[3],
					  dataType: 'text',
					  async: false,
					  success: function(img) {
						src=img;
					  }
					});					
					
				}			
			} else if (url.indexOf('coub') > 0) {
				regExp = /(http|https)?:\/\/(www\.)?coub\.com\/view\/([a-zA-Z\d]+)/;
				match = url.match(regExp);
				console.log(match);
				if (match) {
					video_url = 'https://coub.com/embed/'+ match[3];
					
					$.ajax({
					  url: '/?r=ajax/coub-image&id='+match[3],
					  dataType: 'text',
					  async: false,
					  success: function(img) {
						src=img;
					  }
					});
					
				}			
			}
			
			$('.js-url-video-result').val('');
		
			if (video_url != '') {
				var list = $('.js-preview-video');
				
				var li = $('<li/>', {
					'class': 'preview-list__item preview-list__item_hidden'
				});
		
				$('<div/>', {
					'class': 'loader loader_middle'
				}).appendTo(li);
		
				$('<i/>', {
					'class': 'close-red-small-ico close-red-small-ico_over js-close-preview-video'
				}).appendTo(li);
		
				var img = $('<img/>', {
					'src':src 
				}).appendTo(li);

				var input = $('<input/>', {
					'value': video_url,
					'type': 'hidden',
					'name': settings.modelName + '['+settings.attributeName+'][]'
				}).appendTo(li);
				
				li.appendTo(list);
		
				img.on('load', function() {
					img.parents('li').find('.loader').remove();
					li.removeClass('preview-list__item_hidden');
				});
			}
		});
		
		// удалить 
		$(document).on('click', '.js-close-preview-video', function(event) {
			event.preventDefault();
			$(this).parents('li').remove();
		});	
		
		// удалить загруженое фото
		$(document).on('click', '.js-close-preview-video-uploaded', function(event) {
			event.preventDefault();
			deletedUploadedVideoIds.push($(this).data('id'));

			id = '#'+settings.modelName.toLowerCase()+'-'+settings.attributeDeletedIds;
			$(id).val(deletedUploadedVideoIds.join(',')); 
			
			console.log(id);
			console.log(settings.attributeDeletedIds);
			console.log(deletedUploadedVideoIds);
			$(this).parents('li').remove();
		});     

    });

  };
})( jQuery );

