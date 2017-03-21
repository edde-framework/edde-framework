<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class IncludeMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$source->on(self::EVENT_POST_ENTER, function () use ($value) {
				$this->macro($value);
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macro($node->getAttribute('src'));
		}

		protected function macro($value) {
			echo '<?php include __DIR__.\'/snippet-\'.' . $this->delimite($value) . '.\'.php\'; ?>';
		}
	}
