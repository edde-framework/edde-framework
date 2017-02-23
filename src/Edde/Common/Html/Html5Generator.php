<?php
	declare(strict_types=1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\IHtmlGenerator;
	use Edde\Api\Node\IAttributeList;
	use Edde\Api\Node\INode;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Object;

	/**
	 * Common html5 generator.
	 */
	class Html5Generator extends Object implements IHtmlGenerator {
		public function getTagList(): array {
			return [
				'html',
				'head',
				'title',
				'body',
				'div',
				'span',
				'img',
				'table',
				'thead',
				'tbody',
				'tfoot',
				'td',
				'tr',
				'th',
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6',
				'section',
				'p',
			];
		}

		public function generate(INode $root): string {
			foreach (NodeIterator::recursive($root, true) as $node) {
				$this->open($node);
				$this->close($node);
			}
		}

		public function open(INode $node): string {
			$content = $indentation = str_repeat("\t", $node->getLevel());
			$content .= '<' . $node->getName();
			foreach ($node->getAttributeList() as $name => $value) {
				if ($value instanceof IAttributeList) {
					continue;
				}
				$content .= ' ' . $name . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
			}
			$content .= '>';
			if ($node->isLeaf() === false) {
				$content .= "\n";
			}
			return $content;
		}

		public function close(INode $node): string {
			$content = $node->isLeaf() === false ? str_repeat("\t", $node->getLevel()) : '';
			$content .= '</' . $node->getName();
			$content .= '>';
			return $content;
		}
	}
