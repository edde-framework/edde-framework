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
		public function convert($content, string $mime, string $target = null) {
			switch ($target) {
				case INode::class:
					$this->unsupported($content, $target, $content instanceof \stdClass);
					return NodeUtils::toNode($content);
				case 'object':
				case \stdClass::class:
					$this->unsupported($content, $target, $content instanceof INode);
					return NodeUtils::fromNode($content);
				case 'application/json':
					$this->unsupported($content, $target, $content instanceof INode);
					return $this->converterManager->convert(NodeUtils::fromNode($content), \stdClass::class, [$target])->convert();
			}
			$this->exception($mime, $target);
		}
	}
