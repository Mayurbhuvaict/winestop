<?php
namespace Meigee\Barbour\Plugin\Result;

use Magento\Framework\App\ResponseInterface;
use \Magento\Store\Model\ScopeInterface;

class Page
{
  private $context;
  private $registry;

  public function __construct(
    \Magento\Framework\View\Element\Context $context,
    \Magento\Framework\Registry $registry
  ) {
    $this->context = $context;
    $this->registry = $registry;
    $this->_scopeConfig = $context->getScopeConfig();
    $this->cnf = $this->_scopeConfig->getValue('barbour_general/barbour_layout', ScopeInterface::SCOPE_STORE);
    $this->cnf2 = $this->_scopeConfig->getValue('barbour_general/barbour_header', ScopeInterface::SCOPE_STORE);
  }

  public function beforeRenderResult(
    \Magento\Framework\View\Result\Page $subject,
    ResponseInterface $response
  ){
    $subject->getConfig()->addBodyClass($this->cnf['site_layout']);
    $subject->getConfig()->addBodyClass($this->cnf2['sticky_header_tablet']);
    return [$response];
  }
}