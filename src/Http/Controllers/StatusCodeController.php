<?php

namespace Vlinde\Bugster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vlinde\Bugster\Models\LaravelBugsterStatusCode;

class StatusCodeController extends Controller
{
    public function chart(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        return response()->json([
            'data' => $this->getData($startDate, $endDate),
        ]);
    }

    private function getData(?string $startDate, ?string $endDate): array
    {
        $data = [];

        $this->getStatusCode($startDate, $endDate)
            ->each(function ($stats, $displayName) use (&$data) {
                $tempData['name'] = $displayName;
                $tempData['data'] = [];

                foreach ($stats as $stat) {
                    $tempData['data'][] = [
                        'x' => $stat->date->format('d.m.Y'),
                        'y' => $stat->count,
                    ];
                }

                $data[] = $tempData;
            });

        return $data;
    }

    private function getStatusCode(?string $startDate, ?string $endDate): Collection
    {
        return LaravelBugsterStatusCode::when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('date')
            ->get()
            ->groupBy('status_code_text');
    }
}
