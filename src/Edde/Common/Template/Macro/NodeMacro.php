<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class NodeMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$events = [
				self::EVENT_PRE_ENTER,
				self::EVENT_POST_LEAVE,
			];
			$source->on($events[0], function () use ($value) {
				$this->macroOpen($value);
			});
			$source->on($events[1], function () {
				$this->macroClose();
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macroOpen($node->getAttribute('target'));
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macroClose();
		}

		public function macroOpen($value) {
			echo '<?php ob_start(); ?>';
		}

		public function macroClose() {
			echo '<?php ';
			?>
			$node = $this->converterManager->convert(ob_get_clean(), 'string', [\Edde\Api\Node\INode::class])->convert();
			<?php
			echo '?>';
		}
	}
