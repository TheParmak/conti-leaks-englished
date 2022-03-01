<?php
/**
*  License Information:
*
*  Net_DNS:  A resolver library for PHP
*  Copyright (c) 2002-2003 Eric Kilfoil eric@ypass.net
*  Maintainers:
*  Marco Kaiser <bate@php.net>
*  Florian Anderiasch <fa@php.net>
*
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.01 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_01.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*/

/* Net_DNS_RR_SOA definition {{{ */
/**
 * A representation of a resource record of type <b>SOA</b>
 *
 * @package Net_DNS
 */
class Net_DNS_RR_SOA extends Net_DNS_RR
{
    /* class variable definitions {{{ */
    var $name;
    var $type;
    var $class;
    var $ttl;
    var $rdlength;
    var $rdata;
    var $mname;
    var $rname;
    var $serial;
    var $refresh;
    var $retry;
    var $expire;
    var $minimum;

    /* }}} */
    /* class constructor - RR(&$rro, $data, $offset = '') {{{ */
    function Net_DNS_RR_SOA(&$rro, $data, $offset = '')
    {
        $this->name = $rro->name;
        $this->type = $rro->type;
        $this->class = $rro->class;
        $this->ttl = $rro->ttl;
        $this->rdlength = $rro->rdlength;
        $this->rdata = $rro->rdata;

        if ($offset) {
            if ($this->rdlength > 0) {
                $packet = new Net_DNS_Packet();

                list($mname, $offset) = $packet->dn_expand($data, $offset);
                list($rname, $offset) = $packet->dn_expand($data, $offset);

                $a = unpack("@$offset/N5soavals", $data);
                $this->mname = $mname;
                $this->rname = $rname;
                $this->serial = $a['soavals1'];
                $this->refresh = $a['soavals2'];
                $this->retry = $a['soavals3'];
                $this->expire = $a['soavals4'];
                $this->minimum = $a['soavals5'];
            }
        } elseif (is_array($data)) {
            $this->mname = $data['mname'];
            $this->rname = $data['rname'];
            $this->serial = $data['serial'];
            $this->refresh = $data['refresh'];
            $this->retry = $data['retry'];
            $this->expire = $data['expire'];
            $this->minimum = $data['minimum'];
        } else {
            if (preg_match("/([^ \t]+)[ \t]+([^ \t]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]*$/", $data, $regs))
            {
                $this->mname = preg_replace('/(.*)\.$/', '\\1', $regs[1]);
                $this->rname = preg_replace('/(.*)\.$/', '\\1', $regs[2]);
                $this->serial = $regs[3];
                $this->refresh = $regs[4];
                $this->retry = $regs[5];
                $this->expire = $regs[6];
                $this->minimum = $regs[7];
            }
        }
    }

    /* }}} */
    /* Net_DNS_RR_SOA::rdatastr($pretty = 0) {{{ */
    function rdatastr($pretty = 0)
    {
        if (strlen($this->mname)) {
            if ($pretty) {
                $rdatastr  = $this->mname . '. ' . $this->rname . ". (\n";
                $rdatastr .= "\t\t\t\t\t" . $this->serial . "\t; Serial\n";
                $rdatastr .= "\t\t\t\t\t" . $this->refresh . "\t; Refresh\n";
                $rdatastr .= "\t\t\t\t\t" . $this->retry . "\t; Retry\n";
                $rdatastr .= "\t\t\t\t\t" . $this->expire . "\t; Expire\n";
                $rdatastr .= "\t\t\t\t\t" . $this->minimum . " )\t; Minimum TTL";
            } else {
                $rdatastr  = $this->mname . '. ' . $this->rname . '. ' .
                    $this->serial . ' ' .  $this->refresh . ' ' .  $this->retry . ' ' .
                    $this->expire . ' ' .  $this->minimum;
            }
            return $rdatastr;
        }
        return '; no data';
    }

    /* }}} */
    /* Net_DNS_RR_SOA::rr_rdata($packet, $offset) {{{ */
    function rr_rdata(&$packet, $offset)
    {
        if (strlen($this->mname)) {
            $rdata = $packet->dn_comp($this->mname, $offset);
            $rdata .= $packet->dn_comp($this->rname, $offset + strlen($rdata));
            $rdata .= pack('N5', $this->serial,
                    $this->refresh,
                    $this->retry,
                    $this->expire,
                    $this->minimum);
            return $rdata;
        }
        return null;
    }

    /* }}} */
}
/* }}} */
/* VIM settings {{{
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * soft-stop-width: 4
 * c indent on
 * End:
 * vim600: sw=4 ts=4 sts=4 cindent fdm=marker et
 * vim<600: sw=4 ts=4
 * }}} */
?>
