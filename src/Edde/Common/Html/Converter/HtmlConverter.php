<?php
	declare(strict_types=1);

	namespace Edde\Common\Html\Converter;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Web\LazyJavaScriptCompilerTrait;
	use Edde\Api\Web\LazyStyleSheetCompilerTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * IHtmlControl conversion to html output.
	 */
	class HtmlConverter extends AbstractConverter {
		use LazyJavaScriptCompilerTrait;
		use LazyStyleSheetCompilerTrait;

		/**
		 * HtmlConverter constructor.
		 */
		public function __construct() {
			$this->register(IHtmlControl::class, [
				'application/json',
				'application/json',
				'text/html',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($convert, string $mime, string $target) {
			/** @var $convert IHtmlControl */
			if ($convert instanceof IHtmlControl === false) {
				$this->unsupported($convert, $target);
			}
			switch ($target) {
				case 'application/json':
					$json = [];
					foreach ($convert->invalidate() as $control) {
						if (($id = $control->getId()) === '') {
							throw new ControlException(sprintf('Control [%s; %s] has no assigned id, thus it cannot be rendered.', get_class($control), $control->getNode()
								->getPath()));
						}
						$json['selector']['#' . $id] = [
							'action' => 'replace',
							'source' => $control->render(),
						];
					}
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
					echo $json = json_encode($json);
					return $json;
				case 'text/html':
					echo $render = $convert->render();
					return $render;
			}
			$this->exception($mime, $target);
		}
	}
