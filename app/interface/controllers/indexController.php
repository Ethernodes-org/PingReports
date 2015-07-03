<?php

use AmiLabs\DevKit\Controller;
use AmiLabs\CryptoKit\BlockchainIO;

/**
 * Index controller.
 */
class indexController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // $testnet = (bool)$this->getRequest()->get('testnet');
        // $this->getConfig()->set('CryptoKit/testnet', $testnet);
    }

    /**
     * Index action.
     */
    public function actionIndex()
    {
        $this->oView->set(
            'services',
            array_keys($this->getConfig()->get('services')),
            TRUE
        );
    }
}