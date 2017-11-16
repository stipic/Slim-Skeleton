<?php
namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use App\Models\TestModel;
use Respect\Validation\Validator as Validator;

class HomeController extends Controller
{
	protected $_container;

	protected $_db;

	public $menu_array = array();

	public function __construct($c) 
	{
		$this->_container = $c;
		$this->_db = $c->get('db_mysqli');
	}

	public function index(Request $request, Response $response, $args) 
	{
		/*new TestModel();

		$var = 'test';
		if(Validator::when( Validator::stringType(), Validator::length(5, 5) )->validate($var)) {
			echo 'True';
		}
		else {
			echo 'False';
		}*/

		$this->return_menu_array();
	}

	public function return_menu_array() 
	{
		$menus = $this->_db->get('menus');
		foreach($menus as $menu)
		{
			$menu_id = $menu['id'];
			$name = $menu['name'];
			$desc = $menu['description'];
			$created = $menu['created'];

			$this->menu_array['id'] = $menu_id;
			$this->menu_array['name'] = $name;
			$this->menu_array['desc'] = $desc;
			$this->menu_array['created'] = $created;

			$this->_db->where('menu_id', $menu_id);
			$this->_db->orderBy("'order'", "asc");
			$columns = $this->_db->get('menu_sections');
			$column_counter = 0;
			foreach($columns as $column)
			{
				$column_id = $column['id'];

				$this->_db->where('column_id', $column_id);
				$this->_db->orderBy("id", "asc");
				$items = $this->_db->get('menu_items');

				$new = $this->build_menu_array($items);
				$this->menu_array['items'][$column_counter] = $new;

				++ $column_counter;
			}
		}

		$fixed_array = $this->fix_menu_array_keys($this->menu_array);
		print_r($fixed_array);
	}

	public function build_menu_array($items, $parent_id = null) 
	{
	    $prepared_array = array();
	    foreach($items as $item) 
	    {
	        if($item['parent_id'] == $parent_id) 
	        {
	            $prepared_array[$item['id']] = array(
	                'label' => $item['label'],
	                'parent_id' => $item['parent_id']
	            );

	            $children = $this->build_menu_array($items, $item['id']);
	            if($children) 
	            {
	                $prepared_array[$item['id']]['children'] = $children;
	            }
	        }
	    }
	    return $prepared_array;
	}

	public function fix_menu_array_keys($array) 
	{
	    $numberCheck = false;
	    foreach($array as $key => $val) 
	    {
	        if(is_array($val)) $array[$key] = $this->fix_menu_array_keys($val); 
	        if(is_numeric($key)) $numberCheck = true;
	    }

	    if($numberCheck === true) {
	        return array_values($array);
	    } 
	    else {
	        return $array;
	    }
	}
}