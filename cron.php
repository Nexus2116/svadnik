<?php

require(__DIR__ . '/vendor/autoload.php');

$jobby = new \Jobby\Jobby();

$jobby->add('SendMail', array(
    'command' => 'php commands/proTimeOut.php',
    'schedule' => '55 23 */1 * *',
    'output' => 'cron/logs/command.log',
    'enabled' => true,
    'debug' => true,
));

$jobby->run();