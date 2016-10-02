<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Web\LazyJavaScriptCompilerTrait;
	use Edde\Api\Web\LazyStyleSheetCompilerTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * IHtmlControl conversion to html output.
	 */
	class HtmlConverter extends AbstractConverter {
		use LazyHttpResponseTrait;
		use LazyJavaScriptCompilerTrait;
		use LazyStyleSheetCompilerTrait;

		/**
		 * HtmlConverter constructor.
		 */
		public function __construct() {
			parent::__construct([
				IHtmlControl::class,
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($convert, string $source, string $target, string $mime) {
			/** @var $convert IHtmlControl */
			if ($convert instanceof IHtmlControl === false) {
				$this->unsupported($convert, $target);
			}
			switch ($target) {
				/** @noinspection PhpMissingBreakStatementInspection */
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
					foreach ($convert->invalidate() as $control) {
						if (($id = $control->getId()) !== '') {
							$json['selector']['#' . $id] = [
								'action' => 'replace',
								'source' => $control->render(),
							];
						}
					}
					echo $json = json_encode($json);
					return $json;
				/** @noinspection PhpMissingBreakStatementInspection */
				case 'http+text/html':
					$this->httpResponse->send();
				case 'text/html':
					echo $render = $convert->render();
					return $render;
			}
			$this->exception($source, $target);
		}
	}
