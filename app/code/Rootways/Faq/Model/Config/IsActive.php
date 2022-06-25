<?php
/**
 * FAQ configuration file.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/shop/media/extension_doc/license_agreement.pdf
*/
namespace Rootways\Faq\Model\Config;

class IsActive implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
	
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Active')],
            ['value' => 0, 'label' => __('InActive')]
        ];
    }
	
    public function getStatusOptions($flag = false)
    {
        $options = [];

        if ($flag) {
            $options[''] = '-- Status --';
        }

        $options[self::STATUS_DISABLED] = __('InActive');
        $options[self::STATUS_ENABLED] = __('Active');

        $this->_options = $options;
        return $this->_options;
    }
}
