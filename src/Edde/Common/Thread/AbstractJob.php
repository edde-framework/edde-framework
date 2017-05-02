<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Thread\IJob;
	use Edde\Common\Object;

	abstract class AbstractJob extends Object implements IJob {
		/**
		 * @var IElement
		 */
		protected $element;

		public function __construct(IElement $element) {
			$this->element = $element;
		}
	}
