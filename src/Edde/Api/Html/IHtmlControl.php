<?php
	declare(strict_types = 1);

	namespace Edde\Api\Html;

	use Edde\Api\Control\IControl;

	/**
	 * Specialized control used for html rendering
	 */
	interface IHtmlControl extends IControl {
		/**
		 * set the given tag to this control; pair if the given tag is in a pair (div, span, ...)
		 *
		 * @param string $tag
		 * @param bool $pair
		 *
		 * @return IHtmlControl
		 */
		public function setTag(string $tag, bool $pair = true): IHtmlControl;

		/**
		 * return name of tag for this control; it can be null if only children of this control should be rendered
		 *
		 * @return string|null
		 */
		public function getTag();

		/**
		 * set text value of this control (for example <span>$text</span>)
		 *
		 * @param string $text
		 *
		 * @return $this
		 */
		public function setText(string $text);

		/**
		 * tells if this control is a paired tag
		 *
		 * @return bool
		 */
		public function isPair();

		/**
		 * set a html id
		 *
		 * @param string $id
		 *
		 * @return $this
		 */
		public function setId(string $id);

		/**
		 * retrieve current html id of this control
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * set single html attribute
		 *
		 * @param string $attribute
		 * @param string $value
		 *
		 * @return $this
		 */
		public function setAttribute($attribute, $value);

		/**
		 * set list of attributes
		 *
		 * @param array $attributeList
		 *
		 * @return IHtmlControl
		 */
		public function setAttributeList(array $attributeList): IHtmlControl;

		/**
		 * @param array $attributeList
		 *
		 * @return IHtmlControl
		 */
		public function addAttributeList(array $attributeList): IHtmlControl;

		/**
		 * return attribute by the given namw
		 *
		 * @param string $name
		 * @param mixed $default
		 *
		 * @return mixed
		 */
		public function getAttribute(string $name, $default = '');

		/**
		 * add single html attribute to an array (for example class)
		 *
		 * @param string $attribute
		 * @param string $value
		 *
		 * @return $this
		 */
		public function addAttribute(string $attribute, $value);

		/**
		 * has the given html attribute value? (is present and is not null)
		 *
		 * @param string $attribute
		 *
		 * @return bool
		 */
		public function hasAttribute($attribute);

		/**
		 * return current html attribute list
		 *
		 * @return string[]
		 */
		public function getAttributeList(): array;

		/**
		 * set the given css class
		 *
		 * @param string $class
		 *
		 * @return $this
		 */
		public function addClass(string $class);

		/**
		 * is the given class present in this control?
		 *
		 * @param string $class
		 *
		 * @return bool
		 */
		public function hasClass(string $class);

		/**
		 * return current list of classes
		 *
		 * @return string[]
		 */
		public function getClassList();

		/**
		 * @return IHtmlControl[]
		 */
		public function getControlList();

		/**
		 * execute output rendering of this control - return string version of this control
		 *
		 * @param int $indent indent modifier when "parent" control is not rendered
		 *
		 * @return string
		 */
		public function render(int $indent = 0);

		/**
		 * load client script based on a current class name
		 *
		 * @param string $class
		 * @param string $file
		 *
		 * @return IHtmlControl
		 */
		public function javascript(string $class = null, string $file = null): IHtmlControl;

		/**
		 * @param string|null $file
		 *
		 * @return IHtmlControl
		 * @internal param null|string $class
		 */
		public function stylesheet(string $file = null): IHtmlControl;

		/**
		 * return all invalid (dirty) controls
		 *
		 * @return array|IHtmlControl[]
		 */
		public function invalidate(): array;

		/**
		 * @return IHtmlControl[]
		 */
		public function getIterator();
	}
