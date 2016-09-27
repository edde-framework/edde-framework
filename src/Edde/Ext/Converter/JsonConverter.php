<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;

	/**
	 * Json converter from json encoded string to "something".
	 */
	class JsonConverter extends AbstractConverter {
		/**
		 * You know you've been on line too long when:
		 *
		 * You get a tattoo that says, "This body best viewed with Firefox 1.1."
		 */
		public function __construct() {
			parent::__construct([
				'application/json',
				'json',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
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
