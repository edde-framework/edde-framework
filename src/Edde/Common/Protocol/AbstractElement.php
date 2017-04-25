<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Object;

	abstract class AbstractElement extends Object implements IElement {
		/**
		 * @var string
		 */
		protected $type;

		/**
		 * @param string $type
		 */
		public function __construct(string $type) {
			$this->type = $type;
		}

		/**
		 * @inheritdoc
		 */
		public function getType(): string {
			return $this->type;
		}
	}
