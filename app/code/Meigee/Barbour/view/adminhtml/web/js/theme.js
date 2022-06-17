require(['jquery'], function ($)
{
	jQuery(window).load(function(){
		require(["meigeeColorpicker"], function(color)
		{
			jQuery('input.meigee-color-picker').colorPicker({
				renderCallback: function($elm, toggled) {
					if (toggled === false) {
						multipleColors();
					}
				}
			});
			
			function multipleColors() {
				jQuery('.meigee-group .multiple-result').each(function(){
					values = [];
					inputs = jQuery(this).parent().find('.meigee-color-picker.multiple');
					jQuery(inputs).each(function(){
						values.push(jQuery(this).val());
					});
					new_values = values.join('; ');
					
					jQuery(this).attr('value', '').attr('value', new_values);
				});
			}
			
		
	
	
			multipleColors();
		
			jQuery('label.divider').parents('tr').addClass('divider-row');

			jQuery('.section-config').each(function(){
				this_el = jQuery(this);
				tabItems = jQuery('<table class="tabs-items-wrapper" />');
				tabItemsInner = jQuery('<tbody class="tabs-items" />');
				tabItemsInner.appendTo(tabItems);
			
				if(this_el.find(jQuery('.tab-item').length)) {
					this_el.removeClass('active').find('.entry-edit-head a').removeClass('open');
				}
				jQuery('.tab-item').parents('fieldset.config').hide();
				this_el.find(jQuery('.tab-item')).each(function(i){
					jQuery(this).parents('tr').addClass('tab-title').attr('data-tab-item', i).nextAll('tr').attr('data-tab-item-content', i);
					jQuery(this).parents('tbody').addClass('tabs-content-wrapper').parent('table').before(tabItems);
					jQuery(this).parents('tr').removeAttr('data-tab-item-content').appendTo(tabItems);
					setTimeout(function(){
						firstLoad();
					}, 1000);
					
				});
			});
			
			jQuery(document).on('click', '.section-config .tab-title', function(){
				jQuery(this).parents('.section-config').find(jQuery('.tab-title')).removeClass('active-tab');
				jQuery(this).addClass('active-tab');
				index = jQuery(this).data('tabItem');

				jQuery(this).parents('table').next('table').find('tr').removeClass('show');
				jQuery(this).parents('table').next('table').find('tr[data-tab-item-content="'+index+'"]').addClass('show');
			});
			
			function firstLoad(){
				jQuery('.tabs-content-wrapper').find('tr[data-tab-item-content="0"]').addClass('show');
				jQuery('.tab-title:first-child').trigger('click');
			}
		
		});
	});
	
});










