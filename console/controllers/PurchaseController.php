<?php

namespace console\controllers;

use common\models\PurchaseList;
use common\models\Settings;
use \DateTimeZone;
use \DateTime;
use Yii;
use yii\console\Controller;

class PurchaseController extends Controller
{
    public function actionIndex()
    {
        print "running purchase parser...\n";
        Yii::warning("running purchase parser...\n");
        $last_run = Settings::get('last_parser_run');
        $current_run = time();
        $datetime = date('d.m.Y%20H:i:s', $last_run);
        $itemLimit = 70;
        $ch = curl_init();

        $endpointUrl = 'https://old.zakupki.mos.ru/api/Cssp/Purchase/Query?';
        $endpointUrl .= 'queryDto={"filter":{"publishDateGreatEqual":"';
        $endpointUrl .= $datetime; // current datetime
        $endpointUrl .= '","auctionSpecificFilter":{"stateIdIn":[19000002]},"needSpecificFilter":{"stateIdIn":[20000002]},';
        $endpointUrl .= '"tenderSpecificFilter":{"stateIdIn":[5]}},"order":[{"field":"PublishDate","desc":true}],"withCount":true,"take":';
        $endpointUrl .= $itemLimit; //limit
        $endpointUrl .= ',"skip":0}';

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $endpointUrl);
        $response = curl_exec($ch);
        if (curl_error($ch)) {
            Yii::error(curl_error($ch));
        } else {
            $data = json_decode($response, true);
            $excludeRegions = [
                "г Москва",
                "обл Московская",
                "г Санкт-Петербург",
                "обл Ленинградская",
            ];
            $newPurchases = [];
            $timezone=new DateTimeZone('Europe/Moscow');
            foreach ($data['items'] as $item) {
                $exists = PurchaseList::find()->where(['purchase_id' => $item['id']])->exists();
                $beginDate = explode(' ', $item['beginDate'])[0];
                $endDate = explode(' ', $item['endDate'])[0];
                $isOneDay=$beginDate==$endDate;
                $isNotInRegions=!in_array($item['regionName'], $excludeRegions);
                if($exists)
                    break;
                if ($item['needId'] and $isOneDay and $isNotInRegions) {
                    print "{$beginDate} - {$endDate}\n";
                    $detailUrl = self::getDetailUrl($item);
                    curl_setopt($ch, CURLOPT_URL, $detailUrl);
                    $itemDetails = curl_exec($ch);

                    $purchase = new PurchaseList();
                    $purchase->auction_id = $item['auctionId'];
                    $purchase->need_id = $item['needId'];
                    $purchase->tender_id = $item['tenderId'];
                    $purchase->trade_type = $item['tradeType'];
                    $purchase->number = $item['number'];
                    $purchase->name = $item['name'];
                    $purchase->customers = json_encode($item['customers']);
                    $purchase->purchase_creator_name = $item['purchaseCreator']['name'];
                    $purchase->purchase_creator_inn = $item['purchaseCreator']['inn'];
                    $purchase->purchase_creator_supplier_id = $item['purchaseCreator']['supplierId'];
                    $purchase->purchase_creator_customer_id = $item['purchaseCreator']['customerId'];
                    $purchase->purchase_creator_id = $item['purchaseCreator']['id'];
                    $purchase->has_publisher = $item['hasPublisher'];
                    $purchase->state_name = $item['stateName'];
                    $purchase->state_id = $item['stateId'];
                    $purchase->start_price = $item['startPrice'];
                    $purchase->region_name = $item['regionName'];
                    $purchase->offer_count = $item['offerCount'];
                    $purchase->auction_current_price = $item['auctionCurrentPrice'];
                    $purchase->auction_next_price = $item['auctionNextPrice'];
                    $purchase->begin_date = (new DateTime($item['beginDate'], $timezone))->format('U');
                    $purchase->end_date = (new DateTime($item['endDate'], $timezone))->format('U');
                    $purchase->federal_law_name = $item['federalLawName'];
                    $purchase->region_path = $item['regionPath'];
                    $purchase->is_external_integration = $item['isExternalIntegration'];
                    $purchase->purchase_id = $item['id'];
                    if (!curl_error($ch)) {
                        $itemDetailsArray = json_decode($itemDetails, true);
                        if (array_key_exists('deliveryPlace', $itemDetailsArray))
                            $purchase->delivery_place = $itemDetailsArray['deliveryPlace'];
                    }
                    $purchase->is_notified = false;
                    $purchase->save();
                    $newPurchases[] = $purchase;
                }
            }
        }
        curl_close($ch);
        $email = Settings::get('notification_email', null);
        if ($email and !empty($newPurchases)) {
            $sent = Yii::$app->mailer->compose('parser', ['newPurchases' => $newPurchases])
                ->setFrom(Yii::$app->params['smtpEmail'])
                ->setTo($email)
                ->setSubject('Новые закупки')
                ->send();
            if ($sent) {
                array_walk($newPurchases, function ($purchase) {
                    $purchase->is_notified = true;
                    $purchase->save();
                });
            }
        }
        Settings::set('last_parser_run', $current_run);
        print count($newPurchases) . " new purchases\n";
        Yii::warning(count($newPurchases) . " new purchases\n");
    }

    private static function getDetailUrl($item)
    {
        $itemType = $item['auctionId'] ? 'auction' : ($item['needId'] ? 'need' : 'tender');
        switch ($itemType) {
            case 'auction':
                $detailUrl = 'https://zakupki.mos.ru/newapi/api/Auction/Get?auctionId=' . $item['auctionId'];
                break;
            case 'need':
                $detailUrl = 'https://old.zakupki.mos.ru/api/Cssp/Need/GetEntity?id=' . $item['needId'];
                break;
            case 'tender':
                $detailUrl = 'https://old.zakupki.mos.ru/api/Cssp/Tender/GetEntity?id=' . $item['tenderId'];
        }
        return $detailUrl;
    }
}