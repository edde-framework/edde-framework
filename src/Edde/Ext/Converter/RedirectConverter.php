<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * Convert "redirect" source to an appropriate answer to http or json request.
	 */
	class RedirectConverter extends AbstractConverter {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		/**
		 * You know you're a geek when...
		 *
		 * Nobody ever invites you to their house unless their computer is malfunctioning.
		 */
		public function __construct() {
			parent::__construct([
				'redirect',
			]);
		}

		/**
		 * @param IHttpResponse $httpResponse
		 */
		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($source, string $target) {
			$this->unsupported($source, $target, is_string($source));
			switch ($target) {
				case 'http+text/html':
					$this->httpResponse->header('Location', $source);
					$this->httpResponse->send();
					return $source;
				case 'http+application/json':
					$this->httpResponse->send();
					echo $source = json_encode(['redirect' => $source]);
					return $source;
			}
			$this->exception($target);
		}
	}
