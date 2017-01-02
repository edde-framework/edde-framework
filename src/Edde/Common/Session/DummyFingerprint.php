<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\IFingerprint;
	use Edde\Common\Object;

	/**
	 * Don't use session id method.
	 */
	class DummyFingerprint extends Object implements IFingerprint {
		public function fingerprint() {
			return null;
		}
	}
