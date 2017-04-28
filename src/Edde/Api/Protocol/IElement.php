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
		 * return the element id; format is free to use, but it should be generally unique (for example guid)
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * set element's scope
		 *
		 * @param string|null $scope
		 *
		 * @return IElement
		 */
		public function setScope(string $scope = null): IElement;

		/**
		 * @return string|null
		 */
		public function getScope();

		/**
		 * set tag list of the element
		 *
		 * @param string[]|null $tagList
		 *
		 * @return IElement
		 */
		public function setTagList(array $tagList = null): IElement;

		/**
		 * @return string[]
		 */
		public function getTagList(): array;

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
		 * override current set of data in element
		 *
		 * @param array $data
		 *
		 * @return IElement
		 */
		public function put(array $data): IElement;

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
