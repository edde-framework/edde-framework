<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateFactory;
	use Edde\Common\AbstractObject;

	class TemplateFactory extends AbstractObject implements ITemplateFactory {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IResourceManager $resourceManager
		 * @param IContainer $container
		 */
		public function __construct(IResourceManager $resourceManager, IContainer $container) {
			$this->resourceManager = $resourceManager;
			$this->container = $container;
		}

		public function create(): ITemplate {
			$template = new Template($this->resourceManager);
			return $template;
		}
	}
