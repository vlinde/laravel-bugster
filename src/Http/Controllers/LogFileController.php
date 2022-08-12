<?php

namespace Vlinde\Bugster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogFileController extends Controller
{
    private const IGNORED_FILES = [
        '.', '..', '.gitignore'
    ];

    public function index(Request $request)
    {
        $path = storage_path('logs');
        if ($request->path) {
            $path = $request->path;
        }

        if (!File::exists($path)) {
            abort(404);
        }

        $files = $this->getFiles($path);

        return response()->json($files);
    }

    public function download(Request $request)
    {
        $filePath = $request->file_path;

        if (!$filePath || !File::exists($filePath)) {
            abort(404, 'Invalid file path');
        }

        return response()->download($filePath);
    }

    private function getFiles(string $path): array
    {
        $files = array_diff(scandir($path), self::IGNORED_FILES);

        $logFiles = [];
        $directories = [];

        foreach ($files as $file) {
            $filePath = "$path/$file";

            if (is_dir($filePath)) {
                $directories[] = [
                    'name' => $file,
                    'path' => $filePath
                ];

                continue;
            }

            if (is_file($filePath)) {
                $fileSize = round(filesize($filePath) / 1024 / 1024, 4);

                $logFiles[] = [
                    'name' => $file,
                    'path' => $filePath,
                    'size' => $fileSize,
                    'download_link' => route('log-files.download', ['file_path' => $filePath]),
                    'time' => filemtime($filePath)
                ];
            }
        }

        if (count($logFiles) > 0) {
            $logFilesTimes = array_column($logFiles, 'time');

            array_multisort($logFilesTimes, SORT_DESC, $logFiles);
        }

        return [
            'directories' => $directories,
            'files' => $logFiles
        ];
    }
}
