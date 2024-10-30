<?php

namespace Requiem\LogMiddleware\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RequestApiLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data = [];
    public $type = '';


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( string $channel, $data,string $level)
    {
        $this->channel  = $channel;
        $this->data     = $data;
        $this->level    = $level;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        switch($level)
        {
            case "fatal":
                Log::channel($channel)->emergency($message);
                break;
            case "error":
                Log::channel($channel)->error($message);
                break;
            case "warn":
                Log::channel($channel)->warn($message);
                break;
            case "info":
                Log::channel($channel)->info($message);
                break;
            default :
                Log::channel($channel)->debug($message);
                break;
        }

    }
}
