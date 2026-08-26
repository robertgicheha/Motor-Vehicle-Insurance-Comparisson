<?php

namespace App\Services;

use RuntimeException;

class ClamAVService
{
    protected string $host;
    protected int $port;

    public function __construct()
    {
        $this->host = config('services.clamav.host', '127.0.0.1');
        $this->port = (int) config('services.clamav.port', 3310);
    }

    public function scan(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        try {
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 5);
            if (!$socket) {
                return true;
            }

            stream_set_blocking($socket, true);

            $command = 'nINSTREAM\n';
            fwrite($socket, $command);

            $filename = basename($filePath);
            $fileSize = filesize($filePath);
            $packet = pack('N', strlen($filename)) . $filename . pack('N', 0);
            $packet .= pack('N', $fileSize);
            $packet .= file_get_contents($filePath);
            $packet .= pack('N', 0);

            fwrite($socket, $packet);

            $response = '';
            while (!feof($socket)) {
                $response .= fgets($socket, 1024);
            }

            fclose($socket);

            return str_contains($response, 'OK');
        } catch (RuntimeException $e) {
            return true;
        } catch (\Throwable $e) {
            return true;
        }
    }
}
