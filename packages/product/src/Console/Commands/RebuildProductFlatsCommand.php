<?php

namespace Vendor\Product\Console\Commands;

use Illuminate\Console\Command;
use Vendor\Product\Models\ProductFlat;

class RebuildProductFlatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:rebuild-flats 
                            {--force : Force rebuild without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild product flats table from products and variants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will clear and rebuild the entire product_flats table. Continue?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('Rebuilding product flats table...');
        
        $bar = $this->output->createProgressBar();
        $bar->start();

        try {
            $count = ProductFlat::rebuildAll();
            
            $bar->finish();
            $this->newLine(2);
            
            $this->info("✓ Successfully rebuilt {$count} product flat entries!");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine(2);
            
            $this->error('✗ Failed to rebuild product flats table.');
            $this->error('Error: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}

