<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\File\File;
	use Edde\Common\Usable\AbstractUsable;

	class TemplateManager extends AbstractUsable implements ITemplateManager {
		use LazyInjectTrait;

		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function registerMacro(IMacro $macro): ITemplateManager {
			$this->macroList[] = $macro;
			return $this;
		}

		public function registerMacroList(array $macroList): ITemplateManager {
			$this->macroList = array_merge($this->macroList, $macroList);
			return $this;
		}

		public function template(string $file): ITemplate {
			return $this->compile(new File($file));
		}

		public function compile(IResource $resource): ITemplate {
			if ((($root = $this->resourceManager->resource($resource)) instanceof INode) === false) {
				throw new TemplateException(sprintf('Resource handler for [%s] must return [%s].', $resource->getUrl(), INode::class));
			}
			$template = new Template(sha1((string)$resource->getUrl()) . '_EddeTemplate');

			$this->handle($root, $resource);
		}

		public function handle(INode $node, IResource $resource) {
			if (isset($this->macroList[$name = $node->getName()]) === false) {
				throw new TemplateException(sprintf('Unknown macro [%s] in template resource [%s].', $node->getPath(), $resource->getUrl()));
			}
			$this->macroList[$name]->run($node);
		}

		protected function prepare() {
			$macroList = $this->macroList;
			$this->macroList = [];
			foreach ($macroList as $macro) {
				foreach ($macro->getMacroList() as $name) {
					$this->macroList[$name] = $macro;
				}
			}
		}
	}
