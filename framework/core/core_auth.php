<?php

	/**
	 * Core Auth - xGlide Framework
	 * @author Oliver Ridgway <oli@kineticfaction.co.uk>
	 * @license http://www.gnu.org/licenses/lgpl.html GNU LESSER GENERAL PUBLIC LICENSE
	 * @version 3.0 Dirty Damsel 
	 * 
	 */
	class core_auth {

		// <editor-fold defaultstate="collapsed" desc=" SINGLETON : $instance __construct i _clone ">
		static private  $instance = null; 
		static public function i() { 
			if(self::$instance === null) { 
				$c = __CLASS__; 
				self::$instance = new $c(); 
			} 
			return self::$instance; 
		} 
		private function __construct() { }
	
		public function __clone() {
			throw new Exception("Cannot clone ".__CLASS__." class"); 
		} 
		// </editor-fold>
			
		private $data = array();
		private $http_auth_complete = false;

		final public function connect() {
			if(core_settings::i()->get('CONFIG_SETTINGS_AUTH') == 2) {
				if($_SERVER['REMOTE_ADDR'] == core_settings::i()->get('CONFIG_AUTH_NTLM_IP') && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
					if(!$this->connect_ntlm()) {
						if($this->http_auth_complete) {
							// Only display a 403 if the NTLM is complete.
							core_debug::i()->add('403', 'Cannot find user in Database: ', '');
						} else {
							exit();
						}
					}
				} else {
					if(!$this->connect_ldap()) {
						header('HTTP/1.0 401 Unauthorized');
						header('WWW-Authenticate: Basic realm="iTrafford Login"');
						exit('Unauthorized <a href="/">Retry</a>');
					}
				}
			}
			if(core_settings::i()->get('CONFIG_SETTINGS_AUTH') == 1) {
				$this->connect_db();
			}
		}

		final public function connect_db() {
			
			if(isset($_SESSION['user']->int_id)) {
				$model_users = new model_users();
				$user = $model_users->auth(
					new query_login(
						$_SESSION['user']->str_username
					)
				);
				if(isset($user->str_sessionid)) {
					if($user->str_sessionid == session_id()) {
						core_settings::i()->add('CONFIG_ADMIN',	true);
					} else {
						core_settings::i()->add('CONFIG_ADMIN',	false);
					}
				} else {
					core_settings::i()->add('CONFIG_ADMIN',	false);
				}
			} else {
				core_settings::i()->add('CONFIG_ADMIN',	false);
			}
			
		}
		
		final private function connect_ldap() {
			if (!isset($_SERVER['PHP_AUTH_USER'])) {
				header('HTTP/1.0 401 Unauthorized');
				header('WWW-Authenticate: Basic realm="iTrafford Login"');
			} else {

				if(empty($_SERVER['PHP_AUTH_PW'])) {
					return false;
				}

				
				if(!$ad_connention = @ldap_connect(core_settings::i()->get('CONFIG_SERVERS_LDAP_IP'))) {
					return false;
				}
				ldap_set_option($ad_connention, LDAP_OPT_PROTOCOL_VERSION, 3);
				ldap_set_option($ad_connention, LDAP_OPT_REFERRALS, 0);
				if(!@ldap_bind($ad_connention, $_SERVER['PHP_AUTH_USER'] . core_settings::i()->get('CONFIG_SERVERS_LDAP_USER_SUFFIX'), $_SERVER['PHP_AUTH_PW'])) {
					return false;
				}
				$model_users = new model_users();
				$user = $model_users->auth($_SERVER['PHP_AUTH_USER']);
				if(count($user) > 0) {
					$_SESSION = array();
					$_SESSION['users'] = $user[0];
					return true;
				} else {
					return false;
				}
			}
		}

		final private function connect_ntlm() {
			if(!isset($_SERVER['HTTP_AUTHORIZATION'])) { // step 1
				header("HTTP/1.1 401 Unauthorized"); // step 2
				header("WWW-Authenticate: NTLM");
				
			}			
			if(isset($_SERVER['HTTP_AUTHORIZATION']) && substr($_SERVER['HTTP_AUTHORIZATION'],0,5) == 'NTLM ') {
			
				$chaine=$_SERVER['HTTP_AUTHORIZATION'];
				$chaine=substr($chaine, 5); // type1 message
				$chained64=base64_decode($chaine);
				
				if(ord($chained64{8}) == 1) { // step 3
					$retAuth = "NTLMSSP";
					$retAuth .= chr(0).chr(2).chr(0).chr(0);
					$retAuth .= chr(0).chr(0).chr(0).chr(0);
					$retAuth .= chr(0).chr(40).chr(0).chr(0);
					$retAuth .= chr(0).chr(1).chr(130).chr(0);
					$retAuth .= chr(0).chr(0).chr(2).chr(2);
					$retAuth .= chr(2).chr(0).chr(0).chr(0);
					$retAuth .= chr(0).chr(0).chr(0).chr(0);
					$retAuth .= chr(0).chr(0).chr(0).chr(0).chr(0);
					$retAuth64 =base64_encode($retAuth);
					$retAuth64 = trim($retAuth64);
					header( "HTTP/1.1 401 Unauthorized" ); // step 4
					header( "WWW-Authenticate: NTLM $retAuth64" );
				} else if(ord($chained64{8}) == 3) { // step 5
					$lenght_domain = (ord($chained64[31]) * 256 + ord($chained64[30]));
					$offset_domain = (ord($chained64[33]) * 256 + ord($chained64[32]));
					$domain = substr($chained64, $offset_domain, $lenght_domain);
					$lenght_login = (ord($chained64[39]) * 256 + ord($chained64[38]));
					$offset_login = (ord($chained64[41]) * 256 + ord($chained64[40]));
					$login = substr($chained64, $offset_login, $lenght_login);
					$lenght_host = (ord($chained64[47]) * 256 + ord($chained64[46]));
					$offset_host = (ord($chained64[49]) * 256 + ord($chained64[48]));
					$host = substr($chained64, $offset_host, $lenght_host);
				}
			}
			
			if(isset($login)) {  
				$this->http_auth_complete = true;
				$username = preg_replace("/(.)(.)/","$1",$login);
				$domain = preg_replace("/(.)(.)/","$1",$domain);
				$username = strtolower($username);
				$domain = strtoupper($domain);
			}
			if(isset($username)) {
				$model_users = new model_users();
				$user = $model_users->auth($username);
				if(count($user) > 0 && $domain == core_settings::i()->get('CONFIG_AUTH_NTLM_DOMAIN')) {
					// if the user exists and there on the domain....
					$_SESSION = array();
					$_SESSION['users'] = $user[0];
					return true;
				} else {

					if(!$ad_connention = @ldap_connect(core_settings::i()->get('CONFIG_SERVERS_LDAP_IP'))) {
						//return false;
					}
					ldap_set_option($ad_connention, LDAP_OPT_PROTOCOL_VERSION, 3);
					ldap_set_option($ad_connention, LDAP_OPT_REFERRALS, 0);
					if (!@ldap_bind($ad_connention, core_settings::i()->get('CONFIG_SERVERS_LDAP_BIND_USER') . core_settings::i()->get('CONFIG_SERVERS_LDAP_USER_SUFFIX'), core_settings::i()->get('CONFIG_SERVERS_LDAP_BIND_PASS'))){
						//return false;
					}

					$dn = "OU=Staff,OU=Users,OU=TC,DC=ad,DC=Trafford,DC=ac,DC=uk";
					$filter='(|(sAMAccountName=' . $username . '*))';
					$justthese = array("givenName", "sn", "sAMAccountName", "title", "description","department","telephoneNumber","physicalDeliveryOfficeName","mail");

					$sr=ldap_search($ad_connention, $dn, $filter, $justthese);
					$info = ldap_get_entries($ad_connention, $sr);

					if($info['count'] > 0) {

						$user = new user_itrafford(
							NULL,
							isset($info[0]['samaccountname'][0]) ? $info[0]['samaccountname'][0] : '',
							'firstvisit',
							isset($info[0]['givenname'][0]) ? $info[0]['givenname'][0] : '',
							isset($info[0]['sn'][0]) ? $info[0]['sn'][0] : '',
							'staff',
							NULL,
							NULL,
							isset($info[0]['title'][0]) ? $info[0]['title'][0] : '',
							isset($info[0]['description'][0]) ? $info[0]['description'][0] : '',
							isset($info[0]['department'][0]) ? $info[0]['department'][0] : '',
							isset($info[0]['telephonenumber'][0]) ? $info[0]['telephonenumber'][0] : '',
							isset($info[0]['mail'][0]) ? $info[0]['mail'][0] : '',
							isset($info[0]['physicalDeliveryOfficeName'][0]) ? $info[0]['physicalDeliveryOfficeName'][0] : '',
							NULL
						);
						$model_users->create($user);
						return true;

					} else {

						$dn = "OU=Students,OU=Users,OU=TC,DC=ad,DC=Trafford,DC=ac,DC=uk";
						$filter='(|(sAMAccountName=' . $username . '*))';
						$justthese = array("givenName", "sn", "sAMAccountName", "title", "description","department","telephoneNumber","physicalDeliveryOfficeName","mail");

						$sr=ldap_search($ad_connention, $dn, $filter, $justthese);
						$info = ldap_get_entries($ad_connention, $sr);

						if($info['count'] > 0) {
							$user = new user_itrafford(
								NULL,
								isset($info[0]['samaccountname'][0]) ? $info[0]['samaccountname'][0] : '',
								'firstvisit',
								isset($info[0]['givenname'][0]) ? $info[0]['givenname'][0] : '',
								isset($info[0]['sn'][0]) ? $info[0]['sn'][0] : '',
								'student',
								NULL,
								NULL,
								isset($info[0]['title'][0]) ? $info[0]['title'][0] : '',
								isset($info[0]['description'][0]) ? $info[0]['description'][0] : '',
								isset($info[0]['department'][0]) ? $info[0]['department'][0] : '',
								isset($info[0]['telephonenumber'][0]) ? $info[0]['telephonenumber'][0] : '',
								isset($info[0]['mail'][0]) ? $info[0]['mail'][0] : '',
								isset($info[0]['physicalDeliveryOfficeName'][0]) ? $info[0]['physicalDeliveryOfficeName'][0] : '',
								NULL
							);
							$model_users->create($user);
							return true;
						}

					}

					return false;
				}
			} else {
				return false;
			}
		}

	}
