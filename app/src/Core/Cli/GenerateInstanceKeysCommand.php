<?php

declare(strict_types=1);

namespace Bee\Core\Cli;

use Bee\Core\Exception\CliException;
use Bee\Core\Foundation\ApplicationRoot;
use Bee\Core\Security\InstanceKeyManager;

final readonly class GenerateInstanceKeysCommand implements Command
{
    public function __construct(
        private ApplicationRoot $root,
        private InstanceKeyManager $keys
    ) {
    }

    public function name(): string
    {
        return 'security:keys';
    }

    public function description(): string
    {
        return 'Generate instance salts and API keys (--force rotates existing values).';
    }

    public function execute(array $arguments): int
    {
        $force = false;
        foreach ($arguments as $argument) {
            if ($argument !== '--force') {
                throw new CliException(sprintf('Unknown option for security:keys: %s.', $argument));
            }
            $force = true;
        }

        $result = $this->keys->generate($this->root, $force);
        echo $result->environmentCreated
            ? 'Created app/config/.env from the example template.' . PHP_EOL
            : '';
        echo sprintf('Generated %d instance secrets securely.', count($result->updatedKeys)), PHP_EOL;
        echo 'Secret values were not printed.', PHP_EOL;

        if ($result->backupPath !== null) {
            echo sprintf('Backup created at %s.', $result->backupPath), PHP_EOL;
            echo 'Rotation may invalidate passwords, sessions, nonces, and API clients.', PHP_EOL;
        }

        return 0;
    }
}
