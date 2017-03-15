<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Common\Node\SkipException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class SnippetMacro extends AbstractMacro {
		use LazyTemplateDirectoryTrait;

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, string $name, $value = null) {
			$source->on(self::EVENT_PRE_ENTER, function () use ($template, $iterator, $node, $value) {
				ob_start();
				$macro = $this->traverse($node, $template);
				$iterator->next();
				$macro->enter($node, $iterator, $template);
				$macro->node($node, $iterator, $template);
				$macro->leave($node, $iterator, $template);
				$this->templateDirectory->save($this->getSnippetFile($node, $value), ob_get_clean());
				throw new SkipException();
			});
		}

		/**
		 * @inheritdoc
		 */
		public function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
			ob_start();
		}

		/**
		 * @inheritdoc
		 */
		public function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->templateDirectory->save($this->getSnippetFile($node), ob_get_clean());
		}

		/**
		 * compute snippet filename
		 *
		 * @param INode       $node
		 * @param string|null $name
		 *
		 * @return string
		 */
		protected function getSnippetFile(INode $node, string $name = null): string {
			return 'snippet-' . StringUtils::webalize($name ?? (string)$node->getAttribute('name')) . '.php';
		}
	}
