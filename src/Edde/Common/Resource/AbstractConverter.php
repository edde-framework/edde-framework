<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IConverter;
	use Edde\Common\AbstractObject;

	abstract class AbstractConverter extends AbstractObject implements IConverter {
		/**
		 * @var string
		 */
		protected $source;

		/**
		 * @param string $source
		 */
		public function __construct($source) {
			$this->source = $source;
		}

		public function getMime(): string {
			return $this->source;
		}
	}
