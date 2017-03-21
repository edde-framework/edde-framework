<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\AbstractMacro;

	class SwitchMacro extends AbstractMacro {
		/**
		 * @var \SplStack
		 */
		protected $switch;

		public function __construct() {
			$this->switch = new \SplStack();
		}

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return [
				'switch',
				'case',
				'inner-case',
			];
		}

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ICompiler $compiler, \Iterator $iterator, INode $node, string $name, $value = null) {
			switch ($name) {
				case'switch':
					$source->on(self::EVENT_PRE_ENTER, function () use ($value) {
						$this->macroOpenSwitch($value);
					});
					$source->on(self::EVENT_POST_LEAVE, function () {
						$this->macroCloseSwitch();
					});
					break;
				case 'case':
				case 'inner-case':
					$events = $name === 'case' ? [
						self::EVENT_PRE_ENTER,
						self::EVENT_POST_LEAVE,
					] : [
						self::EVENT_POST_ENTER,
						self::EVENT_PRE_LEAVE,
					];
					$source->on($events[0], function () use ($value) {
						$this->macroOpenCase($value);
					});
					$source->on($events[1], function () {
						$this->macroCloseCase();
					});
					break;
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'switch':
					$this->macroOpenSwitch($node->getAttribute('src'));
					break;
				case 'case':
					$this->macroOpenCase($node->getAttribute('value'));
					break;
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'switch':
					$this->macroCloseSwitch();
					break;
				case 'case':
					$this->macroCloseCase();
					break;
			}
		}

		protected function macroOpenSwitch($value) {
			echo '<?php $' . ($switch = 'switch_' . sha1((string)$this->switch->count())) . ' = ' . $this->delimite($value) . '; ?>';
			$this->switch->push($switch);
		}

		protected function macroCloseSwitch() {
			echo '<?php unset($' . ($switch = $this->switch->pop()) . '); ?>';
		}

		protected function macroOpenCase($value) {
			echo '<?php if($' . $this->switch->top() . ' === ' . $value . ') {?>' . "\n";
		}

		protected function macroCloseCase() {
			echo "<?php } ?>\n";
		}
	}
