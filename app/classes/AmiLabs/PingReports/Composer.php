<?php

namespace AmiLabs\PingReports;

use Composer\Script\Event;

/**
 * Composer event handlers.
 *
 * @package AmiLabs\SigningService
 */
class Composer{
    /**
     * @param  Event $oEvent
     * @return void
     */
    public static function postInstall(Event $oEvent){
        self::postUpdate($oEvent);
    }

    /**
     * @param  Event $oEvent
     * @return void
     */
    public static function postUpdate(Event $oEvent){
        @chmod('./db', 0777);
        @chmod('./db/ping_reports.db', 0777);

        // Update web folders access mode
        @chmod('./web/css', 0777);
        @chmod('./web/js', 0777);
        @chmod('./web/js/modules', 0777);
        @chmod('./web/img', 0777);
    }
}
