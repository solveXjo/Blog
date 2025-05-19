<?php

namespace App\Queue;

class Queue
{
    private $queueDir;

    public function __construct(?string $queueDir = null)
    {
        $this->queueDir = $queueDir ??  'storage/queue';

        if (!is_dir($this->queueDir)) {
            mkdir($this->queueDir, 0755, true);
        }
    }

    public function push(array $data): bool
    {
        $jobId = uniqid('job_', true);
        $filename = $this->queueDir . '/' . $jobId . '.job';
        $serialized = serialize($data);

        return file_put_contents($filename, $serialized) !== false;
    }

    public function getNextJob(): ?array
    {
        $files = glob($this->queueDir . '/*.job');

        if (empty($files)) {
            return null;
        }

        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        $jobFile = $files[0];
        $jobId = basename($jobFile, '.job');
        $jobData = unserialize(file_get_contents($jobFile));

        return [
            'id' => $jobId,
            'file' => $jobFile,
            'data' => $jobData
        ];
    }

    public function deleteJob(string $jobFile): bool
    {
        if (file_exists($jobFile)) {
            return unlink($jobFile);
        }

        return false;
    }
}