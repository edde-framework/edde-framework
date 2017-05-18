<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\NodeUtils;

	/**
	 * Specific converter for including php files which should return array (object).
	 */
	class PhpConverter extends AbstractConverter {
		/**
		 * You know you've been online too long when:
		 *
		 * Tech support calls YOU for help.
		 */
		public function __construct() {
			$this->register([
				'text/x-php',
				'application/x-php',
			], [
				INode::class,
				'array',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($convert, string $mime, string $target) {
			$this->unsupported($convert, $target, $convert instanceof IResource);
			switch ($target) {
				case INode::class:
					return (function (IResource $resource, string $source) {
						return NodeUtils::toNode($this->convert($resource, $source, 'array'));
					})($convert, $mime);
				case 'array':
					if (is_array($include = require (string)$convert->getUrl()) === false) {
						throw new ConverterException(sprintf('Conversion to [%s] failed: php file [%s] has not returned array.', $target, (string)$convert->getUrl()));
					}
					return $include;
			}
			$this->exception($mime, $target);
		}
	}
