/**
 * Structure additional script
 * @author maxshurko@yandex.ru
 */

var page = null;
var block = null;
var message = null;

jQuery(function(){
    if (/msie/i.test(navigator.userAgent)) { jQuery(document)[0].oncontextmenu = function() {return false;}}
    jQuery('#siteStructure a').contextMenu({menu: 'pageMenu'}, function(action, el, pos) {page.process(action, el);});
    jQuery('#widgetsStructure a').click(function() { block.getConfigByAlias(jQuery(this).attr('name'), null, null);});
    
    page = new Page({
		currentId: 			1,
		dialogId: 			'#page-properties',
		saveButtonId: 		'#page-properties-save-button',
		moveButtonId: 		'#page-move-button',
		formId:				'#page-form',
		visualEditorId:		'#visualEditor',
		parentId:			'#page-parent_id'
	});
	
	block = new Block({
		currentPageId: 		1,
		visualEditorId:		'#visualEditor',
		textSettings:		{dialogId: '#text-content', saveButtonId: '#text-content-save-button', formId: '#text-settings'},
		widgetSettings:		{dialogId: '#widget-structure', saveButtonId: '#widget-properties-save-button', formId: '#widget-settings'}
	});
	
	message = new Message({
		messageWindowId:	'#message-form'
	});
});

function blockProcess(currentPageId, action, e) { block.currentPageId = currentPageId; block.process(action, e);}

///
function Page(settings) {
	this.currentId 					= settings.currentId;
	this.dialogId					= settings.dialogId,
	this.saveButtonId 				= settings.saveButtonId;
	this.formId						= settings.formId;
	this.visualEditorId				= settings.visualEditorId;
	this.parentId					= settings.parentId;
	this.domain						= null;
}
Page.prototype = {
	process: function(action, el) {
		
		this.domain = this.getDomain(jQuery(el).attr('href'));
		
		this.currentId = jQuery(el).attr('name');
		jQuery(this.parentId).val(this.currentId);
		
		switch (action) {
			case 'create': this.create(); break;
			case 'update': this.update(); break;
			case 'remove': this.remove(); break;
			case 'move': this.move(); break;
			default: console.log(action);
		}
	},
	create: function() {
		var that = this;
		this.hideErrors();		
		
		$(that.formId).trigger("reset");
		
		jQuery(this.saveButtonId).unbind('click').click(function() {
			that.hideErrors();
			
			
			jQuery.post('/index.php?r=structure/page/create', jQuery(that.formId).serialize(), function(data) {
				if (data.success == 1) {
					jQuery(that.visualEditorId).attr('href', '/index.php?r=structure/page/view&id='+data.pageId);
					document.location.href='/index.php?r=structure/structure&id='+data.pageId;
				} else {
					that.displayErrors(data.errors);
				}
			}, 'json');
			return false;
		});
		
		jQuery('#page-properties .js-modal-title').text('Создание страницы');
		jQuery(this).text('Создать');		
		jQuery(this.dialogId).modal('show');
	},
	update: function() {
		var that = this;
		this.hideErrors();
		jQuery.post('/index.php?r=structure/page/update&mode=get&id='+this.currentId, {}, function(data){ 
			if (data.success == 1) {
				
				jQuery.each(data.attributes, function(index, value){ 
					id = '#page-'+index;
					field = jQuery(id);
					if (field.size() == 1) {
						tagName = field.prop("tagName");
					
						if (tagName == 'INPUT' && field.attr('type') == 'checkbox') {
							field.attr('checked', value?true:false);
						} else {
							field.val(value);
						}						
												
					}
				});				
			
				//навешиваем события на кнопку "сохранить"
				jQuery(that.saveButtonId).unbind('click').click(function(){
					jQuery.post('/index.php?r=structure/page/update&mode=save&id='+that.currentId, jQuery(that.formId).serialize(), function(data){
						if (data.success == 1) {
							document.location.href='/index.php?r=structure/structure&id='+data.pageId;
						} else {
							that.displayErrors(data.errors);
						}
					}, 'json');
					return false;
				});
				
				jQuery('#page-properties .js-modal-title').text('Редактирование страницы');
				jQuery(this).text('Сохранить');
				jQuery(that.dialogId).modal('show');
			}
		}, 'json');
	},
	move: function() {
		var that = this;
		this.hideErrors();
		jQuery.post('/index.php?r=structure/page/move&mode=get&id='+this.currentId, {}, function(data){ 
			$('#page_move_errors').hide();
			$('#move_page_id').val(that.currentId);
			
			if (data.success == 1) {
				select = $('#move_parent_id');
				select.children().not(":first").remove();
				currentStructureId = '';
				jQuery.each(data.pages, function(i, item){ 
					disabled = '';
					if (that.currentId == item.id) { 
						disabled = 'disabled="disabled"';
						currentStructureId = item.structure_id;
					}
					
					select.append('<option '+disabled+' data-structure_id="'+item.structure_id+'" value="'+item.id+'">'+item.title+'</option>');
				});
				$( "option[data-structure_id*='"+currentStructureId+"']" ).prop('disabled', true);
				
				$('#page-move').modal('show');
			}
			
		}, 'json');
	},
	remove: function() {
		if (confirm('Вы уверены, что хотите удалить эту страницу?')) {
			jQuery.post('/index.php?r=structure/page/delete', {id : this.currentId, domain: this.domain}, function(data){
				if (data.success == 1){ document.location.href='/index.php?r=structure/structure&'+(data.pageId ? '?id='+data.pageId : '');}
			}, 'json');
		}
	},
	displayErrors: function(errors) {
		var that = this;
		jQuery.each(errors, function(index, element){ 
		
			var _field = $('#page-'+index);
			if (!_field.next().hasClass('help-inline')) {
				$('<div class="help-inline"></div>').insertAfter('#page-'+index);
			}
			_field.next().html(element.join(', ')).show().closest('.form-group').addClass('has-error');
		});
	},
	hideErrors: function() {
		jQuery(this.formId).find('div.help-inline').css({display: 'none'});
		jQuery(this.formId).find('div.form-group').removeClass('has-error');
	},
	getDomain: function(url) {
		var result = null;
		var parameters = [];
		var urlParts = url.split('?');
		jQuery.each(urlParts[1].split('&'), function(i, e){
			var pair = e.split('=');
			parameters[pair[0]] = pair[1];
		});
		if (parameters.domain != undefined) { result = parameters.domain;}
		return result;
	}
}

