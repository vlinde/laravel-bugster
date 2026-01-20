<?php

namespace Vlinde\Bugster\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vlinde\Bugster\Models\LaravelBugsterWebhook;

class SendWebhookNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        $webhooks = LaravelBugsterWebhook::where('active', true)->get();

        foreach ($webhooks as $webhook) {
            $this->sendToWebhook($webhook);
        }
    }

    private function sendToWebhook(LaravelBugsterWebhook $webhook): void
    {
        try {
            $response = Http::timeout(10)
                ->asJson()
                ->post($webhook->url, $this->replaceVariables($webhook->payload));

            if (! $response->successful()) {
                Log::warning("Buster webhook failed: $webhook->url", [
                    'status' => $response->status(),
                    'hook_id' => $webhook->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Buster webhook error: $webhook->url", [
                'error' => $e->getMessage(),
                'hook_id' => $webhook->id,
            ]);
        }
    }

    private function replaceVariables(array $payload): array
    {
        $json = json_encode($payload);

        foreach ($this->data as $key => $value) {
            if (is_scalar($value)) {
                $json = str_replace('{{'.$key.'}}', $value, $json);
            }
        }

        return json_decode($json, true);
    }
}
