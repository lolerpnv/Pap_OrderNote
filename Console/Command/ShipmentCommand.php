<?php

namespace Pap\OrderNote\Console\Command;

use Magento\Config\Console\Command\EmulatedAdminhtmlAreaProcessor;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Exception\CouldNotShipException;
use Magento\Sales\Exception\DocumentValidationException;
use Magento\Sales\Model\Convert\Order;
use Magento\Sales\Model\Order\OrderStateResolverInterface;
use Magento\Sales\Model\Order\ShipmentDocumentFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ShipOrder;
use Magento\Shipping\Model\ShipmentNotifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\Order\Shipment\NotifierInterface;
use Magento\Sales\Model\Order\Shipment\OrderRegistrarInterface;
use Magento\Sales\Model\Order\Validation\ShipOrderInterface as ShipOrderValidator;
use Psr\Log\LoggerInterface;

/**
 * Class ShipmentCommand
 *
 * @package ShipmentCommand
 */
class ShipmentCommand extends Command
{
    /**
     * @var State
     */
    protected $state;

    /**
     * @var ShipOrder
     */
    protected $shipOrder;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShipmentDocumentFactory
     */
    private $shipmentDocumentFactory;

    /**
     * @var OrderStateResolverInterface
     */
    private $orderStateResolver;

    /**
     * @var OrderConfig
     */
    private $config;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipOrderValidator
     */
    private $shipOrderValidator;

    /**
     * @var NotifierInterface
     */
    private $notifierInterface;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OrderRegistrarInterface
     */
    private $orderRegistrar;

    /**
     * @var Collection
     */
    private $orderCollection;

    /**
     * ShipmentCommand constructor.
     * @param ResourceConnection $resourceConnection
     * @param OrderRepositoryInterface $orderRepository
     * @param ShipmentDocumentFactory $shipmentDocumentFactory
     * @param OrderStateResolverInterface $orderStateResolver
     * @param OrderConfig $config
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipOrderValidator $shipOrderValidator
     * @param OrderRegistrarInterface $orderRegistrar
     * @param Collection $orderCollection
     * @param State $state
     * @param null $name
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        OrderRepositoryInterface $orderRepository,
        ShipmentDocumentFactory $shipmentDocumentFactory,
        OrderStateResolverInterface $orderStateResolver,
        OrderConfig $config,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipOrderValidator $shipOrderValidator,
        OrderRegistrarInterface $orderRegistrar,
        Collection $orderCollection,
        State $state,
        $name = null
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->orderRepository = $orderRepository;
        $this->shipmentDocumentFactory = $shipmentDocumentFactory;
        $this->orderStateResolver = $orderStateResolver;
        $this->config = $config;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipOrderValidator = $shipOrderValidator;
        $this->orderRegistrar = $orderRegistrar;
        $this->orderCollection = $orderCollection;
        $this->state = $state;

        parent::__construct($name);
    }

    /**
     * Command configuration
     */
    protected function configure()
    {
        $this->setName('order-note:shipment')
            ->setDescription('Creates shipment for orders in processing state');
    }

    /**
     * Execute migration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode("adminhtml");

        $this->orderCollection->getSelect()->where('state=?','processing');

        $orders = $this->orderCollection
            ->load()
            ->getItems();

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            try {
                $this->createShipment(
                    $order->getId()
            );
            } catch (CouldNotShipException $e) {
                //issue
            } catch (DocumentValidationException $e) {
                //issue
            }
        }
    }

    /**
     * Code taken from Magento\Sales\Model\ShipOrder
     *
     * @param $orderId
     * @param array $items
     * @param bool $notify
     * @param bool $appendComment
     * @param ShipmentCommentCreationInterface|null $comment
     * @param array $tracks
     * @param array $packages
     * @param ShipmentCreationArgumentsInterface|null $arguments
     * @return int|null
     * @throws \Magento\Sales\Exception\CouldNotShipException
     * @throws \Magento\Sales\Exception\DocumentValidationException
     */
    public function createShipment(
        $orderId,
        array $items = [],
        $notify = false,
        $appendComment = false,
        ShipmentCommentCreationInterface $comment = null,
        array $tracks = [],
        array $packages = [],
        ShipmentCreationArgumentsInterface $arguments = null
    ) {
        $connection = $this->resourceConnection->getConnection('sales');
        $order = $this->orderRepository->get($orderId);
        $shipment = $this->shipmentDocumentFactory->create(
            $order,
            $items,
            $tracks,
            $comment,
            ($appendComment && $notify),
            $packages,
            $arguments
        );
        $validationMessages = $this->shipOrderValidator->validate(
            $order,
            $shipment,
            $items,
            $notify,
            $appendComment,
            $comment,
            $tracks,
            $packages
        );
        if ($validationMessages->hasMessages()) {
            throw new DocumentValidationException(
                __("Shipment Document Validation Error(s):\n" . implode("\n", $validationMessages->getMessages()))
            );
        }
        $connection->beginTransaction();
        try {
            $this->orderRegistrar->register($order, $shipment);
            $order->setState(
                $this->orderStateResolver->getStateForOrder($order, [OrderStateResolverInterface::IN_PROGRESS])
            );
            $order->setStatus($this->config->getStateDefaultStatus($order->getState()));
            $this->shipmentRepository->save($shipment);
            $this->orderRepository->save($order);
            $connection->commit();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $connection->rollBack();
            throw new CouldNotShipException(
                __('Could not save a shipment, see error log for details')
            );
        }
        if ($notify) {
            if (!$appendComment) {
                $comment = null;
            }
            $this->notifierInterface->notify($order, $shipment, $comment);
        }
        return $shipment->getEntityId();
    }
}
