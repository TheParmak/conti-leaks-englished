PREDICTION

The purpose of the document is to give a quick dive into computer and network hacking;
the audience - IT professionals with no such experience, with experience in system programming and administration.


NETWORK LANDSCAPES

Any (almost) modern network can be hacked.
This is due to:
- The redundancy of networks: having many services, different entry points to the same network;
- the priority of convenience over security: a prison is safe, but it is very difficult to do anything in it;
- the human factor: configuration errors, social engineering.
The second point is reinforced by the tangible profit reaction to the slightest slowdown in turnover in the commercial sector,
so without a military transition, the networks of capitalism will always be leaky)
https://habr.com/ru/company/selectel/blog/576482/

If there are no *known* vulnerabilities in the entry points found, this only means that
- you have to look for other entry points;
- you have to look for vulnerabilities yourself (if you really need to get into the network);
- you have to look for a person;
- you have to look for another target with the same information.

Those organizations that have protection requirements imposed by the government are good at protecting networks.
These are not necessarily military networks or security institutions: if you keep names and personal data of customers,
you are obliged to take measures to protect them. Protecting trade secrets is "your" problem.

The value of a target is often inversely proportional to its defense.
Military networks may have 196x calcs lists (useful for military analysts),
and a weakly protected commercial network, or a personal laptop, may contain crucial pharma/IT/engineering developments.

But this is not always the case.
There are super fortresses inside poorly protected networks that can be taken either by a versatile team or an extra-class hacker.
Such scenarios are common in Standoffs (Hack The Box etc) for white hats.
If you're not an extra-class hacker, use your wits (drop the server and catch the network/keyword, outsource the task, etc.)


TECHNIQUE, TACTICS AND STRATEGY

- strategy: why do we need hacking? what to look for, what targets to choose? *
- tactics: the order of actions outside and inside the network, leading us to achieve the goal;
- technique: -scandals-intrigue-investigation-tools, vulnerabilities, research.

