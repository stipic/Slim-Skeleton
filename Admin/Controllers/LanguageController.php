<?php
namespace Admin\Controllers;
defined('APP') OR exit('No direct script access allowed');

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LanguageController extends Controller
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

		$this->_data['page_template'] = 'Languages/index.twig';
		$this->_data['base_url'] = $this->_config['base_url'];

		$this->_data['default_lang'] = $this->_config['current_lang'];
		$this->_data['languages'] = $this->_config['languages'];
	}

	public function index(Request $request, Response $response, $args) 
	{
		$get = $request->getQueryParams();

		$this->_twig->display('layout.twig', $this->_data);
	}

	public function scan(Request $request, Response $response, $args) 
	{
		$base_folder = APP . DIRECTORY_SEPARATOR . APP_DIRECTORY . DIRECTORY_SEPARATOR . APP_VIEW_DIRECTORY;
		$scaned = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base_folder));
		
		$messageKeys = array();
		$existedKeys = array();
		$existedTrans = array();
		foreach($scaned as $file)
		{
			if($file->isDir()) { 
				continue;
			}
			
			$file_content = file_get_contents($file);

			$messageKeys = array_filter(array_unique(array_merge($messageKeys, $this->get_string_between($file_content, 'message("', '")'))));
		}

		//echo '<pre>' .print_r($messageKeys, true) . '</pre>';
		
		// Sada idi po svakom jeziku i provjeri dali .po file postoji
		// ukoliko ne postoji kreiraj ga prvi puta sa message keyevima, a za defaultni jezik
		// će nađeni message keyevi ujedno biti i prijevod
		
		foreach($this->_config['languages'] as $langKey => $lang)
		{
			$poFile = LANG_DIRECTORY . DIRECTORY_SEPARATOR . $lang['locale'] . DIRECTORY_SEPARATOR . 'LC_MESSAGES' . DIRECTORY_SEPARATOR . $lang['locale'] . '.po';
			$moFile = LANG_DIRECTORY . DIRECTORY_SEPARATOR . $lang['locale'] . DIRECTORY_SEPARATOR . 'LC_MESSAGES' . DIRECTORY_SEPARATOR . $lang['locale'] . '.mo';
			if(!file_exists($poFile))
			{
				$po = fopen($poFile, "w");

				$poTemplate =
'msgid ""
msgstr ""
"Language: '.$lang['locale'].'\n"
"Project-Id-Version: '.$this->_config['text_domain'].'\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n"
"%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);\n"
"X-Generator: Poedit 1.8.1\n"
"X-Poedit-SourceCharset: UTF-8\n"
"X-Poedit-KeywordsList: __;_e;_n:1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;esc_attr__;"
"X-Textdomain-Support: yes\n"
"X-Poedit-SearchPath-0: .\n"
"X-Poedit-SearchPathExcluded-0: *.js\n"
';

				foreach($messageKeys as $key)
				{
					$quotedKey = '"' . $key . '"';

					$translation = '"' . $key . '-a-a-a--a-a"';

					$poTemplate .= '
msgid '.$quotedKey;
					$poTemplate .= '
msgstr '.($this->_config['current_lang'] == $langKey ? $translation : '""').'
';
				}

				fwrite($po, $poTemplate);
				fclose($po);
				chmod($poFile, 0777);

				$this->phpmo_convert($poFile, $moFile);
			}
			else
			{
				// Već postoji prijevod za ovaj jezik, učitaj .po file i išćitaj sve keyeve i prijevode
				// i usporedi sa skeniranim message keyevima, one koji se podudaraju ostavi, a one koje ne
				// spremi u array i dodaj bez prijevoda
				

				// napomena: bitno je isfiltrirati prazna polja jer .po fileovi na pocetku imaju prazni msgid i msgstr
				$existedTranslation = file_get_contents($poFile);
				$existedKeys = array_filter(array_unique(array_merge($existedKeys, $this->get_string_between($existedTranslation, 'msgid "', '"'))));
				$existedTrans = array_filter(array_unique(array_merge($existedTrans, $this->get_string_between($existedTranslation, 'msgstr "', '"'))));

				$keysThatExistedTranslationDontHave = array();
				$keysThatExistsButNotInUseAnywhere = array();

				$keysThatExistedTranslationDontHave[$langKey] = array_diff($messageKeys, $existedKeys);
				$keysThatExistsButNotInUseAnywhere[$langKey] = array_diff($existedKeys, $messageKeys);
			}
			
			echo '<pre>' .print_r($keysThatExistsButNotInUseAnywhere, true) . '</pre>';
		}

		//echo '<pre>' .print_r($messageKeys, true) . '</pre>';
		
	}

	protected function get_string_between($string, $start, $end) 
	{
		$last_end = 0;
		$matches = array();
		while(($ini = strpos($string, $start, $last_end)) !== false) 
		{
			$ini += strlen($start);
			$len = strpos($string,$end,$ini) - $ini;
			$matches[] = substr($string, $ini, $len);
			$last_end = $ini + $len + strlen($end);
		}
		return $matches;
	}


	protected function phpmo_convert($input, $output = false) 
	{
		if(!$output)
			$output = str_replace('.po', '.mo', $input);

		$hash = $this->phpmo_parse_po_file($input);
		if ($hash === false) {
			return false;
		} else {
			$this->phpmo_write_mo_file($hash, $output);
			return true;
		}
	}

	protected function phpmo_clean_helper($x) 
	{
		if(is_array($x)) {
			foreach($x as $k => $v) {
				$x[$k] = $this->phpmo_clean_helper($v);
			}
		} else {
			if($x[0] == '"')
				$x = substr($x, 1, -1);
			$x = str_replace("\"\n\"", '', $x);
			$x = str_replace('$', '\\$', $x);
		}
		return $x;
	}

	protected function phpmo_parse_po_file($in) 
	{
		$fh = fopen($in, 'r');
		if($fh === false) {
			return false;
		}

		$hash = array ();
		$temp = array ();
		$state = null;
		$fuzzy = false;

		while(($line = fgets($fh, 65536)) !== false) {
			$line = trim($line);
			if ($line === '')
				continue;
			list ($key, $data) = preg_split('/\s/', $line, 2);
			
			switch ($key) {
				case '#,' : 
					$fuzzy = in_array('fuzzy', preg_split('/,\s*/', $data));
				case '#' : 
				case '#.' : 
				case '#:' :
				case '#|' : 
					if (sizeof($temp) && array_key_exists('msgid', $temp) && array_key_exists('msgstr', $temp)) {
						if (!$fuzzy)
							$hash[] = $temp;
						$temp = array ();
						$state = null;
						$fuzzy = false;
					}
					break;
				case 'msgctxt' :
				case 'msgid' :
				case 'msgid_plural' :
					$state = $key;
					$temp[$state] = $data;
					break;
				case 'msgstr' :
					$state = 'msgstr';
					$temp[$state][] = $data;
					break;
				default :
					if (strpos($key, 'msgstr[') !== FALSE) {
						$state = 'msgstr';
						$temp[$state][] = $data;
					} else {
						switch ($state) {
							case 'msgctxt' :
							case 'msgid' :
							case 'msgid_plural' :
								$temp[$state] .= "\n" . $line;
								break;
							case 'msgstr' :
								$temp[$state][sizeof($temp[$state]) - 1] .= "\n" . $line;
								break;
							default :
								fclose($fh);
								return FALSE;
						}
					}
					break;
			}
		}
		fclose($fh);
		
		if ($state == 'msgstr')
			$hash[] = $temp;

		$temp = $hash;
		$hash = array ();
		foreach ($temp as $entry) {
			foreach ($entry as & $v) {
				$v = $this->phpmo_clean_helper($v);
				if ($v === FALSE) {
					return FALSE;
				}
			}
			$hash[$entry['msgid']] = $entry;
		}

		return $hash;
	}

	protected function phpmo_write_mo_file($hash, $out) 
	{
		ksort($hash, SORT_STRING);
		$mo = '';
		$offsets = array ();
		$ids = '';
		$strings = '';

		foreach ($hash as $entry) {
			$id = $entry['msgid'];
			if (isset ($entry['msgid_plural']))
				$id .= "\x00" . $entry['msgid_plural'];
			if (array_key_exists('msgctxt', $entry))
				$id = $entry['msgctxt'] . "\x04" . $id;
			$str = implode("\x00", $entry['msgstr']);
			$offsets[] = array (
				strlen($ids
			), strlen($id), strlen($strings), strlen($str));
			$ids .= $id . "\x00";
			$strings .= $str . "\x00";
		}

		$key_start = 7 * 4 + sizeof($hash) * 4 * 4;
		$value_start = $key_start + strlen($ids);
		$key_offsets = array();
		$value_offsets = array();
		foreach($offsets as $v) {
			list($o1, $l1, $o2, $l2) = $v;
			$key_offsets[] = $l1;
			$key_offsets[] = $o1 + $key_start;
			$value_offsets[] = $l2;
			$value_offsets[] = $o2 + $value_start;
		}
		$offsets = array_merge($key_offsets, $value_offsets);

		$mo .= pack('Iiiiiii', 0x950412de, 
		0, 
		sizeof($hash),
		7 * 4, 
		7 * 4 + sizeof($hash) * 8,
		0, 
		$key_start 
		);
		foreach($offsets as $offset)
			$mo .= pack('i', $offset);
		$mo .= $ids;
		$mo .= $strings;
		file_put_contents($out, $mo);
	}
}