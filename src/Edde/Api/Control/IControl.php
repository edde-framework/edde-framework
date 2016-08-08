<?php
	declare(strict_types = 1);

	namespace Edde\Api\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Usable\IUsable;

	/**
	 * Control is general element for transfering incoming request into the internal system service and for
	 * generating response.
	 */
	interface IControl extends IUsable {
		/**
		 * return node of this control
		 *
		 * @return INode
		 */
		public function getNode();

		/**
		 * return root control
		 *
		 * @return IControl
		 */
		public function getRoot();

		/**
		 * return parent or null of this control is root
		 *
		 * @return IControl|null
		 */
		public function getParent();

		/**
		 * add the new control to hierarchy of this control
		 *
		 * @param IControl $control
		 *
		 * @return $this
		 */
		public function addControl(IControl $control);

		/**
		 * @param IControl[] $controlList
		 *
		 * @return $this
		 */
		public function addControlList(array $controlList);

		/**
		 * return first level of controls (the same result as self::getNodeList())
		 *
		 * @return IControl[]
		 */
		public function getControlList();
	}
