<?php

/**
 * BPMJ_Email_Validator forked from EmailValidator (https://github.com/nojacko/email-validator) by James Jackson
 * Works for PHP 5.2.17
 */
class WTBP_WPTAO_Email_Validator {

	/**
	 * An array of disposable email domains. Populated when required.
	 *
	 * @var mixed
	 * @access private
	 */
	private $disposable = null;

	/**
	 * An array of role email users. Populated when required.
	 *
	 * @var mixed
	 * @access private
	 */
	private $role = null;

	/**
	 * An array of example TLDs.
	 * As defined in http://tools.ietf.org/html/rfc2606
	 *
	 * @var array
	 * @access private
	 */
	private $exampleTlds = array(
		'.test',
		'.example',
		'.invalid',
		'.localhost',
	);

	/**
	 * An array of example domains.
	 * As defined in http://tools.ietf.org/html/rfc2606
	 *
	 * @var array
	 * @access private
	 */
	private $exampleDomains = array(
		'example.com',
		'example.net',
		'example.org',
	);

	/**
	 * Validate an email address using ALL the functions of this library.
	 *
	 * @param string $email Address
	 * @return boolean
	 */
	public function isValid( $email ) {
		// Does it look like an email address?
		if ( !$this->isEmail( $email ) ) {
			return false;
		}

		// Is is an example domain?
		if ( $this->isExample( $email ) ) {
			return false;
		}

		// Is is a it disposable?
		if ( $this->isDisposable( $email ) ) {
			return false;
		}

		// Is it role based?
		if ( $this->isRole( $email ) ) {
			return false;
		}

		// Does it have an MX record?
		if ( !$this->hasMx( $email ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate an email address using isEmail, isExample and hasMx.
	 *
	 * @param string $email Address
	 * @return boolean
	 */
	public function isSendable( $email ) {
		// Does it look like an email address?
		if ( !$this->isEmail( $email ) ) {
			return false;
		}

		// Is is an example domain?
		if ( $this->isExample( $email ) ) {
			return false;
		}

		// Does it have an MX record?
		if ( !$this->hasMx( $email ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Valid email
	 * Call WordPress function
	 *
	 * @param string $email Address
	 * @return boolean
	 */
	public function isEmail( $email ) {
		return (false !== is_email( $email ));
	}

	/**
	 * Detected example email addresses.
	 * As defined in http://tools.ietf.org/html/rfc2606
	 *
	 * @param string $email Address
	 * @return boolean|null
	 */
	public function isExample( $email ) {
		if ( !$this->isEmail( $email ) ) {
			return null;
		}

		$hostname = $this->hostnameFromEmail( $email );

		if ( $hostname ) {
			if ( in_array( $hostname, $this->exampleDomains ) ) {
				return true;
			}

			foreach ( $this->exampleTlds as $tld ) {
				$length	 = strlen( $tld );
				$subStr	 = substr( $hostname, -$length );

				if ( $subStr == $tld ) {
					return true;
				}
			}

			return false;
		}

		return null;
	}

	/**
	 * Detected disposable email domains
	 *
	 * @param string $email Address
	 * @return boolean|null
	 */
	public function isDisposable( $email ) {
		if ( !$this->isEmail( $email ) ) {
			return null;
		}

		$hostname = $this->hostnameFromEmail( $email );

		if ( $hostname ) {
			// Load disposable domains
			if ( is_null( $this->disposable ) ) {
				$this->disposable = include('data/disposable.php');
			}

			// Search array for hostname
			if ( in_array( $hostname, $this->disposable ) ) {
				return true;
			}

			return false;
		}

		return null;
	}

	/**
	 * Detected role based email addresses
	 *
	 * @param string $email Address
	 * @return boolean|null
	 */
	public function isRole( $email ) {
		if ( !$this->isEmail( $email ) ) {
			return null;
		}

		$user = $this->userFromEmail( $email );

		if ( $user ) {
			// Load disposable domains
			if ( is_null( $this->role ) ) {
				$this->role = include('data/role.php');
			}

			// Search array for hostname
			if ( in_array( $user, $this->role ) ) {
				return true;
			}

			return false;
		}

		return null;
	}

	/**
	 * Test email address for MX records
	 *
	 * @param string $email Address
	 * @return boolean|null
	 */
	public function hasMx( $email ) {
		if ( !$this->isEmail( $email ) ) {
			return null;
		}

		$hostname = $this->hostnameFromEmail( $email );

		if ( $hostname ) {
			return checkdnsrr( $hostname, 'MX' );
		}

		return null;
	}

	// Private Functions

	/**
	 * Get the user form an email address
	 *
	 * @access private
	 * @param string $email Address
	 * @return string|null
	 */
	private function userFromEmail( $email ) {
		$parts = explode( '@', $email );

		if ( count( $parts ) == 2 ) {
			return strtolower( $parts[ 0 ] );
		}

		return null;
	}

	/**
	 * Get the hostname form an email address
	 *
	 * @access private
	 * @param string $email Address
	 * @return string|null
	 */
	private function hostnameFromEmail( $email ) {
		$parts = explode( '@', $email );

		if ( count( $parts ) == 2 ) {
			return strtolower( $parts[ 1 ] );
		}

		return null;
	}

}