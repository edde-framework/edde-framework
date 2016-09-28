<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Xml\LazyXmlParserTrait;
	use Edde\Api\Xml\XmlParserException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Xml\XmlNodeHandler;

	/**
	 * Xml string sourece to "something" converter.
	 */
	class XmlConverter extends AbstractConverter {
		use LazyXmlParserTrait;

		/**
		 * Only 3 things that are infinite
		 * 1.Human Stupidity
		 * 2.Universe
		 * 3.WinRar Trial
		 */
		public function __construct() {
			parent::__construct([
				'text/xml',
				'applicaiton/xml',
				'xml',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws XmlParserException
		 * @throws ConverterException
		 */
		public function convert($source, string $target) {
			$source = $source instanceof IResource ? $source : $this->unsupported($source, $target);
			try {
				switch ($target) {
					case INode::class:
						$this->xmlParser->parse($source, $handler = new XmlNodeHandler());
						return $handler->getNode();
				}
			} catch (XmlParserException $e) {
				throw new XmlParserException(sprintf('Cannot handle resource [%s]: %s', (string)$source->getUrl(), $e->getMessage()), 0, $e);
			}
			$this->exception($target);
		}

		/**
		 * @param IResource $resource
		 * @param INode|null $root
		 *
		 * @return INode
		 * @throws XmlParserException
		 */
		public function handle(IResource $resource, INode $root = null): INode {
			try {
				$this->xmlParser->parse($resource, $handler = new XmlNodeHandler());
				$node = $handler->getNode();
				if ($root !== null) {
					$root->setNodeList($node->getNodeList(), true);
				}
				return $root ?: $node;
			} catch (XmlParserException $e) {
				throw new XmlParserException(sprintf('Cannot handle resource [%s]: %s', (string)$resource->getUrl(), $e->getMessage()), 0, $e);
			}
		}
	}
