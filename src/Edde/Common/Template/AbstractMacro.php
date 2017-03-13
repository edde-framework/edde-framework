<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Node\AbstractTreeTraversal;

	abstract class AbstractMacro extends AbstractTreeTraversal implements IMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(ITemplate $template, \Iterator $iterator, INode $node, string $name, string $value = null) {
		}

		/**
		 * @inheritdoc
		 */
		public function traverse(INode $node, ...$parameters): ITreeTraversal {
			/** @var $template ITemplate */
			$template = reset($parameters);
			return $template->getMacro($node);
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, \Iterator $iterator, ...$parameters) {
			/** @var $template ITemplate */
			list($template) = $parameters;
			$attributeList = $node->getAttributeList();
			$inlineList = $attributeList->get('t', []);
			$attributeList->remove('t');
			foreach ($inlineList as $name => $value) {
				$template->inline($iterator, $node, $name, $value);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function register(ITemplate $template): IMacro {
			foreach ($this->getNameList() as $name) {
				$template->registerMacro($name, $this);
			}
			return $this;
		}
	}
