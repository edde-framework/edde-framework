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
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, $value = null) {
			$source->on(self::EVENT_PRE_ENTER, function () use ($value) {
				echo '<?php foreach(' . $this->delimite($value) . ' as $' . str_repeat('k', $this->foreach) . ' => $' . str_repeat('v', $this->foreach) . ') {?>' . "\n";
				$this->foreach++;
			});
			$source->on(self::EVENT_POST_LEAVE, function () use ($value) {
				echo "<?php } ?>\n";
				$this->foreach--;
			});
		}
	}
