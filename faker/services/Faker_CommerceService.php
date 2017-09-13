<?php
/**
 * Faker plugin for Craft CMS
 *
 * Faker Service
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Faker
 * @since     1.0.0
 */

namespace Craft;

class Faker_CommerceService extends BaseApplicationComponent
{
    private $locale       = 'en_US';
    private $faker;
    private $productTypes = null;

    public function init ()
    {
        parent::init();

        $this->faker = craft()->faker->factory();
    }

    public function createCustomer ()
    {
        //$this->faker->
        // Create Craft users if there doesn't exist one
        $randomEmail = $this->faker->email;

        $user      = craft()->users->getUserByUsernameOrEmail($randomEmail);
        $firstName = $this->faker->firstName;
        $lastName  = $this->faker->lastName;

        if ( !$user ) {
            $user            = new UserModel();
            $user->username  = $randomEmail;
            $user->email     = $randomEmail;
            $user->firstName = $firstName;
            $user->lastName  = $lastName;
            $user->admin     = false;
            $user->archived  = false;
            $user->pending   = false;
            $user->locked    = false;
            $user->suspended = false;

            craft()->users->saveUser($user);
        }

        if ( !$user->hasErrors() ) {
            // Assign users
            $groupIds = [ 1 ];
            craft()->userGroups->assignUserToGroups($user->id, $groupIds);

            $country = craft()->commerce_countries->getCountryByAttributes([ 'iso' => $this->faker->countryCode ]);

            if ( !$country ) {
                $country = craft()->commerce_countries->getCountryByAttributes([ 'iso' => 'US' ]);
            }

            // Create addresses
            $address            = new Commerce_AddressModel();
            $address->firstName = $firstName;
            $address->lastName  = $lastName;
            $address->address1  = $this->faker->streetAddress;
            $address->city      = $this->faker->city;
            $address->zipCode   = $this->faker->postcode;
            $address->phone     = $this->faker->phoneNumber;
            $address->countryId = $country->id;

            //$state = new Commerce_StateModel();
            //$state->

            $addressSaved = craft()->commerce_addresses->saveAddress($address);

            // TODO: Check if there is a customer already
            // Create Commerce customer if there doesn't exist one
            $customer                            = new Commerce_CustomerModel();
            $customer->email                     = $randomEmail;
            $customer->userId                    = $user->id;
            $customer->lastUsedBillingAddressId  = $address->id;
            $customer->lastUsedShippingAddressId = $address->id;

            craft()->commerce_customers->saveCustomer($customer);

            if ( $addressSaved ) {
                $customerAddress = Commerce_CustomerAddressRecord::model()->findByAttributes([
                    'customerId' => $customer->id,
                    'addressId'  => $address->id
                ]);

                if ( !$customerAddress ) {
                    $customerAddress = new Commerce_CustomerAddressRecord;
                }

                $customerAddress->customerId = $customer->id;
                $customerAddress->addressId  = $address->id;

                $customerAddress->save();
            }

            return $customer;
        }

    }

    public function createProduct ($locale = null)
    {
        if ( !$this->productTypes ) {
            $this->productTypes = craft()->commerce_productTypes->getAllProductTypes();
        }


        if ( !$locale ) {
            $locale = craft()->i18n->getPrimarySiteLocaleId();
        }

        if ( $this->productTypes ) {
            $productType = $this->faker->randomElement($this->productTypes);
            $randomTitle = $this->faker->sentence();

            $product               = new Commerce_ProductModel();
            $product->promotable   = true;
            $product->freeShipping = $this->faker->boolean($chanceOfGettingTrue = 30);
            $product->freeShipping = $this->faker->boolean($chanceOfGettingTrue = 30);
            $product->typeId       = $productType->id;

            $product->locale              = $locale;
            $product->localeEnabled       = true;
            $product->getContent()->title = $randomTitle;

            // Variants
            // TODO: Add random amount of variants
            $variant                 = new Commerce_VariantModel();
            $variant->height         = $this->faker->numberBetween(100, 3000);
            $variant->width          = $this->faker->numberBetween(100, 3000);
            $variant->weight         = $this->faker->numberBetween(100, 3000);
            $variant->length         = $this->faker->numberBetween(100, 3000);
            $variant->sku            = StringHelper::toKebabCase($randomTitle);
            $variant->price          = $this->faker->numberBetween(50, 99999);
            $variant->sortOrder      = 0;
            $variant->unlimitedStock = $this->faker->boolean($chanceOfGettingTrue = 70);
            //$variant->productId      = $product->id;
            $variant->isDefault           = true;
            $variant->stock               = $this->faker->numberBetween(100, 30000);
            $variant->getContent()->title = $randomTitle;

            $product->variants = [ $variant ];

            if ( craft()->commerce_products->saveProduct($product) ) {
                echo "Saved product?";
            }
            else {
                echo "Not saved product?";
            }

            //craft()->commerce_variants->saveVariant($variant);

            var_dump($product->getErrors());

            var_dump($product->getAttributes());
            var_dump($product->getContent()->getAttributes());

            //$variant->setProduct($product);

            // TODO: Fill custom fields

            /*
            'typeId'
            'taxCategoryId'
            'shippingCategoryId'
            'promotable'
            'freeShipping'
            'postDate'
            'expiryDate'

            'defaultVariantId'
            'defaultSku'
            'defaultPrice'
            'defaultHeight'
            'defaultLength'
            'defaultWidth'
            'defaultWeight'
            */
        }
    }

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

        }
        else {
            craft()->commerce_orders->completeOrder($order);
            craft()->commerce_cart->forgetCart();
        }

        echo "Done";
    }

}