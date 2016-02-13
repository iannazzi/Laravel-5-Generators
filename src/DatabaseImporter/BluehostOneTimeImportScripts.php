<?php
namespace Iannazzi\Generators\DatabaseImporter;

class BluehostOneTimeScripts
{
    



	public function exportBluehostTableArray()
	{
		//this gets all the tables in blue host as an array,
		//so i can modify them and put in code....
		$tables = [];
		foreach (DatabaseSelector::getTables($dbc) as $table)
		{
			$tables[$table] = [
				'new_name'=>str_replace('pos_','',$table),
				'type' => 'regular'
			];
		}
		$name = database_path('test/array.php');

		file_put_contents($name, var_export($tables, true));
		//$this->files->put($path, 'test');
		dd('checkit');
	}




	public function fix_pos_name()
	{
		$directory = database_path("seeds/csv_startup_data/tenant");
		$files = \File::allFiles($directory);
		foreach ($files as $file)
		{
		    $new_name = str_replace('pos_', '', $file);
		    if(strpos($file, 'pos_'))
		    {
		    	echo 'Rename file from ' .$file . ' to ' .$new_name .PHP_EOL; 
		    	rename($file, $new_name);
		    }
		   	
		}
		echo 'Fixed' .PHP_EOL;
	}
	public function remove_pos_from_files()
	{
		$directory = database_path("seeds/csv_startup_data/craiglorious");
		$files = \File::allFiles($directory);
		foreach ($files as $file)
		{
			$file_contents = file_get_contents($file);
			$file_contents = str_replace("pos_","",$file_contents);
			file_put_contents($file,$file_contents);
		}
	}
	public function replace_creatPos_from_files()
	{
		$directory = database_path("migrations/tenant");
		$files = \File::allFiles($directory);
		foreach ($files as $file)
		{
			$file_contents = file_get_contents($file);
			$file_contents = str_replace("CreatePos","Create",$file_contents);
			file_put_contents($file,$file_contents);
		}
	}
	public function createSeeders()
	{
		//i need a list of seeders...
		$db = 'craiglorious';
		$db = 'tenant';

		$seed_file = database_path("seeds/".$db."/". ucfirst($db)."DatabaseSeeder.php");
		$directory = database_path("migrations/".$db);
		$output_file_path = database_path("seeds/". $db. "/tables/");

		$seeders = '';
		$files = \File::allFiles($directory);
		$seeder_file_names = [];
		$calls = '';
		foreach ($files as $file)
		{
			//get the name and make it a seeder...
			$seeder_name = basename((string)$file, '.php');
			$seeder_parts = explode('_', $seeder_name);
			$name = '';
			for($i=5;$i<sizeof($seeder_parts)-1;$i++)
			{
				$name .= ucfirst($seeder_parts[$i]) ;
			}
			$name .=  'TableSeeder';
			if ($name != 'PasswordResetsTableSeeder')
			{
				$calls .= '$this->call(\''. $name. '\');' .PHP_EOL;
				$seeder_file_names[] = $name ;
			}
		}
		$file_contents = file_get_contents($seed_file);
		$file_contents = str_replace("REPLACE;",$calls,$file_contents);
		file_put_contents($seed_file,$file_contents);
		
		foreach($seeder_file_names as $name)
		{
	$template = "<?php
	use Illuminate\Database\Seeder;
		
	class ".$name." extends Seeder 
	{
		public function run()
		{
		}
	}";
			$output_file = $output_file_path . $name . '.php';
			//dd($output_file . PHP_EOL . $template);
			file_put_contents($output_file ,$template);
			
		}
	}
}