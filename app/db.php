<?php
	declare(strict_types = 1);

	function adminer_object() {
		class EddeAdminer extends Adminer {
			function name() {
				return 'Adminer: Edde Edition';
			}

			function login($Wd, $G) {
				return true;
			}
		}

		return new EddeAdminer();
	}

	require_once(__DIR__ . '/adminer.php');
