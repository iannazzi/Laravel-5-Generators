<?php  namespace Iannazzi\Generators\Commands;
use Iannazzi\Generators\DatabaseImporter\DatabaseConnector;
use Iannazzi\Generators\DatabaseImporter\DatabaseDataImporter;
use Illuminate\Console\Command;


class ImportDatabaseCommand extends Command
{

    protected $signature = 'zz:ImportDatabase
                             {--test=false : If True I will import only 100 rows from the database}';
    protected $description = 'Import Data from production database';
    public function __construct()
    {
        parent::__construct();

    }
    public function handle()
    {
        $test = $this->option('test');
        $test  = ($test == 'true') ? true: false;
        DatabaseConnector::addConnections();
        $dataImporter = new DatabaseDataImporter('POS', $test);
        $dataImporter->importEmbrasseMoiData();

    }

}
?>