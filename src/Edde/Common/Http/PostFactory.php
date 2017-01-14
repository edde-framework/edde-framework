<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IPostFactory;
	use Edde\Api\Http\IPostList;
	use Edde\Common\Object;

	class PostFactory extends Object implements IPostFactory {
		/**
		 * @inheritdoc
		 */
		public function create(): IPostList {
			/**
			 * simple way how to sanitize POST input
			 */
			return PostList::create($_POST ?? []);
		}
	}
