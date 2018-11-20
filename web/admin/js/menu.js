/**
 * Перерисовать пункт меню на основе данных (добавить отображение неактивности и тп)
 *
 * @param jQuery elem объект LI пункта меню
 * @param object data объект данных о пункте меню
 */

function redrawMenuRow(elem, data) {
	var lang = $('html').attr('lang');
    var titleElem = elem.find('span.title:first');
    titleElem.text(data['title_'+lang]);
    if (data['is_active'] == 1) {
        titleElem.addClass('active');
        titleElem.removeClass('na');
    } else {
        titleElem.addClass('na');
        titleElem.removeClass('active');
    }
}

/**
 * Обработка нажатия Удалить возле пункта меню
 * @param elem
 */
function delClick(elem) {
    $(elem).closest('li').remove();
	return false
}

/**
 * Обработка нажатия Редактировать возле пункта меню
 * @param elem
 */
function editClick(elem){
	var listElem = $(elem).closest('li');
	
    var dataList = listElem.data('data');
	
	/*
    itemContainer.find('textarea,select,input').each(function(){
        if ($(this).attr("type") == "checkbox")
        {
            $(this).attr('checked', false);
        } else {
            $(this).val('');
        }
    });
	*/

    /*var parentIdSelect = itemContainer.find('select[name=parent_id]');
    parentIdSelect.empty().append('<option value="0"></option>');
    $.each(goLi($('.menuContainer')), function(){
    	if (this.id!=dataList.id) {
    		parentIdSelect.append('<option value="'+this.id+'">'+this.title+'</option>');
    	}
    });*/
	

	jQuery.each(dataList, function(index, value){ 
		
		console.log(dataList);
		
		//console.log(index +' : '+ value);
		
		id = '#menulink-'+index;
		field = jQuery(id);
		
		
		if (field.size() == 1) {
			tagName = field.prop("tagName");
			if (tagName == 'INPUT' && field.attr('type') == 'checkbox') {
				field.attr('checked', value?true:false);
				inputHidden = field.closest('.form-group').find('input[type="hidden"]');
				inputHidden.val(value);
			} else if (tagName == 'INPUT' && field.attr('name') == 'image') {
				$('#menu_link_image').attr('src', dataList[index]);
			} else {
				field.val(value);
			}														
		}	
	});	

	$('input[type="checkbox"]', itemContainer).change(function(){
		inputHidden = $(this).closest('.form-group').find('input[type="hidden"]');
		inputHidden.val($(this).is(':checked')?1:0);		
	})
    
	/*
    for(var i in dataList)
    {
        console.log(i);
		
		var elems = itemContainer.find('*[name="'+i+'"]');
        
		elems.each(function(){    
			if (i == 'file') {
				$('#menu_link_image').attr('src', dataList[i]);
			} else {
			
				if ($(this).attr("type") == "checkbox") {
					$(this).attr('checked', dataList[i] == '1' ? true : false);
				} else {
					$(this).val();
				}
			}
        });
	}
	*/

    itemContainer.data('currentEdited', listElem);
    itemContainer.modal('show');
    itemLayer.show();
    pageIdField.change();
};

/**
 * Инициализация сортировки
 * @param target
 */
function setNestedSort(target){
    if (menuType=='NORMAL'){
		target.sortable({
			opacity: .3,
			currentNestingClass: 'current-nesting',
		});
		target.disableSelection();
       /* target.Sortable(
        {
            accept: 'page-item2',
            opacity: .5,
            helperclass: 'helper',
			cursor: 'move',
			tolerance: 'pointer',
			forcePlaceholderSize: true,
        });*/
    } else {
		 target.nestedSortable({
			handle: 'div',
			items: 'li',
			toleranceElement: '> div'
			});
		}
      /*  target.NestedSortable(
        {
            accept: 'page-item2',
            opacity: .5,
            helperclass: 'helper',
            nestingPxSpace: '20',
            currentNestingClass: 'current-nesting',
			cursor: 'move',
			tolerance: 'pointer',
			forcePlaceholderSize: true,
        });*/
    }

$(function(){
    // пройдем по всем пунктам меню и отрисуем их в соответствии с их параметрами
    menuLinks.each(function(){
        var dataObj = $(this).data('data');
        if (dataObj) {
            redrawMenuRow($(this), dataObj);
        }
    });

    setNestedSort(menuContainer);

    siteStructureLinks.removeAttr('onclick');
    siteStructureLinks.click(function()
    {
		if ($(this).data('is_not_menu')) {
			alert('This Page disabled for menu!!!');
			return false;
		}
		
		
		var addedElem = $('<li class="clear-element page-item2 sort-handle right"><div><span class="title">'+$(this).text()+'</span><a onclick="delClick(this);return false" class="del" href="#del"><i class="icon-trash"></i></a><a onclick="editClick(this);return false" class="ed" href="#edit"><i class="icon-pencil"></i></a></div></li>');
        var properties = {title:$(this).text(), page_id:$(this).attr('id'), is_active:'1', image:''};
        
        for (var i in siteStructureTitles[jQuery(this).attr('id')]) {
        	properties['title_'+i] = siteStructureTitles[jQuery(this).attr('id')][i];
        }
        addedElem.data('data', properties);
        
        addedElem.appendTo(menuContainer);
        redrawMenuRow(addedElem, addedElem.data('data'));
        setNestedSort(menuContainer);
        
        menuTitles[jQuery(this).attr('id')] = siteStructureTitles[jQuery(this).attr('id')];
		
        return false;
    });


    pageIdField.change(function(){
        if($(this).val() == 0) {
            linkRow.show();
        } else {
            linkRow.hide();
        }
    });

    cancelButton.click(function(){
        itemContainer.data('currentEdited', null);
        itemContainer.modal('hide');
        itemLayer.hide();
    });

    applyButton.click(function(){
        var dataNew = {};
        var listLink = itemContainer.data('currentEdited');
        
		itemContainer.find('select, input, textarea').each(function(){
            if ($(this).attr("type") != "checkbox") {
                dataNew[$(this).attr('name')] = $(this).val();
            } 
        });
		dataNew['is_active'] = $('#menulink-is_active').is(':checked') ? 1 : 0;
				
        listLink.data('data', dataNew);
        if (typeof dataNew.title!='undefined') {
        	listLink.find('span.title:eq(0)').text(dataNew.title);
        }
        itemContainer.modal('hide');
        itemLayer.hide();
        itemContainer.data('currentEdited', null);
			
        redrawMenuRow(listLink, dataNew);
    });
	
    $('#form-menu').submit(function() {
		$('#resultContainer').val($.toJSON(goLi($('.menuContainer'))));
    });

});

function goLi(parentElem){
    var allElemsData = new Array;
    parentElem.find('>ol>li').each(function(){
        var elemData = $(this).data('data');
        var childs = $(this).find('>ol>li');
        if (childs.size() > 0) {
            elemData['childs'] = goLi($(this));
        }
        allElemsData.push(elemData);
    });
    return allElemsData;
}

function readImage(file) {
  
    var reader = new FileReader();
    var image  = new Image();
  
    reader.readAsDataURL(file);  
    reader.onload = function(_file) {
        image.src    = _file.target.result;  
        image.onload = function() {
            $('#menu_link_image').attr('src', this.src);
			$('#menulink-image').val(this.src);
        };
        image.onerror= function() {
            alert('Invalid file type: '+ file.type);
        };      
    };   
}

$("#file").change(function (e) {
    if(this.disabled) return alert('File upload not supported!');
    var F = this.files;
    if(F && F[0]) for(var i=0; i<F.length; i++) readImage( F[i] );
});