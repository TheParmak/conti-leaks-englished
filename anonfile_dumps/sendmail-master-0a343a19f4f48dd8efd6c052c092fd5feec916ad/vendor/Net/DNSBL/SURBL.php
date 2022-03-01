<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PEAR::Net_DNSBL
 *
 * This class acts as interface to generic Realtime Blocking Lists
 * (RBL)
 *
 * PHP versions 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * Net_DNSBL looks up an supplied host if it's listed in 1-n supplied
 * Blacklists
 *
 * @category  Net
 * @package   Net_DNSBL
 * @author    Sebastian Nohn <sebastian@nohn.net>
 * @author    Ammar Ibrahim <fixxme@fixme.com>
 * @copyright 2004-2012 Sebastian Nohn <sebastian@nohn.net>
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id: SURBL.php 325344 2012-04-20 04:31:30Z nohn $
 * @link      http://pear.php.net/package/Net_DNSBL
 * @see       Net_DNS2
 * @since     File available since Release 1.0.0
 */

require_once 'Cache/Lite.php';
require_once 'HTTP/Request2.php';
require_once 'Net/DNSBL.php';
require_once 'PEAR.php';

/**
 * PEAR::Net_DNSBL_SURBL
 *
 * This class acts as interface to the SURBL - Spam URI Realtime Blocklists.
 *
 * Services_SURBL looks up an supplied URI if it's listed in a
 * Spam URI Realtime Blocklists.
 *
 * @category Net
 * @package  Net_DNSBL
 * @author   Sebastian Nohn <sebastian@nohn.net>
 * @license  http://www.php.net/license/3_01.txt PHP License 3.01
 * @version  Release: 1.3.7
 * @link     http://pear.php.net/package/net_dnsbl Package Home
 */

class Net_DNSBL_SURBL extends Net_DNSBL
{

    /**     
     * Array of blacklists.
     *
     * Must have one or more elements.
     *
     * @var    string[]
     * @access protected
     */
     protected $blacklists = array('multi.surbl.org');

    /**
     * File containing whitelisted hosts.
     *
     * There are some whitelisted hosts (co.uk for example). This
     * requires the package to not ask the domain name but the host
     * name (spammer.co.uk instead of co.uk).
     * 
     * @var    string
     * @see    $twoLevelCcTld
     * @access protected
     */
    protected $doubleCcTldFile = 'http://george.surbl.org/two-level-tlds';

    /**
     * Check if the last two parts of the FQDN are whitelisted.
     *
     * @param string $fqdn Host to check if it is whitelisted.
     *
     * @access protected
     * @return boolean True if the host is whitelisted
     */
    protected function isDoubleCcTld($fqdn)
    {
        // 30 Days should be way enough
        $options = array(
                         'lifeTime' => '2592000',
                         'automaticSerialization' => true
                         );
        $id      = md5($this->doubleCcTldFile);

        $cache = new Cache_Lite($options);
        if ($data = $cache->get($id)) {
            // Cache hit
        } else {
            // Cache miss
            $http = new HTTP_Request2($this->doubleCcTldFile);
            if (!PEAR::isError($http->send())) {
                $data = $http->getBody();
            }
            $data = explode("\n", $data);
            $data = array_flip($data);
            $cache->save($data, $id);
        } // if 
        if (array_key_exists($fqdn, $data)) {
            return true;
        } else {
            return false;
        } // if
    } // function

    /**
     * Get Hostname to ask for.
     *
     * Performs the following steps:
     *
     * (1) Extract the hostname from the given URI
     * (2) Check if the "hostname" is an ip
     * (3a) IS_IP Reverse the IP (1.2.3.4 -> 4.3.2.1)
     * (3b) IS_FQDN Check if is in "CC-2-level-TLD"
     * (3b1) IS_IN_2LEVEL: we want the last three names
     * (3b2) IS_NOT_2LEVEL: we want the last two names
     * (4) return the FQDN to query.
     *
     * @param string $uri       URL to check. 
     * @param string $blacklist Blacklist to check against. 
     *
     * @access protected
     * @return string Host to lookup
     */
    protected function getHostForLookup($uri, $blacklist) 
    {
        // (1) Extract the hostname from the given URI
        $host       = '';
        $parsed_uri = parse_url($uri);

        if (empty($parsed_uri['host'])) {
            return false;
        }

        $host       = urldecode($parsed_uri['host']);
        // (2) Check if the "hostname" is an ip
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            // (3a) IS_IP Reverse the IP (1.2.3.4 -> 4.3.2.1)
            $host = $this->reverseIp($host);
        } else {
            $host_elements = explode('.', $host);
            while (count($host_elements) > 3) {
                array_shift($host_elements);
            } // while
            $host_3_elements = implode('.', $host_elements);
            
            $host_elements = explode('.', $host);
            while (count($host_elements) > 2) {
                array_shift($host_elements);
            } // while
            $host_2_elements = implode('.', $host_elements);
            
            // (3b) IS_FQDN Check if is in "CC-2-level-TLD"
            if ($this->isDoubleCcTld($host_2_elements)) {
                // (3b1) IS_IN_2LEVEL: we want the last three names
                $host = $host_3_elements;
            } else {
                // (3b2) IS_NOT_2LEVEL: we want the last two names
                $host = $host_2_elements;
            } // if
        } // if
        // (4) return the FQDN to query
        $host .= '.'.$blacklist;
        return $host;
    } // function
    
} // class
?>
