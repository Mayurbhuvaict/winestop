<?php
namespace Magecomp\Extrafee\Controller\Deliverymethod;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	const DELIVERY_METHOD_COLUMN = 'delivery_method';

	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $pageFactory;

	/**
	 * @var Magento\Framework\Controller\ResultFactory $resultJsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var \Magento\Quote\Model\QuoteRepository
	 */
	protected $quoteRepository;
	
	/**
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * @var \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface
	 */
	protected $maskedQuoteIdToQuoteId;

	/**
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\Controller\ResultFactory $resultJsonFactory
	 * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
	 */
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		ResultFactory $resultJsonFactory,
		\Magento\Quote\Model\QuoteRepository $quoteRepository,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
	) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->quoteRepository = $quoteRepository;
		$this->logger = $logger;
		$this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
		parent::__construct($context);
	}

	public function execute()
	{
		$quoteId = null;
		$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
		$quoteId = $this->getRequest()->getPost('quote_id');
		$deliveryMethod = $this->getRequest()->getPost('delivery_method');
        try {
			if (!is_numeric($quoteId)) {
				$quoteId = $this->maskedQuoteIdToQuoteId->execute($quoteId);
			}
            $quote = $this->quoteRepository->get($quoteId);
			$quote->setData(self::DELIVERY_METHOD_COLUMN, $deliveryMethod);
			$this->quoteRepository->save($quote);
			$resultJson->setData($deliveryMethod);
        } catch (\Exception $exception) {
            $this->logger->critical('Error message', ['exception' => $exception]);
        }
		$resultJson->setData([]);
		return $resultJson;
	}
}
