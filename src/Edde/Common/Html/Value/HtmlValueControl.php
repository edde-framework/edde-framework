<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Value;

	use Edde\Api\Control\Html\IHtmlValueControl;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\Html\AbstractHtmlControl;

	abstract class HtmlValueControl extends AbstractHtmlControl implements IHtmlValueControl {
		/**
		 * @var ISchemaProperty
		 */
		protected $schemaProperty;

		/**
		 * @param ISchemaProperty $schemaProperty
		 */
		public function __construct(ISchemaProperty $schemaProperty = null) {
			$this->schemaProperty = $schemaProperty;
		}

		protected function prepare() {
			parent::prepare();
			$this->addClass('edde-value');
			if ($this->schemaProperty !== null) {
				$this->addAttributeList([
					'data-class' => $this->schemaProperty->getSchema()
						->getSchemaName(),
					'data-property' => $this->schemaProperty->getName(),
				]);
			}
		}
	}
