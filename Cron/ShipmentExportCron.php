<?php

namespace Pap\OrderNote\Cron;

use Pap\OrderNote\Console\Command\ExportShipmentsCommand;
use Psr\Log\LoggerInterface;

class ShipmentExportCron
{

    /**
     * @var ExportShipmentsCommand
     */
    private $exportShipmentsCommand;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ShipmentExportCron constructor.
     * @param ExportShipmentsCommand $exportShipmentsCommand
     * @param LoggerInterface $logger
     */
    public function __construct(
        ExportShipmentsCommand $exportShipmentsCommand,
        LoggerInterface $logger
    ) {
        $this->exportShipmentsCommand = $exportShipmentsCommand;
        $this->logger = $logger;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function execute()
    {
        $this->logger->log(100,"Starting Pap export cron");
        $this->exportShipmentsCommand->exportShipments();//TODO
        $this->logger->log(100,"Ending Pap export cron");

        return $this;
    }
}
