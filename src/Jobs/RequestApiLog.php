<?php

namespace Requiem\LogMiddleware\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RequestApiLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $channel = '';
    public $data = [];
    public $level = '';


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

        switch($this->level)
        {
            case "fatal":
                Log::channel($this->channel)->emergency($this->data);
                break;
            case "error":
                Log::channel($this->channel)->error($this->data);
                break;
            case "warn":
                Log::channel($this->channel)->warn($this->data);
                break;
            case "info":
                Log::channel($this->channel)->info($this->data);
                break;
            default :
                Log::channel($this->channel)->debug($this->data);
                break;
        }

    }
}
