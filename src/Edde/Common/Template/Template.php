<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IFile;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Usable\AbstractUsable;

	class Template extends AbstractUsable implements ITemplate {
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var IFile
		 */
		protected $file;
		/**
		 * @var mixed
		 */
		protected $instnace;

		/**
		 * @param IFile $file
		 */
		public function __construct(IFile $file) {
			$this->file = $file;
		}

		public function getFile(): IFile {
			return $this->file;
		}

		public function getInstance(IContainer $container = null) {
			if ($this->instnace === null) {
				if (class_exists($class = $this->getClass()) === false) {
					(function (IFile $file) {
						require_once($file->getPath());
					})($this->file);
				}
				if ($container !== null) {
					if ($container->has($class) === false) {
						$container->registerFactory($class, FactoryFactory::create($class, $class, false));
					}
					return $this->instnace = $container->create($class);
				}
				$this->instnace = new $class;
			}
			return $this->instnace;
		}

		public function getClass(): string {
			if ($this->class === null) {
				$this->class = str_replace('.php', '', $this->file->getUrl()
					->getResourceName());
			}
			return $this->class;
		}

		protected function prepare() {
		}
	}
