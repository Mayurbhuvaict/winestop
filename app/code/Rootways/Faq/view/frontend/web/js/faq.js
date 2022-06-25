require(['jquery'],function($){
	jQuery(document).ready(function(e) {
		var base_url = jQuery("#base_url").val();
		jQuery(".cat-title").click(function() {
			jQuery(".faq-loader").css("display",'block');
			jQuery(".faq-left-link .cat-title").removeClass('active');
			jQuery(this).addClass("active");
			jQuery(".faq-right_title strong").text(jQuery(this).html());
			var cat_id = jQuery(this).attr('id');
			catid = cat_id.replace(/\D/g,'');
			jQuery("#cur_cat").val(catid);
			jQuery.ajax({
				url : base_url+"/faq/index/ajax",
				type: "GET",
				data : "catid="+catid,
				success: function(data)
				{
					document.getElementById("faqlisting").innerHTML = data;
					jQuery(".faq-loader").css("display",'none');
					//original.click(replace);
				}
			});
            if (jQuery(window).width() <= 768) {
                jQuery('html, body').animate({
                    scrollTop: jQuery(".faq-right").offset().top
                }, 1000);
            }
		});
		jQuery('#faqsearch').keyup(function(e){
			if(e.which == 13){
				if(jQuery("#faqsearch").val() === ''){
					alert('Please enter keyword(s) in search box.');
				}else{
					jQuery(".faq-loader").css("display",'block');
					var search_results_for = 'Search result(s) for';
					jQuery(".faq-right_title strong").text(search_results_for+" '"+jQuery("#faqsearch").val()+"'");
					var search_term = jQuery("#faqsearch").val();
					jQuery.ajax({
						url : base_url+"/faq/index/ajaxsearch",
						type: "GET",
						data : "search_term="+search_term,
						success: function(data)
						{
							 document.getElementById("faqlisting").innerHTML = data;
							 jQuery(".faq-loader").css("display",'none');
						}
					});
				}
			}
		});

		jQuery("#searchfaq").click(function() {
			if(jQuery("#faqsearch").val() === ''){
				alert('Please enter keyword(s) in search box.');
			}else{
				jQuery(".faq-loader").css("display",'block');
				var search_results_for = 'Search result(s) for';
				jQuery(".faq-right_title strong").text(search_results_for+" '"+jQuery("#faqsearch").val()+"'");
				var search_term = jQuery("#faqsearch").val();
				jQuery.ajax({
					url : base_url+"/faq/index/ajaxsearch",
					type: "GET",
					data : "search_term="+search_term,
					success: function(data)
					{
                        document.getElementById("faqlisting").innerHTML = data;
                        jQuery(".faq-loader").css("display",'none');
					}
				});
			}
		});

		jQuery(".faqpagination").click(function() {
			jQuery(".faq-loader").css("display",'block');
			var catid = jQuery("#cur_cat").val();
			var page_num = jQuery(this).attr('id');
			pagenum = page_num.replace(/\D/g,'');
			jQuery.ajax({
				url : base_url+"/faq/index/ajax",
				type: "GET",
				data : "catid="+catid+"&page_num="+pagenum,
				success: function(data)
				{
					document.getElementById("faqlisting").innerHTML = data;
					jQuery(".faq-loader").css("display",'none');
				}
			});
		});
        
        
        jQuery(document).delegate(".faq-question","click",function(e) {
            jQuery(this).closest(".faq_list").toggleClass("active_que");
        });
        
		jQuery(".faq-question").click(function() {
			var car_que = jQuery(this).attr('id');
			car_que = car_que.replace(/\D/g,'');
			jQuery("#ans_id_"+car_que).slideToggle();
		});
	});
});

function FaqqusTog(id){
	car_que = id.replace(/\D/g,'');
	jQuery("#ans_id_"+car_que).slideToggle();	
}

function PageChange(id){
	jQuery(".faq-loader").css("display",'block');
	var base_url = jQuery("#base_url").val();
	var catid = jQuery("#cur_cat").val();
	//var page_num = jQuery(this).attr('id');
	pagenum = id;
	jQuery.ajax({
		url : base_url+"/faq/index/ajax",
		type: "GET",
		data : "catid="+catid+"&page_num="+pagenum,
		success: function(data)
		{
			document.getElementById("faqlisting").innerHTML = data;
			jQuery(".faq-loader").css("display",'none');
		}
	});
}

function PageChangeSearch(id){
	jQuery(".faq-loader").css("display",'block');
	var base_url = jQuery("#base_url").val();
	var search_term = jQuery("#faqsearch").val();
	pagenum = id;
	jQuery.ajax({
		url : base_url+"/faq/index/ajaxsearch",
		type: "GET",
		data : "search_term="+search_term+"&page_num="+pagenum,
		success: function(data)
		{
			document.getElementById("faqlisting").innerHTML = data;
			jQuery(".faq-loader").css("display",'none');
		}
	});

}
	