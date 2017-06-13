<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	interface IElementQueue extends IConfigurable {
		/**
		 * enqueue the given element
		 *
		 * @param IElement $element
		 *
		 * @return IElementQueue
		 */
		public function queue(IElement $element): IElementQueue;

		/**
		 * getQueueList over all enqueued elements by the given rules
		 *
		 * @param string|null $scope
		 * @param array|null  $tagList
		 *
		 * @return IElement[]|\Traversable
		 */
		public function getQueueList(string $scope = null, array $tagList = null);

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
