<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class ForeachMacro extends AbstractMacro {
		/**
		 * @var int
		 */
		protected $foreach = 1;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return [
				'foreach',
				'inner-foreach',
			];
		}

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			$events = $name === $this->getNameList()[0] ? [
				self::EVENT_PRE_ENTER,
				self::EVENT_POST_LEAVE,
			] : [
				self::EVENT_POST_ENTER,
				self::EVENT_PRE_LEAVE,
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
			$this->macroOpen($node->getAttribute('src'));
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->macroClose();
		}

		protected function macroOpen($value) {
			echo '<?php foreach(' . $this->delimite($value) . ' as $' . str_repeat('k', $this->foreach) . ' => $' . str_repeat('v', $this->foreach) . ') {?>' . "\n";
			$this->foreach++;
		}

		protected function macroClose() {
			echo "<?php } ?>\n";
			$this->foreach--;
		}
	}
