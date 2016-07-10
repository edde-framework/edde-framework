<?php
	namespace Edde\Api\Control\Html;

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
		 * @return $this
		 */
		public function setTag($tag, $pair = true);

		/**
		 * return name of tag for this control; it can be null if only children of this control should be rendered
		 *
		 * @return string|null
		 */
		public function getTag();

		/**
		 * tells if this control is paired tag
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
		public function setId($id);

		/**
		 * retrieve current html id of this control
		 *
		 * @return string
		 */
		public function getId();

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
		 * add single html attribute to an array (for example class)
		 *
		 * @param string $attribute
		 * @param string $value
		 *
		 * @return $this
		 */
		public function addAttribute($attribute, $value);

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
		public function getAttributeList();

		/**
		 * set the given css class
		 *
		 * @param string $class
		 *
		 * @return $this
		 */
		public function addClass($class);

		/**
		 * is the given class present in this control?
		 *
		 * @param string $class
		 *
		 * @return bool
		 */
		public function hasClass($class);

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
		 * execute output rendering of this control
		 *
		 * @return $this
		 */
		public function render();
	}
