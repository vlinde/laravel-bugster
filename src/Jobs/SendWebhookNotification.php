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

    private const DEFAULT_WEBHOOK_TYPE = 'general';

    private array $data;

    private ?int $webhookId;

    private ?string $webhookType;

    private bool $activeOnly;

    public function __construct(array $data, ?int $webhookId = null, ?string $webhookType = null, bool $activeOnly = true)
    {
        $this->data = $data;
        $this->webhookId = $webhookId;
        $this->webhookType = $webhookType;
        $this->activeOnly = $activeOnly;
    }

    public function handle(): void
    {
        $webhooks = LaravelBugsterWebhook::query()
            ->when($this->activeOnly, function ($query) {
                $query->where('active', true);
            })
            ->when($this->webhookId, function ($query) {
                $query->where('id', $this->webhookId);
            })
            ->when(! $this->webhookId && $this->webhookType, function ($query) {
                $query->where('type', $this->webhookType);
            })
            ->when(! $this->webhookId && ! $this->webhookType, function ($query) {
                $query->where('type', self::DEFAULT_WEBHOOK_TYPE);
            })
            ->get();

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
        $json = is_array($payload) ? json_encode($payload) : $payload;

        foreach ($this->data as $key => $value) {
            if (is_scalar($value)) {
                $escapedValue = json_encode((string) $value, JSON_UNESCAPED_SLASHES);
                $escapedValue = trim($escapedValue, '"');
                $json = str_replace('{{'.$key.'}}', $escapedValue, $json);
            }
        }

        return json_decode($json, true);
    }
}
