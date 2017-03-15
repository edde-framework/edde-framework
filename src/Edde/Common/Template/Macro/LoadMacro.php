<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	class LoadMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, string $name, $value = null) {
			$source->on(self::EVENT_POST_ENTER, function () use ($template, $iterator, $node, $value) {
				echo '<?php include $this->templateProvider->getResource(' . $this->delimite($value) . ')->getPath(); ?>';
			});
		}

		/**
		 * @inheritdoc
		 */
		protected function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			echo '<?php include $this->templateProvider->getResource(' . $this->delimite($node->getAttribute('src')) . ')->getPath(); ?>';
		}
	}
