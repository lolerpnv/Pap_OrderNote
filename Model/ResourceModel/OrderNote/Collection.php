<?php

namespace Pap\OrderNote\Model\ResourceModel\OrderNote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @package Collection
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'order_note_collection';
    protected $_eventObject = 'note_collection';
    protected $_mainTable = 'pap_order_note';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Pap\OrderNote\Model\OrderNote', 'Pap\OrderNote\Model\ResourceModel\OrderNote');
    }
}
