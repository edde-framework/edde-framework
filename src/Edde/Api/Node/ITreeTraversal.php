<?php
	declare(strict_types=1);

	namespace Edde\Api\Node;

	/**
	 * Quite tricky interface to enable tree node traversal in linear (or semi-linear) way with
	 * proper open -> content -> close handles.
	 *
	 * ***
	 *
	 * One day, Little Johnny saw his grandpa smoking his cigarettes. Little Johnny asked, "Grandpa, can I smoke some of your cigarettes?" His grandpa replied, "Can your penis reach your asshole?"
	 * "No", said Little Johnny. His grandpa replied, "Then you're not old enough."
	 *
	 * The next day, Little Johnny saw his grandpa drinking beer. He asked, "Grandpa, can I drink some of your beer?" His grandpa replied, "Can your penis reach your asshole?"
	 * "No" said Little Johhny. "Then you're not old enough." his grandpa replied.
	 *
	 * The next day, Little Johnny was eating cookies. His grandpa asked, "Can I have some of your cookies?" Little Johnny replied, "Can your penis reach your asshole?"
	 * His grandpa replied, "It most certainly can!" Little Johnny replied, "Then go fuck yourself."
	 */
	interface ITreeTraversal {
		/**
		 * ability to change tree traversal based on the node; by default the current instance
		 *
		 * @param INode $node
		 *
		 * @return ITreeTraversal
		 */
		public function traverse(INode $node): ITreeTraversal;

		/**
		 * open node event
		 *
		 * @param INode $node
		 * @param array ...$parameters
		 *
		 * @return mixed
		 */
		public function open(INode $node, ...$parameters);

		/**
		 * content of node (usually main logic, another tree traversals, ...)
		 *
		 * @param INode     $node
		 * @param \Iterator $iterator
		 * @param array     ...$parameters
		 *
		 * @return mixed
		 */
		public function content(INode $node, \Iterator $iterator, ...$parameters);

		/**
		 * close the node (for example draw closing tag ;))
		 *
		 * @param INode $node
		 * @param array ...$parameters
		 *
		 * @return mixed
		 */
		public function close(INode $node, ...$parameters);
	}
