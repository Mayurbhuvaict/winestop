require([
    'jquery',
    "jquery/ui"
], function($){
    'use strict';
	jQuery(document).ready(function(){
		jQuery('.nav-item.level1 .mega-col').each(function(){
			var sub = jQuery(this).children('.nav-item.level2');
			var length = sub.length;
			if(length>4){
				jQuery(this).append(" <a class='viewmore'>View more</a>");
				sub.each(function(key,val){
					if(key>3){
						jQuery(val).addClass('levelsub subhide');
					}
				});
			}
			});
			jQuery('.viewmore').click(function(){
				var obj = jQuery(this);
				var level1 = obj.parent();
				var hiddenSub = level1.find('.levelsub');
				hiddenSub.toggleClass('subhide');
				level1.toggleClass('active');
				if(obj.html()=='View more'){
					obj.html('View less');
				}else{
					obj.html('View more');
				}
			});			
	});
});

