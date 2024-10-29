<?php

namespace Loki\Logging\Jobs;

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
    public function __construct( string $type,array $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // if($this->type == 'errorLog' && env("ISSENT")) {
            // _errorLog('上传组件_error', ['line' => $this->data['error']['line'], 'file' => $this->data['error']['file'], 'error' => $this->data['error']['error']], $this->data['data'], 1);
        // };

        if($this->type == 'project_request_log' && env("PROJECT_REQUEST_LOG"))  \Log::channel('project_request_log')->Info($this->data);
        if($this->type == 'system_log' && env("SYSTEM_LOG"))  \Log::channel('system_log')->Info($this->data);
        if($this->type == 'vendor_request_log' && env("VENDOR_REQUEST_LOG"))  \Log::channel('vendor_request_log')->Info($this->data);
        if($this->type == 'vendor_response_log' && env("VENDOR_RESPONSE_LOG"))  \Log::channel('vendor_response_log')->Info($this->data);

    }
}
