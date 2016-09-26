<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

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
		 * @throws MacroException
		 */
		public function compileInline(INode $macro, ICompiler $compiler) {
			$compiler->block($this->extract($macro, 't:' . $this->getName()), [$macro]);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$compiler->block($this->attribute($macro, $compiler, 'id'), $macro->getNodeList());
		}
	}
