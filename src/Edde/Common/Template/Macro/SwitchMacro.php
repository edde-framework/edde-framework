<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
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
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, string $name, $value = null) {
			switch ($name) {
				case'switch':
					$source->on(self::EVENT_PRE_ENTER, function () use ($value) {
						echo '<?php $' . ($switch = 'switch_' . sha1((string)$this->switch->count())) . ' = ' . $this->delimite($value) . '; ?>';
						$this->switch->push($switch);
					});
					$source->on(self::EVENT_POST_LEAVE, function () {
						$this->switch->pop();
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
						echo '<?php if($' . $this->switch->top() . ' === ' . $value . ') {?>' . "\n";
					});
					$source->on($events[1], function () use ($value) {
						echo "<?php } ?>\n";
					});
					break;
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			$attributeList = $node->getAttributeList();
			switch ($node->getName()) {
				case 'switch':
					echo '<?php $' . ($switch = 'switch_' . sha1((string)$this->switch->count())) . ' = ' . $this->delimite($attributeList->get('src')) . '; ?>';
					$this->switch->push($switch);
					break;
				case 'case':
					echo '<?php if($' . $this->switch->top() . ' === ' . $attributeList->get('value') . ') {?>' . "\n";
					break;
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'switch':
					$this->switch->pop();
					break;
				case 'case':
					echo "<?php } ?>\n";
					break;
			}
		}
	}
