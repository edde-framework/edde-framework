<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Html\Macro\SchemaMacro;

	/**
	 * Schema inline reference.
	 */
	class SchemaInline extends AbstractHtmlInline {
		/**
		 * There was once a young man who, in his youth, professed his desire to become a great writer. When asked to define "Great" he said, "I want to write stuff that the whole world will read, stuff that people will react to on a truly emotional level, stuff that will make them scream, cry, howl in pain and anger!" He now works for Microsoft, writing error messages.
		 */
		public function __construct() {
			parent::__construct('m:schema', true);
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$schemaList = $compiler->getVariable(SchemaMacro::class);
			list($schema, $property) = explode('.', $this->extract($macro));
			if (isset($schemaList[$schema]) === false) {
				throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $schema, $macro->getPath()));
			}
			$macro->setAttribute('data-schema', $schemaList[$schema]);
			$macro->setAttribute('data-property', $property);
		}
	}
