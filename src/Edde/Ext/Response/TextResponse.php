<?php
	namespace Edde\Ext\Response;

	use Edde\Common\Response\AbstractResponse;

	class TextResponse extends AbstractResponse {
		/**
		 * @var string|callable
		 */
		private $text;

		/**
		 * @param string|callable $text can be callback
		 */
		public function __construct($text) {
			$this->text = $text;
		}

		public function send() {
			echo is_callable($this->text) ? call_user_func($this->text) : $this->text;
		}
	}
