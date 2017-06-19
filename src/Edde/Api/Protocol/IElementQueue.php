<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	interface IElementQueue extends IConfigurable, \IteratorAggregate {
		/**
		 * enqueue the given element
		 *
		 * @param IElement $element
		 *
		 * @return IElementQueue
		 */
		public function queue(IElement $element): IElementQueue;

		/**
		 * get current list of elements
		 *
		 * @return IElement[]
		 */
		public function getElementList();

		/**
		 * @param string $id
		 *
		 * @return IElement[]
		 */
		public function getReferenceList(string $id): array;

		/**
		 * add a reference (when dequing)
		 *
		 * @param IElement $element
		 *
		 * @return IElementQueue
		 */
		public function addReference(IElement $element): IElementQueue;

		/**
		 * is current state of element queue empty?
		 *
		 * @return bool
		 */
		public function isEmpty(): bool;

		/**
		 * force to save current queue list
		 *
		 * @return IElementQueue
		 */
		public function save(): IElementQueue;

		/**
		 * restore previously save queue list
		 *
		 * @return IElementQueue
		 */
		public function load(): IElementQueue;

		/**
		 * removed currently loaded items from queue, including "answers"
		 *
		 * @return IElementQueue
		 */
		public function clear(): IElementQueue;

		/**
		 * clear only queued elements, answers will survive
		 *
		 * @return IElementQueue
		 */
		public function clearQueue(): IElementQueue;
	}
