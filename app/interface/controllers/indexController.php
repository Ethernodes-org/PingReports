<?php

use AmiLabs\DevKit\Controller;

/**
 * Index controller.
 */
class indexController extends Controller
{
    /**
     * Index action.
     *
     * @return void
     */
    public function actionIndex()
    {
        require_once $this->getConfig()->get('path/root') . '/lib/http_build_url.php';

        $services = array(); ;
        foreach ($this->getConfig()->get('services') as $service => $svc) {
            $url = $svc['url'];
            $parsed = parse_url($url);
            if (isset($parsed['user'])) {
                $parsed['user'] = substr($parsed['user'], 0, 2) . '*';
            }
            if (isset($parsed['pass'])) {
                $parsed['pass'] = '*';
            }
            $url = http_build_url($parsed);
            unset($parsed['user'], $parsed['pass']);
            $cleanURL = http_build_url($parsed);
            $services[$service] = array(
                'url'      => $url,
                'cleanURL' => $cleanURL,
            );
        }
        $this->oView->set(
            'services',
            $services
        );
        $this->oView->set(
            'services',
            array_keys($services),
            TRUE
        );
        $this->oView->set(
            'externalLink',
            $this->getConfig()->get('externalLink'),
            TRUE
        );
    }
}
