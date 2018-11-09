<?php

declare(strict_types=1);

namespace Pap\OrderNote\Model\Plugin\Order;

use Pap\OrderNote\Model\OrderNote;
use Pap\OrderNote\Model\ResourceModel\OrderNote\Collection as OrderNoteCollection;

/**
 * Class OrderRepository
 *
 * @package OrderRepository
 */
class OrderRepository
{
    /**
     * @var OrderNoteCollection
     */
    private $orderNoteCollection;

    /**
     * OrderRepository constructor.
     * @param OrderNoteCollection $orderNoteCollection
     */
    public function __construct(
        OrderNoteCollection $orderNoteCollection
    ) {
        $this->orderNoteCollection = $orderNoteCollection;
    }

    /**
     * @param $orderRepository
     * @param $result
     * @return mixed
     */
    public function afterGet($orderRepository, $result)
    {
        $this->orderNoteCollection->getSelect()
            ->where('order_id=?',$result->getEntityId());
        /** @var OrderNote $note */
        $note = $this->orderNoteCollection->load()->getFirstItem();

        $result->setData('customer_note', $note->getNote());
        return $result;
    }
}
