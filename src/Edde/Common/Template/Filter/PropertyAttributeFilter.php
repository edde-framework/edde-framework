<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Filter;

	use Edde\Api\Filter\FilterException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlValueControl;
	use Edde\Common\Filter\AbstractFilter;
	use Edde\Common\Template\AbstractTemplate;

	class PropertyAttributeFilter extends AbstractFilter {
		/**
		 * @var AbstractTemplate
		 */
		protected $abstractTemplate;

		/**
		 * @param AbstractTemplate $abstractTemplate
		 */
		public function __construct(AbstractTemplate $abstractTemplate) {
			$this->abstractTemplate = $abstractTemplate;
		}

		public function input($value, IHtmlControl $htmlControl) {
			if (($htmlControl instanceof IHtmlValueControl) === false) {
				throw new FilterException(sprintf('Control must be instance of [%s].', IHtmlValueControl::class));
			}
			$value = explode('::', $value);
			$schema = $this->abstractTemplate->getSchema($value[0]);
			$htmlControl->setAttribute('data-schema', $schema->getSchemaName());
			$htmlControl->setAttribute('data-property', $schema->getProperty($value[1])
				->getName());
			return false;
		}
	}
