<?php

namespace Modules\Backend\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\Frontend\Http\Controllers\FrontendController;
class UpdateCurrencies extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'update_currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new FrontendController)->storeCurrencies();
    }
}
