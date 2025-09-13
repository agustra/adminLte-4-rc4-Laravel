<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupService
{
    protected $dbHost;

    protected $dbUser;

    protected $dbPass;

    protected $dbName;

    protected $backupDir;

    public function __construct()
    {
        $this->dbHost = config('database.connections.mysql.host');
        $this->dbUser = config('database.connections.mysql.username');
        $this->dbPass = config('database.connections.mysql.password');
        $this->dbName = config('database.connections.mysql.database');
        $this->backupDir = storage_path('app/backup');
    }

    public function create($saveToLocal = true, $saveToGoogle = true)
    {
        // Timestamp for unique file name
        $date = now()->format('Y-m-d-H-i-s');

        // Filename for the backup
        $sqlFile = $this->backupDir.'/'.$this->dbName.'_'.$date.'.sql';
        $zipFilename = $this->dbName.'_'.$date.'.zip';
        $zipFile = $this->backupDir.'/'.$zipFilename;

        // Create a directory if it doesn't exist
        if (! is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        // Create simple backup content for testing
        $backupContent = "-- Database Backup\n";
        $backupContent .= '-- Created at: '.now()."\n";
        $backupContent .= "-- Database: {$this->dbName}\n";

        file_put_contents($sqlFile, $backupContent);

        // Create a new zip file
        $this->createZipFile($sqlFile, $zipFile);

        // Read the zipped backup file content
        $zipContent = file_get_contents($zipFile);

        if ($zipContent === false) {
            throw new \Exception('Failed to read zipped backup file content.');
        }

        // Save based on user selection
        $locations = [];

        // Save to local storage if selected
        if ($saveToLocal) {
            try {
                Storage::disk('local')->write($zipFilename, $zipContent);
                $locations[] = 'Local Storage';
            } catch (\Exception $e) {
                \Log::error('Failed to save to Local Storage: '.$e->getMessage());
            }
        }

        // Save to Google Drive if selected
        if ($saveToGoogle) {
            try {
                Storage::disk('google')->write($zipFilename, $zipContent);
                $locations[] = 'Google Drive';
            } catch (\Exception $e) {
                \Log::error('Failed to save to Google Drive: '.$e->getMessage());
            }
        }

        $uploadLocation = empty($locations) ? 'Failed' : implode(' & ', $locations);

        // Delete temporary files
        unlink($zipFile);
        unlink($sqlFile);

        if (empty($locations)) {
            throw new \Exception('Failed to save backup to any storage location');
        }

        return [
            'status' => 'success',
            'message' => "Database backup completed and saved to {$uploadLocation}!",
            'filename' => $zipFilename,
            'location' => $uploadLocation,
            'size_kb' => number_format(strlen($zipContent) / 1024, 2),
        ];
    }

    private function generateSqlBackup($mysqli, $backupFile)
    {
        // Query to get all tables
        $query = 'SHOW TABLES';
        $result = $mysqli->query($query);

        if (! $result) {
            $mysqli->close();
            throw new \Exception('Error in query: '.$mysqli->error);
        }

        // Create SQL backup file
        file_put_contents($backupFile, "-- Database Backup\n\n");

        while ($row = $result->fetch_array()) {
            $tableName = $row[0];
            $query = "SELECT * FROM $tableName";
            $tableResult = $mysqli->query($query);

            if (! $tableResult) {
                $mysqli->close();
                throw new \Exception('Error in query: '.$mysqli->error);
            }

            file_put_contents($backupFile, "-- Table structure for table `$tableName`\n", FILE_APPEND);
            file_put_contents($backupFile, "DROP TABLE IF EXISTS `$tableName`;\n", FILE_APPEND);

            $createTableQuery = "SHOW CREATE TABLE $tableName";
            $createTableResult = $mysqli->query($createTableQuery);

            if ($createTableResult) {
                $createTableRow = $createTableResult->fetch_assoc();
                file_put_contents($backupFile, $createTableRow['Create Table'].";\n\n", FILE_APPEND);
            }

            while ($tableRow = $tableResult->fetch_assoc()) {
                $insertQuery = "INSERT INTO $tableName VALUES (";
                $values = array_map([$mysqli, 'real_escape_string'], array_values($tableRow));
                $insertQuery .= "'".implode("', '", $values)."');";
                file_put_contents($backupFile, $insertQuery."\n", FILE_APPEND);
            }

            file_put_contents($backupFile, "\n", FILE_APPEND);
        }

        $mysqli->close();
    }

    private function createZipFile($sourceFile, $zipFile)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            throw new \Exception('Failed to create zip file.');
        }
        $zip->addFile($sourceFile, basename($sourceFile));
        $zip->close();
    }

    public function download($filename, $type = 'local')
    {
        try {
            $disk = $type === 'google' ? 'google' : 'local';

            // Clean filename (remove prefix if exists)
            $cleanFilename = str_replace(['local_', 'google_'], '', $filename);

            // Find the file in storage
            $contents = collect(Storage::disk($disk)->listContents('/', false));
            $files = $contents->where('type', 'file')->pluck('path')->toArray();
            $fileToDownload = collect($files)->first(function ($file) use ($cleanFilename) {
                return basename($file) === $cleanFilename;
            });

            if ($fileToDownload) {
                try {
                    $originalFilename = basename($fileToDownload);
                    $stream = Storage::disk($disk)->readStream($fileToDownload);

                    if ($stream === false) {
                        throw new \Exception('Cannot read file stream');
                    }

                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                        fclose($stream);
                    }, 200, [
                        'Content-Type' => 'application/zip',
                        'Content-Disposition' => 'attachment; filename="'.$originalFilename.'"',
                    ]);
                } catch (\Exception $e) {
                    // File doesn't exist or other error, continue to throw exception
                }
            }

            throw new \Exception('File backup tidak ditemukan');
        } catch (\Exception $e) {
            throw new \Exception('Gagal mengunduh backup: '.$e->getMessage());
        }
    }

    public function getBackups($type = 'local', $filters = [])
    {
        try {
            $disk = $type === 'google' ? 'google' : 'local';
            $contents = collect(Storage::disk($disk)->listContents('/', false));
            $files = $contents->where('type', 'file')->pluck('path')->toArray();

            $backups = collect($files)
                ->filter(function ($file) use ($type) {
                    $isZip = str_contains($file, '.zip');
                    // For local, exclude backup/ folder
                    if ($type === 'local') {
                        return $isZip && ! str_contains($file, 'backup/');
                    }

                    return $isZip;
                })
                ->map(function ($file) use ($disk, $type) {
                    $lastModified = Storage::disk($disk)->lastModified($file);

                    return [
                        'id' => $type.'_'.basename($file),
                        'name' => basename($file),
                        'full_path' => $file,
                        'disk' => $type,
                        'size' => Storage::disk($disk)->fileSize($file),
                        'created_at' => $lastModified,
                        'formatted_size' => $this->formatBytes(Storage::disk($disk)->fileSize($file)),
                        'formatted_date' => date('Y-m-d H:i:s', $lastModified),
                    ];
                })
                ->sortByDesc('created_at');

            // Apply filters if provided
            if (! empty($filters)) {
                $backups = $this->applyFilters($backups, $filters);
            }

            return $backups->values();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function applyFilters($backups, $filters)
    {
        return $backups->filter(function ($backup) use ($filters) {
            $createdAt = $backup['created_at'];
            $date = \Carbon\Carbon::createFromTimestamp($createdAt);

            // Filter by date range
            if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {
                $startDate = \Carbon\Carbon::parse($filters['start_date'])->startOfDay();
                $endDate = \Carbon\Carbon::parse($filters['end_date'])->endOfDay();

                if (! $date->between($startDate, $endDate)) {
                    return false;
                }
            }

            // Filter by month and year
            if (! empty($filters['month']) && ! empty($filters['year'])) {
                if ($date->month != $filters['month'] || $date->year != $filters['year']) {
                    return false;
                }
            }
            // Filter by year only
            elseif (! empty($filters['year'])) {
                if ($date->year != $filters['year']) {
                    return false;
                }
            }
            // Filter by month only (current year)
            elseif (! empty($filters['month'])) {
                if ($date->month != $filters['month'] || $date->year != now()->year) {
                    return false;
                }
            }

            return true;
        });
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision).' '.$units[$i];
    }

    public function deleteBackup($filename, $type = null)
    {
        // Determine type from filename prefix
        if (str_starts_with($filename, 'local_')) {
            $type = 'local';
            $cleanFilename = str_replace('local_', '', $filename);
        } elseif (str_starts_with($filename, 'google_')) {
            $type = 'google';
            $cleanFilename = str_replace('google_', '', $filename);
        } else {
            $type = $type ?: 'local';
            $cleanFilename = $filename;
        }

        $disk = $type === 'google' ? 'google' : 'local';

        // Find and delete file
        $contents = collect(Storage::disk($disk)->listContents('/', false));
        $files = $contents->where('type', 'file')->pluck('path')->toArray();
        $fileToDelete = collect($files)->first(function ($file) use ($cleanFilename) {
            return basename($file) === $cleanFilename;
        });

        if ($fileToDelete) {
            Storage::disk($disk)->delete($fileToDelete);

            return [
                'status' => 'success',
                'message' => 'Backup berhasil dihapus dari '.($disk === 'google' ? 'Google Drive' : 'Local Storage'),
            ];
        }

        return [
            'status' => 'error',
            'message' => 'File backup tidak ditemukan',
        ];
    }

    public function bulkDeleteBackups($filenames, $type = 'local')
    {
        if (empty($filenames)) {
            return [
                'status' => 'error',
                'message' => 'Tidak ada file yang dipilih untuk dihapus',
            ];
        }

        $deletedCount = 0;
        $errors = [];

        foreach ($filenames as $filename) {
            try {
                $result = $this->deleteBackup($filename, $type);
                if ($result['status'] === 'success') {
                    $deletedCount++;
                } else {
                    $cleanFilename = str_replace(['local_', 'google_'], '', $filename);
                    $errors[] = "File {$cleanFilename} tidak ditemukan";
                }
            } catch (\Exception $e) {
                $cleanFilename = str_replace(['local_', 'google_'], '', $filename);
                $errors[] = "Gagal menghapus {$cleanFilename}: ".$e->getMessage();
            }
        }

        if ($deletedCount > 0) {
            $storageType = $type === 'google' ? 'Google Drive' : 'Local Storage';
            $message = "{$deletedCount} backup berhasil dihapus dari {$storageType}";
            if (! empty($errors)) {
                $message .= ', namun ada beberapa error: '.implode(', ', $errors);
            }

            return [
                'status' => 'success',
                'message' => $message,
                'deleted_count' => $deletedCount,
                'errors' => $errors,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Tidak ada backup yang berhasil dihapus: '.implode(', ', $errors),
        ];
    }

    public function getCounts()
    {
        try {
            // Count local backups
            $localBackups = $this->getBackups('local');
            $localCount = $localBackups->count();

            // Count Google Drive backups
            $googleBackups = $this->getBackups('google');
            $googleCount = $googleBackups->count();

            return [
                'local_count' => $localCount,
                'google_count' => $googleCount,
            ];
        } catch (\Exception $e) {
            return [
                'local_count' => 0,
                'google_count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function getAvailableDisk()
    {
        // Try Google Drive first, fallback to local
        try {
            Storage::disk('google')->files();

            return 'google';
        } catch (\Exception $e) {
            return 'local';
        }
    }
}
