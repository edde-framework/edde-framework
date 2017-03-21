<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\LazyResourceProviderTrait;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\LazyCompilerTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractTemplateManager extends Object implements ITemplateManager {
		use LazyContainerTrait;
		use LazyResourceProviderTrait;
		use LazyCompilerTrait;
		use ConfigurableTrait;
		/**
		 * @var ICompiler
		 */
		protected $compiler;

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
		public function snippet(string $name, string $namespace = null, ...$parameterList): IFile {
			$this->resourceProvider->setup();
			$this->compiler->setup();
			return $this->compiler->compile($name, $this->resourceProvider->getResource($name, $namespace, ...$parameterList));
		}

		/**
		 * @inheritdoc
		 */
		public function template(string $name, $context = null, string $namespace = null, ...$parameterList) {
			/** @noinspection PhpIncludeInspection */
			require $this->snippet($name, $namespace, ...$parameterList);
		}
	}
