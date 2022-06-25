<?php

namespace Wyomind\SimpleGoogleShopping\Plugin\Framework\App\PageCache;

use Magento\Framework\App\RequestInterface;
class Identifier
{
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * @param $subject
     * @param $proceed
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetValue($subject, $proceed)
    {
        $sha1 = $proceed();
        if ($ps = $this->request->getParam('ps')) {
            $sha1 .= $sha1 . $ps;
        }
        return $sha1;
    }
}