<?php
	/* libraries/api_controller.php
	 *
	 * Copyright (C) by Hugo Leisink <hugo@leisink.net>
	 * This file is part of the Banshee PHP framework
	 * http://www.banshee-php.org/
	 */

	abstract class api_controller extends controller {
		protected function set_error($code) {
			if ($code >= 400) {
				$this->output->add_tag("error", $code);
			}
			$this->output->http_status = $code;
		}

		public function execute() {
			$function = strtolower($_SERVER["REQUEST_METHOD"]);
			if (count($this->page->parameters) > 0) {
				$uri_part = "_".implode("_", $this->page->parameters);
				$function .= $uri_part;
			}

			if (method_exists($this, $function)) {
				call_user_func(array($this, $function));
				return;
			}

			$methods = array_diff(array("GET", "POST", "PUT", "DELETE"), array($_SERVER["REQUEST_METHOD"]));
			$allowed = array();
			foreach ($methods as $method) {
				if (method_exists($this, strtolower($method).$uri_part)) {
					array_push($allowed, $method);
				}
			}

			if (count($allowed) == 0) {
				$this->set_error(405);
			} else {
				$this->set_error(405);
				header("Allowed: ".implode(", ", $allowed));
			}
		}
	}
?>
