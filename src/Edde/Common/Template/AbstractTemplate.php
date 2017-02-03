<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateProvider;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractTemplate extends Object implements ITemplate {
		use ConfigurableTrait;
		/**
		 * @var IMacro[]
		 */
		protected $macroList;
		/**
		 * @var ITemplateProvider
		 */
		protected $templateProvider;
		/**
		 * @var IResource[]
		 */
		protected $resourceList;

		/**
		 * @inheritdoc
		 */
		public function registerMacro(IMacro $macro): ITemplate {
			$this->macroList[] = $macro;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplate {
			$this->templateProvider = $templateProvider;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function import(string $name, IResource $resource): ITemplate {
			$this->resourceList[$name] = $resource;
			return $this;
		}
	}
