<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ListAssets extends Command
{
    protected $signature = 'app:list-assets';
    protected $description = 'List all public asset URLs';

    public function handle()
    {
        $publicPath = public_path();

        $files = File::allFiles($publicPath);

        if (count($files) === 0) {
            $this->warn('No assets found in public directory.');
            return;
        }

        $this->info('Listing Public Asset URLs');
        $this->line(str_repeat('-', 80));

        foreach ($files as $file) {
            // Remove full public path
            $relativePath = str_replace($publicPath . DIRECTORY_SEPARATOR, '', $file->getRealPath());

            // Convert Windows backslashes to URL format
            $relativePath = str_replace('\\', '/', $relativePath);

            $this->line(asset($relativePath));
        }

        $this->line(str_repeat('-', 80));
        $this->info('Total Assets: ' . count($files));
    }
}