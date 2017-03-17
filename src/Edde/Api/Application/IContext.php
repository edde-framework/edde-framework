<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Resource\IResource;

	/**
	 * Application context; basically defines application type (so under one source root could run
	 * more applications, e.g. backend, administration and frontend, ...).
	 *
	 * Parts of Edde supports context's
	 */
	interface IContext {
		/**
		 * return current id of context; could be any type of string
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * should return "hash" of id (could be arbitrary simple string)
		 *
		 * @return string
		 */
		public function getGuid(): string;

		/**
		 * return cascade of strings which should be searched for final result
		 *
		 * @param string      $name
		 * @param string|null $default
		 *
		 * @return string
		 */
		public function cascade(string $name, string $default = null): array;

		/**
		 * resolve the given resource based on cascade; compatible with resource provider
		 *
		 * @param string $name
		 * @param array  $parameters
		 *
		 * @return IResource
		 */
		public function getResource(string $name, ...$parameters): IResource;
	}
