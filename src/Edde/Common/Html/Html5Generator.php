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
		/**
		 * @inheritdoc
		 */
		public function getTagList(): array {
			return [
				'html',
				'head',
				'meta',
				'title',
				'link',
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

		/**
		 * @inheritdoc
		 */
		public function generate(INode $root): string {
			ob_start();
			foreach (NodeIterator::recursive($root, true) as $node) {
				echo $this->open($node);
				echo $this->close($node);
			}
			return ob_get_clean();
		}

		/**
		 * @inheritdoc
		 */
		public function open(INode $node, int $level = null): string {
			$content = '';
			switch ($node->getName()) {
				case 'html':
					$content .= "<!DOCTYPE html>\n";
					break;
			}
			$content .= $indentation = str_repeat("\t", $level ?? $node->getLevel());
			$content .= '<' . $node->getName();
			foreach ($node->getAttributeList() as $name => $value) {
				if ($value instanceof IAttributeList) {
					continue;
				}
				$content .= ' ' . $name . '="' . (is_callable($value) ? $value() : htmlspecialchars($value, ENT_QUOTES)) . '"';
			}
			$content .= '>';
			if ($node->isLeaf() === false) {
				$content .= "\n";
			}
			return $content;
		}

		/**
		 * @inheritdoc
		 */
		public function close(INode $node, int $level = null): string {
			switch ($node->getName()) {
				case 'meta':
				case 'link':
					return '';
					break;
			}
			$content = $node->isLeaf() === false ? str_repeat("\t", $level?? $node->getLevel()) : '';
			$content .= '</' . $node->getName() . '>';
			return $content;
		}
	}
