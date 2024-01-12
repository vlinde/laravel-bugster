<?php

namespace Vlinde\Bugster\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LogFileController extends Controller
{
    private const IGNORED_FILES = [
        '.', '..', '.gitignore',
    ];

    public function index(Request $request)
    {
        $path = storage_path('logs');
        if ($request->path) {
            $path = $request->path;
        }

        if (! File::exists($path)) {
            abort(404);
        }

        if (! $this->checkIfAuthorized($path)) {
            abort(403, 'You are not authorized to view this directory');
        }

        $files = $this->getFiles($path);

        return response()->json($files);
    }

    public function download(Request $request)
    {
        $filePath = $request->file_path;

        if (! $filePath || ! File::exists($filePath)) {
            abort(404, 'Invalid file path');
        }

        if (! $this->checkIfAuthorized($filePath)) {
            abort(403, 'You are not authorized to download this file');
        }

        return response()->download($filePath);
    }

    public function rename(Request $request)
    {
        $filePath = $request->file_path;

        if (! $filePath || ! File::exists($filePath)) {
            abort(404, 'Invalid file path');
        }

        if (! $this->checkIfAuthorized($filePath)) {
            abort(403, 'You are not authorized to download this file');
        }

        $directory = pathinfo($filePath, PATHINFO_DIRNAME);
        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $newFilePath = "$directory/$filename.$extension";

        $counter = 0;
        $incrementExistingCounter = false;

        if (preg_match('/--(\d+)$/', $filename, $matches)) {
            $counter = (int) $matches[1];
            $incrementExistingCounter = true;
        }

        while (File::exists($newFilePath)) {
            $counter++;

            if ($incrementExistingCounter) {
                $newFileName = preg_replace('/--(\d+)$/', "--$counter", $filename);
            } else {
                $newFileName = "$filename--$counter";
            }

            $newFilePath = "$directory/$newFileName.$extension";
        }

        rename($filePath, $newFilePath);

        return response()->json([
            'message' => 'File renamed successfully',
        ]);
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
                    'path' => $filePath,
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
                    'time' => filemtime($filePath),
                ];
            }
        }

        if (count($logFiles) > 0) {
            $logFilesTimes = array_column($logFiles, 'time');

            array_multisort($logFilesTimes, SORT_DESC, $logFiles);
        }

        return [
            'directories' => $directories,
            'files' => $logFiles,
        ];
    }

    private function checkIfAuthorized(string $path): bool
    {
        return Str::contains($path, '/storage/logs');
    }
}
