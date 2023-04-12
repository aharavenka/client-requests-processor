<?php

use Spatie\Async\Pool;
use LeadGenerator\Generator;

require_once 'vendor/autoload.php';
require_once 'LeadProcessorInterface.php';
require_once 'LogLeadProcessor.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env');
$dotenv->load();

$logFilePath = $_ENV['logFilePath'];

$generator = new Generator();

$pool = Pool::create()->concurrency($_ENV['concurrencyLimit']);

$startTime = microtime(true);

$generator->generateLeads($_ENV['operations'], function (LeadGenerator\Lead $generatorLead) use (
    $pool,
    $logFilePath,
    $startTime
) {
    $pool->add(function () use ($generatorLead, $logFilePath) {
        $leadProcessor = new LogLeadProcessor($logFilePath);
        $leadProcessor->process($generatorLead);

        //if you want to return something to console you can un-commit next lines
//        return 'Processed: ' . $generatorLead->id;
//    })->then(function ($output) {
//        echo $output, "\n";
    });

    if ((microtime(true) - $startTime) > 600) {
        echo 'Time limit exceeded', "\n";
        return false;
    }

    return true;
});

$pool->wait();
