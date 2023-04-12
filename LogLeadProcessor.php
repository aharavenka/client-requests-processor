<?php

class LogLeadProcessor implements LeadProcessorInterface
{
    private string $logFilePath;

    public function __construct(string $logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    public function process(LeadGenerator\Lead $lead): void
    {
        //Skipping unsupported category for the Lead
        if (!in_array($lead->categoryName, self::unsupportedCategories)) {
            sleep(2); //process imitation
            $logEntry = sprintf(
                "%d | %s | %s\n",
                $lead->id,
                $lead->categoryName,
                (new DateTime())->format('Y-m-d H:i:s')
            );

            $this->writeWithLock($this->logFilePath, $logEntry);
        }
    }

    private function writeWithLock(string $filePath, string $data): void
    {
        $file = fopen($filePath, 'a');

        if ($file === false) {
            throw new RuntimeException("Failed to open file: {$filePath}");
        }

        while (true) {
            //solve problem with write problem
            if (flock($file, LOCK_EX)) {
                fwrite($file, $data);
                fflush($file);
                flock($file, LOCK_UN);
                break;
            } else {
                usleep(10000);
            }
        }

        fclose($file);
    }
}
