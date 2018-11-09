<?php

namespace Pap\OrderNote\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class OrderNote
 *
 * @package OrderNote
 */
class OrderNote extends AbstractDb
{

    protected $_idFieldName = "entity_id";

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pap_order_note', 'entity_id');
    }
}
