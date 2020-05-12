<?php
namespace App\Console\Commands;

use App\Http\Controllers\channels\RedisChannels;
use App\Http\Controllers\handlers\ChecksHandler;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CheckHandler extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'CheckHandler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle check';

    /**
     * Execute the console command.
     *
     * @return
     */
    public function handle(): void
    {
        (new ChecksHandler(RedisChannels::CHECKS_LIST))->handle();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on.', 'localhost'),

            array('port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on.', 8000),
        );
    }

}
