<?php
	namespace Edde\Api\Control;

	/**
	 * Formal interface for control factory implementation; this should be used instead if direct access to a container.
	 */
	interface IControlFactory {
		/**
		 * create a new control; a control should be created from a container
		 *
		 * @param string $control
		 *
		 * @return IControl
		 */
		public function create($control);
	}
