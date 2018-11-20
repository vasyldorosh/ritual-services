Array.prototype.in_array = function(p_val) {
	for(var i = 0, l = this.length; i < l; i++)	{
		if(this[i] == p_val) {
			return true;
		}
	}
	return false;
}

$(function(){
	/* replace all &rarr; on the span */
	$('h1').each(function() {
		$(this).html($(this).html().replace(/в†’/g, '<span />'));
	})
	
	/* left sidebar height */
	function sidebarHeight() {
		if($('#sidebar').length) {
			$('#sidebar').height('auto');
			var h = $('#wrapper').height() - $('#content').offset().top -41;
			$('#sidebar').height(h);
		}
		
		if ( $('#visualEditor').length ) {
			vis_editor_height()
		}
	}
	setTimeout(function() {
		sidebarHeight()
	},100)
	
	$(window).on('resize scroll', function(){
		sidebarHeight()
	})
	
	$('#siteStructure').on('click', '.hitarea', function() {
		setTimeout(function() {
			sidebarHeight()
		})
	})
	
	/* Header nav toogle */
	var header_nav_vis = 1;

	if ($.cookie('header_nav_vis')) {
		header_nav_vis = $.cookie('header_nav_vis');
	};

	if (header_nav_vis == 0) {
		$('#header_nav').css({'margin-top':-55});
		$('#header_nav .main-nav span').css({'opacity':0});
		$('#header_nav_toggle').addClass('active');
		if($('#sidebar').length) {
			$('#sidebar').css({'height': $('#sidebar').height() + 55})
		}
	};

	$('#header_nav_toggle').on('click', function() {
		if (header_nav_vis == 0) {
			$('#header_nav .main-nav span').animate({opacity:1}, 200, function() {
				$('#header_nav_toggle').removeClass('active');
			})
			$('#header_nav').animate({marginTop:0}, 200);
			
			if( $('#sidebar').length ) {
				if( $('#sidebar').height() > $('#text').height() ) {
					$('#sidebar').animate({height: $('#sidebar').height() - 55}, 200)
				} else {
					$('#sidebar').animate({height: $('#text').height()}, 200)
				}
			}
			
			header_nav_vis = 1;
		}
		else {
			$('#header_nav .main-nav span').animate({opacity:0},200, function(){
				$('#header_nav_toggle').addClass('active');
			});
			$('#header_nav').animate({marginTop:-55},200);

			
			if( $('#sidebar').length ) {
				if( $('#sidebar').height() > $('#text').height() ) {
					$('#sidebar').animate({height: $('#sidebar').height() + 55}, 200)
				} else {
					$('#sidebar').animate({height: $('#text').height()}, 200)
				}
			}
			
			header_nav_vis = 0;
		};
		$.cookie('header_nav_vis', header_nav_vis);
		return false;
	});

	/* Sidebar toogle */
	if( $('#sidebar').length ) {
		var sidebar_vis = 1;

		if ($.cookie('sidebar_vis')) {
			sidebar_vis = $.cookie('sidebar_vis');
		};

		if (sidebar_vis == 0) {
			$('#sidebar').css({'margin-left' : -230});
			$('#text').css({'margin-left' : '0'});
			$('#text_content').css({ paddingLeft: 25 });
			$('#sidebar_toggle').addClass('active');
		};

		$('#sidebar_toggle').on('click', function() {
			if (sidebar_vis == 1) {
				$('#sidebar').animate({ marginLeft: -230 },200, function(){
					$('#sidebar_toggle').addClass('active');			
				});
			
				$('#text').animate({ marginLeft: 0 },200);
				$('#text_content').animate({ paddingLeft: 25 },200);

				sidebar_vis = 0;
			} else {
				$('#sidebar').animate({ marginLeft: 0 },200, function(){
					$('#sidebar_toggle').removeClass('active');	
				});
				$('#text').animate({ marginLeft: 230 },200);
				$('#text_content').animate({ paddingLeft: 20 },200);
				
				sidebar_vis = 1;
			};
			$.cookie('sidebar_vis', sidebar_vis);
			return false;
		});
	}
	
	/* toggle filters */
	$('.filters .filter-thead').on('click', function() {
		$('span.toggle',this).toggleClass('closed').html( $('span.toggle', this).text() == 'РЎРІРµСЂРЅСѓС‚СЊ ' ? 'Р Р°Р·РІРµСЂРЅСѓС‚СЊ <span class="caret"></span>' : 'РЎРІРµСЂРЅСѓС‚СЊ <span class="caret"></span>');
		
		$('span.toggle',this).toggleClass('active');
		
		$('.items.filters').toggleClass('navbar-inner');
		return false;
	});
	
	$('.filters .filter-thead').toggle(function() {
		$('.table-wrap').animate({ height: $('.items.filters table').outerHeight(true), 'border-width': 1 },300, function() { sidebarHeight() });
	}, function() {
		$('.table-wrap').animate({ height: 0, 'border-width': 0 },300, function() { sidebarHeight() });
	});
	
	/* site visual editor height */
	function vis_editor_height() {
		if($('#siteStructure').length) {
			setTimeout(function() {
				$('#visualEditor').height( $('#siteStructure').height() )
			})
		}
	}
	
	$("body").on("change", ".toogle-all-checkboxes", function() {
		_self = $(this);
		$('input[type="checkbox"]', _self.parents('table')).each(function() {
			if (!$(this).prop("disabled"))
				$(this).prop("checked", _self.is(":checked"))
		})		
	})	
	
});

function showActiveTab() {
	$('#action-options .js-params').hide();
	
	var value = $('select[name="action"]').val();
	$('.'+value).show();
}	
$('body').on('change', 'select[name="action"]', function(){
	showActiveTab();
});

$.extend({
  password: function (length, special) {
    var iteration = 0;
    var password = "";
    var randomNumber;
    if(special == undefined){
        var special = false;
    }
    while(iteration < length){
        randomNumber = (Math.floor((Math.random() * 100)) % 94) + 33;
        if(!special){
            if ((randomNumber >=33) && (randomNumber <=47)) { continue; }
            if ((randomNumber >=58) && (randomNumber <=64)) { continue; }
            if ((randomNumber >=91) && (randomNumber <=96)) { continue; }
            if ((randomNumber >=123) && (randomNumber <=126)) { continue; }
        }
        iteration++;
        password += String.fromCharCode(randomNumber);
    }
    return password;
  }
});

function buildNewPassword(id) {
	$('#'+id).val($.password(12,false));
}

$('document').ready(function() {
	$('body').show();
	showActiveTab();

	$( "input[id$='password_hash']" ).val('');
	
	$('body').on('click', '.js-redirect', function(e) {
		e.preventDefault();
		window.location = $(this).attr('href');
	})
})