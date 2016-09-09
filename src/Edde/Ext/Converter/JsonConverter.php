<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;
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
				case 'array':
					return json_decode($source, true);
				case 'object':
					return json_decode($source);
				case 'node':
				case INode::class:
					return NodeUtils::node(new Node(), json_decode($source->get()));
			}
			$this->exception($target);
		}
	}
