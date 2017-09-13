<?php
/**
 * Faker plugin for Craft CMS
 *
 * Faker Controller
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Faker
 * @since     1.0.0
 */

namespace Craft;

class FakerController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = array(
        'actionCreateOrders',
    );

    public function actionCreateOrders ()
    {
        $amount = craft()->request->getParam('amount', 100);

        for ($i = 1; $i <= $amount; $i++) {
            echo $i . "\n";

            craft()->faker->createOrder();
        }

        craft()->end();
    }

    public function actionCreateProducts ()
    {
        $locale = craft()->i18n->getPrimarySiteLocaleId();
        craft()->faker_commerce->createProduct($locale);

        craft()->end();
    }

    /**
     */
    public function createOrder ()
    {
        // Configuration

        // Id of customer for this order
        $customerId           = 1;
        $productId            = 8;
        $productQuantity      = random_int(3, 20);
        $orderStatusId        = 1;
        $paymentMethodId      = 1;
        $shippingMethodHandle = 'bring';

        $cart  = craft()->commerce_cart->getCart();
        $error = "";

        if ( !craft()->commerce_cart->setEmail($cart, "example@test.com", $error) ) {
            // see $error
        }

        $error = "";

        // Get products
        $product  = craft()->commerce_products->getProductById($productId);
        $customer = craft()->commerce_customers->getCustomerById($customerId);

        // Get first address
        $address = $customer->getAddresses()[0];


        // Cart model
        //$items = [ ];

        // Cart items
        //$item = new Commerce_LineItemModel();
        //$item->fillFromPurchasable($product->getDefaultVariant());
        //$item->qty = 5;

        //$items[] = $item;


        // Order model
        $order             = $cart;
        $order->customerId = $customerId;
        //$order->orderStatusId   = $orderStatusId;
        $order->paymentMethodId = $paymentMethodId;
        $order->shippingMethod  = $shippingMethodHandle;

        //$order->setLineItems($items);
        $order->setShippingAddress($address);
        $order->setBillingAddress($address);

        // Create order
        //craft()->commerce_orders->saveOrder($order);

        $addedToCart = craft()->commerce_cart->addToCart($order, $product->getDefaultVariant()->id, $productQuantity, '', $options = [ ], $error);

        if ( !$addedToCart ) {
            echo "Not added to cart";
        }
        else {
            craft()->commerce_orders->completeOrder($order);
        }

        echo "Done";
    }
}