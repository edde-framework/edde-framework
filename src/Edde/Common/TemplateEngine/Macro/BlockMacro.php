<?php
	declare(strict_types = 1);

	namespace Edde\Common\TemplateEngine\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\TemplateEngine\ICompiler;
	use Edde\Api\TemplateEngine\MacroException;
	use Edde\Common\TemplateEngine\AbstractMacro;

	/**
	 * Named block definition macro; defined block can be reffered later.
	 */
	class BlockMacro extends AbstractMacro {
		/**
		 * It is better to "release and patch" than not to release at all.
		 */
		public function __construct() {
			parent::__construct('block');
		}

		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->switchlude($macro, 'id');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 * @throws NodeException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			parent::compile($macro, $compiler);
			$compiler->block($this->attribute($macro, $compiler, 'id'), clone $macro);
		}
	}
