<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Translator\TranslatorException;
	use Foo\Bar\DummyDictionary;
	use Foo\Bar\EmptyDictionary;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class TranslatorTest extends TestCase {
		/**
		 * @var ITranslator
		 */
		protected $translator;

		public function testUseException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Cannot use translator without set language.');
			$this->translator->usse();
		}

		public function testWithoutDictionaryException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			$this->translator->onSetup(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			$this->translator->usse();
		}

		public function testEmptyDictionaryException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Cannot translate [foo]; the given id is not available in no dictionary.');
			$this->translator->registerDitionary(new EmptyDictionary());
			$this->translator->onSetup(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			$this->translator->translate('foo');
		}

		public function testDummyDictionary() {
			$this->translator->registerDitionary(new EmptyDictionary());
			$this->translator->registerDitionary(new DummyDictionary());
			$this->translator->onSetup(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			self::assertEquals('foo.en', $this->translator->translate('foo'));
		}

		protected function setUp() {
			$this->translator = new Translator();
		}
	}
