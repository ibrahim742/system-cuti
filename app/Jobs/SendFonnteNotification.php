<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendFonnteNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $target,
        public string $message
    ) {}

    public function handle(): void
    {
        // app/Jobs/SendFonnteNotification.php
        Http::withHeaders([
            'Authorization' => \App\Models\Setting::get('fonnte_token') ?? config('services.fonnte.token'),
        ])->post('https://api.fonnte.com/send', [
            'target'  => $this->target,
            'message' => $this->message,
        ]);
    }
}
