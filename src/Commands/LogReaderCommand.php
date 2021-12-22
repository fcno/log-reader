<?php

namespace Fcno\LogReader\Commands;

use Illuminate\Console\Command;

class LogReaderCommand extends Command
{
    public $signature = 'log-reader';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
