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

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
		$this->_twig = $c->get('Twig');
		$this->_config = $c->get('settings');

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

		$this->dbExport();
		$this->_data['page_template'] = 'dashboard.twig';
		$this->_twig->display('layout.twig', $this->_data);
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
		$kilobytes = round(disk_free_space($directory)/1024);
	
		$dumpSize = $this->_db->rawQuery("
			SELECT 
				table_schema AS 'Database', 
				SUM(data_length + index_length) / 1024 AS 'Size' 
			FROM 
				information_schema.TABLES GROUP BY table_schema");

		$assocIndex = array_search($this->_config['db']['db'], array_column($dumpSize, 'Database'));
		$db_size_kb = round($dumpSize[$assocIndex]['Size']);
		$dif = $kilobytes - $db_size_kb;
		$perc = (1 - $kilobytes / $dif) * 100;

		echo $perc;
		exit;
		if(count(array_intersect(['mod_deflate', 'mod_gzip'],  apache_get_modules())) > 0) 
		{
			$cmd = "mysqldump -u ".$this->_config['db']['username']." --password=".$this->_config['db']['password']." ".$this->_config['db']['db']." | gzip --best > ".$filename;   
			passthru($cmd);
		}
	}

	public function users(Request $request, Response $response, $args) 
	{

		$get = $request->getQueryParams();

		$this->_data['page_template'] = 'dashboard.twig';
		$this->_twig->display('layout.twig', $this->_data);
	}
}