<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

require __DIR__ . '/../../../../../../../dev/tests/integration/testsuite/Magento/Sales/_files/order.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->create(\Magento\Sales\Model\Order::class)
    ->loadByIncrementId('100000001');

/** @var \Magento\Sales\Api\OrderManagementInterface $orderManagement */
$orderManagement = $objectManager->create(\Magento\Sales\Api\OrderManagementInterface::class);

$status = $order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
$id = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->getStore('default')->getId();
$order->setState($status)
    ->setStatus($status)
    ->setStoreId($id);

/** @var \Magento\Framework\DB\Transaction $transaction */
$transaction = $objectManager->create(\Magento\Framework\DB\Transaction::class);
$order->setIsInProcess(true);
$transaction->addObject($order)->save();
$orderManagement->place($order);
