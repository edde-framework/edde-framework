<?php
	declare(strict_types = 1);

	namespace Edde\Api\Query;

	use Edde\Api\Node\INode;

	/**
	 * General factory for IStaticQuery creation; instance of this factory is usually storage's proprietary
	 * property - every storage can have it's own IStaticQueryFactory.
	 */
	interface IStaticQueryFactory {
		/**
		 * @param IQuery $query
		 *
		 * @return IStaticQuery
		 */
		public function create(IQuery $query);

		/**
		 * create static query from the given fragment
		 *
		 * @param INode $node
		 *
		 * @return IStaticQuery
		 */
		public function fragment(INode $node);
	}
