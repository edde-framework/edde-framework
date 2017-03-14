<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	class ForeachMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, $value = null) {
			$source->on(self::EVENT_PRE_ENTER, function () use ($value) {
				echo '<?php foreach(' . $this->delimite($value) . ' as $a => $b) {?>' . "\n";
			});
			$source->on(self::EVENT_POST_LEAVE, function () use ($value) {
				echo "<?php } ?>\n";
			});
		}
	}
