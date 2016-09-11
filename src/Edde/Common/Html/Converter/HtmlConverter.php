<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Converter;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Converter\AbstractConverter;

	class HtmlConverter extends AbstractConverter {
		use LazyInjectTrait;
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		public function __construct() {
			parent::__construct([
				IHtmlControl::class,
			]);
		}

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function convert($source, string $target) {
			/** @var $source IHtmlControl */
			if ($source instanceof IHtmlControl === false) {
				$this->unsupported($source, $target);
			}
			switch ($target) {
				case 'text/html':
					echo $render = $source->render();
					return $render;
				case 'application/json':
					$this->httpResponse->send();
					$json = [];
//					if ($this->javaScriptCompiler->isEmpty() === false) {
//						$json['javaScript'] = [
//							$this->javaScriptCompiler->compile($this->javaScriptCompiler)
//								->getRelativePath(),
//						];
//					}
//					if ($this->styleSheetCompiler->isEmpty() === false) {
//						$json['styleSheet'] = [
//							$this->styleSheetCompiler->compile($this->styleSheetCompiler)
//								->getRelativePath(),
//						];
//					}
					foreach ($source->invalidate() as $control) {
						if (($id = $control->getId()) !== '') {
							$json['selector']['#' . $id] = [
								'action' => 'replace',
								'source' => $control->render(),
							];
						}
					}
					echo $json = json_encode($json);
					return $json;
				case 'http+text/html':
					$this->httpResponse->send();
					return $this->convert($source, 'text/html');
			}
			$this->exception($target);
		}
	}
