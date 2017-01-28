<?php
	declare(strict_types=1);

	namespace Edde\Common\Xml;

	use Edde\Api\Xml\IXmlExport;
	use Edde\Common\File\File;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use PHPUnit\Framework\TestCase;

	class XmlExportTest extends TestCase {
		/**
		 * @var IXmlExport
		 */
		protected $xmlExport;

		public function testSimple() {
			$this->xmlExport->export(NodeIterator::recursive(new Node('root'), true), new File(__DIR__ . '/export.xml'));
		}

		protected function setUp() {
			$this->xmlExport = new XmlExport();
		}
	}
