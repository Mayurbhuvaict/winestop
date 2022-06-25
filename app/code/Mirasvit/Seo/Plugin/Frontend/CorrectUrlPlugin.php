<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Plugin\Frontend;

use Magento\Framework\App\Action\Action;

/**
 * Case A: http://example.com/abc////abc/ =>  http://example.com/abc/abc/ (301)
 * @see \Magento\Framework\App\Action\Action::dispatch()
 */
class CorrectUrlPlugin
{
    /**
     * @param Action $subject
     * @param object $response
     *
     * @return object
     */
    public function afterDispatch($subject, $response)
    {
        $uri = $subject->getRequest()->getRequestUri();

        if (strpos($uri, '//') !== false) {
            while (strpos($uri, '//') !== false) {
                $uri = str_replace('//', '/', $uri);
            }

            $subject->getResponse()->setRedirect($uri, 301);
        }

        return $response;
    }
}
