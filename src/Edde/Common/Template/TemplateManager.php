<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\File\FileException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\File\File;
	use Edde\Common\Object;

	/**
	 * Default implementation of a template manager.
	 */
	class TemplateManager extends Object implements ITemplateManager {
		use CacheTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function template(string $template, array $importList = []) {
			if ($result = $this->cache->load($cacheId = $template . implode(',', $importList))) {
				return $result;
			}
			foreach ($importList as &$import) {
				$import = new File($import);
			}
			unset($import);
			/** @var $compiler ICompiler */
			$compiler = $this->container->create(ICompiler::class, [], __METHOD__);
			$compiler->setSource(new File($template));
			$compiler->setup();
			return $this->cache->save($cacheId, $compiler->template($importList));
		}

		protected function handleSetup() {
			parent::handleSetup();
			$this->cache();
		}
	}
