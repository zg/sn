<?php
class Message {
	private $type = '';
	private $message = '';

	public function print_unformatted()
	{
		return $this->get_message();
	}

	public function print_formatted()
	{
		$type = $this->get_type();
		$message = $this->get_message();
		$return_str = '<div class="' . $type . '_message">';
			$return_str .= $message;
		$return_str .= '</div>';
		return $return_str;
	}

	public function get_type()
	{
		return $this->type;
	}

	public function set_type($type)
	{
		$this->type = $type;
	}

	public function get_message()
	{
		return $this->message;
	}

	public function set_message($message)
	{
		$this->message = $message;
	}
}

$error_message = '';
$success_message = '';
$message = '';
?>