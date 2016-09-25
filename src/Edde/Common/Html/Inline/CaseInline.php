<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;

	/**
	 * Case inline support.
	 */
	class CaseInline extends AbstractHtmlInline {
		/**
		 * A computer salesman, a hardware engineer, and a software engineer are driving in a car together. Suddenly the right rear tire blows out, and the car rolls to a stop. Our three heroes pile out to investigate.
		 *
		 * The salesman announces sadly, "Time to buy a new car!"
		 *
		 * The hardware engineer says, "Well, first let's try swapping the front and rear tires, and see if that fixes it."
		 *
		 * Replies the software engineer, "Now, let's just restart the engine, and maybe the problem will go away by itself."
		 */
		public function __construct() {
			parent::__construct('m:case', true);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$macro->switch(new Node('case', null, ['name' => $this->extract($macro)]));
		}
	}
