<?php
namespace Craft;

class CreateTestOrder
{
    public function create ($complete = true)
    {
        $complete = filter_var($complete, FILTER_VALIDATE_BOOLEAN);

        $_SERVER    = [
            'SERVER_NAME'     => 'commerce.dev',
            'SCRIPT_FILENAME' => 'create.php',
            'SCRIPT_NAME'     => 'create.php',
        ];
        $productIds = craft()->elements->getCriteria('Commerce_Product', [ 'limit' => null ])->ids();

        //var_dump($productIds);

// Configuration

        $customerIds          = $this->getCustomerIds();
        $customerId           = $customerIds[ array_rand($customerIds) ]; // real customer with email address set
        $productId            = $productIds[ array_rand($productIds) ];
        $productQuantity      = rand(1, 5);
        $paymentMethodId      = 1;
        $shippingMethodHandle = 'bring';

        // https://stripe.com/docs/testing
        $cards = [
            'success'                => [
                'number'    => 4242424242424242,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 163,
            ],
            'invalid_month'          => [
                'number'    => 4242424242424242,
                'exp_month' => 13,
                'exp_year'  => 2020,
                'cvv'       => 163,
            ],
            'invalid_year'           => [
                'number'    => 4242424242424242,
                'exp_month' => 12,
                'exp_year'  => 1970,
                'cvv'       => 163,
            ],
            'invalid_cvv'            => [
                'number'    => 4242424242424242,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 99,
            ],
            'fail_address_zip_check' => [
                'number'    => 4000000000000010,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 534,
            ],
            'fail_address_check'     => [
                'number'    => 4000000000000028,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 534,
            ],
            'fail_zip_check'         => [
                'number'    => 4000000000000036,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 534,
            ],
            'risk_level_elevated'    => [
                'number'    => 4000000000009235,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 534,
            ],
            'card_declined'          => [
                'number'    => 4000000000000002,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 534,
            ],
            'card_declined_fraud'    => [
                'number'    => 4100000000000019,
                'exp_month' => 12,
                'exp_year'  => 2020,
                'cvv'       => 534,
            ],
        ];
        /*
         * By default, passing address or CVC data with the card number causes the address and CVC checks to succeed.
         * If this information isn't specified, the value of the checks is null.
         * Any expiration date in the future is considered valid.
         */

        $order         = new Commerce_OrderModel();
        $order->number = md5(uniqid(mt_rand(), true));

        $order->currency        = craft()->commerce_paymentCurrencies->getPrimaryPaymentCurrencyIso();
        $order->paymentCurrency = craft()->commerce_paymentCurrencies->getPrimaryPaymentCurrencyIso();

        // Get products
        $product  = craft()->commerce_products->getProductById($productId);
        $customer = craft()->commerce_customers->getCustomerById($customerId);

        if ( $customer ) {
            $addresses = $customer->getAddresses();
            if ( isset($addresses[0]) && $address = $addresses[0] ) {
                $order->setShippingAddress($address);
                $order->setBillingAddress($address);

            }

            $order->customerId = $customerId;
            $order->email      = $order->customer->email;

            $order->paymentMethodId = $paymentMethodId;
            $order->shippingMethod  = $shippingMethodHandle;

            craft()->commerce_orders->saveOrder($order);

            $error   = '';
            $success = craft()->commerce_cart->addToCart($order, $product->getDefaultVariant()->id, $productQuantity, '', $options = [ ], $error);

            if ( !$success ) {
                echo $error;
            }

            if ( $complete ) {
                $success = craft()->commerce_orders->completeOrder($order);
            }

            if ( !$success ) {
                //echo 'Success: ' . $order->id;
                //echo "Could’t complete order: <a href='" . $order->getCpEditUrl() . "'>" . $order->number . "</a>'";
            }
            else {
                //echo "Created order: <a href='" . $order->getCpEditUrl() . "'>" . $order->number . "</a>'";
            }

            return $order;
        }


        /**
         * $transaction            = craft()->commerce_transactions->createTransaction($order);
         * $transaction->reference = //stripe charge reference;
         * $transaction->code = //stripe charge response code;
         * $transaction->message = //stripe charge response message sentence;
         * $transaction->response = //stripe charge response data (will be serialised into json);
         * $transaction->status = Commerce_TransactionRecord::STATUS_SUCCESS;
         * craft()->commerce_transaction->saveTransaction($transaction);
         * craft()->commerce_orders->updateOrderPaidTotal($order);
         */
    }

    public function getCustomerIds ()
    {
        $customers = craft()->commerce_customers->getAllCustomers();

        $ids = [ ];
        foreach ($customers as $customer) {
            $ids[] = $customer->id;
        }

        return $ids;
    }
}