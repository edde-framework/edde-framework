<?php
	declare(strict_types=1);

	namespace Edde\Ext\Control;

	use Edde\Api\Control\IControl;
	use Edde\Api\File\IDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Common\File\Directory;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;

	class ControlTemplateResourceProvider extends AbstractResourceProvider {
		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			if (count($parameters) !== 1) {
				throw new UnknownResourceException(sprintf('Cannot get requested resource [%s]; missing control instance parameter.', $name));
			}
			/** @var $control IControl */
			list($control) = $parameters;
			if ($control instanceof IControl === false) {
				throw new UnknownResourceException(sprintf('Cannot get requested resource [%s]; parameter is not control parameter.', $name));
			}
			$reflectionClass = new \ReflectionClass($control);
			$sourceDirectory = new Directory(dirname($reflectionClass->getFileName()));
			$sourceDirectory->realpath();
			/** @var $directoryList IDirectory[] */
			$directoryList = [
				$sourceDirectory->directory('templates'),
				$sourceDirectory->directory('../templates'),
			];
			foreach ($directoryList as $directory) {
				$file = $directory->file($name . '.xml');
				if ($file->isAvailable()) {
					return $file;
				}
			}
			throw new UnknownResourceException(sprintf('Cannot get requested resource [%s]; no file matches.', $name));
		}
	}
