<?php
	declare(strict_types = 1);

	namespace Edde\Common\Xml;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceHandler;
	use Edde\Api\Xml\IXmlParser;
	use Edde\Api\Xml\XmlParserException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Resource\AbstractResourceHandler;

	class XmlResourceHandler extends AbstractResourceHandler {
		use LazyInjectTrait;

		/**
		 * @var IXmlParser
		 */
		protected $xmlParser;

		/**
		 * @param IXmlParser $xmlParser
		 *
		 * @return IResourceHandler
		 */
		public function lazyXmlParser(IXmlParser $xmlParser): IResourceHandler {
			$this->xmlParser = $xmlParser;
			return $this;
		}

		public function getMimeTypeList(): array {
			return [
				'text/xml',
			];
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
