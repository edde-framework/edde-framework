<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class TemplateMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$source->on(self::EVENT_POST_ENTER, function () use ($node, $value) {
				$this->checkAttribute($node, 'template');
				$this->macro($value, $node->getAttribute('template')->get('context'));
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macro($this->attribute($node, 'name'), $this->attribute($node, 'context'));
		}

		protected function macro($name, $context) {
			echo sprintf("<?php \$this->templateManager->template()->template(%s, \$this->container->create(%s, [], %s))->execute(); ?>", $name = $this->delimite($name, true), $this->delimite($context, true), '\'Template macro: \'.' . $name);
		}
	}
