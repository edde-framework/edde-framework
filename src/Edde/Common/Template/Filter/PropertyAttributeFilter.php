<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Filter;

	use Edde\Api\Filter\FilterException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlValueControl;
	use Edde\Common\Filter\AbstractFilter;
	use Edde\Common\Template\Macro\SchemaMacro;

	class PropertyAttributeFilter extends AbstractFilter {
		/**
		 * @var SchemaMacro
		 */
		protected $schemaMacro;

		public function __construct(SchemaMacro $schemaMacro) {
			$this->schemaMacro = $schemaMacro;
		}

		public function input($value, IHtmlControl $htmlControl) {
			if (($htmlControl instanceof IHtmlValueControl) === false) {
				throw new FilterException(sprintf('Control must be instance of [%s].', IHtmlValueControl::class));
			}
			$value = explode('::', $value);
			$schema = $this->schemaMacro->getSchema($value[0]);
			$htmlControl->setAttribute('data-schema', $schema);
			$htmlControl->setAttribute('data-property', $value[1]);
			return false;
		}
	}