//
function Block(settings) {
	this.blockId						= null;
	this.currentPageId 					= settings.currentPageId;
	this.dialogId						= settings.dialogId;
	this.saveButtonId					= settings.saveButtonId;
	this.formId							= settings.formId;
	this.visualEditorId					= settings.visualEditorId;
	this.textSettings					= settings.textSettings;
	this.widgetSettings					= settings.widgetSettings;
	
	this.currentWidgetAlias				= null;
}
Block.prototype = {
	process: function(action, e) {
		
		this.blockId = /^block_(.+?)$/.exec(jQuery(e).attr('id'))[1];
		
		switch (action) {
			case 'text': this.text(); break;
			case 'widget': this.widget(); break;
			case 'clear': this.clear(); break;
		}
	},
	text: function() {
		var that = this;
		//CKEDITOR.instances.textContent.setData('');
        $('#textContent').val('');
        //$('#textContent').setCode('');
		jQuery.post('/index.php?r=structure/block/get', {id : this.currentPageId, block_id : this.blockId}, function(data) {
			if (data.status == 1) {
				//jQuery(that.textSettings.dialogId).find('textarea[name="textContent"]').html(data.content);
                if (data.content){
                    $('#textContent').val(data.content);
                }
				//CKEDITOR.instances.textContent.setData(data.content);
				jQuery(that.textSettings.dialogId).modal('show');
			}
		}, 'json');
		
		//навешиваем события на кнопки "сохранить" и "обновить"
		jQuery(this.textSettings.saveButtonId).unbind('click').click(function() {
			
			jQuery.post('/index.php?r=structure/block/update', {id: that.currentPageId, block_id: that.blockId, content:  $('#textContent').val(), type_id: 2}, function() {
				
			});
			
            $('#textContent').val('');
            $('#textContent').setCode('');
			//CKEDITOR.instances.textContent.setData('');
			//jQuery(that.textSettings.dialogId).jqmHide();
            $('#text-content').modal('hide');
			jQuery(that.visualEditorId).attr('src', '/index.php?r=structure/page/view&id='+that.currentPageId);
			return false;
		});
		
			
	},
	widget: function() {
		var that = this;
		this.hideParameters();
		//получаем параметры виджета, если он уже вставлен в данный контекстный блок
		jQuery.post('/index.php?r=structure/block/get', {id: this.currentPageId, block_id: this.blockId}, function(data) {
            if (data != null && data.alias != undefined) {
				$('#widgetsStructure ul li').removeClass('active');
				$('#widgetsStructure ul li a[name="'+data.alias+'"]').parent().addClass('active');
				
                that.currentWidgetAlias = data.alias;
                
                //обращаемся за контентом настроек виджета
                that.getConfigByAlias(data.alias, that.currentPageId, that.blockId);
            }
            
			//если вошли параметры настройки, то устанавливаем их
			if (data != null && data.parameters != null) { that.applyWidgetParameters(data.parameters);}
			
			jQuery(that.widgetSettings.dialogId).modal('show');
		}, 'json');
	
		//навешиваем события на кнопки "сохранить" и "обновить"
		jQuery(that.widgetSettings.saveButtonId).unbind('click').click(function() {
		
			jQuery.post('/index.php?r=structure/block/update', {
				id: 				that.currentPageId, 
				block_id: 			that.blockId, 
				content: 			that.currentWidgetAlias, 
				config_parameters: 	jQuery('#widget-settings').serialize(),
				type_id: 			1
			}, function(response) {
				if (response.status == 1) {
					jQuery(that.visualEditorId).attr('src', '/index.php?r=structure/page/view&id='+that.currentPageId);
					$('#widget-structure').modal('hide');
				} else {
					jQuery.each(response.errors, function(key, value) {
						_id_err= 'widget-error-'+key;
						_field = $('#widget-'+key);
						_span  = $('#'+_id_err);
						if (_span.size() == 0) {
							_span = $('<span class="errorMessage" id="'+_id_err+'"></span>').insertBefore(_field);
						}
						_span.text(value);
					})
				}
			}, 'json');
			return false;
		});
	},
	hideParameters: function() {
		jQuery('div[name="widgetConfigBox"]').html('');
	},
	clear: function() {
		var that = this;
		if (confirm('Вы уверены, что хотите очистить этот блок?')) {
			jQuery.post('/index.php?r=structure/block/clear', {blockId: this.blockId, id: this.currentPageId}, function(data) {
				if (data.result == 1) {
					jQuery(that.visualEditorId).attr('src', '/index.php?r=structure/page/view&id='+that.currentPageId);
				} else {
					message.show(data.errorMessage);
				}
			}, 'json');
		}
	},
	getConfigByAlias: function(widgetAlias, pageId, blockAlias) {
		this.currentWidgetAlias = widgetAlias;
		var data = {
			widgetAlias: 	widgetAlias,
			pageId: 		pageId,
			blockId: 		blockAlias
		};
		//обращаемся за контентом настроек виджета
		jQuery.post('/index.php?r=structure/block/getwidgetconfig', data, function(data) { if (data != null && data.config != null) { jQuery('div[name=widgetConfigBox]').html(data.config);}}, 'json');
	},
	applyWidgetParameters: function(data) {
		jQuery.each(data, function(key, value) {
			var obj = jQuery('div[name="widgetConfigBox"]').find('*[name="'+key+'"]');
			switch (obj.attr('tagName')) { 
				case 'TEXTAREA':
					obj.html(value);
				break;
				case 'SELECT':
				default:
					obj.val(value);
				break;
			}
		});
	}
}

///
function Message(settings) {
	this.messageWindowId = settings.messageWindowId;
}
Message.prototype = {
	show: function(str) {
		var that = this;
		jQuery(this.messageWindowId).fadeIn(300).html('<p>'+str+'</p>');
		jQuery('.mask_massage-window').fadeIn(300);
		setTimeout(function() { that.hide()}, 3000);
	},
	hide: function() {
		jQuery(this.messageWindowId).fadeOut(300).html('');
		jQuery('.mask_massage-window').fadeOut(300);
	}
}

$(document).ajaxError(function( event, jqxhr, settings, thrownError ) {
	alert(thrownError);
})


jQuery('#page-move-button').click(function(){
	jQuery.post('/index.php?r=structure/page/move&mode=save&id='+$('#move_page_id').val()+'&parent_id='+$('#move_parent_id').val(), {}, function(data){
		if (data.success == 1) {
			document.location.href='/index.php?r=structure/structure&id='+data.pageId;
		} else {
			errors = '';
			jQuery.each(data.errors, function(key, value) {
				errors+= value+"<br/>";
			})			
			$('#page_move_errors').html(errors).show();

		}
	}, 'json');	
});