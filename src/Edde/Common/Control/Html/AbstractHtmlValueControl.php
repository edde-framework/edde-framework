<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Control\Html\IHtmlValueControl;
	use Edde\Api\Schema\IProperty;

	abstract class AbstractHtmlValueControl extends AbstractHtmlControl implements IHtmlValueControl {
		/**
		 * @var IProperty
		 */
		protected $property;

		/**
		 * @param IProperty $property
		 */
		public function __construct(IProperty $property = null) {
			$this->property = $property;
		}

		protected function prepare() {
			parent::prepare();
			$this->addClass('edde-value');
			if ($this->property !== null) {
				$this->addAttributeList([
					'data-class' => $this->property->getSchema()
						->getSchemaName(),
					'data-property' => $this->property->getName(),
				]);
			}
		}
	}
