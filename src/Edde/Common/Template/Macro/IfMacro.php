<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	class IfMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return [
				'if',
				'inner-if',
			];
		}

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, string $name, $value = null) {
			$events = $name === 'if' ? [
				self::EVENT_PRE_ENTER,
				self::EVENT_POST_LEAVE,
			] : [
				self::EVENT_POST_ENTER,
				self::EVENT_PRE_LEAVE,
			];
			$source->on($events[0], function () use ($value) {
				echo '<?php if(' . $this->delimite($value) . ') {?>' . "\n";
			});
			$source->on($events[1], function () use ($value) {
				echo "<?php } ?>\n";
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			$attributeList = $node->getAttributeList();
			echo '<?php if(' . $this->delimite($attributeList->get('src')) . ') {?>' . "\n";
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			echo "<?php } ?>\n";
		}
	}
