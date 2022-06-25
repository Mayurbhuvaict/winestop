<?php 
namespace Meigee\Barbour\Model;

class cssGenerate
{
	
	public function __construct(
		\Meigee\Barbour\Block\Frontend\CustomDesign $customDesign
    ) {
		$this->_customDesign = $customDesign;
    }

	public function getDesignValues($sectionId) {
		// General options
		$bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_bg');
		$bg_page = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_bg_page');
		
		$border = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_border');
		$elements_border = $this->_customDesign->getConfigData($sectionId, 'general_options', 'elements_border');
		$general_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_color');
		$title_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'title_color');
		$links_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'links_color_d');
		$links_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'links_color_h');
		$links_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'links_color_a');
		$links_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'links_color_f');
		// Prices
		$regular_price = $this->_customDesign->getConfigData($sectionId, 'general_options', 'regular_price');
		$special_price = $this->_customDesign->getConfigData($sectionId, 'general_options', 'special_price');
		$old_price = $this->_customDesign->getConfigData($sectionId, 'general_options', 'old_price');
		$sold_out = $this->_customDesign->getConfigData($sectionId, 'general_options', 'sold_out');
		// Breadcrumbs
		$breadcrumbs_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_color_d');
		$breadcrumbs_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_color_h');
		$breadcrumbs_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_color_a');
		$breadcrumbs_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_color_f');
		$breadcrumbs_withbg_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_withbg_color_d');
		$breadcrumbs_withbg_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_withbg_color_h');
		$breadcrumbs_withbg_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_withbg_color_a');
		$breadcrumbs_withbg_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'breadcrumbs_withbg_color_f');
		// Form Elements
		$general_input_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_input_color');
		$general_input_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_input_bg');
		$general_select_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_select_color');
		$general_select_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_select_bg');
		// Reviews design
		$reviews_rating_general_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'reviews_rating_general_color');
		$reviews_rating_active_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'reviews_rating_active_color');
		$reviews_rating_links_color_hover = $this->_customDesign->getConfigData($sectionId, 'general_options', 'reviews_rating_links_color_hover');

		// Buttons Tab
        // Button 1
		$general_btn1_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_color_d');
		$general_btn1_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_color_h');
		$general_btn1_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_color_a');
		$general_btn1_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_color_f');
		$general_btn1_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_bg_d');
		$general_btn1_bg_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_bg_h');
		$general_btn1_bg_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_bg_a');
		$general_btn1_bg_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_bg_f');
		$general_btn1_border = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_border_d');
		$general_btn1_border_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_border_h');
		$general_btn1_border_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_border_a');
		$general_btn1_border_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn1_border_f');
		// Button 2
		$general_btn2_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_color_d');
		$general_btn2_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_color_h');
		$general_btn2_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_color_a');
		$general_btn2_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_color_f');
		$general_btn2_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_bg_d');
		$general_btn2_bg_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_bg_h');
		$general_btn2_bg_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_bg_a');
		$general_btn2_bg_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn2_bg_f');
		// Button 3
		$general_btn3_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_color_d');
		$general_btn3_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_color_h');
		$general_btn3_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_color_a');
		$general_btn3_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_color_f');
		$general_btn3_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_bg_d');
		$general_btn3_bg_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_bg_h');
		$general_btn3_bg_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_bg_a');
		$general_btn3_bg_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_btn3_bg_f');
		// Slider arrows
		$general_slider_btns_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_color_d');
		$general_slider_btns_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_color_h');
		$general_slider_btns_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_color_a');
		$general_slider_btns_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_color_f');
		$general_slider_btns_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_bg_d');
		$general_slider_btns_bg_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_bg_h');
		$general_slider_btns_bg_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_bg_a');
		$general_slider_btns_bg_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_bg_f');
		$general_slider_btns_border = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_border_d');
		$general_slider_btns_border_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_border_h');
		$general_slider_btns_border_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_border_h');
		$general_slider_btns_border_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_border_a');
		$general_slider_btns_border_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_slider_btns_border_f');
		// Home subscribe
		$general_home_subscribe_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_bg');
		$general_home_subscribe_title_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_title_color');
		$general_home_subscribe_text_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_text_color');
		$general_home_subscribe_input_bg = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_input_bg');
		$general_home_subscribe_input_color = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_input_color');
		$general_home_subscribe_btn_bg_d = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_bg_d');
		$general_home_subscribe_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_bg_h');
		$general_home_subscribe_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_bg_a');
		$general_home_subscribe_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_bg_f');
		$general_home_subscribe_btn_color_d = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_color_d');
		$general_home_subscribe_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_color_h');
		$general_home_subscribe_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_color_a');
		$general_home_subscribe_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_color_f');
		$general_home_subscribe_btn_border_d = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_border_d');
		$general_home_subscribe_btn_border_h = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_border_h');
		$general_home_subscribe_btn_border_a = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_border_a');
		$general_home_subscribe_btn_border_f = $this->_customDesign->getConfigData($sectionId, 'general_options', 'general_home_subscribe_btn_border_f');

		// Header
		$header_bg = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_bg');
		$header_color = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_color');
		$header_border = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_border');
        $mobile_menu_icon_color = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'mobile_menu_icon_color');
        
		// Search
		$header_search_bg = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_bg');
		$header_search_color = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_color');
		$header_search_border = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_border');
		$header_search_btn_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_bg_d');
		$header_search_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_bg_h');
		$header_search_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_bg_a');
		$header_search_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_bg_f');
		$header_search_btn_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_color_d');
		$header_search_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_color_h');
		$header_search_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_color_a');
		$header_search_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search_btn_color_f');
		$header_search2_btn_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_color_d');
		$header_search2_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_color_h');
		$header_search2_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_color_a');
		$header_search2_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_color_f');
		$header_search2_btn_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_bg_d');
		$header_search2_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_bg_h');
		$header_search2_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_bg_a');
		$header_search2_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_search2_btn_bg_f');
		// Account
		$header_account_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_bg_d');
		$header_account_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_bg_h');
		$header_account_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_bg_a');
		$header_account_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_bg_f');
		$header_account_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_color_d');
		$header_account_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_color_h');
		$header_account_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_color_a');
		$header_account_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_color_f');
		$header_account_dropdown_bg = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_bg');
		$header_account_dropdown_links_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_links_color_d');
		$header_account_dropdown_links_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_links_color_h');
		$header_account_dropdown_links_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_links_color_a');
		$header_account_dropdown_links_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_links_color_f');
		$header_account_dropdown_links_border = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_links_border');
		$header_account_dropdown_select_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_bg_d');
		$header_account_dropdown_select_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_bg_h');
		$header_account_dropdown_select_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_bg_a');
		$header_account_dropdown_select_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_bg_f');
		$header_account_dropdown_select_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_color_d');
		$header_account_dropdown_select_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_color_h');
		$header_account_dropdown_select_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_color_a');
		$header_account_dropdown_select_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_account_dropdown_select_color_f');
		// Cart
		$header_cart_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_bg_d');
		$header_cart_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_bg_h');
		$header_cart_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_bg_a');
		$header_cart_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_bg_f');
		$header_cart_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_color_d');
		$header_cart_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_color_h');
		$header_cart_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_color_a');
		$header_cart_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_color_f');
		$header_cart_border_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_border_d');
		$header_cart_border_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_border_h');
		$header_cart_border_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_border_a');
		$header_cart_border_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_border_f');
		$header_cart_qty_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_color_d');
		$header_cart_qty_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_color_h');
		$header_cart_qty_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_color_a');
		$header_cart_qty_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_color_f');
		$header_cart_qty_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_bg_d');
		$header_cart_qty_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_bg_h');
		$header_cart_qty_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_bg_a');
		$header_cart_qty_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_qty_bg_f');
		$header_cart_dropdown_bg = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_bg');
		$header_cart_dropdown_color = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_color');
		$header_cart_dropdown_border = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_border');
		$header_cart_dropdown_actions_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_actions_color_d');
		$header_cart_dropdown_actions_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_actions_color_h');
		$header_cart_dropdown_actions_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_actions_color_a');
		$header_cart_dropdown_actions_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_actions_color_f');
		$header_cart_dropdown_product_price_color = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_product_price_color');
		$header_cart_dropdown_product_qty_bg = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_product_qty_bg');
		$header_cart_dropdown_product_qty_color = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_product_qty_color');
		$header_cart_dropdown_total_price_color = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_total_price_color');
		$header_cart_dropdown_cart_btn_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_bg_d');
		$header_cart_dropdown_cart_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_bg_h');
		$header_cart_dropdown_cart_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_bg_a');
		$header_cart_dropdown_cart_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_bg_f');
		$header_cart_dropdown_cart_btn_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_color_d');
		$header_cart_dropdown_cart_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_color_h');
		$header_cart_dropdown_cart_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_color_a');
		$header_cart_dropdown_cart_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_color_f');
		$header_cart_dropdown_cart_btn_border_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_border_d');
		$header_cart_dropdown_cart_btn_border_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_border_h');
		$header_cart_dropdown_cart_btn_border_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_border_a');
		$header_cart_dropdown_cart_btn_border_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_cart_btn_border_f');
		$header_cart_dropdown_checkout_btn_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_bg_d');
		$header_cart_dropdown_checkout_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_bg_h');
		$header_cart_dropdown_checkout_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_bg_a');
		$header_cart_dropdown_checkout_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_bg_f');
		$header_cart_dropdown_checkout_btn_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_color_d');
		$header_cart_dropdown_checkout_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_color_h');
		$header_cart_dropdown_checkout_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_color_a');
		$header_cart_dropdown_checkout_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_cart_dropdown_checkout_btn_color_f');
		// Social links
		$header_socials_bg_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_bg_d');
		$header_socials_bg_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_bg_h');
		$header_socials_bg_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_bg_a');
		$header_socials_bg_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_bg_f');
		$header_socials_color_d = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_color_d');
		$header_socials_color_h = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_color_h');
		$header_socials_color_a = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_color_a');
		$header_socials_color_f = $this->_customDesign->getConfigData($sectionId, 'header_tabs', 'header_socials_color_f');

		//Mega menu
		$header_menu_bg = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_bg');
		$header_menu_item_bg_d = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_bg_d');
		$header_menu_item_bg_h = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_bg_h');
		$header_menu_item_bg_a = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_bg_a');
		$header_menu_item_bg_f = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_bg_f');
		$header_menu_item_color_d = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_color_d');
		$header_menu_item_color_h = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_color_h');
		$header_menu_item_color_a = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_color_a');
		$header_menu_item_color_f = $this->_customDesign->getConfigData($sectionId, 'mega_menu_tabs', 'header_menu_item_color_f');

		//Footer
		$footer_bg = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_bg');
		$footer_color = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_color');
		$footer_border = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_border');
		$footer_title_color = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_title_color');
		$footer_links_color_hover = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_links_color_hover');
		$footer_socials_color_d = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_color_d');
		$footer_socials_color_h = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_color_h');
		$footer_socials_color_a = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_color_a');
		$footer_socials_color_f = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_color_f');
		$footer_socials_bg_d = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_bg_d');
		$footer_socials_bg_h = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_bg_h');
		$footer_socials_bg_a = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_bg_a');
		$footer_socials_bg_f = $this->_customDesign->getConfigData($sectionId, 'footer_tabs', 'footer_socials_bg_f');

		//Category
		$general_product_hover1_color_d = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_color_d');
		$general_product_hover1_color_h = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_color_h');
		$general_product_hover1_color_a = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_color_a');
		$general_product_hover1_color_f = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_color_f');
		$general_product_hover1_bg_d = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_bg_d');
		$general_product_hover1_bg_h = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_bg_h');
		$general_product_hover1_bg_a = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_bg_a');
		$general_product_hover1_bg_f = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_bg_f');
		$general_product_hover1_cart_color_d = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_color_d');
		$general_product_hover1_cart_color_h = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_color_h');
		$general_product_hover1_cart_color_a = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_color_a');
		$general_product_hover1_cart_color_f = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_color_f');
		$general_product_hover1_cart_bg_d = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_bg_d');
		$general_product_hover1_cart_bg_h = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_bg_h');
		$general_product_hover1_cart_bg_a = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_bg_a');
		$general_product_hover1_cart_bg_f = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_product_hover1_cart_bg_f');
		$general_pagination_item_color_d = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_color_d');
		$general_pagination_item_color_h = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_color_h');
		$general_pagination_item_color_a = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_color_a');
		$general_pagination_item_color_f = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_color_f');
		$general_pagination_item_border_d = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_border_d');
		$general_pagination_item_border_h = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_border_h');
		$general_pagination_item_border_a = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_border_a');
		$general_pagination_item_border_f = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_border_f');
		$general_pagination_item_bg_d = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_bg_d');
		$general_pagination_item_bg_h = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_bg_h');
		$general_pagination_item_bg_a = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_bg_a');
		$general_pagination_item_bg_f = $this->_customDesign->getConfigData($sectionId, 'category_tabs', 'general_pagination_item_bg_f');

		//Sidebar
		$cat_sidebar_bg = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_bg');
		$cat_sidebar_border = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_border');
		$cat_sidebar_color = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_color');
		$cat_sidebar_links_color_hover = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_links_color_hover');
		$cat_sidebar_title_color = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_title_color');
		$meigee_sidebar_product_blocks_product_color_d = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_product_blocks_product_color_d');
		$meigee_sidebar_product_blocks_product_color_h = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_product_blocks_product_color_h');
		$meigee_sidebar_product_blocks_product_color_a = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_product_blocks_product_color_a');
		$meigee_sidebar_product_blocks_product_color_f = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_product_blocks_product_color_f');
		$cat_sidebar_price_color = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_price_color');
		$cat_sidebar_special_price_color = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_special_price_color');
		$cat_sidebar_old_price_color = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'cat_sidebar_old_price_color');

		//Sidebar blocks
		$meigee_sidebar_blocks_shopby_title_color = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_title_color');
		$meigee_sidebar_blocks_shopby_subtitle_color = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_subtitle_color');
		$meigee_sidebar_blocks_shopby_links_color_d = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_links_color_d');
		$meigee_sidebar_blocks_shopby_links_color_h = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_links_color_h');
		$meigee_sidebar_blocks_shopby_links_color_a = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_links_color_a');
		$meigee_sidebar_blocks_shopby_links_color_f = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_links_color_f');
		$meigee_sidebar_blocks_shopby_swatches_color_d = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_color_d');
		$meigee_sidebar_blocks_shopby_swatches_color_h = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_color_h');
		$meigee_sidebar_blocks_shopby_swatches_color_a = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_color_a');
		$meigee_sidebar_blocks_shopby_swatches_color_f = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_color_f');
		$meigee_sidebar_blocks_shopby_swatches_bg_d = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_bg_d');
		$meigee_sidebar_blocks_shopby_swatches_bg_h = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_bg_h');
		$meigee_sidebar_blocks_shopby_swatches_bg_a = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_bg_a');
		$meigee_sidebar_blocks_shopby_swatches_bg_f = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_bg_f');
		$meigee_sidebar_blocks_shopby_swatches_border_d = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_border_d');
		$meigee_sidebar_blocks_shopby_swatches_border_h = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_border_h');
		$meigee_sidebar_blocks_shopby_swatches_border_a = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_border_a');
		$meigee_sidebar_blocks_shopby_swatches_border_f = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_border_f');
		$meigee_sidebar_blocks_shopby_swatches_shadow_d = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_shadow_d');
		$meigee_sidebar_blocks_shopby_swatches_shadow_h = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_shadow_h');
		$meigee_sidebar_blocks_shopby_swatches_shadow_a = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_shadow_a');
		$meigee_sidebar_blocks_shopby_swatches_shadow_f = $this->_customDesign->getConfigData($sectionId, 'sidebar_tabs', 'meigee_sidebar_blocks_shopby_swatches_shadow_f');

		//Product page
		$productpage_info_name_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_name_color');
		$productpage_info_add_links_color_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_color_d');
		$productpage_info_add_links_color_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_color_h');
		$productpage_info_add_links_color_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_color_a');
		$productpage_info_add_links_color_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_color_f');
		$productpage_info_add_links_bg_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_bg_d');
		$productpage_info_add_links_bg_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_bg_h');
		$productpage_info_add_links_bg_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_bg_a');
		$productpage_info_add_links_bg_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_bg_f');
		$productpage_info_add_links_border_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_border_d');
		$productpage_info_add_links_border_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_border_h');
		$productpage_info_add_links_border_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_border_a');
		$productpage_info_add_links_border_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_add_links_border_f');
		$general_product_label_sale_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'general_product_label_sale_color');
		$general_product_label_sale_bg = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'general_product_label_sale_bg');
		$general_product_label_new_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'general_product_label_new_color');
		$general_product_label_new_bg = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'general_product_label_new_bg');
		$productpage_info_cart_btn_color_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_color_d');
		$productpage_info_cart_btn_color_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_color_h');
		$productpage_info_cart_btn_color_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_color_a');
		$productpage_info_cart_btn_color_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_color_f');
		$productpage_info_cart_btn_bg_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_bg_d');
		$productpage_info_cart_btn_bg_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_bg_h');
		$productpage_info_cart_btn_bg_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_bg_a');
		$productpage_info_cart_btn_bg_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_cart_btn_bg_f');
		$productpage_options_bg = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_options_bg');
		$productpage_options_label_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_options_label_color');
		$general_product_instock_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'general_product_instock_color');
		$general_product_outofstock_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'general_product_outofstock_color');
		$general_product_availability_only_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'general_product_availability_only_color');
		$productpage_info_qty_label_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_label_color');
		$productpage_info_qty_input_bg = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_bg');
		$productpage_info_qty_input_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_color');
		$productpage_info_qty_input_btns_color_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_color_d');
		$productpage_info_qty_input_btns_color_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_color_h');
		$productpage_info_qty_input_btns_color_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_color_a');
		$productpage_info_qty_input_btns_color_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_color_f');
		$productpage_info_qty_input_btns_bg_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_bg_d');
		$productpage_info_qty_input_btns_bg_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_bg_h');
		$productpage_info_qty_input_btns_bg_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_bg_a');
		$productpage_info_qty_input_btns_bg_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_qty_input_btns_bg_f');
		$productpage_info_delivery_ship_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_delivery_ship_color');
		$productpage_info_size_guide_color_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_size_guide_color_d');
		$productpage_info_size_guide_color_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_size_guide_color_h');
		$productpage_info_size_guide_color_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_size_guide_color_a');
		$productpage_info_size_guide_color_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_size_guide_color_f');
		$productpage_info_additional_info_links_color_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_additional_info_links_color_d');
		$productpage_info_additional_info_links_color_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_additional_info_links_color_h');
		$productpage_info_additional_info_links_color_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_additional_info_links_color_a');
		$productpage_info_additional_info_links_color_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_additional_info_links_color_f');
		$productpage_info_additional_info_check_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_additional_info_check_color');
		$productpage_info_additional_info_label_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_additional_info_label_color');
		$productpage_info_additional_info_text_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_info_additional_info_text_color');
		$productpage_tabs_border = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_border');
		$productpage_tabs_head_color_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_color_d');
		$productpage_tabs_head_color_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_color_h');
		$productpage_tabs_head_color_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_color_a');
		$productpage_tabs_head_color_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_color_f');
		$productpage_tabs_divider_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_divider_color');
		$productpage_tabs_head_bg_d = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_bg_d');
		$productpage_tabs_head_bg_h = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_bg_h');
		$productpage_tabs_head_bg_a = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_bg_a');
		$productpage_tabs_head_bg_f = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_head_bg_f');
		$productpage_tabs_content_table_border = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_tabs_content_table_border');
		$productpage_reviews_bg = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_reviews_bg');
		$productpage_reviews_border = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_reviews_border');
		$productpage_reviews_title_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_reviews_title_color');
		$productpage_reviews_text_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_reviews_text_color');
		$productpage_reviews_date_color = $this->_customDesign->getConfigData($sectionId, 'product_tabs', 'productpage_reviews_date_color');
			
