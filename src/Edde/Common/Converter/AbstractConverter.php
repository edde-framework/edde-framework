<?php
	declare(strict_types = 1);

	namespace Edde\Common\Converter;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IConverter;
	use Edde\Common\AbstractObject;

	abstract class AbstractConverter extends AbstractObject implements IConverter, ILazyInject {
		/**
		 * @var string[]
		 */
		protected $mimeList;

		/**
		 * @param array $mimeList
		 */
		public function __construct(array $mimeList) {
			$this->mimeList = $mimeList;
		}

		public function getMimeList(): array {
			return $this->mimeList;
		}

		protected function unsupported($source, string $target) {
			throw new ConverterException(sprintf('Cannot convert unsupported type [%s] to [%s] in [%s].', is_object($source) ? get_class($source) : gettype($source), $target, static::class));
		}

		protected function exception(string $target) {
			throw new ConverterException(sprintf('Unsuported convertion in [%s] from [%s] to [%s].', static::class, implode(', ', $this->mimeList), $target));
		}
	}
