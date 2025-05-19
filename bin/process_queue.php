
<?php
// bin/process_queue.php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Queue\EmailJob;
use App\Queue\Queue;


$queue = new Queue();
$emailJob = new EmailJob();

echo "Starting queue processor...\n";

$processedCount = 0;
$maxJobs = 10;

while ($processedCount < $maxJobs) {
    $job = $queue->getNextJob();

    if (empty($job)) {
        echo "No jobs in queue.\n";
        break;
    }

    echo "Processing job: {$job['id']}\n";

    try {
        $result = $emailJob->process($job['data']);

        if ($result) {
            echo "Job processed successfully.\n";
        } else {
            echo "Job processing failed.\n";
        }

        $queue->deleteJob($job['file']);
        $processedCount++;

    } catch (\Exception $e) {
        echo "Error processing job: " . $e->getMessage() . "\n";
    }
}

echo "Queue processor finished. Processed $processedCount jobs.\n";