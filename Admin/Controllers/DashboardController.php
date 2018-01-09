<?php
namespace Admin\Controllers;
defined('APP') OR exit('No direct script access allowed');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController extends Controller
{
	protected $_container;

	protected $_db;

	protected $_twig;

	protected $_config;

	protected $_data = array();

	protected $_auth;

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
		$this->_twig = $c->get('Twig');
		$this->_config = $c->get('settings');
		$this->_auth = $c->get('Auth');

		$this->_data['page_template'] = 'dashboard.twig';
		$this->_data['base_url'] = $this->_config['base_url'];
	}

	public function index(Request $request, Response $response, $args) 
	{

		$get = $request->getQueryParams();

		// provjeri dali ima kakvih migracija za izvršiti.
		// ukoliko ima, pronađi koje i kreni redom
		// prvo idu one s starijim datumom prema novijim
		// nakon toga updateaj tablicu s migracijama
		
		$this->_execute_migrations();

		//$this->dbExport();
		$this->_data['page_template'] = 'dashboard.twig';
		$this->_twig->display('layout.twig', $this->_data);
	}

	protected function _execute_migrations()
	{
		$base_folder = APP . DIRECTORY_SEPARATOR . DB_DIRECTORY . DIRECTORY_SEPARATOR . MIGRATION_DIRECTORY;
		$scaned = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base_folder));
		foreach($scaned as $file)
		{
			$file = pathinfo($file);
			if($file['extension'] === 'php')
			{
				$basename = $file['basename'];
				$url = $file['dirname'] . DIRECTORY_SEPARATOR . $file['basename'];

				$this->_db->where("filename", $basename);
				$migration = $this->_db->getOne("wc_migrations");
				if(count($migration) == 0)
				{
					include($url);
					if(isset($migration))
					{
						if($migration['autoexec'] === true)
						{
							$date = $migration['exec_date'];
							$time = $migration['exec_time'];

							$dt = \DateTime::createFromFormat("d.m.Y H:i", $date . ' ' . $time);
							$ts = $dt->getTimestamp();
							if(time() >= $ts && !empty($checkSql) && !empty($execMigrationSql))
							{
								$this->_db->startTransaction();

								// Ok, zadovoljeno, pokreni test sql!
								$test = $this->_db->rawQuery($checkSql);

								if(count($test) == 0) 
								{
									// ok e sada kada test nije prošo, pokreni SQL migracije
									$mg = $this->_db->rawQuery($execMigrationSql);
									if($mg) 
									{
										$this->_db->commit();
									}
									else
									{
										$this->_db->rollback();
									}
								}

								$migrationData = array(
									'filename' => $basename,
									'author_id' => $this->_auth->getUserId(),
									'executed' => time()
								);

								$finishedMg = $this->_db->insert("wc_migrations", $migrationData);
								if($finishedMg && $test) 
								{
									$this->_db->commit();
								}
								else
								{
									$this->_db->rollback();
								}
							}
						}
					}
				}
			}
		}
	}

	protected function dbExport()
	{
		// Pokrenuti mysql dump i spremiti ga u DB/Backups/xxxx
		// ukoliko je gzip dostupan pretvori .sql u gzip
		// provjeriti koliko je memorije dostupno na serveru prije nego dump krene
		// vidjeti dali ce dump biti veci od 20% slobodne memorije ukoliko da, obavjesti prije
		// da mora nesto obrisati ili da nema memorije
		// također prije dumpa provjeriti sys_getloadavg() ukoliko je procesor zadnjih 5 minuta bio preko 20-30% NE dozvoli
		// dump baze nego obavijesti korisnika da priceka trenutno.
		// provjeriti jel ima pravo zapisa na exports folder
		// nakon sto export bude gotov bilo bi dobro da server sam napravit commit odnosno push (znaci treba implementirati GIT)
		
		$directory = APP . DIRECTORY_SEPARATOR . DB_DIRECTORY . DIRECTORY_SEPARATOR . BACKUP_DIRECTORY . DIRECTORY_SEPARATOR;
		$filename =  $directory .'export_' . date("d-m-Y") . '_' . time() . '.sql.gz';
		$kilobytes = disk_free_space($directory)/1024/1024;
	
		$dumpSize = $this->_db->rawQuery("
			SELECT 
				table_schema AS 'Database', 
				SUM(data_length + index_length) / 1024 / 1024 AS 'Size' 
			FROM 
				information_schema.TABLES GROUP BY table_schema");

		$assocIndex = array_search($this->_config['db']['db'], array_column($dumpSize, 'Database'));
		$db_size_kb = $dumpSize[$assocIndex]['Size'];
		$new_db_size = ($kilobytes - $db_size_kb) - 50000;

		$perc = $new_db_size / $kilobytes * 100;
		$diff = round(100 - $perc, 2);
		if($diff > 20)
		{
			// Dump bi mogao zauzeti više od 20% ukupnog slobodnog prostora. Ne dozvoli!
			exit;
		}

		$server_load = $this->_get_server_load();
		if(is_null($server_load)) 
		{
			// ne mozemo procjeniti trenutno stanje server resursa zbog starog windowsa
			// ili linux nema pravo citanja
			exit;
		}
		//print_r($server_load);

		if($server_load > 25)
		{
			// Server je dosta zauzet (preko 25%), kako ne bi puklo nesto bolje pricekati i kasnije napraviti dump
			exit;
		}

		if(count(array_intersect(['mod_deflate', 'mod_gzip'],  apache_get_modules())) > 0) 
		{
			$cmd = "mysqldump -u ".$this->_config['db']['username']." --password=".$this->_config['db']['password']." ".$this->_config['db']['db']." | gzip --best > ".$filename;   
			@exec($cmd);

			// Ok sada kada je export gotov, potrebno je povuci zadnje promjene (ukoliko ima nekih) i nakon toga commitati export file 
			// i poslati ga na GIT - ali treba jako paziti mozda cak i to NE raditi.
			
			/*$repo = new Cz\Git\GitRepository(APP);
			$repo->addRemote('remote-name', 'repository-url', array('--options'));
			$repo->addRemote('origin', 'git@github.com:czproject/git-php.git');
			if($repo->hasChanges())
			{
				$repo->addAllChanges();
				$repo->commit('Server Export DB: ' . $filename);
				$repo->push('origin', array('master', '-u'));
			}*/
		}
	}

	protected function _get_server_load()
	{
		$load = null;
		if(stristr(PHP_OS, "win"))
		{
			$cmd = "wmic cpu get loadpercentage /all";
			@exec($cmd, $output);

			if($output)
			{
				foreach($output as $line)
				{
					if($line && preg_match("/^[0-9]+\$/", $line))
					{
		    			$load = $line;
		    			break;
					}
				}
			}
		}
		else
		{
			if(is_readable("/proc/stat"))
			{
				$statData1 = $this->_get_server_load_linux_data();
				sleep(1);
				$statData2 = $this->_get_server_load_linux_data();

				if((!is_null($statData1)) && (!is_null($statData2)))
				{
					$statData2[0] -= $statData1[0];
					$statData2[1] -= $statData1[1];
					$statData2[2] -= $statData1[2];
					$statData2[3] -= $statData1[3];

					$cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

					$load = 100 - ($statData2[3] * 100 / $cpuTime);
				}
			}
		}
		return $load;
	}

	protected function _get_server_load_linux_data()
	{
		if(is_readable("/proc/stat"))
		{
			$stats = @file_get_contents("/proc/stat");

			if($stats !== false)
			{
				$stats = preg_replace("/[[:blank:]]+/", " ", $stats);

				$stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
				$stats = explode("\n", $stats);

				foreach($stats as $statLine)
				{
					$statLineData = explode(" ", trim($statLine));
					if((count($statLineData) >= 5) && ($statLineData[0] == "cpu"))
					{
						return array(
							$statLineData[1],
							$statLineData[2],
							$statLineData[3],
							$statLineData[4],
						);
					}
				}
			}
		}
		return null;
	}

	public function users(Request $request, Response $response, $args) 
	{
		$get = $request->getQueryParams();

		$this->_data['page_template'] = 'dashboard.twig';
		$this->_twig->display('layout.twig', $this->_data);
	}
}