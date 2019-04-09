<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryReservations\Test\Integration\Model;

use Magento\InventoryReservationCli\Model\GetOrderInPendingState;
use Magento\InventoryReservationCli\Model\GetOrderWithBrokenReservation;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\Registry;

class GetListReservationsTotOrdersTest extends TestCase
{
    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryReservationCli/Test/Integration/_fixtures/order_with_reservation.php
     */
    public function testShouldNotFindAnyInconsistency(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var GetOrderWithBrokenReservation $subject */
        $subject = $objectManager->get(GetOrderWithBrokenReservation::class);

        /** @var GetOrderInPendingState $getOrderInPendingState */
        $getOrderInPendingState = $objectManager->get(GetOrderInPendingState::class);

        /** @var array $result */
        $result = $subject->execute();

        /** @var OrderInterface[] $orders */
        $orders = $getOrderInPendingState->execute(array_keys($result));

        self::assertSame([], $orders);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/InventoryReservationCli/Test/Integration/_fixtures/order_pending_with_reservation.php
     * @magentoDbIsolation enabled
     */
    public function testShouldReturnOneReservationInconsistency(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var GetOrderWithBrokenReservation $subject */
        $subject = $objectManager->get(GetOrderWithBrokenReservation::class);

        /** @var GetOrderInPendingState $getOrderInPendingState */
        $getOrderInPendingState = $objectManager->get(GetOrderInPendingState::class);

        /** @var array $result */
        $result = $subject->execute();

        /** @var OrderInterface[] $orders */
        $orders = $getOrderInPendingState->execute(array_keys($result));

        self::assertCount(1, $orders);
    }

    public function tearDown()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Framework\Registry $registry */
        $registry = $objectManager->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);
        $orderCollection = $objectManager->create(OrderCollectionFactory::class)->create();

        /** @var Order $order */
        foreach ($orderCollection as $order){
            $order->delete();
        }
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }
}
