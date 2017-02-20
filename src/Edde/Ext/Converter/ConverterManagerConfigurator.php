<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Translator\Dictionary\CsvDictionaryConverter;

	class ConverterManagerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param IConverterManager $instance
		 */
		public function config($instance) {
			$instance->registerConverter($this->container->create(HttpConverter::class));
			$instance->registerConverter($this->container->create(JsonConverter::class));
			$instance->registerConverter($this->container->create(NodeConverter::class));
			$instance->registerConverter($this->container->create(PhpConverter::class));
			$instance->registerConverter($this->container->create(RedirectConverter::class));
			$instance->registerConverter($this->container->create(CsvDictionaryConverter::class));
			$instance->registerConverter($this->container->create(XmlConverter::class));
			$instance->registerConverter($this->container->create(ExceptionConverter::class));
		}
	}
