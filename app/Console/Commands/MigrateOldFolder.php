<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MigrateOldFolder extends Command
{
    protected $signature = 'migrate:oldfolder {folder}';
    protected $description = 'Move migration files from a custom folder to database/migrations and run migrate';

    public function handle()
    {
        $folder = $this->argument('folder');
        $sourcePath = base_path($folder); // e.g., app/0مي
        $destPath = database_path('migrations');

        if (!File::exists($sourcePath)) {
            $this->error("Folder does not exist: $sourcePath");
            return 1;
        }

        $files = File::files($sourcePath);

        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $newFileName = preg_replace('/[^\w\d\._]/u', '', $fileName); // remove non-ASCII chars
            File::copy($file->getRealPath(), $destPath . '/' . $newFileName);
            $this->info("Copied: $fileName → $newFileName");
        }

        $this->info('All files copied. Running migrations...');
        Artisan::call('migrate', ['--force' => true]);
        $this->info(Artisan::output());

        return 0;
    }
}
