<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaCollection;
	use Edde\Api\Schema\ISchemaLink;
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
				$source[] = "\tnamespace $namespace;\n\n";
			}
			$source[] = sprintf("\tuse %s;\n\n", $this->parent);
			$parent = explode('\\', $this->parent);
			$source[] = sprintf("\tclass %s extends %s {\n", $schema->getName(), end($parent));
			foreach ($schema->getPropertyList() as $schemaProperty) {
				$source[] = $this->generateSchemaProperty($schemaProperty);
			}
			foreach ($schema->getCollectionList() as $schemaCollection) {
				$source[] = $this->generateCollection($schemaCollection);
			}
			$source[] = "\t}\n";
			$sourceList[$schema->getSchemaName()] = implode('', $source);
			return $sourceList;
		}

		protected function generateSchemaProperty(ISchemaProperty $schemaProperty) {
			$source[] = $this->generateGetter($schemaProperty);
			$source[] = $this->generateSetter($schemaProperty);
			$source[] = '';
			return implode("\n", $source);
		}

		protected function generateGetter(ISchemaProperty $schemaProperty) {
			$source[] = "\t\t/**\n";
			$source[] = sprintf("\t\t * @return %s\n", $schemaProperty->getType());
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function get%s() {\n", StringUtils::camelize($schemaProperty->getName()));
			$source[] = sprintf("\t\t\treturn \$this->get('%s');\n", $schemaProperty->getName());
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		protected function generateSetter(ISchemaProperty $schemaProperty) {
			$parameter = StringUtils::firstLower($camelized = StringUtils::camelize($propertyName = $schemaProperty->getName()));
			$source[] = "\t\t/**\n";
			$source[] = sprintf("\t\t * @param %s $%s\n", $schemaProperty->getType(), $parameter);
			$source[] = "\t\t * \n";
			$source[] = "\t\t * @return \$this\n";
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function set%s(\$%s) {\n", $camelized, $parameter);
			$source[] = sprintf("\t\t\t\$this->set('%s', \$%s);\n", $propertyName, $parameter);
			$source[] = "\t\t\treturn \$this;\n";
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		protected function generateCollection(ISchemaCollection $schemaCollection) {
			$source[] = '';
			$source[] = "\t\t/**\n";
			$source[] = "\t\t * \n";
			$source[] = sprintf("\t\t * @return %s\n", ICollection::class);
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function collection%s() {\n", StringUtils::camelize($collectionName = $schemaCollection->getName()));
			$source[] = sprintf("\t\t\treturn \$this->collection('%s');\n", $collectionName);
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		protected function generateLink(ISchemaLink $schemaLink) {
			$source[] = '';
			return implode('', $source);
		}

		protected function prepare() {
			$this->parent = Crate::class;
		}
	}
