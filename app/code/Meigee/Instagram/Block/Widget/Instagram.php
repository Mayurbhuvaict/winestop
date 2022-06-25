<?php
namespace Meigee\Instagram\Block\Widget;

class Instagram extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('widget/instagram.phtml');
	}
	
	function fetchData($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	function getImages()
	{
		$user_id = $this->getInstagramUserId();
		$access_token = $this->getInstagramAccessToken();
		
		$result = $this->fetchData("https://api.instagram.com/v1/users/".$user_id."/media/recent/?access_token=".$access_token."");
		$result = json_decode($result);
		return $result;
	}
	
}
