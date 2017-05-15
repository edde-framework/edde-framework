<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Node\NodeUtils;

	class NodeConverter extends AbstractConverter {
		use LazyConverterManagerTrait;

		public function __construct() {
			$this->register([
				'object',
				\stdClass::class,
			], INode::class);
			$this->register(INode::class, [
				'object',
				\stdClass::class,
				'application/json',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 * @throws NodeException
		 */
		public function convert($convert, string $mime, string $target) {
			switch ($target) {
				case INode::class:
					$this->unsupported($convert, $target, $convert instanceof \stdClass);
					return NodeUtils::toNode($convert);
				case 'object':
				case \stdClass::class:
					$this->unsupported($convert, $target, $convert instanceof INode);
					return NodeUtils::fromNode($convert);
				case 'application/json':
					$this->unsupported($convert, $target, $convert instanceof INode);
					return $this->converterManager->convert(NodeUtils::fromNode($convert), \stdClass::class, [$target])->convert();
			}
			$this->exception($mime, $target);
		}
	}
