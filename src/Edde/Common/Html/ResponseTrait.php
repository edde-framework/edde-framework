<?php
	declare(strict_types=1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Application\Response;

	/**
	 * Response dependency and implementation for controls
	 */
	trait ResponseTrait {
		public function response() {
			$this->responseManager->response(new Response($this, IHtmlControl::class));
		}
	}
