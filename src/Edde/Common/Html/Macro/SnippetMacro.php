<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Node\Node;

	/**
	 * Snippet is piece of template which can be called without any other dependencies.
	 */
	class SnippetMacro extends AbstractHtmlMacro {
		/**
		 * My brother-in-law was typing on his new laptop. His ten-year-old daughter sneaked up behind him. Then she turned and ran into the kitchen, squealing to the rest of the family, "I know Daddy's password! I know Daddy's password!"
		 *
		 * "What is it? I asked her eagerly.
		 *
		 * Proudly she replied, "Asterisk, asterisk, asterisk, asterisk, asterisk!"
		 */
		public function __construct() {
			parent::__construct('t:snippet');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function compileInline(INode $macro, ICompiler $compiler, INode $root) {
			$macro->setMeta('snippet', true);
			$compiler->block($this->extract($macro, $this->getName()), (new Node('snippet-root'))->addNode($macro));
		}
	}
