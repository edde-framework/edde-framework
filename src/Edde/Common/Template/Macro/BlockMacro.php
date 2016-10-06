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
		public function compileInline(INode $macro, ICompiler $compiler, INode $root) {
			$root->addNode($root = new Node('block', null, ['id' => $this->extract($macro, self::COMPILE_PREFIX . $this->getName())]));
			$root->addNode($macro, true);
			return $root;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */

		/**
		 * @inheritdoc
		 * @throws MacroException
		 * @throws NodeException
		 */
		public function compile(INode $macro, ICompiler $compiler, INode $root) {
			$compiler->block($id = $this->attribute($macro, $compiler, 'id'), $macro);
		}
	}
