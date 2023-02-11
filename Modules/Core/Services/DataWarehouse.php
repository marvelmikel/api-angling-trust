<?php

namespace Modules\Core\Services;

class DataWarehouse
{
    const URL = 'https://www.managemypreferences.co.uk/api/anglingtrust/2a5ae61f-3b9d-422d-973e-110dd09fdd5b-ee076f7e-1e43-4c44-b210-8baa87780120.aspx';

    public static function postData($type, $batch, $data)
    {
        try {

            self::logInfo("Sending: {$type} batch {$batch}");

            $options = array(
                'http' => array(
                    'method'  => 'POST',
                    'content' => json_encode($data),
                    'header'=>  "Content-Type: application/json\r\n" . "Accept: application/json\r\n"
                )
            );

            $url = self::URL . "?type={$type}&batch={$batch}";

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            self::logInfo($result);

            self::logInfo("Completed: {$type} batch {$batch}");

        } catch (\Exception $exception) {
            self::logError($exception->getMessage());
        }
    }

    public static function logInfo($message)
    {
        \Log::channel('warehouse')
            ->info($message);
    }

    public static function logError($message)
    {
        \Log::channel('warehouse')
            ->error($message);
    }
}
