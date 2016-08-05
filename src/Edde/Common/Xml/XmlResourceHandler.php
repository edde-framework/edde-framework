<?php
	declare(strict_types = 1);

	namespace Edde\Common\Xml;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Xml\IXmlParser;
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
		 */
		public function __construct(IXmlParser $xmlParser) {
			$this->xmlParser = $xmlParser;
		}

		public function getMimeTypeList(): array {
			return [
				'text/xml',
			];
		}

		public function handle(IResource $resource): INode {
			$this->xmlParser->parse($resource, $handler = new XmlNodeHandler());
			return $handler->getNode();
		}
	}
