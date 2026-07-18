<?php

declare(strict_types=1);

namespace Bee\Core\Foundation;

enum ExecutionMode: string
{
    case Http = 'http';
    case Cli = 'cli';
    case Cron = 'cron';
    case Test = 'test';
    case Updater = 'updater';
}
