<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;
use Vlinde\Bugster\Models\AdvancedBugsterStat;

class UpdateBugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->updateErrors();
        $this->updateStats();
    }

    public function updateErrors(): void
    {
        AdvancedBugsterDB::where([
            'created_at', '<', Carbon::now(),
            'created_at', '>', Carbon::yesterday()
        ])
            ->update([
                'last_apparition' => 'Today'
            ]);

        AdvancedBugsterDB::where([
            'created_at', '<', Carbon::today()->subDay(),
            'created_at', '>', Carbon::today()->subWeek()
        ])
            ->update([
                'last_apparition' => 'This week'
            ]);

        AdvancedBugsterDB::where([
            'created_at', '<', Carbon::today()->subWeek(),
            'created_at', '>', Carbon::today()->subMonth()
        ])
            ->update([
                'last_apparition' => 'This month'
            ]);
    }

    public function updateStats(): void
    {
        AdvancedBugsterStat::where([
            'generated_at', '<', Carbon::now()->subDay(),
            'category', '=', 'daily'
        ])
            ->update([
                'category' => 'weekly',
            ]);

        AdvancedBugsterStat::where([
            'generated_at', '<', Carbon::now()->subWeek(),
            'category', '=', 'weekly'
        ])
            ->update([
                'category' => 'monthly',
            ]);
    }
}
