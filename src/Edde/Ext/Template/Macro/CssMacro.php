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
		protected $external = false;

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
				case 'external-css':
					if ($this->external) {
						throw new MacroException(sprintf('Css external does not support recursion.'));
					}
					$this->external = true;
					break;
				case 'css':
					if ($this->minify) {
						echo '<?php $cssCompiler->addResource($this->resourceProvider->getResource(' . $this->attribute($node, 'src') . ')); ?>' . "\n";
					} else if ($this->external) {
						echo $this->htmlGenerator->generate(new Node('link', null, [
							'href' => $this->attribute($node, 'src'),
							'rel'  => 'stylesheet',
							'type' => 'text/css',
						]));
					} else {
						throw new MacroException(sprintf('Minify/external css tag is not opened.'));
					}
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
					echo $this->htmlGenerator->generate(new Node('link', null, [
						'href' => function () {
							return '<?=$cssCompiler->compile()->getRelativePath()?>';
						},
						'rel'  => 'stylesheet',
						'type' => 'text/css',
					]));
					echo '<?php unset($cssCompiler); ?>' . "\n";
					break;
				case 'external':
					$this->external = false;
					break;
			}
		}
	}
