<?php

namespace PcbPlus\PcbCpq\Console;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PcbPlus\PcbCpq\Concerns\HasVersion;
use PcbPlus\PcbCpq\Imports\FactorImport;
use PcbPlus\PcbCpq\Models\Product;
use PcbPlus\PcbCpq\Models\Version;

class ImportFactorsCommand extends Command
{
    use HasVersion;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cpq-factors {version_number} {product_code} {file_path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import factors from an excel file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $version = Version::query()
            ->where('number', $this->argument('version_number'))
            ->first();

        if (! $version) {
            return $this->error('Version not found');
        }

        if ($version->is_submitted) {
            return $this->error('Version must be unsubmitted');
        }

        if ($version->is_active) {
            return $this->error('Version must be inactive');
        }

        $product = Product::query()
            ->where('version_id', $version->id)
            ->where('code', $this->argument('product_code'))
            ->first();

        if (! $product) {
            return $this->error('Product not found');
        }

        $filePath = $this->argument('file_path');

        if (! file_exists($filePath)) {
            return $this->error('File not exists');
        }

        Excel::import(new FactorImport($product), $filePath);

        $this->info('import success');
    }
}
