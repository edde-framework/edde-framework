<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateFactory;
	use Edde\Common\AbstractObject;
	use Edde\Common\Html\ButtonControl;
	use Edde\Common\Html\DivControl;
	use Edde\Common\Html\Value\PasswordInputControl;
	use Edde\Common\Html\Value\TextInputControl;
	use Edde\Common\Template\Filter\ActionAttributeFilter;
	use Edde\Common\Template\Filter\BindAttributeFilter;
	use Edde\Common\Template\Filter\ClassAttributeFilter;
	use Edde\Common\Template\Filter\IncludeAttributeFilter;
	use Edde\Common\Template\Filter\PropertyAttributeFilter;
	use Edde\Common\Template\Filter\ValueAttributeFilter;
	use Edde\Common\Template\Macro\ControlMacro;
	use Edde\Common\Template\Macro\HtmlMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\SchemaMacro;

	class TemplateFactory extends AbstractObject implements ITemplateFactory {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IResourceManager $resourceManager
		 * @param IContainer $container
		 */
		public function __construct(IResourceManager $resourceManager, IContainer $container) {
			$this->resourceManager = $resourceManager;
			$this->container = $container;
		}

		public function create(): ITemplate {
			$template = new Template($this->resourceManager);
			$template->registerMacro(new HtmlMacro());
			$template->registerMacro($schemaMacro = new SchemaMacro());
			$template->registerMacro(new IncludeMacro($this->resourceManager));
			$template->registerMacro($controlMacro = new ControlMacro($this->container));
			$controlMacro->registerControlList([
				'div' => DivControl::class,
				'button' => ButtonControl::class,
				'text' => TextInputControl::class,
				'password' => PasswordInputControl::class,
			]);
			$controlMacro->registerFilterList([
				'class' => new ClassAttributeFilter(),
				'action' => new ActionAttributeFilter(),
				'value' => new ValueAttributeFilter(),
				'property' => new PropertyAttributeFilter($schemaMacro),
				'bind' => new BindAttributeFilter(),
				'include' => new IncludeAttributeFilter($this->resourceManager),
			]);
			return $template;
		}
	}
