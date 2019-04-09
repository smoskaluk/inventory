<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryReservations\Command;

use Magento\InventoryReservations\Model\GetOrderInPendingState as GetOrderInPendingStateAlias;
use Magento\InventoryReservations\Model\GetOrderWithBrokenReservation;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PendingOrderInconsistency extends Command
{
    /**
     * @var GetOrderWithBrokenReservation
     */
    private $getOrderWithBrokenReservation;
    /**
     * @var GetOrderInPendingStateAlias
     */
    private $getOrderInPendingState;

    /**
     * @param GetOrderWithBrokenReservation $getOrderWithBrokenReservation
     * @param GetOrderInPendingStateAlias $getOrderInPendingState
     */
    public function __construct(
        GetOrderWithBrokenReservation $getOrderWithBrokenReservation,
        GetOrderInPendingStateAlias $getOrderInPendingState
    ) {
        parent::__construct();
        $this->getOrderWithBrokenReservation = $getOrderWithBrokenReservation;
        $this->getOrderInPendingState = $getOrderInPendingState;
    }

    protected function configure()
    {
        $this
            ->setName('inventory:reservation:pending-order-inconsistency')
            ->setDescription('Show all reservation inconsistencies for pending orders');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        /** @var array $ordersBrokenReservation */
        $ordersBrokenReservation = $this->getOrderWithBrokenReservation->execute();

        /** @var OrderInterface[] $orders */
        $orders = $this->getOrderInPendingState->execute(array_keys($ordersBrokenReservation));

        /** @var Order $order */
        foreach($orders as $order) {
            $output->writeln(
                __('Order %1 got inconsistency on inventory reservation',
                    $order->getIncrementId()
                )
            );
        }
    }
}
