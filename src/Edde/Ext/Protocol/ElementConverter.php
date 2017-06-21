<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;
	use Edde\Common\Node\NodeUtils;
	use Edde\Common\Protocol\Element;

	class ElementConverter extends AbstractConverter {
		use LazyConverterManagerTrait;

		public function __construct() {
			$this->register(IElement::class, [
				'stream+application/json',
				'application/json',
				'*/*',
			]);
			$this->register([
				'stream+application/json',
				'application/json',
			], IElement::class);
		}

		/**
		 * @inheritdoc
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			switch ($target) {
				case IElement::class:
					$this->unsupported($content, $target, is_string($content));
					return new Content(NodeUtils::toNode($this->converterManager->convert($content, $mime, [\stdClass::class])->convert()->getContent(), null, Element::class), IElement::class);
				case 'stream+application/json':
				case 'application/json':
					$this->unsupported($content, $target, $content instanceof INode);
					return $this->converterManager->convert($content, INode::class, [$target])->convert();
				case '*/*':
					return new Content($this->converterManager->convert($content, INode::class, ['text/xml'])->convert()->getContent(), 'text/xml');
			}
			return $this->exception($mime, $target);
		}
	}
