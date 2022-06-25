<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */











































namespace Aheadworks\AdvancedReports\Model\Email\Template\Magento230;

use Magento\Framework\Mail\MailMessageInterface;
use Zend\Mime\Mime;
use Aheadworks\AdvancedReports\Model\Email\Template\Message as BaseMessage;

/**
 * Class Message
 *
 * @package Aheadworks\AdvancedReports\Model\Email\Template\Magento230
 */
class Message extends BaseMessage implements MailMessageInterface
{
    /**
     * @inheritdoc
     */
    public function setBodyText($content)
    {
        $textPart = $this->partFactory->create();
        $textPart->setContent($content)
            ->setType(Mime::TYPE_TEXT)
            ->setCharset($this->zendMessage->getEncoding());
        $this->parts[] = $textPart;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setBodyHtml($content)
    {
        $htmlPart = $this->partFactory->create();
        $htmlPart->setContent($content)
            ->setType(Mime::TYPE_HTML)
            ->setCharset($this->zendMessage->getEncoding());
        $this->parts[] = $htmlPart;

        return $this;
    }

    /**
     * Set parts to Zend message body
     *
     * @return $this
     */
    public function setPartsToBody()
    {
        $mimeMessage = $this->mimeMessageFactory->create();
        $mimeMessage->setParts($this->parts);
        $this->zendMessage->setBody($mimeMessage);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFrom($fromAddress)
    {
        $this->zendMessage->setFrom($fromAddress);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setBody($body)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setMessageType($type)
    {
        return $this;
    }
}
