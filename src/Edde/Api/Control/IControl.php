<?php
	declare(strict_types=1);

	namespace Edde\Api\Control;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponse;
	use Edde\Api\Node\INode;

	/**
	 * Control is general element for transferring incoming request into the internal system service and for
	 * generating response.
	 */
	interface IControl {
		/**
		 * @return INode
		 */
		public function getNode(): INode;

		/**
		 * add the new control to hierarchy of this control
		 *
		 * @param IControl $control
		 *
		 * @return IControl
		 */
		public function addControl(IControl $control): IControl;

		/**
		 * @param IControl[] $controlList
		 *
		 * @return IControl
		 */
		public function addControlList(array $controlList): IControl;

		/**
		 * return first level of controls (the same result as self::getNodeList())
		 *
		 * @return IControl[]
		 */
		public function getControlList(): array;

		/**
		 * execute the given method in this controls
		 *
		 * @param IRequest $request
		 *
		 * @return IResponse
		 */
		public function request(IRequest $request): IResponse;

		/**
		 * get current action
		 *
		 * @return string
		 */
		public function getAction(): string;

		/**
		 * request content of the request converted to the target type
		 *
		 * @param string $target
		 *
		 * @return mixed
		 */
		public function getContent(string $target = 'array');

		/**
		 * traverse through whole control tree
		 *
		 * @param bool $self === true, include current control too
		 *
		 * @return IControl[]
		 */
		public function traverse(bool $self = true);
	}
