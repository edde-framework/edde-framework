<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Api\Xml\XmlParserException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Xml\XmlNodeHandler;

	class XmlConverter extends AbstractConverter {
		use LazyInjectTrait;
		/**
		 * @var IXmlParser
		 */
		protected $xmlParser;

		public function __construct() {
			parent::__construct([
				'text/xml',
				'applicaiton/xml',
				'xml',
			]);
		}

		public function lazyXmlParser(IXmlParser $xmlParser) {
			$this->xmlParser = $xmlParser;
		}

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
