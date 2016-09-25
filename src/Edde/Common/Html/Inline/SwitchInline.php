<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;

	/**
	 * Switch inline support.
	 */
	class SwitchInline extends AbstractHtmlInline {
		/**
		 * "In C we had to code our own bugs. In C++ we can inherit them."
		 *
		 * - Anonymous
		 */
		public function __construct() {
			parent::__construct('m:switch', true);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$macro->switch(new Node('switch', null, ['src' => $this->extract($macro)]));
		}
	}
