<?php
	declare(strict_types = 1);

	namespace Edde\Api\Xml;

	use Edde\Api\Resource\IResource;

	/**
	 * Event based xml parser.
	 */
	interface IXmlParser {
		/**
		 * parse the input stream and emit events to the given xml handler
		 *
		 * @param IResource $resource
		 * @param IXmlHandler $xmlHandler
		 *
		 * @return IXmlParser
		 */
		public function parse(IResource $resource, IXmlHandler $xmlHandler): IXmlParser;
	}
