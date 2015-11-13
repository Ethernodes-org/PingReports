<?php

header('Content-Type: text/plain');

readfile(dirname(__FILE__) . '/../bin/daemon.ping.log');

