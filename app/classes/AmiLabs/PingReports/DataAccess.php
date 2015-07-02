<?php

namespace AmiLabs\PingReports;

use AmiLabs\DevKit\LayerFactory;

/**
 * Factory retuning data access layer.
 */
class DataAccess extends LayerFactory{
    /**
     * @var string
     */
    protected static $namespace = 'AmiLabs\\PingReports';

    /**
     * @var string
     */
    protected static $class = 'DataAccess';
}
