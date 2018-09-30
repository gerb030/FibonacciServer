<?php

class Controller_Api_Package extends Controller_Abstract
{
    /**
     * @param Request $request
     * @param Config $config
     * @param HttpClient $httpClient
     */
    public function __construct(Request $request, Config $config, HttpClient $httpClient)
    {
        $this->setRequest($request);
        $this->setConfig($config);
        $this->setHttpClient($httpClient);
    }

    public function handleRequest()
    {
        header('Content-type: application/json; charset=utf-8');

        try {
            $pdo = DbFactory::build();
            $options = $this->_config->getOptions();

            $siteMapper = new Mapper_Site($pdo);
            $sitegameMapper = new Mapper_SiteGame($pdo);

            $packagesService = new Model_Service_Packages(
                $this->getHttpClient(),
                $options->services->packages->url
            );

            $request = $this->getRequest();

            $gameId = $request->getProperty('gameId');
            $siteId = $request->getProperty('siteId');
            if ($request->getProperty('countryCode')) {
                $countryCode = $request->getProperty('countryCode');
            } else {
                $countryCode = 'NL'; // TODO: Shouldn't need to hardcode a default code.
            }
            if ($request->getProperty('ip')) {
                $countryCode = $request->getCountryByIP($request->getProperty('ip')) ?: $countryCode;
            }
            $languageCode = $request->getProperty('languageCode');

            if ($request->getProperty('limit')) {
                $limit = (int) $request->getProperty('limit');
            } else {
                $limit = 5;
            }

            Logger::info(sprintf('gameId = %d, siteId = %d, countryCode = %s, languageCode = %s',
                            $gameId, $siteId, $countryCode, $languageCode));

            $siteGame = $sitegameMapper->find(array('gameId' => $gameId, 'siteId' => $siteId));

            $username = $request->getProperty('userId');

            $shop = new Model_Shop(
                $gameId,
                $siteId,
                $countryCode,
                $languageCode,
                $packagesService,
                null,
                $username
            );

            $packages = array();
            $promotionsJson = array();

            // get skus & offers
            $skutypes = $shop->getSkutypes();

            foreach ($skutypes as $skutype) {
                $offersList = $skutype->packages;
                if (!count($offersList)) {
                    continue;
                }

                $skutypeData = clone $skutype;
                unset($skutypeData->packages);

                usort($offersList, function ($a, $b) {
                    $a = $a->packageOffer->amount;
                    $b = $b->packageOffer->amount;
                    if ($a === $b) {
                        return 0;
                    }
                    return $a < $b ? -1 : 1 ;
                });

                $adjustedLimit = (count($offersList) < $limit) ? count($offersList) : $limit;

                //foreach ($offersList as $offer) {
                if ($adjustedLimit > 0) {
                    for ($a = 0; $a < $adjustedLimit; $a++) {
                        $offer = $offersList[$a];
                        if ($offer->packageOffer->isEnabled) {
                            $package = array();
                            $package['skuUnit'] = (int)$offer->skuUnit;
                            if (isset($offer->orgSkuUnit)) {
                                $package['orgSkuUnit'] = (int)$offer->orgSkuUnit;
                            }
                            $package['skuTypeId'] = (int)$offer->skuTypeId;
                            $offer = $offer->packageOffer;
                            $package['imageLink'] = $offer->imageLink;
                            $package['price'] = $offer->localizedPrice;
                            $package['preselectionOrder'] = (int)$offer->preselectionOrder;
                            if (isset($offer->orgLocalizedPrice)) {
                                $package['orgLocalizedPrice'] = $offer->orgLocalizedPrice;
                            }
                            if (isset($offer->spilcoinPrice)) {
                                $package['spilcoinPrice'] = $offer->spilcoinPrice;
                            }
                            if ($offer->isBestValue == 1) {
                                $package['bestValue'] = 1;
                            }

                            if (!empty($offer->promotions)) {
                                foreach ($offer->promotions as $promotion) {

                                    switch ($promotion->type) {
                                        case 'TIMED':
                                            if (floatval($promotion->multiplier) >= 1.0) {
                                                $multiplierType = 'skuMultiplier';
                                            }
                                            else {
                                                $multiplierType = 'priceMulitplier';
                                            }
                                            $promotionsJson[$offer->id][] = array(
                                                'addedValue' => array(
                                                    $multiplierType => floatval($promotion->multiplier)
                                                )
                                            );
                                            break;

                                        case 'EXTRAS':
                                            $promotionsJson[$offer->id][] = array(
                                                'extraValue' => array(
                                                    'text' => $promotion->translatedText ?: $promotion->defaultText
                                                )
                                            );
                                            break;
                                    }
                                }
                            }

                            $offerId = $offer->id;

                            $packages[$offerId] = $package;
                        }
                    }
                }
            }

            $json = json_encode(array('packages' => $packages, 'promotions' => $promotionsJson));

            print $json;
        } catch (Exception $e) {
            Logger::error($e);
            // render final page
            echo json_encode(array("error" => $e));
        }

        exit;
    }

    public function getPaymentMethods()
    {
        // get all the payment methods
        $paymentMethodsList = $shop->getPaymentMethods();
        foreach ($paymentMethodsList as $paymentMethod) {
            /**
             * we skip Spilcoins payment method if we are in the
             * pseudo game Spilcoins
             */
            if (! (($paymentMethod->paymentMethodId == $options->providers->spilcoin->paymentMethodId
                            && ($gameId == $options->providers->spilcoin->gameId))
            )) {
                /**
                 * @var Domain_PaymentMethod $paymentMethodDomain
                 */
                $template->set('minAmount', '')->set('maxAmount', '');
                $paymentMethodDomain = $paymentMethodMapper->find(array('id' => $paymentMethod->paymentMethodId));

                $localecode_tokens = explode(',', $request->getLocaleCode());
                $countryCode = reset($localecode_tokens);
                $minMaxAmount = $shop->getMinMaxForPaymentMethod(
                                $pdo,
                                $paymentMethodDomain->getId(),
                                $request->getCountryByIP(),
                                $countryCode
                );

                $tariffSteps = '';
                if (isset($minMaxAmount['id'])) {
                    $tariffSteps = $shop->getStepsForTariffplan($pdo, $minMaxAmount['id']);
                    $tariffSteps = $shop->getTariffplanStepsAsCommaSeparatedString($tariffSteps, $minMaxAmount);
                }
                $template->set('tariffSteps', $tariffSteps);

                if (isset($minMaxAmount['min_amount']) && $minMaxAmount['min_amount'] > 0) {
                    $paymentMethod->minAmount = $minMaxAmount['min_amount'];
                }

                if (isset($minMaxAmount['max_amount']) && $minMaxAmount['max_amount'] > 0) {
                    $paymentMethod->maxAmount = $minMaxAmount['max_amount'];
                }

                $template->set('uiHeight', $paymentMethodDomain->getUiHeight());
                $template->set('uiWidth', $paymentMethodDomain->getUiWidth());
                $template->set('uiType', $paymentMethodDomain->getUiType());
                $template->assignRecursive($paymentMethod);
                $paymentMethodTemplates[] = (string)$template->render('paymentmethod.phtml');
            }
        }
    }
}
