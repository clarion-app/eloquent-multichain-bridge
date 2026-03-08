<?php

namespace ClarionApp\EloquentMultiChainBridge\Commands;

use Illuminate\Console\Command;
use ClarionApp\MultiChain\Facades\MultiChain;
use Log;

class HandleBlockNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datastream:handle-block-notification {hash}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handles block notification from MultiChain';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hash = $this->argument('hash');
        $block = MultiChain::getBlock($hash);
        
        if(!$block)
        {
            Log::info('No block found for hash: '.$hash);
            return;
        }

        $this->call('datastream:new-block', ['data' => $block]);
        Log::info('Processed block: '.$hash);
    }
}