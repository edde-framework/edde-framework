<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Translator\Dictionary\CsvDictionaryConverter;
	use Edde\Ext\Protocol\ElementConverter;

	class ConverterManagerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IConverterManager $instance
		 */
		public function configure($instance) {
			static $converterList = [
				ExceptionConverter::class,
				JsonConverter::class,
				NodeConverter::class,
				PhpConverter::class,
				CsvDictionaryConverter::class,
				XmlConverter::class,
				ElementConverter::class,
				PostConverter::class,
			];
			foreach ($converterList as $converter) {
				$instance->registerConverter($this->container->create($converter, [], __METHOD__));
			}
		}
	}
