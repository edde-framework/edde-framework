<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	/**
	 * Macro for simplier work with schemas.
	 */
	class SchemaMacro extends AbstractHtmlMacro {
		/**
		 * "If you were plowing a field, what would you rather use? 2 strong oxen or 1024 chickens?"
		 *
		 * - Seymour Cray
		 */
		public function __construct() {
			parent::__construct('schema');
		}

		public function compileInline(INode $macro, ICompiler $compiler) {
			$schemaList = $compiler->getVariable(static::class);
			list($schema, $property) = explode('.', $this->extract($macro, self::COMPILE_PREFIX . $this->getName()));
			if (isset($schemaList[$schema]) === false) {
				throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $schema, $macro->getPath()));
			}
			$macro->setAttribute('data-schema', $schemaList[$schema]);
			$macro->setAttribute('data-property', $property);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$schemaList = $compiler->getVariable(static::class, []);
			$schemaList[$this->attribute($macro, $compiler, 'name', false)] = $this->attribute($macro, $compiler, 'schema', false);
			$compiler->setVariable(static::class, $schemaList);
		}
	}
