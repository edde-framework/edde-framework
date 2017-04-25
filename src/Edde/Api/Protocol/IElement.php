<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	interface IElement {
		/**
		 * return simple string name of the element name
		 *
		 * @return string
		 */
		public function getType(): string;

		/**
		 * set internal property of element
		 *
		 * @param string $name
		 * @param mixed  $value
		 *
		 * @return IElement
		 */
		public function set(string $name, $value): IElement;

		/**
		 * @param string $name
		 * @param null   $default
		 *
		 * @return mixed
		 */
		public function get(string $name, $default = null);

		/**
		 * this method should return "abstract" object in packet format prepared to be converted to target
		 * format (json, xml, ...)
		 *
		 * @return \stdClass
		 */
		public function packet(): \stdClass;
	}
