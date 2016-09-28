<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * General converter frtom an array to one of the supported output types.
	 */
	class ArrayConverter extends AbstractConverter {
		use LazyHttpResponseTrait;

		/**
		 * Objective: shoot yourself in the foot using a computer language.
		 *
		 * Perl: You shoot yourself in the foot.
		 *
		 * PHP: You make a website of yourself shooting yourself in the foot.
		 *
		 * Java: Your foot shoots itself while it waits for the team of programmers to finish the code.
		 *
		 * C++: You accidentally create a dozen instances of yourself and shoot them all in the foot. Providing emergency medical assistance is impossible since you can't tell which are bitwise copies and which are just pointing at others and saying, "That's me, over there."
		 *
		 * Visual Basic: You shoot Steve Jobs in the foot.
		 *
		 * FORTRAN: You shoot yourself in each toe, iteratively, until you run out of toes, then you read in the next foot and repeat. If you run out of bullets, you continue with the attempts to shoot yourself anyways because you have no exception-handling capability.
		 *
		 * Pascal: The compiler won't let you shoot yourself in the foot.
		 *
		 * LISP: You shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds the gun with which you shoot yourself in the appendage which holds...
		 *
		 * BASIC: Shoot yourself in the foot with a water pistol.
		 *
		 * Unix:
		 *
		 * % ls
		 * foot.c foot.h foot.o toe.c toe.o
		 * % rm * .o
		 * rm:.o no such file or directory
		 * % ls
		 * %Concurrent Euclid: You shoot yourself in somebody else's foot.
		 *
		 * Assembler: You try to shoot yourself in the foot, only to discover you must first invent the gun, the bullet, the trigger, and your foot.
		 */
		public function __construct() {
			parent::__construct([
				'array',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($source, string $target) {
			if (is_array($source) === false) {
				$this->unsupported($source, $target);
			}
			switch ($target) {
				case 'http+json':
				case 'http+application/json':
					$this->httpResponse->send();
					echo $json = json_encode($source);
					return $json;
				case 'json':
				case 'application/json':
					return json_encode($source);
			}
			$this->exception($target);
		}
	}
