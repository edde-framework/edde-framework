<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Application\JsonResponse;

	class ProtocolService extends AbstractService {
		use LazyProtocolServiceTrait;
		protected $action;
		protected $id;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			if (($match = $url->match('~^/api/v1/protocol(/(?<action>.+?)(/(?<id>.+?))?)?$~')) === null) {
				return false;
			}
			$this->action = $match['action'] ?? null;
			$this->id = $match['id'] ?? null;
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, ...$parameterList) {
			return parent::link('/api/v1/protocol', ...$parameterList);
		}

		public function restGet(string $scope = null) {
			switch ($this->action) {
				case 'reference':
					// $this->protocolService->reference();
					dump('give the reference of ' . $this->id);
					break;
			}
			return new JsonResponse([]);
		}

		public function restPost() {
			return new JsonResponse([]);
		}
	}
