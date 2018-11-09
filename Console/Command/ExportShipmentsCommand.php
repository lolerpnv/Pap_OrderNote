<?php

namespace Pap\OrderNote\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as ShipmentCollection;
use Pap\OrderNote\Model\OrderNote;
use Pap\OrderNote\Model\ResourceModel\OrderNote\Collection as OrderNoteCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportShipmentsCommand extends Command
{

    /**
     * @var State
     */
    protected $state;

    /**
     * @var DirectoryList
     */
    private $dir;

    /**
     * @var ShipmentCollection
     */
    private $shipmentCollection;

    /**
     * @var OrderGridCollection
     */
    private $orderFactory;

    /**
     * @var OrderNoteCollection
     */
    private $orderNoteCollection;

    /**
     * ExportShipmentsCommand constructor.
     * @param OrderFactory $orderFactory
     * @param OrderNoteCollection $orderNoteCollection
     * @param ShipmentCollection $shipmentCollection
     * @param DirectoryList $dir
     * @param State $state
     * @param null $name
     */
    public function __construct(
        OrderFactory $orderFactory,
        OrderNoteCollection $orderNoteCollection,
        ShipmentCollection $shipmentCollection,
        DirectoryList $dir,
        State $state,
        $name = null
    ) {
        $this->dir = $dir;
        $this->state = $state;
        $this->shipmentCollection = $shipmentCollection;
        $this->orderNoteCollection = $orderNoteCollection;
        $this->orderFactory = $orderFactory;

        parent::__construct($name);
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('order-note:shipment-export')->setDescription('Exports shipments');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode("adminhtml");
        $this->exportShipments();
    }

    /**
     * export shipments
     *
     * @return int|null|void
     * @throws \Exception
     */
    public function exportShipments()
    {
        $path = $this->dir->getRoot().'/var/export/order_note';

        if (!file_exists($path)) {
            mkdir($path, 0775, true);//all to user and owner
        }

        $this->shipmentCollection->getSelect()->where('exported=?',0);

        $shipments = $this->shipmentCollection->load()->getItems();

        /** @var Shipment $shipment */
        foreach ($shipments as $shipment) {

            $id = $shipment->getEntityId();

            // creating filename based on current time
            $fName = date('dmY')."T".date('Gi').'ID'.$id;
            $file = fopen($path.'/'.$fName.'.csv','w');

            /** @var Order $order */
            $order = $this->orderFactory->create()->load($shipment->getOrderId());

            $this->orderNoteCollection->getSelect()->where('order_id=?',$shipment->getOrderId());
            /** @var OrderNote $orderNote */
            $orderNote = $this->orderNoteCollection->load()->getFirstItem();

            fputcsv($file,['shipment_increment_number','order_increment_number','order_note']);

            $data = [
                $shipment->getIncrementId(),
                $order->getIncrementId(),
                $orderNote->getNote()
            ];

            fputcsv($file,$data);

            $shipment->setData('exported',1);
            $shipment->save();

            fclose($file);
        }
    }
}
