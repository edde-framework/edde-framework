<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\IConvertable;
	use Edde\Api\Converter\IConverter;
	use Edde\Common\Object;

	abstract class AbstractConvertable extends Object implements IConvertable {
		/**
		 * @var IConverter
		 */
		protected $converter;
		protected $result;
		/**
		 * @var mixed
		 */
		protected $convert;
		/**
		 * @var string
		 */
		protected $source;
		/**
		 * @var string
		 */
		protected $target;
		/**
		 * @var string
		 */
		protected $mime;

		/**
		 * A biologist, a chemist and a statistician are out hunting.
		 * The biologist shoots at a deer and misses 5th to the left.
		 * The chemist takes a shot and misses 5th to the right.
		 * The statistician yells "We got 'em!"
		 *
		 * @param IConverter $converter
		 * @param mixed      $convert
		 * @param string     $source
		 * @param string     $target
		 */
		public function __construct(IConverter $converter, $convert, string $source, string $target, string $mime) {
			$this->converter = $converter;
			$this->convert = $convert;
			$this->source = $source;
			$this->target = $target;
			$this->mime = $mime;
		}

		public function getTarget(): string {
			return $this->target;
		}

		public function convert() {
			if ($this->result === null) {
				$this->result = $this->converter->convert($this->convert, $this->source, $this->target, $this->mime);
			}
			return $this->result;
		}
	}
