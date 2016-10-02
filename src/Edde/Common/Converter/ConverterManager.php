<?php
	declare(strict_types = 1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverter;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * Default implementation of a convertion manager.
	 */
	class ConverterManager extends AbstractDeffered implements IConverterManager {
		/**
		 * @var IConverter[]
		 */
		protected $converterList = [];

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function registerConverter(IConverter $converter, bool $force = false): IConverterManager {
			foreach ($converter->getMimeList() as $mime) {
				if (isset($this->converterList[$mime]) && $force === false) {
					throw new ConverterException(sprintf('Converter [%s] has conflict with converter [%s] on mime [%s].', get_class($converter), get_class($this->converterList[$mime]), $mime));
				}
				$this->converterList[$mime] = $converter;
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($source, string $mime, string $target) {
			$this->use();
			/** @noinspection SuspiciousLoopInspection */
			foreach (explode(';', $mime) as $mime) {
				$source = $this->converter($source, $mime, $target);
			}
			return $source;
		}

		/**
		 * execute converter based on a mime type
		 *
		 * @param mixed $source
		 * @param string $mime
		 * @param string $target
		 *
		 * @return mixed
		 * @throws ConverterException
		 */
		protected function converter($source, string $mime, string $target) {
			if (isset($this->converterList[$mime]) === false) {
				throw new ConverterException(sprintf('Cannot convert unknown source mime [%s] to [%s].', $mime, $target));
			}
			return $this->converterList[$mime]->convert($source, $target);
		}
	}
