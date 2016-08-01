<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Usable\AbstractUsable;

	class CrateGenerator extends AbstractUsable implements ICrateGenerator {
		/**
		 * @var string
		 */
		protected $parent;

		public function generate(ISchema $schema) {
			$this->usse();
			$sourceList = [];
			$source[] = "<?php\n";
			if (($namespace = $schema->getNamespace()) !== null) {
				$source[] = "namespace $namespace;\n";
			}
			$source[] = sprintf("use %s;\n", $this->parent);
			$parent = explode('\\', $this->parent);
			$source[] = sprintf("class %s extends %s {\n", $schema->getName(), end($parent));
			foreach ($schema->getPropertyList() as $schemaProperty) {
				$source[] = $this->generateSchemaProperty($schemaProperty);
			}
			$source[] = "}\n";
			$sourceList[$schema->getSchemaName()] = implode('', $source);
			return $sourceList;
		}

		protected function generateSchemaProperty(ISchemaProperty $schemaProperty) {
			$source[] = $this->generateGetter($schemaProperty);
			$source[] = $this->generateSetter($schemaProperty);
			return implode("\n", $source);
		}

		protected function generateGetter(ISchemaProperty $schemaProperty) {
			$source[] = sprintf("public function get%s() {\n", StringUtils::camelize($schemaProperty->getName()));
			$source[] = sprintf("return \$this->get('%s');\n", $schemaProperty->getName());
			$source[] = "}\n";
			return implode('', $source);
		}

		protected function generateSetter(ISchemaProperty $schemaProperty) {
			$parameter = StringUtils::firstLower($camelized = StringUtils::camelize($schemaProperty->getName()));
			$source[] = "/**\n";
			$source[] = " * @return \$this\n";
			$source[] = " */\n";
			$source[] = sprintf("public function set%s(\$%s) {\n", $camelized, $parameter);
			$source[] = sprintf("\$this->set('%s', \$%s);\n", $schemaProperty->getName(), $parameter);
			$source[] = "return \$this;\n";
			$source[] = "}\n";
			return implode('', $source);
		}

		protected function prepare() {
			$this->parent = Crate::class;
		}
	}
