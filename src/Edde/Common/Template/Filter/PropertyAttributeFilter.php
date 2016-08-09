<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Filter;

	use Edde\Api\Filter\FilterException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlValueControl;
	use Edde\Common\Filter\AbstractFilter;
	use Edde\Common\Template\TemplateFactory;

	class PropertyAttributeFilter extends AbstractFilter {
		/**
		 * @var TemplateFactory
		 */
		protected $templateFactory;

		/**
		 * @param TemplateFactory $abstractTemplate
		 */
		public function __construct(TemplateFactory $abstractTemplate) {
			$this->templateFactory = $abstractTemplate;
		}

		public function input($value, IHtmlControl $htmlControl) {
			if (($htmlControl instanceof IHtmlValueControl) === false) {
				throw new FilterException(sprintf('Control must be instance of [%s].', IHtmlValueControl::class));
			}
			$value = explode('::', $value);
			$schema = $this->templateFactory->getSchema($value[0]);
			$htmlControl->setAttribute('data-schema', $schema->getSchemaName());
			$htmlControl->setAttribute('data-property', $schema->getProperty($value[1])
				->getName());
			return false;
		}
	}
