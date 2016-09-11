<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;

	class JsonConverter extends AbstractConverter {
		public function __construct() {
			parent::__construct([
				'application/json',
				'json',
			]);
		}

		public function convert($source, string $target) {
			$source = $source instanceof IFile ? $source->get() : $source;
			switch ($target) {
				case 'array':
					return json_decode($source, true);
				case 'object':
					return json_decode($source);
				case 'node':
				case INode::class:
					return NodeUtils::node(new Node(), json_decode($source));
			}
			$this->exception($target);
		}
	}
