<?php
	declare(strict_types = 1);

	namespace Edde\Common\Xml;

	use Edde\Api\Node\INode;
	use Edde\Api\Xml\XmlException;
	use Edde\Common\Node\Node;

	/**
	 * Static XML tree handler; reads whole XML input into a memory.
	 */
	class XmlNodeHandler extends AbstractXmlHandler {
		/**
		 * @var INode
		 */
		protected $node;
		/**
		 * @var INode
		 */
		protected $current;

		public function onTextEvent(string $text) {
			$this->current->setValue($text);
		}

		public function onDocTypeEvent(string $docType) {
			$this->node->addNode(new Node($docType));
		}

		public function onOpenTagEvent(string $tag, array $attributeList) {
			if ($this->node === null) {
				$this->current = $this->node = new Node($tag, null, $attributeList);
				return;
			}
			$this->current->addNode($node = new Node($tag, null, $attributeList));
			$this->current = $node;
		}

		public function onCloseTagEvent(string $name) {
			$this->current = $this->current->getParent();
		}

		public function onShortTagEvent(string $tag, array $attributeList) {
			if ($this->node === null) {
				$this->node = new Node($tag, null, $attributeList);
				return;
			}
			$this->current->addNode(new Node($tag, null, $attributeList));
		}

		public function getNode(): INode {
			if ($this->node === null) {
				throw new XmlException('Nothing has been parsed. One cute kitten will be killed because of you!');
			}
			return $this->node;
		}
	}
