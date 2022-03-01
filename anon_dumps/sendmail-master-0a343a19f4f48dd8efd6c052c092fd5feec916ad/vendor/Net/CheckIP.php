<?php
/* 
 * Copyright (c) 2002-2006 Martin Jansen
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the 
 * "Software"), to deal in the Software without restriction, including 
 * without limitation the rights to use, copy, modify, merge, publish, 
 * distribute, sublicense, and/or sell copies of the Software, and to 
 * permit persons to whom the Software is furnished to do so, subject to 
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
 * DEALINGS IN THE SOFTWARE.
 *
 * $Id: CheckIP.php,v 1.8 2006/12/15 17:42:26 mj Exp $
 */

/**
* Class to validate the syntax of IPv4 adresses
*
* Usage:
*   <?php
*   require_once "Net/CheckIP.php";
*     
*   if (Net_CheckIP::check_ip("your_ip_goes_here")) {
*       // Syntax of the IP is ok
*   }
*   ?>
*
* @author  Martin Jansen <mj@php.net>
* @author  Guido Haeger <gh-lists@ecora.de>
* @package Net_CheckIP
* @version 1.1
* @access  public
*/
class Net_CheckIP
{

    /**
    * Validate the syntax of the given IP adress
    *
    * This function splits the IP address in 4 pieces
    * (separated by ".") and checks for each piece
    * if it's an integer value between 0 and 255.
    * If all 4 parameters pass this test, the function
    * returns true.
    *
    * @param  string $ip IP adress
    * @return bool       true if syntax is valid, otherwise false
    */
    function check_ip($ip)
    {
        $oct = explode('.', $ip);
        if (count($oct) != 4) {
            return false;
        }

        for ($i = 0; $i < 4; $i++) {
            if (!preg_match("/^[0-9]+$/", $oct[$i])) {
                return false;
            }

            if ($oct[$i] < 0 || $oct[$i] > 255) {
                return false;
            }
        }

        return true;
    }
}
?>
