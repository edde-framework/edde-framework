<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverter;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Object;

	/**
	 * Default implementation of a convertion manager.
	 */
	class ConverterManager extends Object implements IConverterManager {
		use ConfigurableTrait;
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
		public function convert($convert, string $source, array $targetList) {
			$exception = null;
			$unknown = true;
			foreach ($targetList as $target) {
				if (isset($this->converterList[$mime = ($source . '|' . $target)])) {
					$unknown = false;
					try {
						return $this->converterList[$mime]->convert($convert, $source, $target, $mime);
					} catch (\Exception $exception) {
					}
				}
			}
			throw new ConverterException(sprintf('Cannot convert %ssource mime [%s] to any of [%s].', $unknown ? 'unknown/unsupported ' : '', $source, implode(', ', $targetList)), 0, $exception);
		}
	}
