<?php
	declare(strict_types = 1);

	namespace Edde\Service\Http;

	use Edde\Api\Http\ICookieFactory;
	use Edde\Api\Http\ICookieList;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Object;

	class CookieFactory extends Object implements ICookieFactory {
		public function create(): ICookieList {
			return CookieList::create($_COOKIE);
		}
	}
