<?php
namespace wildcats1369\Filametrics\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    protected $signature = 'filametrics:install {--with-npm}';
    protected $description = 'Setup Filametrics package with published files';

    public function handle()
    {
        $this->info('Publishing Filametrics assets...');
        $this->callSilent('vendor:publish', [
            '--provider' => "wildcats1369\\Filametrics\\Providers\\FilametricsServiceProvider",
        ]);
        $this->info('✅ Views and configs published.');

        $this->info('Running migrations...');
        $this->call('migrate');
        $this->info('✅ Database migrations complete.');

        $this->info('Installing Puppeteer with npm...');
        $process = new Process(['npm', 'install', 'puppeteer', '--save']);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        $this->info('🎉 Filametrics setup complete!');
    }
}