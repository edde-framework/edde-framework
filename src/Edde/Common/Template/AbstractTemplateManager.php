<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\ITemplateProvider;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractTemplateManager extends Object implements ITemplateManager {
		use ConfigurableTrait;

		protected $templateProviderList = [];

		/**
		 * @inheritdoc
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplateManager {
			$this->templateProviderList[] = $templateProvider;
			return $this;
		}
	}
