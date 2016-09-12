<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;

	/**
	 * Specific converter for including php files which should return array (object).
	 */
	class PhpConverter extends AbstractConverter {
		public function __construct() {
			parent::__construct([
				'text/x-php',
				'application/x-php',
			]);
		}

		public function convert($source, string $target) {
			$source = $source instanceof IResource ? $source : $this->unsupported($source, $target);
			switch ($target) {
				case INode::class:
					return (function (IResource $resource) {
						NodeUtils::node($root = new Node(), $this->convert($resource, 'array'));
						return $root;
					})($source);
				case 'array':
					if (is_array($include = require((string)$source->getUrl())) === false) {
						throw new ConverterException(sprintf('Convertion to [%s] failed: php file [%s] has not returned array.', $target, (string)$source->getUrl()));
					}
					return $include;
			}
			$this->exception($target);
		}
	}