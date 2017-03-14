<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
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
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, string $name, $value = null) {
			$events = $name === 'foreach' ? [
				self::EVENT_PRE_ENTER,
				self::EVENT_POST_LEAVE,
			] : [
				self::EVENT_POST_ENTER,
				self::EVENT_PRE_LEAVE,
			];
			$source->on($events[0], function () use ($value) {
				echo '<?php foreach(' . $this->delimite($value) . ' as $' . str_repeat('k', $this->foreach) . ' => $' . str_repeat('v', $this->foreach) . ') {?>' . "\n";
				$this->foreach++;
			});
			$source->on($events[1], function () use ($value) {
				echo "<?php } ?>\n";
				$this->foreach--;
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			$attributeList = $node->getAttributeList();
			echo '<?php foreach(' . $this->delimite($attributeList->get('src')) . ' as $' . str_repeat('k', $this->foreach) . ' => $' . str_repeat('v', $this->foreach) . ') {?>' . "\n";
			$this->foreach++;
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			echo "<?php } ?>\n";
			$this->foreach--;
		}
	}
