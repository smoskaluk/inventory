<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventorySales\Test\Api;

/**
 * Web Api order creation with bundle product in multi stock mode tests.
 */
class OrderCreateMultiStockModeBundleProductTest extends OrderPlacementBase
{
    /**
     * Create order with bundle product - registered customer, default stock, default website.
     *
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryBundleProduct/Test/_files/default_stock_bundle_products.php
     */
    public function testCustomerPlaceOrderDefaultWebsiteDefaultStock()
    {
        $this->_markTestAsRestOnly();
        $this->assignStockToWebsite(1, 'base');
        $this->getCustomerToken('customer@example.com', 'password');
        $this->createCustomerCart();
        $this->addBundleProduct('bundle-product-in-stock');
        $this->estimateShippingCosts();
        $this->setShippingAndBillingInformation();
        $orderId = $this->submitPaymentInformation();
        $order = $this->getOrder($orderId);
        $this->assertEquals('customer@example.com', $order['customer_email']);
        $this->assertEquals('Bundle Product In Stock', $order['items'][0]['name']);
        $this->assertEquals('bundle', $order['items'][0]['product_type']);
        $this->assertEquals('bundle-product-in-stock-simple', $order['items'][0]['sku']);
        $this->assertEquals(10, $order['items'][0]['price']);
        $this->assertEquals('Simple Product', $order['items'][1]['name']);
        $this->assertEquals('simple', $order['items'][1]['product_type']);
        $this->assertEquals('simple', $order['items'][1]['sku']);
        $this->assertEquals(0, $order['items'][1]['price']);
    }

    /**
     * Create order with bundle product - registered customer, default stock, additional website.
     *
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventorySalesApi/Test/_files/websites_with_stores.php
     * @magentoApiDataFixture ../../../../app/code/Magento/InventoryBundleProduct/Test/_files/product_bundle.php
     */
    public function testCustomerPlaceOrderCustomWebsiteDefaultStock()
    {
        $this->_markTestAsRestOnly();
        $this->setStoreView('store_for_us_website');
        $this->assignStockToWebsite(1, 'us_website');
        $this->assignProductsToWebsite(['bundle', 'simple_product_bundle_option'], 'us_website');
        $this->getCustomerToken('customer@example.com', 'password');
        $this->createCustomerCart();
        $this->addBundleProduct('bundle');
        $this->estimateShippingCosts();
        $this->setShippingAndBillingInformation();
        $orderId = $this->submitPaymentInformation();
        $order = $this->getOrder($orderId);
        $this->assertEquals('customer@example.com', $order['customer_email']);
        $this->assertEquals('Bundle Product', $order['items'][0]['name']);
        $this->assertEquals('bundle', $order['items'][0]['product_type']);
        $this->assertEquals('bundle-simple_10', $order['items'][0]['sku']);
        $this->assertEquals(10, $order['items'][0]['price']);
        $this->assertEquals('Simple product 10', $order['items'][1]['name']);
        $this->assertEquals('simple', $order['items'][1]['product_type']);
        $this->assertEquals('simple_10', $order['items'][1]['sku']);
        $this->assertEquals(0, $order['items'][1]['price']);
    }
}
