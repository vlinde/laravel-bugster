<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;
use Vlinde\Bugster\Models\AdvancedBugsterStat;

class GenerateStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:generate:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate stats for logs';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        set_time_limit(0);

        $this->groupBugs();
    }

    public function groupBugs(): void
    {
        AdvancedBugsterDB::select('id', 'message', 'category', 'file')
            ->chunkById(1000, function ($logs) {
                foreach ($logs as $log) {
                    if (AdvancedBugsterStat::where('error', $log->message)->exists()) {
                        continue;
                    }

                    AdvancedBugsterStat::create([
                        'error' => $log->message,
                        'category' => $log->category,
                        'file' => $log->file,
                        'generated_at' => now()
                    ]);
                }
            });

        AdvancedBugsterStat::select('id', 'error', 'daily', 'weekly', 'monthly')
            ->chunkById(1000, function ($stats) {
                foreach ($stats as $stat) {
                    $dailyCount = AdvancedBugsterDB::whereDate('created_at', now()->subDay())
                        ->where('message', $stat->error)
                        ->count();

                    $weeklyCount = AdvancedBugsterDB::whereDate('created_at', '>=', now()->subWeek())
                        ->whereDate('created_at', '<=', now()->subDay())
                        ->where('message', $stat->error)
                        ->count();

                    $monthlyLogsId = AdvancedBugsterDB::select('id')
                        ->whereDate('created_at', '>=', now()->subMonth())
                        ->whereDate('created_at', '<=', now()->subDay())
                        ->where('message', $stat->error)
                        ->pluck('id')
                        ->toArray();

                    $monthlyCount = count($monthlyLogsId);

                    $stat->daily = $dailyCount;
                    $stat->weekly = $weeklyCount;
                    $stat->monthly = $monthlyCount;

                    $stat->save();

                    $stat->bugs()->sync($monthlyLogsId);
                }
            });
    }
}
