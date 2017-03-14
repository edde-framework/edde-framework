<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class InnerForeachMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, $value = null) {
			$value = StringUtils::toCamelHump($value);
			$source->on(self::EVENT_POST_ENTER, function () use ($value) {
				echo '<?php foreach($context[null]->' . $value . " as \$a => \$b) {?>";
			});
			$source->on(self::EVENT_PRE_LEAVE, function () use ($value) {
				echo "<?php } ?>\n";
			});
		}
	}
