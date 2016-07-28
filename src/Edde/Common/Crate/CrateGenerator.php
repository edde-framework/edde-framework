<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\IProperty;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Usable\AbstractUsable;

	class CrateGenerator extends AbstractUsable implements ICrateGenerator {
		/**
		 * @var string
		 */
		protected $postfix;
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
			$source[] = sprintf("class %s%s extends %s {\n", $schema->getName(), $this->postfix, end($parent));
			foreach ($schema->getPropertyList() as $property) {
				$source[] = $this->generateProperty($property);
			}
			$source[] = "}\n";
			$sourceList[$schema->getSchemaName()] = implode('', $source);
			return $sourceList;
		}

		protected function generateProperty(IProperty $property) {
			$source[] = $this->generateGetter($property);
			$source[] = $this->generateSetter($property);
			return implode("\n", $source);
		}

		protected function generateGetter(IProperty $property) {
			$source[] = sprintf("public function get%s() {\n", StringUtils::camelize($property->getName()));
			$source[] = sprintf("return \$this->get('%s');\n", $property->getName());
			$source[] = "}\n";
			return implode('', $source);
		}

		protected function generateSetter(IProperty $property) {
			$parameter = StringUtils::firstLower($camelized = StringUtils::camelize($property->getName()));
			$source[] = "/**\n";
			$source[] = " * @return \$this\n";
			$source[] = " */\n";
			$source[] = sprintf("public function set%s(\$%s) {\n", $camelized, $parameter);
			$source[] = sprintf("\$this->set('%s', \$%s);\n", $property->getName(), $parameter);
			$source[] = "return \$this;\n";
			$source[] = "}\n";
			return implode('', $source);
		}

		protected function prepare() {
			$this->postfix = 'Crate';
			$this->parent = Crate::class;
		}
	}
