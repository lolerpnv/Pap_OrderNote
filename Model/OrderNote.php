<?php

namespace Pap\OrderNote\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class OrderNote
 *
 * @package OrderNote
 */
class OrderNote extends AbstractModel
{
    const CACHE_TAG = 'pap_order_note';

    protected $_cacheTag = 'pap_order_note';

    protected $_eventPrefix = 'pap_order_note';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Pap\OrderNote\Model\ResourceModel\OrderNote');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * @return string
     */
    public function getNote() {
        return $this->getData('note');
    }

    /**
     * @param $note string
     */
    public function setNote($note) {
        $this->setData('note',$note);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    /**
     * @param $orderId int
     */
    public function setOrderId($orderId): void
    {
        $this->setData('order_id',$orderId);
    }
}
