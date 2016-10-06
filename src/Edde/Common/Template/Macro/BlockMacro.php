<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;
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
		 * @throws NodeException
		 */
		public function compileInline(INode $macro, ICompiler $compiler) {
			$compiler->block($id = $this->extract($macro, self::COMPILE_PREFIX . $this->getName()), $this->block(clone $macro, $id));
		}

		/**
		 * @param INode $macro
		 * @param string $id
		 *
		 * @return INode
		 * @throws NodeException
		 */
		protected function block(INode $macro, string $id): INode {
			return (new Node('block-root', null, ['id' => $id]))->addNode($macro, true, true);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */

		/**
		 * @inheritdoc
		 * @throws MacroException
		 * @throws NodeException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$compiler->block($id = $this->attribute($macro = clone $macro, $compiler, 'id'), $this->block($macro, $id));
			parent::compile($macro, $compiler);
		}
	}
