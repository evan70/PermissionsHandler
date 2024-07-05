<?php

declare(strict_types=1);

namespace Umpirsky\PermissionsHandler;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class PermissionsSetter implements PermissionsSetterInterface
{
    /**
     * @return string
     */
    protected function getHttpdUser(): string
    {
        return $this->runProcess(
            "ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1"
        );
    }

    /**
     * @param string $command
     * @param string $path
     * @return void
     */
    protected function runCommand(string $command, string $path)
    {
        $this->runProcess(str_replace(
            ['%httpduser%', '%path%'],
            [$this->getHttpdUser(), $path],
            $command
        ));
    }

    /**
     * @param string $commandline
     * @return string
     */
    protected function runProcess(string $commandline): string
    {
        $process = Process::fromShellCommandline($commandline);
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }
}
