<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IContext;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;

	abstract class AbstractContext extends AbstractResourceProvider implements IContext {
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
		public function cascade(string $delimiter): array {
			return [];
		}

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, ...$parameters): IResource {
			foreach (array_merge([null], $this->cascade('/')) as $cascade) {
				if ($this->resourceManager->hasResource($cascade . $name, ...$parameters)) {
					return $this->resourceManager->getResource($cascade . $name, ...$parameters);
				}
			}
			throw new UnknownResourceException(sprintf('Requested unknown resource [%s].', $name));
		}
	}
