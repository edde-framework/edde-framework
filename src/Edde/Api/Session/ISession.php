<?php
	declare(strict_types = 1);

	namespace Edde\Api\Session;

	interface ISession {
		/**
		 * is this sessio fragment set?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function isset(string $name): bool;

		/**
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return ISession
		 */
		public function set(string $name, $value): ISession;

		/**
		 * @param string $name
		 * @param mixed $default
		 *
		 * @return ISession
		 */
		public function get(string $name, $default = null): ISession;

		/**
		 * return internal array (only for read)
		 *
		 * @return array
		 */
		public function array(): array;
	}
