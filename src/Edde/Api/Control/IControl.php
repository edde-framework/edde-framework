<?php
	declare(strict_types = 1);

	namespace Edde\Api\Control;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Event\IEventBus;
	use Edde\Api\Node\INode;

	/**
	 * Control is general element for transfering incoming request into the internal system service and for
	 * generating response.
	 */
	interface IControl extends IDeffered, IEventBus, \IteratorAggregate {
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
		 * has this control some children?
		 *
		 * @return bool
		 */
		public function isLeaf(): bool;

		/**
		 * return parent or null of this control is root
		 *
		 * @return IControl|null
		 */
		public function getParent();

		/**
		 * remove this control from control tree
		 *
		 * @return IControl
		 */
		public function disconnect(): IControl;

		/**
		 * add the new control to hierarchy of this control
		 *
		 * @param IControl $control
		 *
		 * @return $this
		 */
		public function addControl(IControl $control);

		/**
		 * called when this control is attached to the given one
		 *
		 * @param IControl $control
		 *
		 * @return IControl
		 */
		public function attached(IControl $control): IControl;

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

		/**
		 * return all invalid (dirty) controls
		 *
		 * @return array|IControl[]
		 */
		public function invalidate(): array;

		/**
		 * mark control as dirty; this should change state of all child controls
		 *
		 * @param bool $dirty
		 *
		 * @return IControl
		 */
		public function dirty(bool $dirty = true): IControl;

		/**
		 * is this control dirty?
		 *
		 * @return bool
		 */
		public function isDirty(): bool;

		/**
		 * execute the given method in this controls
		 *
		 * @param string $method
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function handle(string $method, array $parameterList);

		/**
		 * @return IControl[]
		 */
		public function getIterator();
	}
