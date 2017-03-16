<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractTemplateManager extends Object implements ITemplateManager {
		use LazyContainerTrait;
		use LazyResourceManagerTrait;
		use ConfigurableTrait;
		/**
		 * @var ITemplate
		 */
		protected $template;

		/**
		 * @inheritdoc
		 */
		public function compile(array $nameList): ITemplateManager {
			foreach ($nameList as $name) {
				$this->snippet($name);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function snippet(string $name): IFile {
			$this->resourceManager->setup();
			$template = $this->createTemplate();
			return $template->compile($name, $this->resourceManager->getResource($name));
		}

		protected function createTemplate(): ITemplate {
			return $this->template ? $this->template : $this->template = $this->container->create(ITemplate::class);
		}
	}
