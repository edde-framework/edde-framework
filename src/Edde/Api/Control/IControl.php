<?php
	declare(strict_types = 1);

	namespace Edde\Api\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Usable\IUsable;

	/**
	 * Control is general element for transfering incoming request into the internal system service and for
	 * generating response.
	 */
	interface IControl extends IUsable, \IteratorAggregate {
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
		 * register a new snippet (function for a deferred control creation)
		 *
		 * @param string $name
		 * @param callable $snippet
		 * @param callable $callback optional invalidator callback
		 *
		 * @return IControl
		 */
		public function addSnippet(string $name, callable $snippet, callable $callback = null): IControl;

		/**
		 * execute the given snippet; snippet will use provided parent or current control
		 *
		 * @param string $name
		 *
		 * @return IControl
		 */
		public function snippet(string $name): IControl;

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

		public function handle(string $method, array $parameterList, array $crateList);

		/**
		 * @return IControl[]
		 */
		public function getIterator();
	}
