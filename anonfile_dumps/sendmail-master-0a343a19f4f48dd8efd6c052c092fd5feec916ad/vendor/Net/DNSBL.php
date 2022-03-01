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
 * @version   CVS: $Id: DNSBL.php 325344 2012-04-20 04:31:30Z nohn $
 * @link      http://pear.php.net/package/Net_DNSBL
 * @see       Net_DNS
 * @since     File available since Release 1.0.0
 */

require_once base_path('vendor/Net/DNS.php');

/**
 * PEAR::Net_DNSBL
 *
 * This class acts as interface to DNSBLs
 *
 * Net_DNSBL looks up an supplied IP if it's listed in a
 * DNS Blacklist.
 *
 * @category Net
 * @package  Net_DNSBL
 * @author   Sebastian Nohn <sebastian@nohn.net>
 * @license  http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version  Release: 1.3.7
 * @link     http://pear.php.net/package/net_dnsbl Package Home
 */

class Net_DNSBL
{

    /**     
     * Array of blacklists.
     *
     * Must have one or more elements.
     *
     * @var    array
     * @access protected
     */
    protected $blacklists = array('sbl-xbl.spamhaus.org',
                                  'bl.spamcop.net');

    /**     
     * Array of Results
     *
     * @var    array
     * @access protected
     */
    protected $results = array();

    /**
     * Set the blacklist to a desired blacklist.
     *
     * @param array $blacklists Array of blacklists to use.
     *
     * @access public
     * @return bool true if the operation was successful
     */
    public function setBlacklists($blacklists)
    {
        if (is_array($blacklists)) {
            $this->blacklists = $blacklists;
            return true;
        } else {
            return false;
        } // if
    } // function

    /**
     * Get the blacklists.
     *
     * @access public
     * @return array Currently set blacklists.
     */
    public function getBlacklists()
    {
        return $this->blacklists;
    }

    /** 
     * Returns Blacklist and Reply from the Blacklist, a host is listed in.
     *
     * @param string $host Host to check
     *
     * @access public
     * @return array result. $result['dnsbl'] contains DNSBL,
     *               $result['record'] contains returned DNS record.
     */
    public function getDetails($host)
    {
        if (isset($this->results[$host])) {
            return $this->results[$host];
        } else {
            return false;
        }
    } // function

    /**
     * Returns Blacklist, host is listed in.
     *
     * @param string $host Host to check
     *
     * @access public
     * @return bl, a host is listed in or false
     */
    public function getListingBl($host)
    {
        if (isset($this->results[$host]['dnsbl'])) {
            return $this->results[$host]['dnsbl'];
        }

        if (isset($this->results[$host]) && is_array($this->results[$host])) {
            $result = array_keys($this->results[$host]);
            if ($result == null) {
                return false;
            }

            return 'multiple ('.implode(', ', $result).')';          
        }
        return false;
    } // function

    /**
     * Returns Blacklists, host is listed in. isListed() must have
     * been called with checkall = true
     *
     * @param string $host Host to check
     *
     * @access public
     * @return array blacklists, a host is listed in or false
     */
    public function getListingBls($host)
    {
        if (isset($this->results[$host]) && is_array($this->results[$host])) {
            $result = array_keys($this->results[$host]);
            if ($result == null) {
                return false;
            }

            return $result;           
        }

        return false;
        
    } // function

    /**
     * Returns result, when a host is listed.
     *
     * @param string $host Host to check
     *
     * @access public
     * @return bl, a host is listed in or false
     */
    public function getListingRecord($host)
    {
        if (isset($this->results[$host]['record'])) {
            return $this->results[$host]['record'];
        } else {
            return false;
        }
    } // function

    /**
     * Returns TXT-Records, when a host is listed.
     *
     * @param string $host Host to check
     *
     * @access public
     * @return array TXT-Records for this host
     */
    public function getTxt($host)
    {
        if (isset($this->results[$host]['txt'])) {
            return $this->results[$host]['txt'];
        } else {
            return false;
        }
    } // function

    /** 
     * Checks if the supplied Host is listed in one or more of the
     * RBLs.
     *
     * @param string  $host     Host to check for being listed.
     * @param boolean $checkall Iterate through all blacklists and
     *                          return all A records or stop after 
     *                          the first hit?
     *
     * @access public
     * @return boolean true if the checked host is listed in a blacklist.
     */
    public function isListed($host, $checkall = false)
    {
        $isListed = false;
        $resolver = new Net_DNS_Resolver;

        if (!is_string($host)) {
            return false;
        }

        foreach ($this->blacklists as $blacklist) {
            $response = $resolver->query($this->getHostForLookup($host, $blacklist));
            if ($response) {
                $isListed = true;
                if ($checkall) {
                    $this->results[$host][$blacklist] = array();
                    foreach ($response->answer as $answer) {
                        $this->results[$host][$blacklist]['record'][] 
                            = $answer->address;
                    }
                    $response_txt 
                        = $resolver->query(
                            $this->getHostForLookup(
                                $host, 
                                $blacklist
                            ), 
                            'TXT'
                        );
                    if (isset($response_txt->answer)) {
                        foreach ($response_txt->answer as $txt) {
                            $this->results[$host][$blacklist]['txt'][]
                                = $txt->text[0];
                        }
                    }
                } else {
                    $this->results[$host]['dnsbl']  = $blacklist;
                    $this->results[$host]['record'] = $response->answer[0]->address;
                    $response_txt
                        = $resolver->query(
                            $this->getHostForLookup(
                                $host, 
                                $blacklist
                            ), 
                            'TXT'
                        );
                    if ((isset($response_txt)) && ($response_txt != false)) {
                        foreach ($response_txt->answer as $txt) {
                            $this->results[$host]['txt'][] = $txt->text[0];
                        }
                    }
                    // if the Host was listed we don't need to check other RBLs,
                    break;
                }
            } // if
        } // foreach
        
        return $isListed;
    } // function

    /** 
     * Get host to lookup. Lookup a host if neccessary and get the
     * complete FQDN to lookup.
     *
     * @param string $host      Host OR IP to use for building the lookup.
     * @param string $blacklist Blacklist to use for building the lookup.
     *
     * @access protected
     * @return string Ready to use host to lookup
     */    
    protected function getHostForLookup($host, $blacklist) 
    {
        // Currently only works for v4 addresses.
        if (filter_var($host, FILTER_VALIDATE_IP)) {
                                        $ip = $host;
        } else {
            $resolver = new Net_DNS_Resolver;
            $response = $resolver->query($host);
            $ip       = isset($response->answer[0]->address) ? 
                        $response->answer[0]->address : null;
        }
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return;
        }

        return $this->buildLookUpHost($ip, $blacklist);
    } // function

    /**
     * Build the host to lookup from an IP.
     *
     * @param string $ip        IP to use for building the lookup.
     * @param string $blacklist Blacklist to use for building the lookup.
     *
     * @access protected
     * @return string Ready to use host to lookup
     */    
    protected function buildLookUpHost($ip, $blacklist)
    {
        return $this->reverseIp($ip).'.'.$blacklist;        
    } // function

    /**
     * Reverse the order of an IP. 127.0.0.1 -> 1.0.0.127. Currently
     * only works for v4-adresses
     *
     * @param string $ip IP address to reverse.
     *
     * @access protected
     * @return string Reversed IP
     */    
    protected function reverseIp($ip) 
    {        
        return implode('.', array_reverse(explode('.', $ip)));        
    } // function
} // class
?>
