<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Api\Template\IInline;
	use Edde\Common\Html\Macro\AbstractHtmlMacro;

	/**
	 * Basic class for html based inline macros.
	 */
	abstract class AbstractHtmlInline extends AbstractHtmlMacro implements IInline {
	}
