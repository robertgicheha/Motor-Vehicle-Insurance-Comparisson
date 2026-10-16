<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

/**
 * Runs the ESET Server Security for Linux command-line scanner (odscan) against
 * an uploaded file. This replaces the old ClamAV socket scan.
 *
 * The scanner is expected on the same DigitalOcean host as the portal, so an
 * upload is scanned locally as it is received, before it is accepted and
 * attached to the underwriter email.
 */
class EsetScanService
{
    public const STATUS_CLEAN = 'clean';
    public const STATUS_INFECTED = 'infected';
    public const STATUS_ERROR = 'error';
    public const STATUS_UNAVAILABLE = 'unavailable';

    protected string $binary;
    protected int $timeout;

    public function __construct()
    {
        $this->binary = (string) config('services.eset.binary', '/opt/eset/efs/bin/odscan');
        $this->timeout = (int) config('services.eset.timeout', 60);
    }

    public function enabled(): bool
    {
        return (bool) config('services.eset.enabled', true);
    }

    /**
     * Scan a single file path.
     *
     * @return array{status:string, result:string, output:string}
     */
    public function scan(string $filePath): array
    {
        if (! $this->enabled()) {
            return $this->result(self::STATUS_CLEAN, 'ESET scanning disabled — file accepted without scan.');
        }

        if (! is_file($filePath) || ! is_readable($filePath)) {
            return $this->result(self::STATUS_ERROR, 'File not found or not readable for scanning.');
        }

        // Preflight: if the scanner isn't installed (e.g. a local dev box
        // without ESET Server Security), don't run anything — a missing
        // binary would otherwise surface as a non-zero exit and look like a
        // threat, blocking every upload.
        if (! is_file($this->binary) || ! is_executable($this->binary)) {
            Log::warning("ESET scanner binary not found or not executable: {$this->binary}");

            return $this->result(self::STATUS_UNAVAILABLE, 'ESET scanner unavailable — passed intake checks only.');
        }

        $process = new Process([
            $this->binary,
            '--scan-path=' . $filePath,
            '--clean-mode=none', // leave the file alone so Laravel decides its fate
        ]);
        $process->setTimeout($this->timeout);

        try {
            $process->run();
        } catch (ProcessTimedOutException $e) {
            Log::warning('ESET scan timed out: ' . $e->getMessage());

            return $this->result(self::STATUS_UNAVAILABLE, 'ESET scan timed out — passed intake checks only.');
        } catch (\Throwable $e) {
            // Binary missing / not executable — expected on local dev boxes
            // that do not run ESET Server Security.
            Log::warning('ESET scanner unavailable: ' . $e->getMessage());

            return $this->result(self::STATUS_UNAVAILABLE, 'ESET scanner unavailable — passed intake checks only.');
        }

        $output = trim($process->getOutput() . "\n" . $process->getErrorOutput());
        $exitCode = $process->getExitCode();

        // Command-not-found wording means the scanner never actually ran.
        if ($exitCode === 127 || preg_match('/(not recognized|command not found|no such file)/i', $output)) {
            Log::warning("ESET scanner could not be started (exit {$exitCode}): {$output}");

            return $this->result(self::STATUS_UNAVAILABLE, 'ESET scanner unavailable — passed intake checks only.');
        }

        // odscan returns 0 when the file is clean, 1 when a threat is found.
        if ($exitCode === 0) {
            return $this->result(self::STATUS_CLEAN, 'ESET scan clean.', $output);
        }

        if ($exitCode === 1) {
            return $this->result(self::STATUS_INFECTED, $this->threatLine($output) ?? 'ESET detected a potential threat.');
        }

        Log::warning("ESET scanner returned exit code {$exitCode}: {$output}");

        return $this->result(self::STATUS_ERROR, 'ESET scan could not complete — passed intake checks only.');
    }

    public function isClean(string $filePath): bool
    {
        return $this->scan($filePath)['status'] === self::STATUS_CLEAN;
    }

    /**
     * @return array{status:string, result:string, output:string}
     */
    protected function result(string $status, string $result, string $output = ''): array
    {
        return ['status' => $status, 'result' => $result, 'output' => $output];
    }

    /**
     * Pull the most descriptive threat line out of the scanner output, if any.
     */
    protected function threatLine(string $output): ?string
    {
        foreach (preg_split('/\r?\n/', $output) as $line) {
            $line = trim($line);

            if ($line !== '' && stripos($line, 'detected') !== false) {
                return $line;
            }
        }

        return null;
    }
}
