<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;

	class ProtocolService extends AbstractService {
		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			return $url->match('~^/api/v1/protocol$~') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, ...$parameterList) {
			return parent::link('/api/v1/protocol', ...$parameterList);
		}
	}
