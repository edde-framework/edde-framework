<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Xml\LazyXmlParserTrait;
	use Edde\Api\Xml\XmlParserException;
	use Edde\Common\Converter\AbstractConverter;
	use Edde\Common\Converter\Content;
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
			$this->register([
				'text/xml',
				'application/xml',
				'application/xhtml+xml',
				'xml',
				'string',
			], INode::class);
			$this->register([
				'text/xml',
				'application/xml',
				'application/xhtml+xml',
				'xml',
				'string',
			], '*/*');
		}

		/**
		 * @inheritdoc
		 * @throws XmlParserException
		 * @throws ConverterException
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			$this->unsupported($content, $target, $content instanceof IResource || is_string($content));
			try {
				switch ($target) {
					case INode::class:
						$this->xmlParser->{is_string($content) ? 'string' : 'parse'}($content, $handler = new XmlNodeHandler());
						return new Content($handler->getNode(), INode::class);
					case '*/*':
						return new Content($content, 'application/xml');
				}
			} catch (XmlParserException $e) {
				throw new XmlParserException(sprintf('Cannot handle resource [%s]: %s', (string)$content->getUrl(), $e->getMessage()), 0, $e);
			}
			return $this->exception($mime, $target);
		}

		/**
		 * @param IResource  $resource
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
