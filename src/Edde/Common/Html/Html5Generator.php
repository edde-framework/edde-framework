<?php
	declare(strict_types=1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\IHtmlGenerator;
	use Edde\Api\Node\INode;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Object;

	/**
	 * Common html5 generator.
	 */
	class Html5Generator extends Object implements IHtmlGenerator {
		public function getTagList(): array {
			return [
				'div',
			];
		}

		public function generate(INode $root): string {
			foreach (NodeIterator::recursive($root, true) as $node) {
				$this->open($node);
				$this->close($node);
			}
		}

		public function open(INode $node): string {
		}

		public function close(INode $node): string {
		}
	}