$cssData = '
	/***** New Css styles *****/
	
	body.boxed-layout .content-wrapper .container,
	.boxed-layout .breadcrumbs-wrapper .container,
	html body.wide-layout {
		background-color: '.$bg.';
	}
	html body.boxed-layout,
	.boxed-layout .footer {background-color: '.$bg_page.';}
	body.boxed-layout .content-wrapper .container,
	.boxed-layout .breadcrumbs-wrapper .container {
		border-color: '.$border.';
	}
	.products-list li.item + li.item,
	.block-dashboard-orders .block-title,
	.block-dashboard-addresses .block-title,
	.block-dashboard-info .block-title,
	.block-reviews-dashboard .block-title,
	.block-dashboard-info .block-content .box.box-information,
	.block-dashboard-info .block-content .box.box-newsletter,
	.block-dashboard-addresses .block-content .box.box-billing-address,
	.block-dashboard-addresses .block-content .box.box-shipping-address,
	.box .box-title,
	.table > thead > tr > th,
	.table > tbody > tr > th,
	.table > tfoot > tr > th,
	.table > thead > tr > td,
	.table > tbody > tr > td,
	.table > tfoot > tr > td,
	.products-grid.wishlist li.product-item .product-item-actions a.btn-remove,
	#review-form {
		border-color: '.$elements_border.';
	}
	html body,
	.reviews-actions a,
	.toolbar label,
	.toolbar .label,
	.toolbar .modes i,
	.block.review-add .review-field-ratings .label,
	.review-field-rating .rating-values,
	.product-info-main .title-wrapper .add-review,
	.block-title strong,
	.block-collapsible-nav-title strong,
	.block-dashboard-orders .block-title strong,
	.block-dashboard-addresses .block-title strong,
	.block-dashboard-info .block-title strong,
	.block-reviews-dashboard .block-title strong,
	.box .box-title {
		color: '.$general_color.';
	}
	.product-info-main .title-wrapper .add-review::before {
		background-color: '.$general_color.';
	}
	.page-title,
	.page-title h1 {
		color: '.$title_color.';
	}
	body a,
	.v-ellipsis .read-more-link,
	#my-orders-table a,
	.account .content-inner a {
		color: '.$links_color.';
	}
	body a:hover,
	.v-ellipsis .read-more-link:hover,
	#my-orders-table a:hover,
	.account .content-inner a:hover {
		color: '.$links_color_h.';
	}
	body a:active,
	.v-ellipsis .read-more-link:active,
	#my-orders-table a:active,
	.account .content-inner a:active {
		color: '.$links_color_a.';
	}
	body a:focus,
	.v-ellipsis .read-more-link:focus,
	#my-orders-table a:focus,
	.account .content-inner a:focus {
		color: '.$links_color_f.';
	}
	.price {
		color: '.$regular_price.';
	}
	.price-box .special-price .price {
		color: '.$special_price.';
	}
	.old-price .price {
		color: '.$old_price.';
	}
	.products-list .sold-out .price,
	.products-grid .sold-out .price,
	.products-list .sold-out .price-currency,
	.products-grid .sold-out .price-currency,
	.products-list .sold-out .price-pennie,
	.products-grid .sold-out .price-pennie {
		color: '.$sold_out.';
	}
	ul.breadcrumb li a,
	body .breadcrumbs > .items li a,
	.breadcrumbs-wrapper .breadcrumb > li + li::before,
	.breadcrumbs-wrapper .breadcrumbs > .items  > li + li::before{
		color: '.$breadcrumbs_color.';
	}
	ul.breadcrumb li a:hover,
	body .breadcrumbs > .items li a:hover{
		color: '.$breadcrumbs_color_h.';
	}
	ul.breadcrumb li a:active,
	body .breadcrumbs > .items li a:active,
	ul.breadcrumb li strong,
	body .breadcrumbs > .items li strong{
		color: '.$breadcrumbs_color_a.';
	}
	ul.breadcrumb li a:focus,
	body .breadcrumbs > .items li a:focus{
		color: '.$breadcrumbs_color_f.';
	}
	body input[type="text"],
	body input,
	body input[type="email"],
	body input[type="password"],
	body input.form-control,
	body input,
	body textarea.form-control,
	body textarea,
	.block.review-add .field.required .form-control,
	.block.review-add .form-control {
		color: '.$general_input_color.';
		background-color: '.$general_input_bg.';
	}
	body select.form-control,
	body select,
	.toolbar select.form-control,
	.toolbar select {
		color: '.$general_select_color.';
		background-color: '.$general_select_bg.';
	}
	.rating-result,
	.review-control-vote::before {
		color: '.$reviews_rating_general_color.';
	}
	.rating-result span,
	.review-control-vote label::before {
		color: '.$reviews_rating_active_color.';
	}
	.reviews-actions a:hover {
		color: '.$reviews_rating_links_color_hover.';
	}
	body .btn,
	body button.action,
	.sidebar .block li a.action.tocart,
	.actions-toolbar a.primary,
	.sidebar .block.block-compare .actions-toolbar .action,
	.sidebar .block.block-wishlist .actions-toolbar a.action.details,
	.sidebar .block.block-reorder .actions-toolbar .secondary a.action.view,
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart,
	body .cart-container .cart.actions a.action.continue, body .weltpixel-quickview,
	button.action-primary,
	button.action-secondary,
	body.checkout-index-index button[type="submit"],
	.weltpixel-quickview-catalog-product-view button.action,
	.weltpixel_quickview-catalog_product-view button.action,
	.product-info-main .actions button {
		color: '.$general_btn1_color.';
		background-color: '.$general_btn1_bg.';
		border-color: '.$general_btn1_border.';
	}
	body .btn:hover,
	body button.action:hover,
	.sidebar .block li a.action.tocart:hover,
	.actions-toolbar a.primary:hover,
	.sidebar .block.block-compare .actions-toolbar .action:hover,
	.sidebar .block.block-wishlist .actions-toolbar a.action.details:hover,
	.sidebar .block.block-reorder .actions-toolbar .secondary a.action.view:hover,
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart:hover,
	body .cart-container .cart.actions a.action.continue:hover,
	body .weltpixel-quickview:hover,
	button.action-primary:hover,
	button.action-secondary:hover,
	body.checkout-index-index button[type="submit"]:hover,
	.weltpixel-quickview-catalog-product-view button.action:hover,
	.weltpixel_quickview-catalog_product-view button.action:hover,
	.product-info-main .actions button:hover {
		color: '.$general_btn1_color_h.';
		background-color: '.$general_btn1_bg_h.';
		border-color: '.$general_btn1_border_h.';
	}
	body .btn:active,
	body button.action:active,
	.sidebar .block li a.action.tocart:active,
	.actions-toolbar a.primary:active,
	.sidebar .block.block-compare .actions-toolbar .action:active,
	.sidebar .block.block-wishlist .actions-toolbar a.action.details:active,
	.sidebar .block.block-reorder .actions-toolbar .secondary a.action.view:active,
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart:active,
	body .cart-container .cart.actions a.action.continue:active,
	body .weltpixel-quickview:active,
	button.action-primary:active,
	button.action-secondary:active,
	body.checkout-index-index button[type="submit"]:active,
	.weltpixel-quickview-catalog-product-view button.action:active,
	.weltpixel_quickview-catalog_product-view button.action:active,
	.product-info-main .actions button:active {
		color: '.$general_btn1_color_a.';
		background-color: '.$general_btn1_bg_a.';
		border-color: '.$general_btn1_border_a.';
	}
	body .btn:focus,
	body button.action:focus,
	.sidebar .block li a.action.tocart:focus,
	.actions-toolbar a.primary:focus,
	.sidebar .block.block-compare .actions-toolbar .action:focus,
	.sidebar .block.block-wishlist .actions-toolbar a.action.details:focus,
	.sidebar .block.block-reorder .actions-toolbar .secondary a.action.view:focus,
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart:focus,
	body .cart-container .cart.actions a.action.continue:focus,
	body .weltpixel-quickview:focus,
	button.action-primary:focus,
	button.action-secondary:focus,
	body.checkout-index-index button[type="submit"]:focus,
	.weltpixel-quickview-catalog-product-view button.action:focus,
	.weltpixel_quickview-catalog_product-view button.action:focus,
	.product-info-main .actions button:focus {
		color: '.$general_btn1_color_f.';
		background-color: '.$general_btn1_bg_f.';
		border-color: '.$general_btn1_border_f.';
	}
	

	body .totals-wrapper .checkout-methods-items button:hover,
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart,
	.sidebar .block.block-compare .actions-toolbar .primary a.action,
	body .weltpixel-quickview, body .btn.btn-primary {
		color: '.$general_btn2_color.';
		background-color: '.$general_btn2_bg.';
		border-color: '.$general_btn2_bg.';
	}
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart:hover,
	.sidebar .block.block-compare .actions-toolbar .primary a.action:hover,
	body .weltpixel-quickview, body .btn.btn-primary:hover {
		color: '.$general_btn2_color_h.';
		background-color: '.$general_btn2_bg_h.';
		border-color: '.$general_btn2_bg_h.';
	}
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart:active,
	.sidebar .block.block-compare .actions-toolbar .primary a.action:active,
	body .weltpixel-quickview, body .btn.btn-primary:active {
		color: '.$general_btn2_color_a.';
		background-color: '.$general_btn2_bg_a.';
		border-color: '.$general_btn2_bg_a.';
	}
	.sidebar .block.block-reorder .actions-toolbar .primary button.action.tocart:focus,
	.sidebar .block.block-compare .actions-toolbar .primary a.action:focus,
	body .weltpixel-quickview, body .btn.btn-primary:focus {
		color: '.$general_btn2_color_f.';
		background-color: '.$general_btn2_bg_f.';
		border-color: '.$general_btn2_bg_f.';
	}
	body .btn.btn-primary.type-2,
	.weltpixel-quickview-catalog-product-view button.action,
	.weltpixel_quickview-catalog_product-view button.action {
	  color: '.$general_btn3_color.';
	  background-color: '.$general_btn3_bg.';
	}
	body .btn.btn-primary.type-2:hover,
	.weltpixel-quickview-catalog-product-view button.action:hover,
	.weltpixel_quickview-catalog_product-view button.action:hover{
	  color: '.$general_btn3_color_h.';
	  background-color: '.$general_btn3_bg_h.';
	}
	body .btn.btn-primary.type-2:active,
	.weltpixel-quickview-catalog-product-view button.action:active,
	.weltpixel_quickview-catalog_product-view button.action:active{
	  color: '.$general_btn3_color_a.';
	  background-color: '.$general_btn3_bg_a.';
	}
	body .btn.btn-primary.type-2:focus,
	.weltpixel-quickview-catalog-product-view button.action:focus,
	.weltpixel_quickview-catalog_product-view button.action:focus{
	  color: '.$general_btn3_color_f.';
	  background-color: '.$general_btn3_bg_f.';
	}
	.owl-nav div,
	.text-banner .owl-nav div {
		color: '.$general_slider_btns_color.';
		background-color: '.$general_slider_btns_bg.';
		border-color: '.$general_slider_btns_border.';
	}
	.owl-nav div:hover,
	.text-banner .owl-nav div:hover {
		color: '.$general_slider_btns_color_h.';
		background-color: '.$general_slider_btns_bg_h.';
		border-color: '.$general_slider_btns_border_h.';
	}
	.owl-nav div:active,
	.text-banner .owl-nav div:active {
		color: '.$general_slider_btns_color_a.';
		background-color: '.$general_slider_btns_bg_a.';
		border-color: '.$general_slider_btns_border_a.';
	}
	.owl-nav div:focus,
	.text-banner .owl-nav div:focus {
		color: '.$general_slider_btns_color_f.';
		background-color: '.$general_slider_btns_bg_f.';
		border-color: '.$general_slider_btns_border_f.';
	}
	body .subscribe-block {
		background-color: '.$general_home_subscribe_bg.';
	}
	.subscribe-block .block-title,
	.subscribe-block .block-title strong {
		color: '.$general_home_subscribe_title_color.';
	}
	body .subscribe-block .content label {
		color: '.$general_home_subscribe_text_color.';
	}
	body .subscribe-block .content input {
		background-color: '.$general_home_subscribe_input_bg.';
		color: '.$general_home_subscribe_input_color.';
	}
	body .subscribe-block .content input::-webkit-input-placeholder {
	  color: '.$general_home_subscribe_input_color.';
	}
	body .subscribe-block .content input::-moz-placeholder {
	  color: '.$general_home_subscribe_input_color.';
	}
	body .subscribe-block .content input:-ms-input-placeholder {
	  color: '.$general_home_subscribe_input_color.';
	}
	body .subscribe-block .content input:-moz-placeholder {
	  color: '.$general_home_subscribe_input_color.';
	}
	.newsletter .btn.btn-primary.type-2,
	body .subscribe-block .newsletter .btn.btn-primary {
		color: '.$general_home_subscribe_btn_color_d.';
		background-color: '.$general_home_subscribe_btn_bg_d.';
		border-color: '.$general_home_subscribe_btn_border_d.';
	}
	.newsletter .btn.btn-primary.type-2:hover,
	.newsletter .btn.btn-primary.type-2.hover,
	body .subscribe-block .newsletter .btn.btn-primary:hover,
	body .subscribe-block .newsletter .btn.btn-primary.hover {
		color: '.$general_home_subscribe_btn_color_h.';
		background-color: '.$general_home_subscribe_btn_bg_h.';
		border-color: '.$general_home_subscribe_btn_border_h.';
	}
	.newsletter .btn.btn-primary.type-2:active,
	.newsletter .btn.btn-primary.type-2.active,
	body .subscribe-block .newsletter .btn.btn-primary:active,
	body .subscribe-block .newsletter .btn.btn-primary.active {
		color: '.$general_home_subscribe_btn_color_a.';
		background-color: '.$general_home_subscribe_btn_bg_a.';
		border-color: '.$general_home_subscribe_btn_border_a.';
	}
	.newsletter .btn.btn-primary.type-2:focus,
	.newsletter .btn.btn-primary.type-2.focus,
	body .subscribe-block .newsletter .btn.btn-primary:focus,
	body .subscribe-block .newsletter <div class="btn btn-primary focus"></div> {
		color: '.$general_home_subscribe_btn_color_f.';
		background-color: '.$general_home_subscribe_btn_bg_f.';
		border-color: '.$general_home_subscribe_btn_border_f.';
	}
	.wide-layout .page-header.header-1 > *,
	.boxed-layout .header-wrapper .page-header.header-1 > * .container,
	.cms-index-index.wide-layout .page-header.header-1,
	.cms-index-index.boxed-layout .page-header.header-1 .container,
	.wide-layout .page-header.header-2 > *,
	.boxed-layout .page-header.header-2 > * .container,
	.wide-layout:not(.checkout-cart-index) .header-wrapper .page-header.header-3,
	.boxed-layout:not(.checkout-cart-index) .header-wrapper .page-header.header-3 .container,
	.wide-layout .page-header.header-4,
	body.boxed-layout .page-header.header-4 .container,
	.cms-index-index.wide-layout .page-header.header-4,
	.cms-index-index.boxed-layout .page-header.header-4 .container,
	.wide-layout .page-header.header-5 > *,
	.boxed-layout .page-header.header-5 > * .container,
	.wide-layout:not(.checkout-cart-index) .header-wrapper .page-header.header-6,
	.boxed-layout:not(.checkout-cart-index) .header-wrapper .page-header.header-6 .container,
	.wide-layout:not(.checkout-cart-index) .header-wrapper .page-header.header-6 .top-block,
	.wide-layout:not(.checkout-cart-index) .header-wrapper .page-header.header-7 > *,
	.boxed-layout:not(.checkout-cart-index) .header-wrapper .page-header.header-7 .container,
	.page-header.header-8 .middle-block,
	.page-header.header-9 .middle-block .container,
	.page-header.header-9 .menu-wrapper .container  {
		background-color: '.$header_bg.';
	}
	.page-header.header-8 .middle-block .container,
	.page-header.header-8 .menu-wrapper .container {background-color: transparent;}
	body .header-wrapper .page-header[class*="header-"],
	.page-header[class*="header-"] .meigee-menu,
	.page-header[class*="header-"] .header-info .item a,
	.page-header[class*="header-"] .header-info .item i,
	body .header-wrapper .page-header[class*="header-"] .top-block-inner {
		color: '.$header_color.';
	}
	.wide-layout .page-header.header-1 .menu-wrapper,
	.page-header.header-1 .menu-wrapper .menu-inner,
	.wide-layout .header-wrapper .page-header.header-2 .menu-wrapper,
	.boxed-layout .header-wrapper .page-header.header-2 .menu-wrapper .menu-inner,
	.page-header.header-3 .top-block .top-block-inner,
	.page-wrapper.header-5 .middle-block,
	.boxed-layout .header-wrapper .page-header.header-6 .menu-wrapper .menu-inner,
	.wide-layout .page-header.header-8 .middle-block,
	.boxed-layout .page-header.header-8 .middle-block,
	.wide-layout .page-header.header-8 .menu-wrapper,
	.boxed-layout .page-header.header-8 .menu-wrapper,
	.wide-layout .header-wrapper .page-header.header-9 .menu-wrapper,
	.boxed-layout .header-wrapper .page-header.header-9 .menu-wrapper .menu-inner {
		border-color: '.$header_border.';
	}
	body .header-wrapper .page-header.header-6 .top-block-inner {
		border-bottom: 1px solid '.$header_border.';
	}
	.wide-layout .header-wrapper .page-header.header-6 .menu-wrapper::before {
		background-color: '.$header_border.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search .input-group {
		background-color: '.$header_search_bg.';
		border-color: '.$header_search_border.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search input,
	.header-wrapper .page-header[class*="header-"] .block-search .search-icon i {
		color: '.$header_search_color.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search .search-icon::after {
		background-color: '.$header_search_color.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search input::-webkit-input-placeholder {
	  color: '.$header_search_color.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search input::-moz-placeholder {
	  color: '.$header_search_color.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search input:-ms-input-placeholder {
	  color: '.$header_search_color.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search input:-moz-placeholder {
	  color: '.$header_search_color.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search .btn {
		color: '.$header_search_btn_color_d.';
		background-color: '.$header_search_btn_bg_d.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search .btn:hover,
	.header-wrapper .page-header[class*="header-"] .block-search .btn.hover {
		color: '.$header_search_btn_color_h.';
		background-color: '.$header_search_btn_bg_h.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search .btn:active,
	.header-wrapper .page-header[class*="header-"] .block-search .btn.active {
		color: '.$header_search_btn_color_a.';
		background-color: '.$header_search_btn_bg_a.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search .btn:focus,
	.header-wrapper .page-header[class*="header-"] .block-search .btn.focus {
		color: '.$header_search_btn_color_f.';
		background-color: '.$header_search_btn_bg_f.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search.type-2 .search-button {
		color: '.$header_search2_btn_color_d.';
		background-color: '.$header_search2_btn_bg_d.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search.type-2 .search-button:hover,
	.header-wrapper .page-header[class*="header-"] .block-search.type-2 .search-button.hover {
		color: '.$header_search2_btn_color_h.';
		background-color: '.$header_search2_btn_bg_h.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search.type-2 .search-button:active,
	.header-wrapper .page-header[class*="header-"] .block-search.type-2 .search-button.active {
		color: '.$header_search2_btn_color_a.';
		background-color: '.$header_search2_btn_bg_a.';
	}
	.header-wrapper .page-header[class*="header-"] .block-search.type-2 .search-button:focus,
	.header-wrapper .page-header[class*="header-"] .block-search.type-2 .search-button.focus {
		color: '.$header_search2_btn_color_f.';
		background-color: '.$header_search2_btn_bg_f.';
	}
	.page-header[class*="header-"] .options-wrapper .options-block {
		color: '.$header_account_color_d.';
		background-color: '.$header_account_bg_d.';
	}
	.page-header[class*="header-"] .options-wrapper .options-block:hover {
		color: '.$header_account_color_h.';
		background-color: '.$header_account_bg_h.';
	}
	.page-header[class*="header-"] .options-wrapper .options-block.open,
	.page-header[class*="header-"] .options-wrapper .options-block:active {
		color: '.$header_account_color_a.';
		background-color: '.$header_account_bg_a.';
	}
	.page-header[class*="header-"] .options-wrapper .options-block:focus {
		color: '.$header_account_color_f.';
		background-color: '.$header_account_bg_f.';
	}
	.header-wrapper .page-header[class*="header-"] .options-wrapper .options-block .title i {color: inherit;}
	.page-header[class*="header-"] .options-wrapper .options-dropdown {
		background-color: '.$header_account_dropdown_bg.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li a,
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li.authorization-link a {
		color: '.$header_account_dropdown_links_color_d.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li a:hover,
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li.authorization-link a:hover {
		color: '.$header_account_dropdown_links_color_h.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li a:active,
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li.authorization-link a:active {
		color: '.$header_account_dropdown_links_color_a.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li a:focus,
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li.authorization-link a:focus {
		color: '.$header_account_dropdown_links_color_f.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header.links li + li {
		border-color: '.$header_account_dropdown_links_border.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options strong {
		background-color: '.$header_account_dropdown_select_bg_d.';
		color: '.$header_account_dropdown_select_color_d.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options strong:hover {
		background-color: '.$header_account_dropdown_select_bg_h.';
		color: '.$header_account_dropdown_select_color_h.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options.active strong,
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options strong:active {
		background-color: '.$header_account_dropdown_select_bg_a.';
		color: '.$header_account_dropdown_select_color_a.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options strong:focus {
		background-color: '.$header_account_dropdown_select_bg_f.';
		color: '.$header_account_dropdown_select_color_f.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options .action.toggle::after {
		border-top-color: '.$header_account_dropdown_select_color_d.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options:hover .action.toggle::after {
		border-top-color: '.$header_account_dropdown_select_color_h.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options.active .action.toggle::after {
		border-top-color: '.$header_account_dropdown_select_color_a.';
	}
	.page-header[class*="header-"] .options-wrapper .options-dropdown .header-switcher .options:focus .action.toggle::after {
		border-top-color: '.$header_account_dropdown_select_color_f.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart {
		background-color: '.$header_cart_bg_d.';
		color: '.$header_cart_color_d.';
		border-color: '.$header_cart_border_d.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart .icon i {color: inherit;}
	.page-header[class*="header-"] .minicart-wrapper .title-cart:hover {
		background-color: '.$header_cart_bg_h.';
		color: '.$header_cart_color_h.';
		border-color: '.$header_cart_border_h.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart.active,
	.page-header[class*="header-"] .minicart-wrapper .title-cart:active {
		background-color: '.$header_cart_bg_a.';
		color: '.$header_cart_color_a.';
		border-color: '.$header_cart_border_a.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart.focus,
	.page-header[class*="header-"] .minicart-wrapper .title-cart:focus {
		background-color: '.$header_cart_bg_f.';
		color: '.$header_cart_color_f.';
		border-color: '.$header_cart_border_f.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart .counter-number {
		background-color: '.$header_cart_qty_bg_d.';
		color: '.$header_cart_qty_color_d.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart:hover .counter-number {
		background-color: '.$header_cart_qty_bg_h.';
		color: '.$header_cart_qty_color_h.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart:active .counter-number,
	.page-header[class*="header-"] .minicart-wrapper .title-cart.active .counter-number {
		background-color: '.$header_cart_qty_bg_a.';
		color: '.$header_cart_qty_color_a.';
	}
	.page-header[class*="header-"] .minicart-wrapper .title-cart:focus .counter-number {
		background-color: '.$header_cart_qty_bg_f.';
		color: '.$header_cart_qty_color_f.';
	}
	.page-header[class*="header-"] .minicart-wrapper .block-minicart {
		background-color: '.$header_cart_dropdown_bg.';
		color: '.$header_cart_dropdown_color.';
	}
	.block-minicart .subtotal .label,
	.minicart-wrapper .product-item-name a {
		color: '.$header_cart_dropdown_color.';
	}
	.page-header[class*="header-"] .block-minicart .subtotal {
		border-color: '.$header_cart_dropdown_border.';
	}
	.page-header[class*="header-"] .minicart-items .action.edit,
	.page-header[class*="header-"] .minicart-items .action.delete {
		color: '.$header_cart_dropdown_actions_color_d.';
	}
	.page-header[class*="header-"] .minicart-items .action.edit:hover,
	.page-header[class*="header-"] .minicart-items .action.delete:hover {
		color: '.$header_cart_dropdown_actions_color_h.';
	}
	.page-header[class*="header-"] .minicart-items .action.edit:active,
	.page-header[class*="header-"] .minicart-items .action.delete:active {
		color: '.$header_cart_dropdown_actions_color_a.';
	}
	.page-header[class*="header-"] .minicart-items .action.edit:focus,
	.page-header[class*="header-"] .minicart-items .action.delete:focus {
		color: '.$header_cart_dropdown_actions_color_f.';
	}
	.page-header[class*="header-"] .minicart-price .price {
		color: '.$header_cart_dropdown_product_price_color.';
	}
	.page-header[class*="header-"] .minicart-items .item-qty {
		color: '.$header_cart_dropdown_product_qty_color.';
		background-color: '.$header_cart_dropdown_product_qty_bg.';
	}
	.page-header[class*="header-"] .block-minicart .subtotal .price-container .price {
		color: '.$header_cart_dropdown_total_price_color.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.secondary .btn {
		color: '.$header_cart_dropdown_cart_btn_color_d.';
		background-color: '.$header_cart_dropdown_cart_btn_bg_d.';
		border-color: '.$header_cart_dropdown_cart_btn_border_d.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.secondary .btn:hover, 
	.page-header[class*="header-"] .minicart-wrapper .actions div.secondary .btn.hover {
		color: '.$header_cart_dropdown_cart_btn_color_h.';
		background-color: '.$header_cart_dropdown_cart_btn_bg_h.';
		border-color: '.$header_cart_dropdown_cart_btn_border_h.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.secondary .btn:active, 
	.page-header[class*="header-"] .minicart-wrapper .actions div.secondary .btn.active {
		color: '.$header_cart_dropdown_cart_btn_color_a.';
		background-color: '.$header_cart_dropdown_cart_btn_bg_a.';
		border-color: '.$header_cart_dropdown_cart_btn_border_a.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.secondary .btn:focus, 
	.page-header[class*="header-"] .minicart-wrapper .actions div.secondary .btn.focus {
		color: '.$header_cart_dropdown_cart_btn_color_f.';
		background-color: '.$header_cart_dropdown_cart_btn_bg_f.';
		border-color: '.$header_cart_dropdown_cart_btn_border_f.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.primary .btn {
		color: '.$header_cart_dropdown_checkout_btn_color_d.';
		background-color: '.$header_cart_dropdown_checkout_btn_bg_d.';
		border-color: '.$header_cart_dropdown_checkout_btn_bg_d.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.primary .btn:hover,
	.page-header[class*="header-"] .minicart-wrapper .actions div.primary .btn.hover {
		color: '.$header_cart_dropdown_checkout_btn_color_h.';
		background-color: '.$header_cart_dropdown_checkout_btn_bg_h.';
		border-color: '.$header_cart_dropdown_checkout_btn_bg_h.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.primary .btn:active,
	.page-header[class*="header-"] .minicart-wrapper .actions div.primary .btn.active {
		color: '.$header_cart_dropdown_checkout_btn_color_a.';
		background-color: '.$header_cart_dropdown_checkout_btn_bg_a.';
		border-color: '.$header_cart_dropdown_checkout_btn_bg_a.';
	}
	.page-header[class*="header-"] .minicart-wrapper .actions div.primary .btn:focus,
	.page-header[class*="header-"] .minicart-wrapper .actions div.primary .btn.focus {
		color: '.$header_cart_dropdown_checkout_btn_color_f.';
		background-color: '.$header_cart_dropdown_checkout_btn_bg_f.';
		border-color: '.$header_cart_dropdown_checkout_btn_bg_f.';
	}
	.page-header[class*="header-"] ul.social-links li a {
		color: '.$header_socials_color_d.';
		background-color: '.$header_socials_bg_d.';
	}
	.page-header[class*="header-"] ul.social-links li a:hover {
		color: '.$header_socials_color_h.';
		background-color: '.$header_socials_bg_h.';
	}
	.page-header[class*="header-"] ul.social-links li a:active {
		color: '.$header_socials_color_a.';
		background-color: '.$header_socials_bg_a.';
	}
	.page-header[class*="header-"] ul.social-links li a:focus {
		color: '.$header_socials_color_f.';
		background-color: '.$header_socials_bg_f.';
	}
	
	.wide-layout .page-header.header-1 .menu-wrapper,
	.boxed-layout .page-header.header-1 .menu-wrapper .container,
	.wide-layout .page-header.header-2 .menu-wrapper,
	.boxed-layout .page-header.header-2 .menu-wrapper .menu-inner,
	.header-wrapper .page-header.header-3 .navbar-collapse.collapse,
	.wide-layout .header-wrapper .page-header.header-5 .menu-wrapper,
	.boxed-layout .header-wrapper .page-header.header-5 .menu-wrapper .menu-inner,
	.page-header.header-7 .menu-wrapper .container,
	.wide-layout .header-wrapper .page-header.header-6 .menu-wrapper,
	.boxed-layout .header-wrapper .page-header.header-6 .menu-wrapper .container,
	.page-header.header-4 .toggle-nav,
	.wide-layout .page-header.header-8 .menu-wrapper,
	.boxed-layout .page-header.header-8 .menu-wrapper,
	.wide-layout .header-wrapper .page-header.header-9 .menu-wrapper,
	  .boxed-layout .header-wrapper .page-header.header-9 .menu-wrapper .menu-inner {
	    background: '.$header_menu_bg.';
	  }
	  .wide-layout .header-wrapper .page-header.header-9 .menu-wrapper .container {
	    background-color: transparent;
	  }
    
    .page-header .action.nav-toggle{color: '.$mobile_menu_icon_color.';}
    
	@media only screen and (min-width: 1008px) {
		.header-wrapper .page-header[class*="header-"] .navbar-collapse.collapse a.level-top {
			background-color: '.$header_menu_item_bg_d.';
			color: '.$header_menu_item_color_d.';
		}
		.header-wrapper .navbar-collapse.collapse a.level-top .ui-menu-icon:after {color: inherit; opacity: 0.6;}
		.header-wrapper .page-header[class*="header-"] .navbar-collapse.collapse a.level-top:hover,
		.header-wrapper .page-header[class*="header-"] .navbar-collapse.collapse a.level-top.ui-state-focus {
			background-color: '.$header_menu_item_bg_h.';
			color: '.$header_menu_item_color_h.';
		}
		.header-wrapper .page-header[class*="header-"] .navbar-collapse.collapse a.level-top:active,
		.header-wrapper .page-header[class*="header-"] .navbar-collapse.collapse li.active > a.level-top,
		.header-wrapper .page-header[class*="header-"] .navbar-collapse.collapse a.level-top.ui-state-active {
			background-color: '.$header_menu_item_bg_a.';
			color: '.$header_menu_item_color_a.';
		}
		.header-wrapper .page-header[class*="header-"] .navbar-collapse.collapse a.level-top:focus {
			background-color: '.$header_menu_item_bg_f.';
			color: '.$header_menu_item_color_f.';
		}
	}

	body.wide-layout .footer,
	body.boxed-layout .footer .container {
		background: '.$footer_bg.';
	}
	.footer {
		color: '.$footer_color.';
	}
	.footer .links a::before {
		background-color: '.$footer_color.';
	}
	.footer hr.solid, .footer hr.dotted, .footer hr.dashed, .footer hr.solid.red {
		border-color: '.$footer_border.';
	}
	body.wide-layout .footer .footer-bottom,
	body.boxed-layout .footer .footer-bottom .container,
	body.wide-layout .footer .footer-top .container,
	body.boxed-layout .footer .footer-top .container,
	body.wide-layout .footer .footer-middle .container,
	body.boxed-layout .footer .footer-middle .container {
		border-color: '.$footer_border.';
	}
	.footer .footer-block-title {
		color: '.$footer_title_color.';
	}
	.footer a:hover,
	.footer a:focus,
	.footer a:active,
	.footer .links a:hover,
	.footer .horizontal-links li a:hover {
		color: '.$footer_links_color_hover.';
	}
	.footer .links a:hover::after {
		background-color: '.$footer_links_color_hover.';
	}
	body .footer ul.social-links li a {
		color: '.$footer_socials_color_d.';
		background-color: '.$footer_socials_bg_d.';
	}
	body .footer ul.social-links li a:hover {
		color: '.$footer_socials_color_h.';
		background-color: '.$footer_socials_bg_h.';
	}
	body .footer ul.social-links li a:active {
		color: '.$footer_socials_color_a.';
		background-color: '.$footer_socials_bg_a.';
	}
	body .footer ul.social-links li a:focus {
		color: '.$footer_socials_color_f.';
		background-color: '.$footer_socials_bg_f.';
	}

	body .product-hover-1 .hover-buttons a {
		color: '.$general_product_hover1_color_d.';
		background-color: '.$general_product_hover1_bg_d.';
	}
	body .product-hover-1 .hover-buttons a:hover {
		color: '.$general_product_hover1_color_h.';
		background-color: '.$general_product_hover1_bg_h.';
	}
	body .product-hover-1 .hover-buttons a:active {
		color: '.$general_product_hover1_color_a.';
		background-color: '.$general_product_hover1_bg_a.';
	}
	body .product-hover-1 .hover-buttons a:focus {
		color: '.$general_product_hover1_color_f.';
		background-color: '.$general_product_hover1_bg_f.';
	}
	body [class*="product-hover-"] .hover-buttons .btn,
	body [class*="product-hover-"] .hover-buttons .btn-default {
		color: '.$general_product_hover1_cart_color_d.';
		background-color: '.$general_product_hover1_cart_bg_d.';
	}
	body [class*="product-hover-"] .hover-buttons .btn:hover,
	body [class*="product-hover-"] .hover-buttons .btn-default:hover,
	body [class*="product-hover-"] .hover-buttons .btn.hover,
	body [class*="product-hover-"] .hover-buttons .btn-default.hover {
		color: '.$general_product_hover1_cart_color_h.';
		background-color: '.$general_product_hover1_cart_bg_h.';
	}
	body [class*="product-hover-"] .hover-buttons .btn:active,
	body [class*="product-hover-"] .hover-buttons .btn-default:active,
	body [class*="product-hover-"] .hover-buttons .btn.active,
	body [class*="product-hover-"] .hover-buttons .btn-default.active {
		color: '.$general_product_hover1_cart_color_a.';
		background-color: '.$general_product_hover1_cart_bg_a.';
	}
	body [class*="product-hover-"] .hover-buttons .btn:focus,
	body [class*="product-hover-"] .hover-buttons .btn-default:focus,
	body [class*="product-hover-"] .hover-buttons .btn.focus,
	body [class*="product-hover-"] .hover-buttons .btn-default.focus {
		color: '.$general_product_hover1_cart_color_f.';
		background-color: '.$general_product_hover1_cart_bg_f.';
	}

	.toolbar .pagination > li > a,
	.toolbar .pagination > li > span {
		color: '.$general_product_hover1_cart_color_d.';
		background-color: '.$general_product_hover1_cart_bg_d.';
	}
	.toolbar .pagination > li > a:hover,
	.toolbar .pagination > li > span:hover {
		color: '.$general_product_hover1_cart_color_h.';
		background-color: '.$general_product_hover1_cart_bg_h.';
	}
	.toolbar .pagination > .active > a,
	.toolbar .pagination > .active > span {
		color: '.$general_product_hover1_cart_color_a.';
		background-color: '.$general_product_hover1_cart_bg_a.';
	}
	.toolbar .pagination > li > a:focus,
	.toolbar .pagination > li > span:focus {
		color: '.$general_product_hover1_cart_color_f.';
		background-color: '.$general_product_hover1_cart_bg_f.';
	}

	/*Sidebar*/
	body[class*="blog-"] .sidebar:not(.sidebar-wrapper),
	.catalogsearch-result-index .sidebar:not(.sidebar-wrapper), 
	catalogsearch-advanced-result .sidebar:not(.sidebar-wrapper),
	.catalog-category-view .sidebar:not(.sidebar-wrapper),
	body[class*="blog-"].page-layout-3columns .sidebar-wrapper,
	.catalogsearch-result-index.page-layout-3columns .sidebar-wrapper,
	.catalogsearch-advanced-result.page-layout-3columns .sidebar-wrapper,
	.catalog-category-view.page-layout-3columns .sidebar-wrapper {
		background-color: '.$cat_sidebar_bg.';
	}
	body[class*="blog-"] .sidebar .block,
	.catalogsearch-result-index .sidebar .block,
	.catalogsearch-advanced-result .sidebar .block,
	.catalog-category-view .sidebar .block,
	.sidebar .block li + li,
	.sidebar .block .actions-toolbar {
		border-color: '.$cat_sidebar_border.';
	}
	body[class*="blog-"] .sidebar .block,
	.catalogsearch-result-index .sidebar .block,
	.catalogsearch-advanced-result .sidebar .block,
	.catalog-category-view .sidebar .block,
	.sidebar .block.block-compare .action.delete,
	.sidebar .block.block-wishlist .product-item-details .product-item-actions a.btn-remove::before,
	.sidebar .block.block-wishlist .product-item-details .product-item-actions .action.tocart::before {
		color: '.$cat_sidebar_color.';
	}
	.sidebar .block li a:hover,
	.sidebar .block.account-nav li strong,
	.sidebar .block.account-nav li a:hover,
	.sidebar .block li a.delete:hover,
	.sidebar .block.block-wishlist .product-item-details .product-item-actions .action.tocart:hover::before,
	.sidebar .block.block-compare .action.delete:hover,
	.sidebar .block.block-wishlist .product-item-details .product-item-actions a.btn-remove:hover::before,
	.sidebar .block.block-wishlist .product-item-details .product-item-actions .action.tocart:hover::before {
		color: '.$cat_sidebar_links_color_hover.';
	}

	.cart-summary .block > .title,
	.sidebar .block .block-title,
	.sidebar .block .title {
		color: '.$cat_sidebar_title_color.';
	}
	.sidebar .price {
		color: '.$cat_sidebar_price_color.';
	}
	.sidebar .price-box .special-price .price {
		color: '.$cat_sidebar_special_price_color.';
	}
	.sidebar .old-price .price {
		color: '.$cat_sidebar_old_price_color.';
	}
	.sidebar .block.filter .block-title {
		color: '.$meigee_sidebar_blocks_shopby_title_color.';
	}
	.sidebar .block.filter .options dt,
	.filter-options-title {
		color: '.$meigee_sidebar_blocks_shopby_subtitle_color.';
	}
	.sidebar .block.filter ol li a {
		color: '.$meigee_sidebar_blocks_shopby_links_color_d.';
	}
	.sidebar .block.filter ol li a:hover {
		color: '.$meigee_sidebar_blocks_shopby_links_color_h.';
	}
	.sidebar .block.filter ol li a:active {
		color: '.$meigee_sidebar_blocks_shopby_links_color_a.';
	}
	.sidebar .block.filter ol li a:focus {
		color: '.$meigee_sidebar_blocks_shopby_links_color_f.';
	}
	.sidebar .block.filter .swatch-attribute-options .swatch-option.text {
		color: '.$meigee_sidebar_blocks_shopby_swatches_color_d.';
		border-color: '.$meigee_sidebar_blocks_shopby_swatches_border_d.';
		background-color: '.$meigee_sidebar_blocks_shopby_swatches_bg_d.';
	}
	.sidebar .block.filter .swatch-attribute-options .swatch-option.text:hover {
		color: '.$meigee_sidebar_blocks_shopby_swatches_color_h.';
		border-color: '.$meigee_sidebar_blocks_shopby_swatches_border_h.';
		background-color: '.$meigee_sidebar_blocks_shopby_swatches_bg_h.';
	}
	.sidebar .block.filter .swatch-attribute-options .swatch-option.text:active {
		color: '.$meigee_sidebar_blocks_shopby_swatches_color_a.';
		border-color: '.$meigee_sidebar_blocks_shopby_swatches_border_a.';
		background-color: '.$meigee_sidebar_blocks_shopby_swatches_bg_a.';
	}
	.sidebar .block.filter .swatch-attribute-options .swatch-option.text:focus {
		color: '.$meigee_sidebar_blocks_shopby_swatches_color_f.';
		border-color: '.$meigee_sidebar_blocks_shopby_swatches_border_f.';
		background-color: '.$meigee_sidebar_blocks_shopby_swatches_bg_f.';
	}

	body .swatch-option.text::before,
	body .swatch-option.image::before,
	body .swatch-option.color::before {
		-webkit-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_d.';
		-moz-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_d.';
		box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_d.';
	}
	body .swatch-option.image:hover::before,
	body .swatch-option.image:not(.disabled):hover::before,
	body .swatch-option.color:hover::before,
	body .swatch-option.color:not(.disabled):hover::before,
	body .swatch-option.text:hover::before,
	body .swatch-option.text:not(.disabled):hover::before {
		-webkit-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_h.';
		-moz-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_h.';
		box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_h.';
		border-color: '.$meigee_sidebar_blocks_shopby_swatches_border_h.';
	}
	body .swatch-option.image.selected::before,
	body .swatch-option.color.selected::before,
	body .swatch-option.text.selected::before {
		-webkit-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_a.';
		-moz-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_a.';
		box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_a.';
		border-color: '.$meigee_sidebar_blocks_shopby_swatches_border_a.';
	}
	body .swatch-option.image:focus::before,
	body .swatch-option.image:not(.disabled):focus::before,
	body .swatch-option.color:focus::before,
	body .swatch-option.color:not(.disabled):focus::before,
	body .swatch-option.text:focus::before,
	body .swatch-option.text:not(.disabled):focus::before {
		-webkit-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_f.';
		-moz-box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_f.';
		box-shadow: 0px 1px 15px 0px '.$meigee_sidebar_blocks_shopby_swatches_shadow_f.';
		border-color: '.$meigee_sidebar_blocks_shopby_swatches_border_f.';
	}

	.sidebar .block.block-wishlist .product-item-details .product-item-name a,
	.sidebar .block.block-reorder li .product-item-name a,
	.sidebar .block.block-compare li .product-item-name a,
	.sidebar .block li .product-item-name a {
		color: '.$meigee_sidebar_product_blocks_product_color_d.';
	}
	.sidebar .block.block-wishlist .product-item-details .product-item-name a:hover,
	.sidebar .block.block-reorder li .product-item-name a:hover,
	.sidebar .block.block-compare li .product-item-name a:hover,
	.sidebar .block li .product-item-name a:hover {
		color: '.$meigee_sidebar_product_blocks_product_color_h.';
	}
	.sidebar .block.block-wishlist .product-item-details .product-item-name a:active,
	.sidebar .block.block-reorder li .product-item-name a:active,
	.sidebar .block.block-compare li .product-item-name a:active,
	.sidebar .block li .product-item-name a:active {
		color: '.$meigee_sidebar_product_blocks_product_color_a.';
	}
	.sidebar .block.block-wishlist .product-item-details .product-item-name a:focus,
	.sidebar .block.block-reorder li .product-item-name a:focus,
	.sidebar .block.block-compare li .product-item-name a:focus,
	.sidebar .block li .product-item-name a:focus {
		color: '.$meigee_sidebar_product_blocks_product_color_f.';
	}
	.product-info-main .page-title {
		color: '.$productpage_info_name_color.';
	}
	.product-info-main .product-social-links a.mailto,
	.bundle-options-container .product-add-form .product-addto-links a,
	.product-info-main .product-addto-links a {
		color: '.$productpage_info_add_links_color_d.';
		background-color: '.$productpage_info_add_links_bg_d.';
		border-color: '.$productpage_info_add_links_border_d.';
	}
	.product-info-main .product-mail-to a:hover,
	.bundle-options-container .product-add-form .product-addto-links a:hover,
	.product-info-main .product-social-links a:hover {
		color: '.$productpage_info_add_links_color_h.';
		background-color: '.$productpage_info_add_links_bg_h.';
		border-color: '.$productpage_info_add_links_border_h.';
	}
	.product-info-main .product-mail-to a:active,
	.bundle-options-container .product-add-form .product-addto-links a:active,
	.product-info-main .product-social-links a:active {
		color: '.$productpage_info_add_links_color_a.';
		background-color: '.$productpage_info_add_links_bg_a.';
		border-color: '.$productpage_info_add_links_border_a.';
	}
	.product-info-main .product-mail-to a:focus,
	.bundle-options-container .product-add-form .product-addto-links a:focus,
	.product-info-main .product-social-links a:focus {
		color: '.$productpage_info_add_links_color_f.';
		background-color: '.$productpage_info_add_links_bg_f.';
		border-color: '.$productpage_info_add_links_border_f.';
	}
	.product-labels span.label-sale,
	.product-labels span.label-sale span {
		color: '.$general_product_label_sale_color.';
		background-color: '.$general_product_label_sale_bg.';
	}
	.product-labels span {
		color: '.$general_product_label_new_color.';
		background-color: '.$general_product_label_new_bg.';
	}
	.catalog-product-view .product-info-main .box-tocart .btn {
		color: '.$productpage_info_cart_btn_color_d.';
		background-color: '.$productpage_info_cart_btn_bg_d.';
		border-color: '.$productpage_info_cart_btn_bg_d.';
	}
	.catalog-product-view .product-info-main .box-tocart .btn:hover,
	.catalog-product-view .product-info-main .box-tocart .btn.hover {
		color: '.$productpage_info_cart_btn_color_h.';
		background-color: '.$productpage_info_cart_btn_bg_h.';
		border-color: '.$productpage_info_cart_btn_bg_h.';
	}
	.catalog-product-view .product-info-main .box-tocart .btn:active,
	.catalog-product-view .product-info-main .box-tocart .btn.active {
		color: '.$productpage_info_cart_btn_color_a.';
		background-color: '.$productpage_info_cart_btn_bg_a.';
		border-color: '.$productpage_info_cart_btn_bg_a.';
	}
	.catalog-product-view .product-info-main .box-tocart .btn:focus,
	.catalog-product-view .product-info-main .box-tocart .btn.focus {
		color: '.$productpage_info_cart_btn_color_f.';
		background-color: '.$productpage_info_cart_btn_bg_f.';
		border-color: '.$productpage_info_cart_btn_bg_f.';
	}
	.product-info-main .product-options-wrapper {
		background-color: '.$productpage_options_bg.';
	}
	.product-info-main .product-options-wrapper .label,
	.product-info-main .product-options-wrapper .swatch-attribute-label,
	body .swatch-attribute-selected-option {
		color: '.$productpage_options_label_color.';
	}
	.stock.available {
		color: '.$general_product_instock_color.';
	}
	.stock {
		color: '.$general_product_outofstock_color.';
	}
	.product-info-main .availability.only {
		color: '.$general_product_availability_only_color.';
	}
	.catalog-product-view .product-info-main .box-tocart label[for="qty"] {
		color: '.$productpage_info_qty_label_color.';
	}
	.catalog-product-view .product-info-main .box-tocart .field.qty .control {
		background-color: '.$productpage_info_qty_input_bg.';
	}
	.catalog-product-view .product-info-main .box-tocart .field.qty input.qty {
		color: '.$productpage_info_qty_input_color.';
	}
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-decrease i,
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-increase i {
		color: '.$productpage_info_qty_input_btns_color_d.';
		background-color: '.$productpage_info_qty_input_btns_bg_d.';
	}
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-decrease i:hover,
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-increase i:hover {
		color: '.$productpage_info_qty_input_btns_color_h.';
		background-color: '.$productpage_info_qty_input_btns_bg_h.';
	}
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-decrease i:active,
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-increase i:active {
		color: '.$productpage_info_qty_input_btns_color_a.';
		background-color: '.$productpage_info_qty_input_btns_bg_a.';
	}
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-decrease i:focus,
	.catalog-product-view .product-info-main .box-tocart .field.qty div.quantity-increase i:focus {
		color: '.$productpage_info_qty_input_btns_color_f.';
		background-color: '.$productpage_info_qty_input_btns_bg_f.';
	}
	.catalog-product-view .static-block-top .block-link {
		color: '.$productpage_info_delivery_ship_color.';
	}
	.product-options-wrapper .block-link.size-chart {
		color: '.$productpage_info_size_guide_color_d.';
	}
	.product-options-wrapper .block-link.size-chart:hover {
		color: '.$productpage_info_size_guide_color_h.';
	}
	.product-options-wrapper .block-link.size-chart:active {
		color: '.$productpage_info_size_guide_color_a.';
	}
	.product-options-wrapper .block-link.size-chart:focus {
		color: '.$productpage_info_size_guide_color_f.';
	}
	.catalog-product-view .additional-information li,
	.catalog-product-view .additional-information li a {
		color: '.$productpage_info_additional_info_links_color_d.';
	}
	.catalog-product-view .additional-information li:hover,
	.catalog-product-view .additional-information li a:hover {
		color: '.$productpage_info_additional_info_links_color_h.';
	}
	.catalog-product-view .additional-information li:active,
	.catalog-product-view .additional-information li a:active {
		color: '.$productpage_info_additional_info_links_color_a.';
	}
	.catalog-product-view .additional-information li:focus,
	.catalog-product-view .additional-information li a:focus {
		color: '.$productpage_info_additional_info_links_color_f.';
	}
	.catalog-product-view .additional-information i,
	.catalog-product-view .additional-information a i {
		color: '.$productpage_info_additional_info_check_color.';
	}
	.product-info-main .sku strong,
	.product-info-main .manufacturer strong {
		color: '.$productpage_info_additional_info_label_color.';
	}
	.product-info-main .sku div,
	.product-info-main .manufacturer div {
		color: '.$productpage_info_additional_info_text_color.';
	}
	.product.data.items {
		border-color: '.$productpage_tabs_border.';
	}
	.product.data.items > .item:first-child::before {
		background-color: '.$productpage_tabs_head_bg_d.';
	}
	.product.data.items > .item.title > .switch {
		color: '.$productpage_tabs_head_color_d.';
	}
	.product.data.items > .item.title:not(.disabled) > .switch:hover {
		color: '.$productpage_tabs_head_color_h.';
		background-color: '.$productpage_tabs_head_bg_h.';
	}
	.product.data.items > .item.title:not(.disabled) > .switch:active,
	.product.data.items > .item.title.active > .switch,
	.product.data.items > .item.title.active > .switch:focus,
	.product.data.items > .item.title.active > .switch:hover {
		color: '.$productpage_tabs_head_color_a.';
		background-color: '.$productpage_tabs_head_bg_a.';
	}
	.product.data.items > .item.title:not(.disabled) > .switch:active::after,
	.product.data.items > .item.title.active > .switch::after,
	.product.data.items > .item.title.active > .switch:focus::after,
	.product.data.items > .item.title.active > .switch:hover::after {
		background-color: '.$productpage_tabs_head_bg_a.';
	}
	.product.data.items > .item.title:not(.disabled) > .switch:focus {
		color: '.$productpage_tabs_head_color_f.';
		background-color: '.$productpage_tabs_head_bg_f.';
	}
	.product.data.items > .item.title:not(:first-of-type):not(.active) .switch::before {
		background-color: '.$productpage_tabs_divider_color.';
	}
	.additional-attributes-wrapper .table tbody tr {
		border-color: '.$productpage_tabs_content_table_border.';
	}
	#product-review-container .review-item .top-block,
	#product-review-container .review-box {
		background-color: '.$productpage_reviews_bg.';
		border-color: '.$productpage_reviews_border.';
	}
	#product-review-container .review-title {
		color: '.$productpage_reviews_title_color.';
	}
	#product-review-container .rating-summary .label,
	#product-review-container .review-box,
	#product-review-container .review-item .customer-info .review-author {
		color: '.$productpage_reviews_text_color.';
	}
	#product-review-container .review-item .customer-info .review-author strong,
	#product-review-container .review-item .customer-info .date {
		color: '.$productpage_reviews_date_color.';
	}

	';

	if (stristr($general_home_subscribe_bg, 'rgba') === FALSE) {
        $cssData .= 'body .subscribe-block {
        background: '.$general_home_subscribe_bg.';
        }';
    } else {
        $colors = explode("," , $general_home_subscribe_bg);

        $opacity = substr($colors[3], 0, -1);
        if ($opacity != 0) {
         $cssData .= 'body .subscribe-block {
              background: '.$general_home_subscribe_bg.';
              }';
        }
    }

		return $cssData;
	}
}