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



namespace Mirasvit\SeoContent\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\SeoContent\Model\TemplateFactory;
use Mirasvit\SeoContent\Model\ResourceModel\Template\CollectionFactory;

class TemplateRepository implements TemplateRepositoryInterface
{
    /**
     * @var TemplateFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * TemplateRepository constructor.
     * @param TemplateFactory $factory
     * @param CollectionFactory $collectionFactory
     * @param EntityManager $entityManager
     */
    public function __construct(
        TemplateFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @return TemplateInterface[]|\Mirasvit\SeoContent\Model\ResourceModel\Template\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     * @return bool|false|TemplateInterface|\Mirasvit\SeoContent\Model\Template|mixed
     */
    public function get($id)
    {
        $template = $this->create();
        $template = $this->entityManager->load($template, $id);

        if (!$template->getId()) {
            return false;
        }

        return $template;
    }

    /**
     * @param TemplateInterface $template
     * @return TemplateInterface|object
     * @throws \Exception
     */
    public function save(TemplateInterface $template)
    {
        return $this->entityManager->save($template);
    }

    /**
     * @param TemplateInterface $template
     * @return bool
     * @throws \Exception
     */
    public function delete(TemplateInterface $template)
    {
        $this->entityManager->delete($template);

        return true;
    }
}
