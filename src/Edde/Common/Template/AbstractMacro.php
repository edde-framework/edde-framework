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
		public function inline(ITemplate $template, \Iterator $iterator, INode $node, $value = null) {
		}

		/**
		 * @inheritdoc
		 */
		public function traverse(INode $node, ...$parameters): ITreeTraversal {
			/** @var $template ITemplate */
			list($template) = $parameters;
			return $template->getMacro($node->getName(), $node);
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, \Iterator $iterator, ...$parameters) {
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