(*very incomplete illustration https://habr.com/ru/company/cloud4y/blog/551376/ orientation to small commerce)

All three levels in capable hands - from target selection, reconnaissance, implementation and exploit testing, to covering the target and the prize:
https://gist.github.com/jaredsburrows/9e121d2e5f1147ab12a696cf548b90b0


RECONNAISSANCE AND TARGET SELECTION

If there is no goal to get into a specific network and you have an exploit, you use a scan of the network
(the whole Internet or ranges of interest) in search of vulnerable services.
If you do not have time, the well-known service shodan.io will help, but it is better to have your own scanner.

A targeted attack (a specific object) requires reconnaissance.
You can start by analyzing a company's domain zone and its public services.
Large corporations with huge networks usually have their own autonomous systems (AS, Autonomous System), with a known range of networks.
Mapping at least some external services can be done using WHOIS (https://hackertarget.com/whois-lookup/) and DNS (https://habr.com/ru/post/554458/),
as well as the desire of network administrators to maintain hierarchy in the description of domain zones.

There are scouting search engines like https://www.zoominfo.com with general and detailed information about companies.

Next, we look for the weakest link (see below).

Social engineering requires knowledge of personalities.
Everything is important: phone numbers, place of residence, dog's name, hometown, favorite color, favorite band, hobbies.
Of particular importance: your candidate's personal network of contacts, especially business contacts.
The structure of organizations reflects the structure of society.
As you move from one person to another through a network of contacts, you can change your entry point within one network, or open up new networks.
Both OSINT intelligence tools are used to gather information,
and information found in previously opened networks about contacts (Outlook address books, correspondence, etc.).
Reconnaissance for social engineering is called doxing: https://securelist.ru/corporate-doxing/101055/

This data is then used either through phishing emails or phone calls.
In both cases, the load is triggered by a person.

OSINT toolkit
* A wide range of search engines
  https://github.com/laramies/theHarvester - collection of email addresses, subdomains, virtual hosts, open ports/banners, employee names from public sources.
  https://github.com/Bafomet666/OSINT-SAN OSINT-SAN - a wide range harvester
  https://mor-pah.net/software/dmitry-deepmagic-information-gathering-tool/ - Dmitry - analog of previous tool
  https://github.com/0xInfection/TIDoS-Framework - universal framework with network analysis features (DNS, whois, emails)
  https://github.com/smicallef/spiderfoot
  https://osintframework.com/ - OSINT tools rubricator, general search by social networks, large set of different search tools:
  https://hunter.io/ - collecting email info by domain name
  https://hackertarget.com/

* Company search engines
  https://www.zoominfo.com - company data search engine
  https://opencorpdata.com/ - Open corporate business database
  https://domainbigdata.com/ - big database of domains and whois records
  https://opencorporates.com/ - largest open database of companies in the world
  https://www.sec.gov/edgar/searchedgar/cik.htm - EDGAR Company Filings | Central Index Key Lookup
  http://www.orsr.sk/search_osoba.asp?lan=en - business register | Ministry of Justice of the Slovak Republic
  More tools in the article https://habr.com/ru/company/pentestit/blog/554006/

* Search by USERNAME/NICKNAME:
  https://namechk.com/
  https://github.com/snooppr/snoop

* Search by EMAIL:
  https://haveibeenpwned.com/
  https://hacked-emails.com/
  https://ghostproject.fr/
  https://weleakinfo.com/
  https://pipl.com/
  https://leakedsource.ru/
  http://mailtester.com/ - E-mail address verification
  Skype app

* Search by phone number:
  https://phonenumber.to
  https://pipl.com/
  GetContact" application
  NumBuster app
  Truecaller app or https://www.truecaller.com/
  http://doska-org.ru/
  Skype app

* Network mapping
  https://www.shodan.io/
  https://spyse.com/
  https://crt.sh/ - search for subdomains
  https://archive.org/web/ - search in the past (view sites as of a certain date)
  https://viewdns.info/ - data about a given website or IP address
  https://github.com/Fadavvi/Sub-Drill Simple script for finding subdomains based on [free] online services without any dependency to API-keys
  https://builtwith.com/ - what the site is built on

* Search for the location of the cellular operator's base station:
  http://unwiredlabs.com
  http://xinit.ru/bs/

* Search by social networks, unsorted
  http://sanstv.ru/photomap - Retrieving photos from social networks from the local area (by geo-tagging):
  https://foller.me/ - Twitter Analytics
  https://followerwonk.com/ - social analytics mega-tool that digs through Twitter data
  https://tinfoleak.com/ - Search for Twitter users leaks
  https://twicsy.com/ - Twicsy is social pics
  https://www.spokeo.com/- iskat infu po USA.
  https://github.com/jivoi/awesome-osint

* Literature
  https://anonfiles.com/X0md34ycu1/Operator_Handbook_Red_Team_OSINT_Blue_Team_Reference_pdf
  Bellingcat's Online Investigation Toolkit - https://docs.google.com/document/d/1BfLPJpRtyq4RFtHJoNpvWQjmGnyVkfE2HYoICKOGguA/edit

* MITM/phishing scanners (require interaction with the target):
  https://beefproject.com/
  https://github.com/beefproject/beef BeeF to collect information about browsers and their operation,
  getting information about social networking sessions, TOR availability, visiting interesting sites, etc.
  https://github.com/kgretzky/evilginx2 - to intercept sessions and bypass 2FA on websites


In what follows, we consider Windows networks.
The advantage of operating them is less effort compared to operating Linux networks.
It is easier to open the Windows segment of the network, and get passwords/admins keys for the Linux segment.
The reverse migration (Linux->Windows) is much more difficult simply because Linux does not have the necessary tools.


SELECTING A POINT AND METHOD OF ENTRY

Any publicly available network service (public IP address:port) is a potential entry point.
If you can't get through, it means that there is no *known* vulnerability in it.
There are statistics that certain vectors are more frequent:
- narrowly-specialized hardware (printers, routers, smart firewalls (router-like hardware with a firewall function));
- Popular web applications (wordpress, other cms-systems, etc.);
- Entries from botnets.

The firmware of narrowly-specialized hardware is not updated - this in itself is a dangerous process;
manufacturers ignore support for hardware older than H years (and fresh hardware too).
In addition, hardware as well as web applications are not taken seriously in the context of security.

In other cases, social engineering (a letter or a phone call) is used.

NETWORK PROTECTION

An effective way to detect intrusion is to detect traffic anomalies.
Log legitimate traffic, remember the approximate picture (protocols, exchange frequency, packet weight, network subscribers, etc.).
Find and study sources of strange traffic.
https://habr.com/ru/company/dsol/blog/541832/
https://habr.com/ru/company/otus/blog/541582/
Suricata https://suricata-ids.org/
Example of manual analysis: https://malware-traffic-analysis.net/
PyWhat library for automatic traffic parsing
https://habr.com/ru/company/dcmiran/news/t/563206/
https://github.com/bee-san/pyWhat

Traffic is logged, through network taps, for the purposes of forensics, legal evidence, and incident resolution.

According to various sources, SSL/TLS traffic cannot/can be decrypted due to proxying using a MITM certificate in the corpnet.
(Palo Alto firewalls definitely have an SSL decryption option).
In the first case, machine learning is used to find anomalies: https://github.com/WalterDiong/TLS-Malware-Detection-with-Machine-Learning
(load lengths, frequency distribution and protocols/ports used, exchange frequency, ... - randomize it all)
In the second case, SSL cert pinning can be used in the bot's network subsystem implementation to detect MITM.

Technical means of network IS monitoring: https://habr.com/ru/post/549050/

A dubious but effective defense is whitelisting work.
Here's a list of allowed sites, and hell, you don't need to go to sites for work, sorry, no web.
You don't need messengers either, here's your corporate email.
Here's a list of white apps that can be run on your computer; everything else is blocked.

TOOLS

The main hacking tools are frameworks.
You can do without them, but they give automation:
1. Metasploit Framework (MSF) (+armitage GUI) - the largest collection of splits and modules
2. Core Impact (+impacket python) - the most pentest-friendly features (Windows only)
3. Powershell Empire - pure powershell framework with all that implies
4. Posh2c
5. Koadik - these two are exotic, i.e. they have less detectable traffic
6. Cobalt Strike - expandability
   https://www.cobaltstrike.com/downloads/csmanual43.pdf
7. Burp Suite - web oriented, very popular
8. Pupy - RAT (Remote Administration Tool) in Python, difficult for AV, since this is not the usual AV native code and not "native" for Windows (and AMSI) scripting language
   https://github.com/n1nj4sec/pupy
   https://ptestmethod.readthedocs.io/en/latest/pupy.html
and an injector to it https://github.com/infodox/python-dll-injection

Most of the basic operations can be done from the regular cmd shell with the standard means of the OS.

A framework is:
- splots;
- scanners to them;
- search for misconfigurations;
- phasers;
- sniffers;
- task scripts.
Approximate composition of the framework https://www.offensive-security.com/metasploit-unleashed/modules-and-locations/

Network Scanners
Enum4linux https://github.com/CiscoCXSecurity/enum4linux (Windows/Samba network resources)

Password Stylers, Password Dumpers, Password Brute Force
Mimikatz review https://habr.com/ru/company/varonis/blog/539340/
Patator https://github.com/lanjelot/patator (password brute forcer)

SQL injection and Web vulnerability scanners
https://habr.com/ru/post/542190/
SQLMap http://sqlmap.org/
jSQL-Injection https://github.com/ron190/jsql-injection
https://github.com/commixproject/commix command injection scanner, article https://habr.com/ru/post/550252/
Wapiti https://wapiti.sourceforge.io/ (web scanner/fuzzer)
OWASP ZAP https://www.zaproxy.org/ (web scanner/fuzzer/MITM proxy)

The common disadvantage of all publicly available tools is that they are known to ABs.
You have to make a serious effort to clean them, or get your own private set of clean tools.


ATTACK TACTICS

The primary target of most attacks is the Active Directory Domain Controller or Domain Controller of a Windows network.
Access to these nodes gives at least broad vectors to move around the network, at most full control over the network.
Active Directory is more operationally friendly, because there are typical misconfigurations, vulnerabilities of the service itself, shared resources,
making the work of both employees and hackers comfortable.
Control over Domain Controller of a Windows network gives at least:
- Logs of authorizations within the domain of various users on domain workstations
- NTLM hashes of passwords of all domain users, which don't even have to be brute-force (cmd5.org service).
The hashes are bruteforced quickly and easily as they are not salted.
- Almost always domain controller holds DNS server with all records.

That is, access to the DC (both AD and Windows) gives a lot of information at once also due to the presence of all the services on the DC - LDAP, Kerberos, DNS.
LDAP allows you to "talk" inside AD, to access all the services involved in Active Directory authorization.
https://www.varonis.com/blog/the-difference-between-active-directory-and-ldap/

It is not worth fixating on DCs, since these nodes are important and tightly monitored.
For anchoring, server machines that hold some services knocking outside are preferred,
that is, which are allowed outbound traffic.

The ideal backdoor is always legitimate access - knocking services like VPN/thin client/RDWeb/RDP etc,
that you can disguise your traffic as. Looking for any suitable for this creeds and computers on the network.
Next priority may be routing traffic through application/business software (Outlook, IIS/PHP webshell, etc).
Next, DNS/TCP/HTTPS protocols, using standard Windows tools for hardening
(an obvious disadvantage is that the use of standard operating system tools for hardening is tightly monitored).

IT company networks usually have their own virtual machine farms.
One means of avoiding detection can be to use your own clean virtual machine (without AB, EDR) with access to the network.

The secondary target is any host in the DMZ (the "seeing" Internet segment of the network).
The next most convenient target is any machine with DMZ visibility.

One password for everything, and the user's presence on multiple hosts is the biggest help to a hacker.

The development of the attack is iterative and involves the use at each subsequent stage of the data obtained at the previous stages:
1. scanned, subpoenaed, scrubbed
2. tried the mined creds on the next/previous node; expanded presence
3. goto 1.

If we have a shell from the domain machine, an approximate scenario for the development of the attack:
1. our first goal in taking over the AD forest is to find the domain administrator's password hashes;
any AD hash means complete compromise of the domain and all its users.
NTLM hashes allow to move around the network without knowing the password, so it is not necessary to brute force them.
2. query the domain composition (with the adfind.exe utility, net /view /all /domain, etc.);
3. analyze data: passwords of users/service accounts/even domain administrators themselves, in addition to information about network structure);
4. Within local machine, check whether we can increase permissions to the SYSTEM.
The next step is to check if we could do it or not.
SYSTEM allows
5. poison the ARP cache and pretend to be another host in order to intercept its traffic and its passwords (their hashes);
6. dump hashes (ntdsutil, mimikatz) on the local node.
All the hashes used are used to develop the attack (one password for everything!)
If SYSTEM could not be obtained:
7. go through the current machine with a styler and look for accesses inside the network
8. check all visible domain machines for RCE (remote code execution vulnerabilities)
9. check if the current user has administrative privileges at any machine in the domain: see groups he/she is a member of,
make an empirical prediction, then check if he/she sees $ADMIN somewhere via SMB;
If it does, we jump there and get the SYSTEM there.
10. we do a kerberoast attack to get kerberos hashes for further brute force;
11. if the network is small, we can gently brute-force the users, checking the lockout threshold beforehand (so they won't be blocked by the brute-force);
12. if we see a writable directory inetpub - write the aspx load there and execute by accessing it via the web;
13. Scan all subnets for available network devices and check the available crads.

AD Hacks: https://github.com/Integration-IT/Active-Directory-Exploitation-Cheat-Sheet

MITRE Matrix for Windows
https://attack.mitre.org/matrices/enterprise/windows/

Working with AD passwords: https://habr.com/ru/post/543806/

MITRE

It is a systematic database of hacking techniques, giving different cuts and aspects of the tactics.
The official purpose is to enhance protection, to help IS departments and specialists.
Absolutely all (no) hacks are classified and get here.

Incomplete and in Russian Adversarial Tactics, Techniques & Common Knowledge (ATT@CK):
https://habr.com/post/423405/
https://habr.com/post/424027/
https://habr.com/post/425177/
https://habr.com/post/428602/
https://habr.com/post/432624/

If you're stumped, look here for non-standard (unknown to you personally) tricks and hacks.

PURPOSE - INFORMATION

What it's all about.
1. Mail, correspondence, contacts, address books, lists of counterparties
2. databases
3. program source code
4. Documents
5. Accounting
6. Design documentation
7. Passwords to other networks.
8. Electronic wallets
TODO
what files to look for (extensions)
TODO how is it downloaded?

ANALYTICS, ATTRIBUTION, HANDWRITING

Analyzing open sources about your activities is important: you will know the part of the tricks that have already been uncovered, and therefore they have become ineffective.
However, you do not know the part of the tricks that have not been disclosed. For the sake of this, the adversary may launch disinformation, concealment, and deception.

Sooner or later any hacker reads the analysis of his works of art in IS articles.
And is surprised to find that important information has been left out and insignificant information has been emphasized.
There are several reasons for this:
- True incident reports are information with a veneer of
- no one will give valid prescriptions to the public
- most articles are advertising "buy our IS solution", impeding facts are ignored, insignificant facts are hypertrophied.
- Gods do not burn pots - analysts may really miss important details.
- they know everything, but don't let on - to use it against you
- along with the true facts you can be fed your own bullshit.

Be careful, each APT group has its own known safe handwriting - favorite tactics for entering, fixing, and moving,
YARA tool profiles.

View from the IB: https://habr.com/ru/company/group-ib/blog/545104/

