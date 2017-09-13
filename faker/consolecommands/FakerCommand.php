<?php
/**
 * Faker plugin for Craft CMS
 *
 * Faker Command
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Faker
 * @since     1.0.0
 */

namespace Craft;

require_once __DIR__ . '/create.php';

use Crew\Unsplash\UnsplashCollection;
use Crew\Unsplash\UnsplashException;
use Crew\Unsplash\HttpClient as Unsplash;
use Dariuszp\CliProgressBar;


class FakerCommand extends BaseCommand
{
    private $locale;

    public function init ()
    {
        parent::init();

        $this->locale = craft()->i18n->getPrimarySiteLocaleId();
    }

    /**
     */
    public function actionIndex ($param = "")
    {
        echo 'works';
    }

    public function actionSeed ($amount = 2, $type = 'books')
    {
        Unsplash::init([
            'applicationId' => '1626fd8369ac65bd82a376e84dc01e3060da9adca1f2086baeddc99b9e4ff0cb',
            'secret'        => '14658ca4159003b6073dd80216867640a8a90eb958e8f2cb8e32c062c93158ee',
            'callbackUrl'   => 'urn:ietf:wg:oauth:2.0:oob'
        ]);

        switch ($type) {
            case 'food';
                $collectionID = 140489;
                break;
            case 'books';
            default;
                $collectionID = 228444;
                break;
        }

        try {
            $collection = UnsplashCollection::find($collectionID);
        }
        catch (UnsplashException $e) {
            FakerPlugin::log("Collection doesn't exist for " . $collectionID . ".", LogLevel::Error);

            return;
        }

        $tempPath = craft()->path->getTempPath() . 'faker' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;

        if ( !IOHelper::folderExists($tempPath) ) {
            IOHelper::createFolder($tempPath);
        }

        $bar = new CliProgressBar($amount, 0);
        //$order->create();

        echo "(Faker) Seeding $amount customers, wohoo!\n";
        $bar->display();
        for ($i = 1; $i <= $amount; $i++) {
            //echo $i . "\n";
            $bar->progress();
            $bar->setProgressTo($i);
            craft()->faker_commerce->createCustomer();
        }

        $bar->setColorToGreen();
        $bar->end();
    }

    public function actionOrders ($amount = 20, $complete = true, $from = '')
    {
        $bar      = new CliProgressBar($amount, 0);
        $order    = new CreateTestOrder();
        $orderIds = [ ];
        //$order->create();

        echo "(Faker) Creating $amount orders, wohoo!\n";
        $bar->display();
        for ($i = 1; $i <= $amount; $i++) {
            //echo $i . "\n";
            $bar->progress();
            $bar->setProgressTo($i);
            $orderModel = $order->create($complete);

            if ( $orderModel ) {
                $orderIds[] = $orderModel->id;
            }

            //craft()->faker->createOrder();
        }

        $bar->end();
    }

    public function actionInactivateOrders ()
    {
        $edge             = new \DateTime();
        $interval         = new \DateInterval('P2DT4H');
        $interval->invert = 1;
        $edge->add($interval);

        $ids = craft()->db->createCommand()->select('orders.id')
                          ->from('commerce_orders orders')
                          ->where('isCompleted=:isCompleted AND dateUpdated >= :edge', [ ':isCompleted' => 'not 1', 'edge' => $edge->format('Y-m-d H:i:s') ])
                          ->queryColumn();

        $dateUpdated = $edge->format('Y-m-d H:i:s');
        foreach ($ids as $id) {

        }

        echo 'Ids to update: ' . implode(', ', $ids) . PHP_EOL;
        echo "Setting to: " . $edge->format('Y-m-d H:i:s') . PHP_EOL;

        $command = craft()->db->createCommand()
                              ->update('commerce_orders', [ 'dateUpdated' => $dateUpdated ], 'id in (' . implode(',', $ids) . ')', $params = [], $includeAuditColumns = false);
                              ;

        var_dump($command);
    }

    public function actionCustomers ($amount = 20, $from = '')
    {
        $bar = new CliProgressBar($amount, 0);
        //$order->create();

        echo "(Faker) Creating $amount customers, wohoo!\n";
        $bar->display();
        for ($i = 1; $i <= $amount; $i++) {
            //echo $i . "\n";
            $bar->progress();
            $bar->setProgressTo($i);
            craft()->faker_commerce->createCustomer();
        }

        $bar->setColorToGreen();
        $bar->end();

    }

    public function actionProducts ($amount = 20, $from = '')
    {
        $bar = new CliProgressBar($amount, 0);
        //$order->create();

        echo "(Faker) Creating $amount products, wohoo!\n";
        $bar->display();
        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;

        craft()->faker_commerce->createProduct($this->locale);

        $transaction->commit();
        for ($i = 1; $i <= $amount; $i++) {
            //echo $i . "\n";
            $bar->progress();
            $bar->setProgressTo($i);
            //craft()->faker_commerce->createCustomer();
        }

        $bar->setColorToGreen();
        $bar->end();

    }
}