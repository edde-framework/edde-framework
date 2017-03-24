<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\LazyResourceProviderTrait;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\LazyCompilerTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Object;

	class Template extends Object implements ITemplate {
		use LazyResourceProviderTrait;
		use LazyContainerTrait;
		use LazyCompilerTrait;
		/**
		 * @var string
		 */
		protected $execute;

		/**
		 * @inheritdoc
		 */
		public function template(string $name, $context = null, string $namespace = null, ...$parameterList): ITemplate {
			$this->execute = [
				$name,
				$context ? (is_array($context) ? $context : [
					null => $context,
					'.current' => $context,
				]) : null,
				$namespace,
				$parameterList,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function compile(string $name, string $namespace = null, ...$parameterList): IFile {
			$this->resourceProvider->setup();
			$this->compiler->setup();
			return $this->compiler->compile($name, $this->resourceProvider->getResource($name, $namespace, ...$parameterList));
		}

		/**
		 * @inheritdoc
		 */
		public function snippet(string $name, array $context, string $namespace = null, ...$parameterList): ITemplate {
			/** @noinspection PhpIncludeInspection */
			require $this->compile($name, $namespace, ...$parameterList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(): ITemplate {
			if ($this->execute === null) {
				throw new TemplateException(sprintf('You have to prepare template by calling [%s::template()].', static::class));
			}
			$this->snippet($this->execute[0], $this->execute[1], $this->execute[2], ...$this->execute[3]);
			return $this;
		}

		public function __clone() {
			$this->execute = null;
		}
	}
