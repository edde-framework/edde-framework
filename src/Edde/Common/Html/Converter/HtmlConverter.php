<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Converter;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Converter\AbstractConverter;

	class HtmlConverter extends AbstractConverter {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var IJavaScriptCompiler
		 */
		protected $javaScriptCompiler;
		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;

		public function __construct() {
			parent::__construct([
				IHtmlControl::class,
			]);
		}

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function lazyJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		public function convert($source, string $target) {
			/** @var $source IHtmlControl */
			if ($source instanceof IHtmlControl === false) {
				$this->unsupported($source, $target);
			}
			switch ($target) {
				case 'http+application/json':
					$this->httpResponse->send();
				case 'application/json':
					$json = [];
					if ($this->javaScriptCompiler->isEmpty() === false) {
						$json['javaScript'] = [
							$this->javaScriptCompiler->compile($this->javaScriptCompiler)
								->getRelativePath(),
						];
					}
					if ($this->styleSheetCompiler->isEmpty() === false) {
						$json['styleSheet'] = [
							$this->styleSheetCompiler->compile($this->styleSheetCompiler)
								->getRelativePath(),
						];
					}
					foreach ($source->invalidate() as $control) {
						if (($id = $control->getId()) !== '') {
							$json['selector']['#' . $id] = [
								'action' => 'replace',
								'source' => $control->render(),
							];
						}
					}
					echo json_encode($json);
					return null;
				case 'http+text/html':
					$this->httpResponse->send();
				case 'text/html':
					echo $source->render();
					return null;
			}
			$this->exception($target);
			return null;
		}
	}
