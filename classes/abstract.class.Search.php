<?php
abstract class Search {
	private $results;

	abstract public function query($order_by);
	abstract public function get_query();
	abstract public function set_query($query);
	abstract public function get_results();
	abstract public function set_results($results);
}
?>