<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Collection\IList;
	use Edde\Api\Http\HttpException;
	use Edde\Api\Http\ICookie;
	use Edde\Api\Http\ICookieList;
	use Edde\Common\Collection\AbstractList;

	class CookieList extends AbstractList implements ICookieList {
		static public function create(array $cookieList) {
			$self = new self();
			foreach ($cookieList as $name => $value) {
				$self->addCookie(new Cookie($name, $value, 0, null, null));
			}
			return $self;
		}

		public function addCookie(ICookie $cookie) {
			parent::set($cookie->getName(), $cookie);
			return $this;
		}

		public function set(string $name, $value): IList {
			throw new HttpException(sprintf('Cannot directly set value [%s] to the cookie list; use [%s::addCookie()] instead.', $name, static::class));
		}
	}
