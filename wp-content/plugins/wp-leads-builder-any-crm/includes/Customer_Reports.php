<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class Customer_Reports extends WP_List_Table
{
	public $limit = 10;

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->table_data();
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = $this->limit;
        	$currentPage = $this->get_pagenum();
        	$totalItems = count($data);

		$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	));

		$data = array_slice($data, (($currentPage-1) * $perPage), $perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns()
	{
		$columns = array(
				'id'			=> 'ID',
				'user_name'       	=> 'Name',
				'country' 	        => 'Country',
				'product_name'		=> 'Product Name',
				'button_name' 		=> 'Event',
				'date'                  => 'Date',
				);

		return $columns;
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b )
	{
		// Set defaults
		$orderby = 'id';
		$order = 'asc';

		// If orderby is set, use this as the sort column
		if(!empty($_GET['orderby']))
		{
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if(!empty($_GET['order']))
		{
			$order = $_GET['order'];
		}

		$result = strnatcmp( $a[$orderby], $b[$orderby] );

		if($order === 'asc')
		{
			return $result;
		}

		return -$result;
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns()
	{
		return array('id' => array('id', false));
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns()
	{
		return array();
	}


	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_data()
	{
		global $wpdb;
		$res = array();
		$data = $wpdb->get_results('select * from wci_events',ARRAY_A);
		foreach($data as $dkey => $btn_value) {

			$res[] = array('id'=>$btn_value['id'],'user_name'=>$btn_value['user_id'],'country'=>$btn_value['country'],'product_name'=>$btn_value['product'],'button_name'=>$btn_value['button_name'] , 'date'=>$btn_value['date'] ) ;

		}
		return $res; 
	}

	/**
         * return assessment id
         * @param string $type
         * @param integer $user_id
         * @return string $assessmentId
         */
      
	// Used to display the value of the id column
	public function column_id($item)
	{
		return $item['id'];
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default($item, $column_name)
	{
		switch($column_name)	{
			case 'id':
				return $item[$column_name];
			case 'user_name':
				return $item[$column_name];
			case 'country':
				return $item[$column_name];
			case 'date':
				return $item[$column_name];
			case 'product_name':
				return $item[$column_name];
			case 'button_name':
				return $item[$column_name];
			default:
				return print_r($item, true);

		}
	}
}
