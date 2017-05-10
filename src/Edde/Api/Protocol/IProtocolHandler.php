<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Handles individual type of protocol item.
	 */
	interface IProtocolHandler extends IConfigurable {
		/**
		 * check if the element is compatible with this handler; should throw an exception
		 *
		 * @param IElement $element
		 *
		 * @return IProtocolHandler
		 */
		public function check(IElement $element): IProtocolHandler;

		/**
		 * can this handler handle the given element?
		 *
		 * @param IElement $element
		 *
		 * @return bool
		 */
		public function canHandle(IElement $element): bool;

		/**
		 * enqueue the given element
		 *
		 * @param IElement $element
		 *
		 * @return IProtocolHandler
		 */
		public function queue(IElement $element): IProtocolHandler;

		/**
		 * this method could execute the given element; if the Element is async, it would be queued
		 *
		 * @param IElement $element
		 *
		 * @return mixed
		 */
		public function element(IElement $element);

		/**
		 * immediately executes an element if it's supported (and eventually return an answer)
		 *
		 * @param IElement $element
		 *
		 * @return mixed
		 */
		public function execute(IElement $element);

		/**
		 * execute current queue of elements
		 *
		 * @param string|null $scope
		 * @param array|null  $tagList
		 *
		 * @return IProtocolHandler
		 */
		public function dequeue(string $scope = null, array $tagList = null): IProtocolHandler;

		/**
		 * create current packet
		 *
		 * @param string|null   $scope
		 * @param string[]|null $tagList
		 * @param IPacket       $packet
		 *
		 * @return IPacket
		 */
		public function packet(string $scope = null, array $tagList = null, IPacket $packet = null): IPacket;

		/**
		 * get related Elements to the given element (so returned elements are referencing the input element)
		 *
		 * @param IElement $reference
		 *
		 * @return IElement[]
		 */
		public function reference(IElement $reference): array;
	}
