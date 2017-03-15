<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\ITemplateProvider;
	use Edde\Common\Config\ConfigurableTrait;

	abstract class AbstractTemplateManager extends AbstractTemplateProvider implements ITemplateManager {
		use LazyContainerTrait;
		use ConfigurableTrait;
		/**
		 * @var ITemplateProvider[]
		 */
		protected $templateProviderList = [];

		/**
		 * @inheritdoc
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplateManager {
			$this->templateProviderList[] = $templateProvider;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name) {
			foreach ($this->templateProviderList as $templateProvider) {
				if ($resource = $templateProvider->getResource($name)) {
					return $resource;
				}
			}
			throw new UnknownTemplateException(sprintf('Requested template name [%s] cannot be found%s.', $name, empty($this->templateProviderList) ? '; there are no template providers - please register instance of [' . ITemplateProvider::class . ']' : ''));
		}

		protected function createTemplate(): ITemplate {
			return $this->container->create(ITemplate::class);
		}
	}
