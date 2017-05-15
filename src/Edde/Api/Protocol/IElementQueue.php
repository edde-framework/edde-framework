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
		 * @return IElement[]
		 */
		public function getQueueList(string $scope = null, array $tagList = null);

		/**
		 * execute current queue list
		 *
		 * @return IElementQueue
		 */
		public function execute(): IElementQueue;

		/**
		 * @param string $id
		 *
		 * @return IElement[]
		 */
		public function getReferenceList(string $id): array;

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
	}
