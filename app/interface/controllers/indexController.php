<?php

use AmiLabs\DevKit\Controller;

/**
 * Index controller.
 */
class indexController extends Controller
{
    /**
     * Index action.
     */
    public function actionIndex()
    {
        $services = array_keys($this->getConfig()->get('services'));
        $this->oView->set(
            'services',
            $services,
            TRUE
        );

        $service = $this->getRequest()->get('service', FALSE);
        if (
            FALSE === $service ||
            !in_array($service, $services)
        ) {
            $service = reset($services);
        }
        $this->oView->set(
            'service',
            $service,
            TRUE
        );
        $this->oView->set(
            'service',
            $service
        );
    }
}