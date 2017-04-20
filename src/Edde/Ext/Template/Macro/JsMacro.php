<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template\Macro;

	use Edde\Api\Html\LazyHtmlGeneratorTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\MacroException;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Common\Node\Node;
	use Edde\Common\Template\AbstractMacro;

	class JsMacro extends AbstractMacro {
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
				'minify-js',
				'js',
				'external-js',
			];
		}

		/**
		 * @inheritdoc
		 */
		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-js':
					if ($this->minify) {
						throw new MacroException(sprintf('Js minify does not support recursion.'));
					}
					$this->minify = true;
					echo '<?php $this->resourceProvider->setup(); ?>' . "\n";
					echo '<?php $jsCompiler = $this->container->create(\'' . IJavaScriptCompiler::class . '\'); ?>' . "\n";
					break;
				case 'external-js':
					if ($this->external) {
						throw new MacroException(sprintf('Js external does not support recursion.'));
					}
					$this->external = true;
					break;
				case 'js':
					if ($this->minify) {
						echo '<?php $jsCompiler->addResource($this->resourceProvider->getResource(' . $this->attribute($node, 'src') . ')); ?>' . "\n";
					} else if ($this->external) {
						echo $this->htmlGenerator->generate(new Node('script', null, array_merge([
							'src'  => $this->attribute($node, 'src', true),
							'type' => 'text/javascript',
						], $node->getAttributeList()
							->array())));
					} else {
						throw new MacroException(sprintf('Minify/external js tag is not opened.'));
					}
					break;
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			switch ($node->getName()) {
				case 'minify-js':
					$this->minify = false;
					echo $this->htmlGenerator->generate(new Node('script', null, [
						'src'  => function () {
							return '<?=$jsCompiler->compile()->getRelativePath()?>';
						},
						'type' => 'text/javascript',
					]));
					echo '<?php unset($jsCompiler); ?>' . "\n";
					break;
				case 'external':
					$this->external = false;
					break;
			}
		}
	}
