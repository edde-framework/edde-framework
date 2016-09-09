<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Common\Resource\AbstractConverter;

	class JsonConverter extends AbstractConverter {
		public function __construct() {
			parent::__construct([
				'application/json',
				'json',
			]);
		}

		public function convert($source, string $target) {
			switch ($target) {
//				case INode::class:
//					 json_decode($source, true);
//					break;
				case 'array':
					return json_decode($source, true);
					break;
				case 'object':
					return json_decode($source);
					break;
			}
			$this->exception($target);
		}
	}
