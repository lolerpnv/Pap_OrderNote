<?php

namespace Pap\OrderNote\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;
use Pap\OrderNote\Model\ResourceModel\OrderNote\Collection;

/**
 * Class Note
 *
 * @package Note
 */
class Note extends AbstractOrder
{
    /**
     * @var OrderNoteFactory
     */
    protected $orderNoteCollection;

    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        Collection $orderNoteCollection,
        array $data = []
    ) {
        $this->orderNoteCollection = $orderNoteCollection;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentNote()
    {
        $order = $this->getOrder();

        $this->orderNoteCollection
            ->getSelect()
            ->where('order_id', $order->getId());

        $note = $this->orderNoteCollection->load()->getFirstItem();

        return $note->getNote();
    }
}