<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\MacroException;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Node\Node;
	use Edde\Common\Template\AbstractMacro;

	class CssMacro extends AbstractMacro {
		use LazyHtmlGeneratorTrait;
		/**
		 * @var bool
		 */
		protected $minify = false;

		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return [
				'minify-css',
				'css',
				'external-css',
			];
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-css':
					if ($this->minify) {
						throw new MacroException(sprintf('Css minify does not support recursion.'));
					}
					$this->minify = true;
					echo '<?php $cssCompiler = $this->container->create(\'' . IStyleSheetCompiler::class . '\'); ?>' . "\n";
					break;
				case 'css':
					if ($this->minify === false) {
						throw new MacroException(sprintf('Minify/external css tag is not opened.'));
					}
					echo '<?php $cssCompiler->addResource($this->resourceProvider->getResource(' . $this->delimite($node->getAttribute('src')) . ')); ?>' . "\n";
					break;
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-css':
					$this->minify = false;
					$cssNode = new Node('css', null, [
						'href' => '<?=$cssCompiler->compile()->getRelativePath()?>',
					]);
					echo $this->htmlGenerator->generate($cssNode);
					echo '<?php unset($cssCompiler); ?>' . "\n";
					break;
			}
		}
	}
