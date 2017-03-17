<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IContext;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Common\Object;

	abstract class AbstractContext extends Object implements IContext {
		use LazyResourceManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function getGuid(): string {
			return sha1($this->getId());
		}

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, ...$parameters): IResource {
			foreach ($this->cascade($name) as $name) {
				if ($this->resourceManager->hasResource($name, ...$parameters)) {
					return $this->resourceManager->getResource($name, ...$parameters);
				}
			}
			throw new ApplicationException(sprintf('Cannot find resource by the given name [%s].', $name));
		}
	}
