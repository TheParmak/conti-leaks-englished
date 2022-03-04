A QUICK START ON HIDING, EMBEDDING, AND DEFENDING TECHNIQUES IN WINDOWS

               INTRODUCTION


It should be remembered that none of the defense or attack techniques under consideration are absolute.
There are countermeasures for each.
Some techniques are used despite obsolescence, because:
- they guarantee to run on old operating systems, of which there will always be many;
- nothing has been invented instead.

In parallel with this tutorial, we recommend reading the "Malware development" series of articles on the https://0xpat.github.io blog

               I. CONCEALMENT AND INFILTRATION TECHNIQUES

Windows allows you to manipulate processes in a very wide range,
due to a set of system calls CreateRemoteThread(), ReadProcessMemory(), WriteProcessMemory().
The purpose of these calls is to allow debuggers to work (it logically follows that similar
(It logically follows that similar mechanics are available in all OSes allowing to debug a third-party process.
Linux has them too: see ptrace,
https://habr.com/post/430302/
https://github.com/gaffe23/linux-inject
https://www.evilsocket.net/2015/05/01/dynamically-inject-a-shared-library-into-a-running-process-on-androidarm/ ).
Interesting injection technique without ptrace: https://github.com/DavidBuchanan314/dlinject
A simple injector for Linux: https://stackoverflow.com/questions/24355344/inject-shared-library-into-a-process


So introducing and manipulating other processes (within access rights) is a matter of technique.

1. INJECTION INTO THE PROCESS

Injection into a process is needed to execute your code in someone else's process of the same user, in a situation where there is already access to the system.
Classical targets are browsers (intercepting and replacing traffic to implement a MITM attack), messengers, mail clients, etc.
The sequence of calls is approximately as follows:

OpenProcess - open someone else's process
VirtualAlloc - allocate memory in it
VirtualProtect - let this memory be executed
WriteProcessMemory - write our shell-code into this memory
CreateRemoteThread - run shellcode as a new thread

If the shell code is short and written with relative addressing, this is usually enough.
But this is a rare case, such code is usually written in assembly language.
More often it is necessary to execute large pieces of logic which are written as dlls.
To run the dll in someone else's process this dll must be copied into someone else's memory,
and configure it - write all the imports, configure the addresses of the files, etc.
Normally this is done with the LoadLibrary function.
But it's too conspicuous and also accesses a disk.
There are two libraries that implement completely diskless injection from memory:

https://github.com/stephenfewer/ReflectiveDLLInjection
https://github.com/dismantl/ImprovedReflectiveDLLInjection

The second library is just an improvement of the first one:
- cross-bit injection works (32->64, 64->32, 32->32, 64->64)

In principle, the implementation has an injectable loader that uses only relative addressing.
The injection goes like this:
- A loader is written to one piece of memory
- into another piece of memory is written dll
- control is passed to the loader
- the loader searches the import table for functions LoadLibrary and GetProcAddress by hashes of their names
- the loader searches the memory for dll and sets it up
- after configuring the loader passes control to the DllMain

This technique can be improved by converting the injected loader into an external one:
- it will be executed from the attacking process, not from within the attacked one
- It should replace all memory handling functions with WriteProcessMemory/ReadProcessMemory.
I haven't found the source code of the ready implementation on the net, but there is such an implementation in the wild.

Reflexively load the dll into its own process: https://github.com/fancycode/MemoryModule

Reflective 80-level download: https://github.com/bats3c/DarkLoadLibrary/blob/master/DarkLoadLibrary/src/ldrutils.c
https://www.mdsec.co.uk/2021/06/bypassing-image-load-kernel-callbacks/
Claims to bypass the kernel triggers to load the image into memory, though how this differs from reflective loading is not very clear
(UPD: it is reported that the module's entry does not go into the PEB)

Additionally, execution of arbitrary shell code in the context of the current process:
https://github.com/DimopoulosElias/SimpleShellcodeInjector/blob/master/SimpleShellcodeInjector.c


2. FUNCTION INTERCEPTION (HOOKS)

A technique known for decades:
- in the prologue of most system functions there are several nop (up to 5) provided
specifically for this purpose
- the function's prologue is replaced by our handler with jmp
- The handler calls the original function by the offset of the prologue after the nop (or not - it depends on the logic)

For everything to work without errors, our handler function signature must match the data types exactly,
return type and call convention.

If there is no special prologue, you can still put a hook - not always. You need to look at the contents of the prologue.
Just overwrite jmp instructions must be copied and transferred to the body of the new handler - but there is a creative approach,
and there are no universal recipes.

The Microsoft compiler has a switch: project properties -> C/C++ -> Create code -> Create patchable image
(patchable image) specifically for the purpose of generating empty function dialogs.

This hook is also called a springboard, detour or inline hook.

In the article "Trampolines In x64" https://www.ragestorm.net/blogs/?p=107 you can see variations of trampolines.

This article https://www.malwaretech.com/2013/10/ring3-ring0-rootkit-hook-detection-22.html
more types of hooks (including ring0) - IAT hooks, inline hooks, SSDT hooks, SYSENTER_EIP Hooks, IRP Major Function Hook


3. PROCESS HOLLOWING

Process Hollowing (aka RunPE) is a Windows implementation of the fork()/exec() call pair from Unix. That is, starting a process,
and replace its text with another executable, setting it in process memory and starting from the entry point.
Windows uses CreateProcess() instead of fork()/exec(), doing both fork() and exec() at once.
That is, the process text is substituted, which we cannot control.
That's why we have to implement text substitution, import settings and relocations by ourselves, which is quite difficult
and essentially duplicates the Windows system process loader.

The advantages of this trick are that:
- our code looks like another process
- the other process can be a trusted OS process (explorer, svchost),
added to UAC, firewall and antivirus exceptions.

An implementation with a detailed description is available at
https://github.com/m0n0ph1/Process-Hollowing

Antiviruses recognize the process hollowing by comparing the text of the process in memory and on disk.

VBScript implementation, for use in dropper macros:
https://github.com/itm4n/VBA-RunPE

4. PROCESS DOPPELGäNGING

The purpose of the technique is the same - to hide the name of the real triggering process.
The essence of the execution is different.

- opens an NTFS transaction to write to some executable file. As usual, it will be
any of the trusted processes - svchost, explorer, etc.
- The .exe body is replaced by our text
- process starts
- transaction is rolled back. There is no physical write to the file body.

For all other processes it looks like the original file is started.
But the thread that opened the transaction sees the new text in the file being launched.

The technique has had time to become obsolete, because starting with some version of Windows 10 transactions NTFS seems to have been abolished altogether.

The implementation is available at
https://github.com/hasherezade/process_doppelganging/blob/master/main.cpp

Description in English:
https://hshrzd.wordpress.com/2017/12/18/process-doppelganging-a-new-way-to-impersonate-a-process/

There is a combo of two techniques:
Process Doppelgänging combo Process Hollowing
https://blog.malwarebytes.com/threat-analysis/2018/08/process-doppelganging-meets-process-hollowing_osiris/
Transacted hollowing
https://github.com/hasherezade/transacted_hollowing

4.1. PROCESS HERPADERPING

https://jxy-s.github.io/herpaderping/
https://github.com/jxy-s/herpaderping.git

The technique is similar to Process doppelganging:
- real program text is written to a file and CreateProcess is run
- text is overwritten with noise (e.g., host program text) before real thread is started
- uses race condition in Windows Defender to slip it left-handed program text


5. REPLACING THE PEB

A lightweight technique for hiding a process after the fact.
PEB is a Process Environment Block, a structure with basic information about the process, present in every process.
It contains in particular the process name and the process command line. These can be replaced from within the process and from outside
(by suspending the threads of a process with SuspendThread(), replacing the data with WriteProcessMemory() and resuming the threads
with ResumeThread()). After that you can see the changed process name and command line in the process list.

Another interesting trick is to overwrite the .dll export table after loading into memory.
After all the addresses of all the necessary function addresses have been obtained, you can overwrite the entire export and thus
make it harder to identify the process by the known/unique names of the functions.

Additionally, you can overwrite the resources section if they are not used (or after they have been loaded).
In short, variations with substitution and overwrite of service tables/areas are limited only by imagination.

6. SHELL CODE

The name comes from ancient times, when the injected code was meant to get a remote shell on the machine under attack.
Therefore, the code had to meet the requirements:
- be as brief as possible (to mash what is less in the attacked process)
- be position-independent (relative addressing only)

Nowadays, shell code refers to any insertion of machine code, regardless of the application context.
However, the typical context is still injection into a process, e.g. roughly into a chunk of an executable thread (to bypass DEP).
So the above requirements still apply.

Typical shell code for Windows processes does bootstrapping (including ASLR traversal):
- determines important milestones in memory - pointers to TEB and PEB
- gets the address of the import table from the PEB
- gets the address of kernel32.dll from the import table
- gets LoadLibrary and GetProcAddress addresses from kernel32.dll (usually by name hash, not by name itself)
- When you have LoadLibrary and GetProcAddress, you can do anything.
https://habr.com/ru/post/522966/
http://www.hick.org/code/skape/papers/win32-shellcode.pdf
(This dude http://www.hick.org/~mmiller/ by the way has a bunch of interesting, albeit outdated stuff)
https://www.corelan.be/index.php/2010/02/25/exploit-writing-tutorial-part-9-introduction-to-win32-shellcoding/

The same code can be written in C without using assembler.
Example - ReflectiveLoader() function https://github.com/dismantl/ImprovedReflectiveDLLInjection/blob/master/dll/src/ReflectiveLoader.c

Hell's Gate: a parsing of system call lookups in position-independent C code
https://vxug.fakedoma.in/papers/VXUG/Exclusive/HellsGate.pdf

PE->shell converter
https://github.com/hasherezade/pe_to_shellcode

7. INJECT PE / INFECT PE

Implementing a load into an existing PE file:
https://github.com/secrary/InfectPE/
https://github.com/JonDoNym/peinjector

Unpacking code (loader) and the load is added to the file, the original entry point to the loader is redirected.
This way you can hook your loads to legal software.
Digital signatures are of course lost.


               II. DEFENSIVE TECHNIQUES


HOW ANTIVIRUSES WORK

AVs get information about what is going on in the system in one of two ways:
- By subscribing to kernel events using Event Trace for Windows (ETW) https://docs.microsoft.com/en-us/windows-hardware/drivers/devtest/event-tracing-for-windows--etw-
- by hooking up their minifilter driver in kernel mode (ring0), and thereby subscribing to file system activity and similar events (Windows Defender, Eset, WebRoot, McAfee)
- Inserting their hooks in user mode (ring3) directly into executable processes (Avast, BitDefender, Symantec, TrendMicro) for functions from ntdll.dll, user32.dll, kernel32.dll, etc.
In the first case nothing can be done, in the second case the hooks can be removed.

WINDOWS DEFENDER

Windows Defender uses a minifilter-type driver called WdFilter to subscribe to events on the system.
Intercepted events are:
- process creation and startup
- process image loading
- thread loading
- Manipulations with a non-native process - writing to memory and launching a remote thread
- drivers being loaded are intercepted
- Registry operations are intercepted.

The most useful thing I could find in the parsing was a list of conditions under which such attention can be avoided:
- a white list of processes (including svchost, werfault and WinDefender's own processes)
- list of hardened registry keys

https://n4r1b.com/posts/2020/01/dissecting-the-windows-defender-driver-wdfilter-part-1/
https://n4r1b.com/posts/2020/02/dissecting-the-windows-defender-driver-wdfilter-part-2/
https://n4r1b.com/posts/2020/03/dissecting-the-windows-defender-driver-wdfilter-part-3/
https://n4r1b.com/posts/2020/04/dissecting-the-windows-defender-driver-wdfilter-part-4/

EMULATOR DEVICE

The AB emulator is designed for additional analysis of possible program behavior during static binary analysis:
https://findpatent.ru/patent/251/2514141.html
1) Emulator does not execute all system calls, making "stub" calls. The emulator can perform those which are especially necessary for program functioning (such as memory allocation),
But not in general case;
2) the emulator is more interested in the sequences of system calls than in their results;
3) in some AB Emulator performs all branching in code regardless of conditions fulfillment (according to some VBA32 testimonies).
In order to look into all code branches.
4) the emulator is limited by the running time (on the order of tens of seconds, or by the number of executed system calls).

In some AVs, the emulator starts with hooks embedded in your code.
Avast Emulator Detect:
    // Avast CyberCapture counteraction.
    BYTE* A = (BYTE*)GET_API(SendMessageA); // Any function which Avast traps
    if (A[0] == 0xe9) // If the first command is JMP
    {
        // In the sandbox, Avast traps with a jump to the address where the FF25 00000000 byte sequence is written (another JMP)
        // i.e. two consecutive JMPs, E9 and FF 25 in your function's prologue, are characteristic of Avast emulator
        // In normal mode Avast also puts hooks, but there are fewer of them and they lead to the addresses of aswhook.dll
        SIZE_T W = (SIZE_T)(A + 5) + (SIZE_T)(*(INT32*)(A + 1));
        if (*(WORD*)W == 0x25FF && *(DWORD*)(W + 2) == 0)
        {
            debug_printfA(ORANGE, "Avast CyberCapture (sandbox) detected\n");
            GET_API(ExitProcess)(-1);
        }
    }

The design of the BitDefender shell code detecting emulator is described here https://habr.com/ru/company/skillfactory/blog/527512/
Emulator sources are available at https://github.com/bitdefender/bddisasm
Emulator Triggers:
- shell code detection in memory:
  - access to the EIP/RIP instruction pointer register
  - access to TEB/PEB structures
  - search for system functions like GetProcAddress
- self-modification (on-the-spot decoding)
- decrypting strings on the stack
- stack execution
- using SYSCALL instead of entry points in library .dlls
- in kernel mode
  - SWAPGS type specific instructions
  - MSR register access
  - access to the KPCR (Kernel Processor Control Area)

COUNTERING ANTIVIRUSES

There are two types of antivirus detections (hereinafter referred to as AV):
- static (by signature)
- Dynamic (by behavior).

The first is easy to deal with, the second is difficult.

In addition, ABs use fuzzy hashing (ssdeep, imphash), neural networks, and Bayesian filters to detect previously unknown malware
by the degree of similarity of text or behavior to already known patterns:
https://xakep.ru/2020/09/28/clear-hash/

ABs necessarily put detects on known strings and import functions.

Necessary (but not sufficient) measures to protect against AB:
- string obfuscation
- import obfuscation (i.e. imported from third party .dll functions)
- code noise injection
- code encryption with decryption before execution.

Additional measures to counteract ABs:
- disabling AV (with sufficient rights)
- removal of hooks (only for AVs working in userland)

The KHOBE (Kernel HOoks Bypassing Engine) technique is known. There is no code in the public, only general information is known.
It is necessary to use race condition (whose?), presenting for the analysis of safe code at the moment and quickly switch context immediately after AB analysis.

For more information, see Cleaning anti-virus detections.


SEARCH FOR SIGNATURES IN KNOWN DATABASES

Windows Defender rules decoder:
https://github.com/hfiref0x/WDExtract

The easiest way is to look for known signatures in the YARA signature database.
Yara is a free signature analysis tool for malware
https://virustotal.github.io/yara/
But the rules themselves are not centrally available.
There are commercial rulebooks available such as
https://www.nextron-systems.com/2018/12/21/yara-rule-sets-and-rule-feed/
There are free low quality ones at https://github.com/Neo23x0/signature-base/.
A large collection of bases, some of which are crap and some up to date: https://github.com/InQuest/awesome-yara
Syntax and possibilities of rules: https://habr.com/ru/company/varonis/blog/584618/


You can convert the ClamAV database to the human-understandable Yara format and then search for the necessary detects:
https://resources.infosecinstitute.com/topic/yara-simple-effective-way-dissecting-malware/
> YARA with ClamAV Rules
>
> YARA can be integrated with ClamAv rule database. Perform the below steps to integrate ClamAv rules with YARA:
>
> Download the ClamAV to YARA Python script here: https://code.google.com/p/malwarecookbook/source/browse/trunk/3/3/clamav_to_yara.py
> Download and unpack the ClamAV db: http://database.clamav.net/main.cvd
> Run the ClamAV to YARA Python script:
> python clamav_to_yara.py -f main.cvd -o testing_clamav.yara
> Now test the converted rules with YARA like below:
> yara -r testing_clamav.yara /directory/to/check"


DISABLING WINDOWS DEFENDER

Sometimes you can simply disable AB, in particular you can add yourself to Windows Defender exceptions (not always) here
HKLM|HKCU\MACHINE\SOFTWARE\Microsoft\Windows Defender\Exclusions\Paths
HKLM|HKCU\MACHINE\SOFTWARE\Policies\Microsoft\Windows Defender\Exclusions\Paths
And in about the same place, disable AV altogether.
Notice that this is done through policies!


SYSTEM CALL OBFUSCATION

The simplest technique is to get the function address using GetProcAddress(LoadLibrary(decode("lib.dll"), decode("funcname"))
However, the GetProcAddress and LoadLibrary calls light up here.

The old but still effective GetApi technique was used in the Carberp Trojan:
https://github.com/hzeroo/Carberp/blob/master/source%20-%20absource/pro/all%20source/RemoteCtl/DrClient/GetApi.h

Its essence is to search for functions in the import table by their name hash. This throws off signature detections, although it is available for automated analysis
disassemblers. The hashing method can be changed periodically.

Another approach is inline system calls - stick the entire boilerplate code for preparing the syscall into the assembler
https://github.com/JustasMasiulis/inline_syscall

A library of surrogates for some WinAPI calls:
https://github.com/vxunderground/WinAPI-Tricks

In the end, system calls are still caught by AVs working in kernel mode via call hijacking subscriptions.

CODE OBFUSCATION

Usually code in the form of .dll is compressed, encrypted and packed into an array (see below Hiding data in a code segment),
at runtime, they allocate memory, extract and decompress the code, then they set up the .dll in memory
(relocations, imports, and all that). See below about cryptors and packers - this is it.
This trick is known to AV and it does not save from proactive protection.

That's why the thought has moved on:

1. Unpack each function just before it is executed.
Each function defines a prologue and an epilogue, which decrypts and encrypts the body of the function, respectively.
Signatures are used to find the boundary of the function.
An external encoder is required to initially encrypt the functions in the file.
Other on-the-fly decoding options are also possible, but all of them are difficult to implement.

2. proprietary JIT interpreters.
For example, https://github.com/jacob-baines/jit_obfuscation_poc
The idea is clear from the name - we translate one code into another code, which is unknown to anti-viruses and unavailable
to automatic reverse-engineering and analysis.

VMProtect-2 - obfuscating virtual machine
https://back.engineering/tags/vmprotect-2/
	
There is an interesting LLVM-based obfuscating compiler project:
https://github.com/obfuscator-llvm/obfuscator
and an explanation of its algorithms
https://0xpat.github.io/Malware_development_part_6/
Another commercial one:
http://www.star-force.ru/products/starforce-obfuscator/

An interesting approach to decrypting code is to get the decryption key from the server.
This gives a certain effect against AV emulators.

STRING OBFUSCATION

Before constexpr came along, string obfuscation was done by a third-party utility and two-pass assembly:
- lines in the code were marked with special tokens, usually in a global table of lines
- strings were accessed through the decryption function, by its index in the table
- the utility traversed the ready-made .exe and replaced them with the ciphertext.

This approach made debugging very difficult and required additional build steps,
making the code unreadable.

constexpr allows you to do string encryption at the build stage, solving all the above problems.
However, this works in Visual Studio version 2015 or later, requiring C++ standard 14.
Andrivet ADVObfuscator pre-built encryption library:
https://github.com/andrivet/ADVobfuscator
There's also
https://github.com/Snowapril/String-Obfuscator-In-Compile-Time (based on Andrivet)
https://github.com/adamyaxley/Obfuscate
https://github.com/fritzone/obfy
https://github.com/revsic/cpp-obfuscator

Obfuscation works at maximum optimization settings in Visual Studio:
- C/C++/ Optimization: Full optimization
- C/C++/ Optimize / Unfold inline optimization: On
- C/C++/ Optimization / Deploy inline-functions: off
- C/C++/Codevelopment / Enable merge lines: Yes
However, this option should be tested and it affects differently on x64/x86
- Other optimization settings also need to be tested, it may affect them.

Developer comment about obfuscator not working on full optimization for x86 compilation:
"...why the string obfuscator doesn't always work.
It's about the decrypt method, it turns out when the optimization is enabled to unfold the substituted functions,
The compiler inserts its body at the place where this method is called, and since this body uses expressions that it can parse at compile time,
it decrypts the string at compile time.
So it encrypts and decrypts string at compile time.
Fixed by disabling optimization of expandable substituted functions."

One of the known disadvantages of inline obfuscators is the limitation on the length of strings.
Each additional character in the string is an additional recursion of the compiler when computing at the compilation stage.
The MSVC2015 stack ends at about 100 characters long.

There is also a simple trick used in the absence of C++ (in pure C)
char str[] = { 'H', 'E', 'L', 'L', 'O' }
In such an initialization, the string is put into an array on the stack by mov at runtime, and as a string will not get into the .data segment
(i.e. will turn into a set of assembler instructions in .text of this form:

       mov edx, 8EB5h
       push edx
       mov edx, 6C6CD488h
       push edx
etc.

By the way, the transcription of the string in place looks something like this:

       mov edx, 8EB5h
       xor edx, 8EDAh
       push edx
       mov edx, 6C6CD488h
       xor edx, 0B1C0h
       push edx
       mov esi, esp
       sub esp, 20h
       mov edx, 0F478h
       xor edx, 0F459h
       push edx
       mov edx, 74690CD7h
       xor edx, 2CB2h
       push edx

A clean line looks like this in the disassembler:

.code:004010A5 aTest001 db 'Test001',0
.code:004010AD aLoremIpsumDolo db 'Lorem ipsum dolor sit amet',0

)

char str[] = "HELLO"; will be filled in at compile time as a string and will go into the .data.

ENTRY POINT OBFUSCATION

This measure is used to counteract AV emulators and manual analysis.
The real entry point is different from the one declared in PE/ELF headers.
For example, the .dll has a fake harmless export (some DllMain, DoTheWork, etc.) that does some kind of diversionary action.
To start a real load, you need to pull a function which is not exported to a location known only to the context that triggers it.
Another option is to use a DOS stub. Changing the MZ signature to any other in the PE binary will start the binary in DOS mode.
As a consequence, the AB will ignore the true entry point.
In 16-bit mode, however, emulators don't work; you can run a third-party binary through the 4B DOS interrupt function.
This can be used in the "break-chain" technique.

HIDING DATA IN A CODE SEGMENT

ABs are sensitive to unusually large data section (.data, .rdata) - this is a sign of hiding encrypted load code in it.
Data can be hidden in the text section. The Microsoft C++ compiler lets you do this with this trick:

#pragma code_seg(push, ".text")
#pragma code_seg(pop)
unsigned char __declspec(allocate(".text")) hiddencode[]={ . };

On a similar principle, you can stack the load in other sections, by pragmas/declspec data_seg, const_seg.
However, linker can multiply sections with the same names and different access rights, so there is also such variant:

#pragma comment(linker,"/SECTION:.data,EW")
unsigned char PayloadName0[]={}
#pragma comment(linker,"/SECTION:.rdata,R")
unsigned char PayloadName2[]={}

Counteracting this measure on the AB side is the frequency analysis of the code section. The code section has low entropy,
because the number of opcodes is limited, and the statistical distribution of characters in the code has a well-defined structure.
Therefore, hiding of encrypted and/or packed arrays is quite clearly traceable.
This, in turn, can be countered by encryption techniques that weakly change entropy - for example,
xor 1 byte (of course, if such array hides code in the form of .dll. If there is other data, it won't help).

About entropy and generally what your PE file looks like for AV:
https://0xpat.github.io/Malware_development_part_4/
You can measure entropy with DIE (Detect It Easy).

CRYPTORS AND PACKERS

Robust implementations of packers have been known since at least the mid-90s.
The idea is simple - one .exe is packed inside another .exe, and when executed, it performs the Baron Munchausen trick
of pulling and running the load out of itself.
Of course, this is a great way to hide code.
Encryption is also added to the packaging.
Packaging can be multi-layered, to make it difficult to analyze.
Packers peaked in popularity in the 0's.

The PEId program (discontinued in 2011) was used to determine the type of packer.

Much smarter cryptors are now being used.
The cryptor in addition takes on the functions of bypassing AB emulation, sandbox detection,
sometimes even UAC bypass and privilege escalation (due to the nature of load starts, these functions
may be appropriate to put it on the cryptor).
Also, aside from trivial pulling the load from the encrypted array internally,
a good cryptor generates plausible import tables, plausible code that confuses the AB,
dilutes load entropy, spreads load randomly across different sections,
generates real resources (strings in localization), in short, pretends to be a real program.
A similar approach is described in https://xss.is/threads/39006/

In short, it's a protective shell that hides your code.

Of course, cryptors are not omnipotent, and behavioral detectors they will not remove.

An interesting approach to crypto construction: the load decryption key is separate from the crypt and passed through the command line (or whatever):
https://habr.com/ru/company/solarsecurity/blog/519994/

Using combos from public code in a packer from scratch
https://iwantmore.pizza/posts/PEzor.html
including using
Donut packer https://github.com/TheWover/donut
Shikata Ga Nai Morpher https://github.com/EgeBalci/sgn

An open source cryptor:
https://github.com/oddcod3/Phantom-Evasion
Another one:
https://github.com/ximerus/Kryptonite

Tread with "nanomites."
https://www.apriorit.com/white-papers/293-nanomite-technology
The debugging of the process by its own debugger has been applied;
replacing ALL transition instructions in the child thread with INT 3 opcodes (debug interrupt);
and forming a transition address in the debugger process.

HOOK LOSS

To remove foreign hooks you can use the comparison of the prologue of a function in process memory
with the prologue in the corresponding .dll file. If they are different, it is a sign that an alien hook has been placed on the function.
The next step is to read the body of the function from the file and replace the body of the function in memory.
The first 10 bytes are enough.
An overview and comparison of different techniques:
https://www.first.org/resources/papers/telaviv2019/Ensilo-Omri-Misgav-Udi-Yavo-Analyzing-Malware-Evasion-Trend-Bypassing-User-Mode-Hooks.pdf
Demo: https://github.com/apriorit/antirootkit-anti-splicer
More: https://github.com/CylanceVulnResearch/ReflectiveDLLRefresher
Detect hooks: https://github.com/asaurusrex/Probatorum-EDR-Userland-Hook-Checker
Comparison of different EDR userland hooks; direct work with syscalls: https://github.com/Mr-Un1k0d3r/EDRs

Keep in mind that hooks on your process can be restored after you remove them.


INTERCEPTION OF OTHER PEOPLE'S FLOWS (INJECTION PROTECTION)

You can intercept someone else's injection into a process by placing your handler on the BaseThreadInitThunk() function.
The creation of a new thread starts with it (including one initiated from outside the process).
In this handler, you can decide whether to allow or block the thread from running, based on certain attributes.
The simplest approach is to start all your threads at once and then block everything else.
If this is unacceptable, you can look at the address and properties of the memory page where the code is launched from.
The thread being injected is usually a heap. A healthy thread has a text section (.text).

In particular, this is how the protection against injections in the browser Mozilla Firefox.

This technique can be successfully counteracted - from outside the process you can remove the hook on BaseThreadInitThunk
using the hook removal technique described above, after which injection is possible.

Another way is to turn all mitigations (see below) to maximum immediately after the process starts, particularly for DEP
and code signing.

https://ethicalchaos.dev/2020/06/14/lets-create-an-edr-and-bypass-it-part-2/
It describes how to protect the process by:
- "innocent" code development
- hook removal
- direct sysvocations
- mitigations
- SharpBlock is another technique that uses intercepting the start of a child thread via debug events, and patching its entry point to obfuscate the EDR.


PROCESS PROTECTION FROM TERMINATION

1. Deny access via discretionary access control list (DACL). DACL is empty => process can be killed only by admin:
https://stackoverflow.com/questions/6185975/prevent-user-process-from-being-killed-with-end-process-from-process-explorer

2. Marking a process as critical (RtlSetProcessIsCritical, NtSetInformationProcess).
Any attempt to stop such process will result in BSOD;
Attempting to kill such a process in the Task Manager will result in a warning that it is a critical process
and its removal may cause a system crash.
Requires administrator rights and SeDebugPrivelege privilege:

RtlSetProcessIsCritical:
https://www.codeproject.com/Articles/43405/Protecting-Your-Process-with-RtlSetProcessIsCriti

NtSetInformationProcess with parameter ProcessInformationClass = BreakOnTermination:
http://www.rohitab.com/discuss/topic/40275-set-a-process-as-critical-process-using-ntsetinformationprocess-function/

Using these calls may cause crashes.
It is established empirically that NtSetInformationProcess with the BreakOnTermination parameter works stably on 32-bit operating systems,
and RtlSetProcessIsCritical - on 64-bit ones.

3. if you have a private key from Microsoft's digital code signature (lol))), starting with Windows Vista
it is possible to make any process protected from any changes from the outside.
Also, a protected parent process can spawn a protected child process,
by calling the CreateProcess function with the flag CREATE_PROTECTED_PROCESS.
This mechanism has been improved in Windows 8.1, but it is not perfect and does not rule out the possibility of making any process protected
or remove protection from digitally signed system processes.

An example of how to create a protected child process is in the MSDN description of the UpdateProcThreadAttribute function:
https://docs.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-updateprocthreadattribute

An article about protected processes by Alex Ionescu:
https://www.crowdstrike.com/blog/evolution-protected-processes-part-1-pass-hash-mitigations-windows-81
https://www.crowdstrike.com/blog/evolution-protected-processes-part-2-exploitjailbreak-mitigations-unkillable-processes-and

Presentation by Alex Ionescu:
http://www.nosuchcon.org/talks/2014/D3_05_Alex_ionescu_Breaking_protected_processes.pdf

An example of an exploit for a Capcom driver vulnerability to make any process secure:
https://www.unknowncheats.me/forum/anti-cheat-bypass/271789-pplib-ppl-processes.html
https://github.com/notscimmy/pplib

An article with examples on how to make a process secure and elevate its privileges by patching the process memory:
https://www.blackhat.com/docs/asia-17/materials/asia-17-Braeken-Hack-Microsoft-Using-Microsoft-Signed-Binaries-wp.pdf

Source drivers that remove digital signature protection:
https://github.com/Mattiwatti/PPLKiller
https://github.com/katlogic/WindowsD

4. Other not the most effective ways to protect processes are described here:
https://security.stackexchange.com/questions/30985/create-a-unterminable-process-in-windows

PROTECTING THE PROCESS FROM BEING TAKEN OFF DURING A SYSTEM SHUTDOWN

1. You can set a console event handler by calling SetConsoleCtrlHandler,
which returns 0 to CTRL_LOGOFF_EVENT and CTRL_SHUTDOWN_EVENT events.
- Works for console programs for which no other console event handlers are running.
- As of Windows 7, CTRL_LOGOFF_EVENT and CTRL_SHUTDOWN_EVENT event handling does not work
for programs that use functions of user32.dll and gdi32.dll libraries.

Example on MSDN:
https://docs.microsoft.com/en-us/windows/console/registering-a-control-handler-function

2. You can call the AbortSystemShutdown function in an infinite loop.
- Requires administrator rights and SeShutdownPrivilege
- Doesn't have time to work if shutdown command with /t switch in the console is executed with the value 0 (timeout 0 seconds)
- it does not save from executing shutdown command with /f switch in the console
- Doesn't seem to work on Windows 10.

You can create an invisible window and return 0 in the window event handler on events WM_QUERYENDSESSION and WM_ENDSESSION.
- Starting with Windows Vista, you need to call the ShutdownBlockReasonCreate function on the WM_QUERYENDSESSION event,
or hide the window by calling the ShowWindow function with the second parameter set to FALSE (although the window will be invisible anyway).
- This does not save you from pressing the forced shutdown button.
- it does not save you from running the shutdown command with the /f switch in the console
- does not work for console programs, in particular, it is useless to use this technique inside a dll running through rundll32

See MSDN for more information:
https://docs.microsoft.com/en-us/windows/win32/shutdown/shutdown-changes-for-windows-vista
https://docs.microsoft.com/en-us/previous-versions/windows/desktop/ms700677(v=vs.85)

MITIGATIONS

see. SetProcessMitigationPolicy()/UpdateProcThreadAttribute(PROC_THREAD_ATTRIBUTE_MITIGATION_POLICY)
Allows to enable DEP, ASLR, prohibit dynamic code generation,
Checks for signature, handles validity, SEHOP exceptions and much more.
Browsers and AVs take advantage of this, cranking mitigations to the max in order to make it harder to inject or skim the process.
On Windows 10, this is really effective.

Good article http://www.sekoia.fr/blog/microsoft-edge-binary-injection-mitigation-overview/
and a code to it
https://github.com/SekoiaLab/BinaryInjectionMitigation/
demonstrates code protection by code-signature verification mitigation.

This article https://habr.com/ru/post/494000/ gives an overview of mitigation policies, including the shadow stack , cited:

"Code Integrity Guard (CIG) imposes a mandatory requirement for the signature of downloaded binaries.

Arbitrary Code Guard (ACG) ensures that signed pages are unchanged,
and dynamic code cannot be generated, which guarantees the integrity of downloaded binaries.

With the introduction of CIG and ACG, attackers are increasingly resorting to control interception via indirect calls and returns,
known as Call/Jump Oriented Programming (COP/JOP) and Return Oriented Programming (ROP)."


NETWORK ASYMMETRY

Antivirus performance depends on the country.
Most AV vendors are from the collective West.
AV efficiency depends mainly on neural network checks in the "cloud".
With the beginning of the cyber confrontation between Russia and the USA, the latter have prioritized all traffic from their country, to the detriment of other countries (including Western Europe),
in order to strengthen their own security. Apparently, verification capacities are not unlimited.
So it has become normal that the same load does not work in the US and works in other countries.


CLEANING OF ANTIVIRUS DETECTIONS

The first thing to do before cleaning is to make sure that the antivirus does not leak samples:
- virustotal ALWAYS leaks samples live
- dyncheck leaks samples during dynamic behavioral checks. Static checks do not seem to leak samples
- For Windows Defender you need to disable "Send samples" option
- For other AVs, you need to find the Send Samples and Cloud Protection option and disable them.

The general method of cleaning is as follows:
1. Find the specific lines of code for which the detector is triggered;
2. Replace it.

Point 1 is long and tedious, it is done as follows:
- we disable the comment or ifdef with ALL the code in the program, reassemble
- AB shuts down
- uncomment half of the code
- AV is silent
- another half of the code
- AB is silent
- half of half of
- AB is shouting -> we have found a section!
Then we use the same dichotomy to get to specific lines:
- uncomment the line - screams, comment the line - silent.

Consider the optimizing compiler: the optimizer can throw away a huge block of code,
if it does not see the impact of this code on the overall behavior.
For example, if you put return in the middle of the function under test as part of the detection search,
the optimizer can drop both the tail of the function and its start from the resulting binary,
because the optimizer believes that there is nothing useful and affecting the general execution in the remaining part of the function.
By the same principle, the optimizer throws out unexpected parts of the code, which messes up the maps a lot.

As a rule, antivirus detections are set to:
- binary file name (since the lit name will give detections)
- Microsoft Visual C++ adds a string with project name to the binary: Project Properties -> General -> Target object name ($(ProjectName) is there by default)
you need to randomize it, or just overwrite it with zeros/spaces in the post-event build directly in the binary
- separate system calls (CreateRemoteThread, VirtualProtect, CreateFile, CreateProcess, OpenProcess, registry handling, etc.)
- sequences of system calls (is silent on single system calls, but is shouting on sequences)
- open lines
- distinctive algorithms (random number generators, (de)encryption, (de)compression)
- high entropy of the binary file (encrypted/archived arrays, including in the code section)

Additionally, a clean file can be detected:
- When downloading it from a site with a low reputation (which already had complaisons about the presence of suspicious files)
- Alternatively, when downloading a file from another country (both client and site address are affected)
- If there is no hash of the file in the AB database (there is a limited number of runnable files in the world)

Under Linux, don't forget to remove characters and extra strings with the strip utility (or better yet, sstrip).

System calls are either obfuscated by GetApi.h (can be taken directly from carberp/GetApi.h
https://github.com/hzeroo/Carberp/blob/master/source%20-%20absource/pro/all%20source/RemoteCtl/DrClient/GetApi.h)
or, if GetApi does not have the necessary call, the following sequence:

HANDLE h = LoadLibrary(_OBFUSCATED("dll.dll"));
void* f = GetProcAddress(h, _OBFUSCATED("funcname"));

both lines are obfuscated here.

Sequences of syscalls are obfuscated in the same way, or syscalls are creatively replaced by analogs.

How to obfuscate strings see above.

We dilute the characteristic algorithms with noise code:
- we add/remove volatile to local variables
- change the definition of a local variable in the code (if it was in the prologue of a function, put it closer to where it is used, and vice versa)
- add noise code between lines (incrementing a garbage volatile variable, additions, subtractions, other operations)
- such noise code may be written as inline-function or macro: in debug build the body of the function is disabled, in real build it is written with constexpr
constexpr's are used to create random code patterns.
- entropy is removed by stacking the array pieces into different sections and augmenting the arrays with unused constant bytes,
assembling array from pieces before using it.

Another tactic to remove detects is to randomize the addresses of functions in the resulting .exe file.
This can be accomplished by simply shuffling the list of object files on the linker command line:

link.exe /out:file.exe foo.obj bar.obj --> no detection
link.exe /out:file.exe bar.obj foo.obj --> no detection

There is a utility to search for signatures in PE files:
https://github.com/vxlabinfo/SignFinder
based on the articles
https://vxlab.info/%d1%87%d0%b8%d1%81%d1%82%d0%ba%d0%b0-pe32-%d1%87%d0%b0%d1%81%d1%82%d1%8c-1/
https://vxlab.info/%d1%87%d0%b8%d1%81%d1%82%d0%ba%d0%b0-pe32-%d1%87%d0%b0%d1%81%d1%82%d1%8c-2/
The site is now defunct but you can still find copies, e.g. here
https://ru-sfera.org/threads/chistka-ot-signaturnogo-detekta-antivirusov.2870/
and google clean-pe32-part-1 clean-pe32-part-2


FILE REPUTATION

A file with a new unknown hash will be blocked by AV just in case.
This is called "no reputation".
That is why they usually give the file a reputation on controlled machines: run it, and when blocked by AV,
unlock it manually, add it to an exception and make it work.
The same way the regular SmartScreen, and the Chrome AV screen work.
A description of the reputation mechanism in Mozilla Firefox:
https://wiki.mozilla.org/Security/Features/Application_Reputation_Design_Doc

CHOPPER BREAKING

When bypassing AB in case of multistage load starting, almost the only way to avoid detection is to break the startup chain.
So the parent process of subsequent stages is not the previous stage, but a legitimate OS file.
For example, we want to download and run the second stage of the load from the loader.
If we do this directly, the AB will detect a link between the lowder and the load.
If we add an intermediate link in the chain (e.g. starting the load with the AT command), the chain will be broken.

A kind soul has made a catalog of such system utilities and criteria for their use https://lolbas-project.github.io/
https://github.com/api0cradle/LOLBAS

SUBSTITUTION OF THE PARENT PROCESS

A kind of chain break that breaks the link to the process being spawned.
In CreateProcessA lpStartupInfo is passed, which lpAttributesList specifies the descriptor of the desired parent process.
By the way, this way you can escalate privileges by inheriting the security context of the process.
Details at https://blog.f-secure.com/detecting-parent-pid-spoofing/

ZERG RUSH

Run 100500 different crypt hashes of the same load, thereby overloading the AB.
If we are lucky, AV will only cut 10499:
https://habr.com/ru/company/solarsecurity/blog/519994/

AMSI ROUNDABOUT

AMSI is Antimalware Scan Interface, an anti-virus module for analyzing code from Windows scripting languages.
It processes code in PowerShell, C#, VBScript, JavaScript, Windows Script Host (wscript.exe and cscript.exe),
Office VBA macros, and UAC.

A brief gist: analyze source code (decompile if necessary), only statically, put detects on strings - variable names, strings,
similar patterns, use of bridges for WinAPI.
Clean up accordingly.

A dude named S3cur3Th1sSh1t has done a great job of systematizing all the AMSI circumvention methods.

Two articles describe how AMSI works and how to bypass it:
https://s3cur3th1ssh1t.github.io/Bypass_AMSI_by_manual_modification/
https://s3cur3th1ssh1t.github.io/Bypass-AMSI-by-manual-modification-part-II/
TL;DR: AMSI puts detections on strings, so we actively rename identifiers in scripts,
glue strings on the fly, use lefty encodings to store scripts.

Detection scanner (finds strings with detections):
https://github.com/RythmStick/AMSITrigger

Anti-AMSI obfuscator:
https://amsi.fail/

The circumvention techniques are collected here:
https://github.com/S3cur3Th1sSh1t/Amsi-Bypass-Powershell

PowerShell script obfuscator:
https://reconshell.com/chimera-powershell-obfuscation-script-for-bypass-amsi-and-antivirus/

The documents are cleaned like this:
- the first thing we do is change the layout (hash of pictures, arrangement of elements, texts, etc.)
- obfuscate the VBA code
- Timeout before unpacking the dropper and before launching the file


FAT BINARY

An undeveloped but long-known and promising technique related to the need to deliver a universal 32/64-bit workload:
https://en.wikipedia.org/wiki/Fat_binary
https://habr.com/ru/company/macloud/blog/545278/
By manipulating executable headers, the starting prologue is generic,
which further selects the right load with the right entry point.


               III. DETECTING SANDBOXES AND DEBUGGERS

SANDBOX DETECTION

Sandbox detection is necessary to avoid executing in it. Do not load and do not shine the main load.
Sandboxes are mostly made of virtual machines, but in itself this criterion is insufficient, because the VM may well be running a legitimate terminal server.

Many methods are systematized here:
Al-Khaser: https://github.com/LordNoteworthy/al-khaser
PaFish: https://github.com/a0rtega/pafish

Good article on detecting emulators and sandboxes: https://0xpat.github.io/Malware_development_part_2/

Below is a very brief and incomplete summary of the strategies:

1. By machine name (https://www.blackhat.com/docs/us-17/thursday/us-17-Kotler-The-Adventures-Of-Av-And-The-Leaky-Sandbox.pdf):
*ESET: REYNAPC, MALVAPC, ELEANOREPC, WRIGHTPC, BRIAPC, JORIPC, GABBIPC, HELSAPC, MAMEPC, SHARAIPC, ARACHONPC, FLORIANPC, EDITHPC
*Various: WIN7-PC, ROGER-PC, DAVID-PC, ADMIN-PC, APIARY7-PC, ANTONY-PC, LUSER-PC, PERRY-PC, KLONE_X64-PC, 0M9P60J5W-PC, MIKI-PC
*Avira: C02TT22, C02TT26, C02TT36, C02TT18, C06TT43
*Comodo: spurtive, circumstellar
*Others: ZGXTIQTG8837952 (Comodo), ABC-WIN7, PC, WIN-U9ELNVPPAD0, PC-4A095E27CB, WIN-LRG949QLD21

2. By serial numbers and hardware name - MAC-addresses of network card, hard disk volume name
(vbox, qemu, vmware, virtual hd)

3. By running in a virtual machine.

4. According to CPUID instruction execution time
4.1 By the difference of GetTickCount() before and after Sleep();

5. According to the lack of activity in the interactive session (mouse, keyboard)

6. Resolving a known non-existent domain (NotPetya killswitch)

A few examples of sandbox detection implementation:
- https://habr.com/ru/company/solarsecurity/blog/473086/
- An overview of cheap methods from Positive Technologies: https://habr.com/ru/company/pt/blog/507912/
- Combine with multistage sandbox detection:
https://blog.talosintelligence.com/2020/05/astaroth-analysis.html
read from Anti-analysis/Anti-sandbox mechanisms section

BYPASSING EMULATORS

The emulator is usually part of the anti-virus, and it needs to determine very quickly whether the code in question is allowed to run or not.
Because of this, the emulator check usually does not take long.
This is the main strategy for bypassing emulators: execution delay.
A simple Sleep() does not work anymore, since it is intercepted by the emulator, so there is no real delay.
Therefore, as a rule, instead of delay, a cycle of calculations is used (e.g. calculating Pi with great accuracy).

Lots of interesting and simple techniques for bypassing emulators:
https://wikileaks.org/ciav7p1/cms/files/BypassAVDynamics.pdf

An approach based on the imperfection of WinAPI emulation is to analyze the ECX EDX registers after returning from a call:
https://winternl.com/fuzzing-the-windows-api-for-av-evasion/
https://github.com/jackullrich/Windows-API-Fuzzer
Earlier work
https://github.com/SPTHvx/SPTH/blob/master/articles/files/dynamic_anti_emulation.txt

Masking the true sequence of system calls in a large number of noise calls:
https://habr.com/ru/company/pt/blog/551954/

DEBUGGER DETECTION, DEBUGGING PROTECTION

1. IsDebuggerPresent() - unreliable, the function gets patched and returns "we are not debugged

2. Search for processes by name (windbg, idapro, etc.)

3. Notations of the passage time of characteristic pieces of code

OnionCrypter has interesting debugging protection methods: https://decoded.avast.io/jakubkaloc/onion-crypter/
- use signatures from known packers (UPX) to loop auto-analyze and confuse simpler reversers.
Of course, the load is not covered by the packer whose traces are left behind.
- an exception is thrown after the debugger is detected
- Three different memory allocation fi ings - HeapAlloc GlobalAlloc VirtualAlloc. Many false allocations make manual analysis difficult,
making a breakpoint and hook on those f-i's useless.
- Starts the load via the callback of a system function. I.e. not "pass control to so-and-so address",
but "call EnumWhateverA and pass the load entry point to this function as the callback".

               IV. FIXING TECHNIQUES

On Windows, the classic fixing methods are as follows:
- Autoload [HKLM|HKCU]\Software\Microsoft\Windows\CurrentVersion\Run
- scheduled task CoCreateInstance(CLSID_TaskScheduler, ...)
On the plus side, no admin rights are required, on the downside, they are so obvious that they give an immediate behavioral detection.
- Installing itself as a service (can't do without privileges)
- BITS - not used that often but it does not bother AV
- ... (lots of techniques)
There's https://habr.com/ru/post/425177/ a good overview of the techniques.
There's http://www.hexacorn.com/blog/2017/01/28/beyond-good-ol-run-key-all-parts/ a huge number of standard
and non-standard extension points in Windows. In particular, the ideas of starting with non-standard triggers - for hardware events,
extension points of popular programs, etc.

Also see "Breaking the Chain" above.


               V. BACKCHANNEL AND COMMUNICATION

C&C servers are always hidden: either behind pads (reverse proxies) or behind a tor-domain.
The pads line up in cascades and there are many of them, so that a failure of one does not crash the whole network.

When implementing a communication channel with the C&C, it should be remembered that the OS may be configured with its own system proxy.

For the initial C&C search, you can use the Domain Generation Algorithm (DGA)
http://www.marc-blanchard.com/BotInvaders/index.php
The idea is to generate pseudo-random domain names
- which are not too many (up to 10k) to search through in a reasonable amount of time;
- there are enough of them that they can't be scraped or otherwise banned/spoofed;
- list of domains for one month is different from the list of domains for another month;
- it is difficult to make a regular to cut them out on dns-servers.

Usually HTTPS is used to communicate with C&C, but not always the right port or protocol is open.
If DPI-filter cuts HTTP(s)-traffic, other protocols are used:
- you can make specific DNS queries to the _necessary_ (your own) DNS server, and hide the information in domain names
- You can make specific ICMP-parcels ***.
- SMTP/IMAP/POP3 mail protocols are used
  https://habr.com/ru/company/kaspersky/blog/522128/
In short, you can use different modulation options for a useful signal over the carrier,
which is guaranteed to pass through the firewall.

Traffic is intercepted and analyzed by systems like Suricata https://suricata-ids.org/.
that recognize anomalies and look for patterns in the traffic.
PyWhat library for automatic traffic parsing
https://habr.com/ru/company/dcmiran/news/t/563206/
https://github.com/bee-san/pyWhat

There are blacklists of domains, addresses, SSL certificates, traffic profiles, for example:
https://sslbl.abuse.ch
https://urlhaus.abuse.ch
https://feodotracker.abuse.ch

Techniques such as JA3 client fingerprinting/JA3S server fingerprinting/JARM (fuzzy hashing) are used:
https://engineering.salesforce.com/tls-fingerprinting-with-ja3-and-ja3s-247362855967
https://habr.com/ru/company/acribia/blog/560168/
Their essence is that TLS handshake is predictable for client+server bonding because there are many combinations of ciphers in the handshake,
taking into account their mutual arrangement. The handshake of the same client with the same server is always the same.
This handshake is fingerprinted by gluing the TLS version, accepted ciphers, extension list, elliptic curves and elliptic curve formats, and overlaying MD5.
The fingerprinting tool:
https://github.com/salesforce/ja3
https://ja3er.com/form
The remedy is to randomize the TLS stack (Cipher-Stunting) on both client and server (randomizing the SSLCipherSuite and similar settings):
https://www.bc-security.org/post/ja3-s-signatures-and-how-to-avoid-them
https://www.vectra.ai/blogpost/c2-evasion-techniques
TL;DR: the Enable-TlsCipherSuite commandlet allows to change the client cipher combination - but - this is a system-wide setting.
BCryptAddContextFunction
https://docs.microsoft.com/en-us/windows/win32/secauthn/prioritizing-schannel-cipher-suites


It is necessary to provide constant relevance of the address of the C&C-server in the body of the program.
Therefore, instead of using conventional domain names and DNS infrastructure,
it is possible to use a public infrastructure that cannot be revoked:
- Emercoin domains and DNS (both Emercoin-specific queries can be made,
as well as normal DNS queries to OpenNIC servers)
- cryptocurrency blockchain records (modulation of information in amounts, addresses or service records)
- TOR domains (TOR is not open everywhere and requires a specific client code to work over TOR)
- Tweets and other public social networks (less often, because the account can be revoked)
  e.g. https://safe.cnews.ru/news/top/2020-09-08_hakerynaemniki_shest_let

Overview of communication techniques courtesy of Positive Technologies: https://habr.com/ru/company/pt/blog/497608/
DNS Tunneling: https://habr.com/ru/company/varonis/blog/513160/

Traffic-masking approaches are used to combat network exchange detections.
Designs like the C2 Malleable Profile for Cobalt Strike are used to work on an industrial scale
https://www.cobaltstrike.com/help-malleable-c2
https://github.com/threatexpress/random_c2_profile
or the C3 gateway constructor https://github.com/FSecureLABS/C3
The principle is to take the transport layer of the software out of the software itself, make it flexibly configurable and maskable,
to allow fast expansion and flexible reaction to detections through modularity (the main module is the main functional part of the software,
network functionality - an external plug-in module).


If all channels from inside the network are completely closed, the information can still be sent.

One option is mail (SMTP):
- Looking for a mail client on the local machine and put the letter into Outbox, not forgetting to clean up after yourself
- look for an SMTP server on your local machine (although they now always require authentication)
- Also webmail (e.g. OWA = Outlook Web Access) may be available on the local machine and the Internet is closed at the same time.

Another option is the so-called "rocket:
https://www.blackhat.com/docs/us-17/thursday/us-17-Kotler-The-Adventures-Of-Av-And-The-Leaky-Sandbox.pdf
consisting, as the name suggests, of two stages.
The first stage must be Fully Undetectable.
It collects information from the system, forms a message, and pushes it into the second stage.
The second stage is a binary with the following properties:
- it annoys the AB (there are signature detects)
- it knows how to contact the C&C server and send it a carrier-embroidered parcel.

Further events unfold as follows:
- corporate AV sends the second stage to the cloud to run in the sandbox
- the second stage enters the sandbox and is launched
- the sandbox is in the cloud, outside the defensible perimeter, and there is a connection to the internet from there
- the task of the sandbox is to investigate the behavior of the specimen, so its activity is not jammed, although it is recorded
- the second stage sends data to the C&C, and from there it doesn't care
- PROFIT


               VI. INCREASE IN PRIVILEGES

UAC OVERRIDE

This is the first thing you have to do: https://github.com/hfiref0x/UACME
Methods up to about the 20th are obsolete and don't work; up to the 40th through one.

In a nutshell, the workaround idea is as follows:
1. we disguise the current process as a legitimate one, for which Windows never asks for elevation (by changing the process name in the PEB)
2. Let's pull another process with auto-levation, so that it runs the desired .exe (similar to suid root in Unix)
For the second point, there are a shitload of ways.

Never run it on a personal machine! YOU CAN RENDER THE OPERATING SYSTEM UNUSABLE!


ELEVATION OF PRIVILEGES (LPE)

On privilege escalation, an overview of general strategies here: https://habr.com/ru/post/428602/
Specific exploits are quickly becoming obsolete, so we will not list them here.
The general idea of LPE in Windows - obtaining and using someone else's security token https://habr.com/ru/company/pt/blog/563436/
LPE theory and practice: https://habr.com/ru/company/otus/blog/530596/
General techniques of exploiting kernel vulnerabilities: https://habr.com/ru/company/pt/blog/566698/
(HalDispatchTable patching, token stealing)


               VII. RESEARCH

How do you figure out what's going on inside a black box, whether it's covered by a .exe packer, or an unknown system altogether?

INTERCEPTING SYSTEM CALLS

Windows has API Monitor (from rohitab package) and Linux has strace.
We eavesdrop on system calls of the executable we are interested in, filter according to the necessary criteria, and understand the picture of what is happening.

TRAFFIC INTERCEPTION

Wireshark, you can find out addresses, ports, protocols. If you're lucky, you can even see the details.
Because of the ubiquity of SSL it's not easy, but you can slip your root certificate into the system, raise a proxy using it,
and catch the traffic on the proxy.
https://mitmproxy.org/ proxy for HTTPS, also useful for catching problems at subsystem junctions

PATCH DIFFING / BINARY DIFFING

A way to find a vulnerability by fixing it.
Take the old executable, take the new executable (with the vulnerability patch), see the difference,
calculate exploitation details from the difference.
https://habr.com/ru/company/dsec/blog/479972/
https://wumb0.in/extracting-and-diffing-ms-patches-in-2020.html

FUZZING

Feed the system to the input a fierce nonsense generated randomly (but by the rules), and see when it breaks (catch crashes).
Then, we correlate the data on the input with the crashes and find a stable picture.
Then we can use it to layout the shellcode for exploitation.

There are a lot of tools for this, no one does it manually, everything is automated.

Description of white box phasing (with known sources).
https://habr.com/ru/company/dsec/blog/517596/
This is more for autotesting, quality control, and on-the-fly test generation than for research/reversal.
Still, the article gives a general idea of the techniques and tools.

Popular AFL (American Fuzzy Lop) phaser based on genetic algorithms (morphing the correct input sample)
https://github.com/google/AFL
it's also for vindu
https://github.com/googleprojectzero/winafl

Rationale for phasing approaches with a breakdown of theory:
https://habr.com/ru/company/bizone/blog/570312/
https://habr.com/ru/company/bizone/blog/570534/
https://wcventure.github.io/FuzzingPaper/
Phasing just a black box is futile. We need
- Reverse/analyze the code
- look for input checks
- stuff the phaser with them so that it can correctly mutate the input and break through the checks



               VIII. AUXILIARY SERVICE TECHNICIANS

EXCEPTIONS AND POSTMORTEM STACK

Catching bugs and debugging on an industrial scale is a fairly trivial task, but the solutions to it are not known to everyone and are not always trivial.
The main way to find out what went wrong is to take a postmortem call stack and send it by telemetry to the server.
Theory and practice of exception handling in Windows: https://habr.com/ru/post/536990/
To do that you have to take off the death stack.
To do that you need to catch a program crash and remove the necessary information before letting it die.
There are two main ways to do this:
* VEH - Vectored Exception Handling
* SEH - Structured Exception Handling
On Linux/*nix there are signals (SIGBUS, SIGSTOP, SIGILL, etc), man signal

And again:
- VEH is AddVectoredExceptionHandler() and article https://docs.microsoft.com/ru-ru/windows/win32/debug/using-a-vectored-exception-handler
- SEH is __try ... __except and the article https://docs.microsoft.com/en-us/cpp/cpp/try-except-statement?view=vs-2019
If SEH is used, you have to wrap all main threads in try/except.
If VEH is used, it is enough to set one common handler in program's prologue.

On the downside of SEH - Error C2712 Cannot use __try in functions that require object unwinding, and the solution is described here
https://stackoverflow.com/questions/51701426/cannot-use-try-in-functions-that-require-object-unwinding-fix
Properties / C/C++ / Code creation / Enable C++ exceptions: None

The main drawback of any approach is that with process hollowed processes, no method will give line numbers and function names,
because character loading will not work. There will only be bare addresses.

In the exception handler we have to remove the stack (code below).
If we want the code line numbers, we need symbols (.pdb), and the project must be built with the options
- C/C++ / General / Debug format: program database (/Zi)
- Builder / Debug / Create debugging information: Optimize for debugging (/DEBUG)
- Linker / Debug / Create full program database file: Yes
and the .pdb must lie BETWEEN the dying .exe or .dll.

This will not work for battle builds, but it may work for debugging on internal resources. For battle builds, the stack will just contain addresses, which is not insignificant either.

The stack removal code is quite small, we will give it here:

#include <windows.h>
#include <Psapi.h>

// Some versions of imagehlp.dll lack the proper packing directives themselves
// so we need to do it.
#pragma pack( push, before_imagehlp, 8 )
#include <imagehlp.h>
#pragma pack( pop, before_imagehlp )

#pragma comment(lib, "psapi.lib")
#pragma comment(lib, "dbghelp.lib")


__declspec(noinline) DWORD DumpStackTrace() {
    unsigned int i;
    void * stack[100];
    unsigned short frames;
    SYMBOL_INFO * symbol;
    HANDLE process;

    debug_printf("PROGRAM CRASHED, STACK TRACE FOLLOWS:\r\n");

    process = GetCurrentProcess();

    if (!SymInitialize(process, NULL, TRUE))
        return 0;

    DWORD symOptions = SymGetOptions();
    symOptions |= SYMOPT_LOAD_LINES | SYMOPT_UNDNAME | SYMOPT_LOAD_ANYTHING | SYMOPT_CASE_INSENSITIVE;
    SymSetOptions(symOptions);

    frames = CaptureStackBackTrace(0, 100, stack, NULL);
    symbol = (SYMBOL_INFO *)calloc(sizeof(SYMBOL_INFO) + 256 * sizeof(char), 1);
    symbol->MaxNameLen = 255;
    symbol->SizeOfStruct = sizeof(SYMBOL_INFO);

    DWORD offset_from_symbol = 0;
#ifdef _WIN64
    IMAGEHLP_LINE64* line = (IMAGEHLP_LINE64*)calloc(sizeof(IMAGEHLP_LINE64), 1);
    line->SizeOfStruct = sizeof(IMAGEHLP_LINE64);
#else
    IMAGEHLP_LINE* line = (IMAGEHLP_LINE*)calloc(sizeof(IMAGEHLP_LINE), 1);
    line->SizeOfStruct = sizeof(IMAGEHLP_LINE);
#endif


    for (i = 0; i < frames; i++)
    {
        SymFromAddr(process, (DWORD64)(stack[i]), 0, symbol);

        SymGetLineFromAddr(process, (DWORD64)(stack[i]), &offset_from_symbol, line);

        debug_printf("%i: %s (%s:%i) - 0x%0X\n", frames - i - 1, symbol->Name,
            line->FileName, line->LineNumber, symbol->Address);
        symbol->Name[0] = 0;
        symbol->Address = 0;
        if(line->FileName)
            line->FileName[0] = 0;
        line->LineNumber = 0;
    }

    free(symbol);
    free(line);

    return 1;
}


MAP FILES (.MAP)

Enabled in the linker:
Visual Studio / Project Properties / Linker / Debug / Create mapping file: YES

If the crashed program has no characters in the .pdb, but you know the crash address, you can find the address of the function from the map, as described here:
https://www.codeproject.com/articles/3472/finding-crash-information-using-the-map-file


CODE REDUCTION

CRT (C RUNTIME LIBRARY) REJECTION

An example of a program that compiles to an .exe of size 3k:

hello.cpp:
#include <windows.h>

const char *str="Message";

int MyMain()
{
  MessageBoxA(NULL,str,str,MB_OK);
  ExitProcess(0);
  return 0;
}

build.bat:
set PATH=c:\LLVM9\bin

clang++.exe -DUNICODE -c -D_UNICODE -m32 -std=c++14 -Wall -Os -mno-sse -fms-extensions -fms-compatibility -fno-exceptions -fno-rtti -fomit-frame-pointer -ffunction-sections -fdata-sections -Wno-c++11-narrowing -Wc++11-compat-deprecated-writable-strings *.cpp -I "c:\Program Files (x86)\Microsoft Visual Studio 14.0\VC\include"

lld-link.exe /subsystem:windows /nodefaultlib /entry:MyMain /libpath: "c:\Program Files (x86)\Microsoft Visual Studio 14.0\VC\lib" /libpath: "c:\Program Files (x86)\Microsoft SDKs\Windows\v7.1A\Lib" *.o kernel32.lib user32.lib

The default entry point is WinMainCRTStartup in CRT. When its own entry point, CRT is not needed.
In addition there is a CRT disable switch /nodefaultlib.
But strcpy functions have to be written internally and exceptions cannot be used.
But strcpy is already in shlwapi.lib, so do in the code

#include <Shlwapi.h>
#pragma comment(lib, "Shlwapi.lib")

DISABLING SECURITY CHECKS

The Microsoft compiler puts a lot of extra security code in the final code - stack canaries, array exit checks,
nulling variables at the input to the function. All this gives extra kilobytes and is not needed in combat code.
- Properties / C/C++ / Code Creation / Basic Runtime Checks (/RTC) - by default (it's not really clear what to set here)
- Properties / C/C++ / Code creation / Control flow protection - None
- Properties / C/C++ / Code creation / Create image with updates - No (only if you will not put hooks to your own functions)
- Properties / C/C++ / Language / Delete code and data not referenced - Yes /Zc:inline)
- Properties / C/C++ / Language / Include information about runtime types - No


OPTIMIZATIONS

- Omit frame pointers - by default the current stack top is in the BSP register when entering a function.
This is how you can separate the private stack of the current function from the stack of higher-level functions.
If you disable stack frame saving, another register will be freed up and the number of assembler instructions will be reduced,
both by not writing to it and by allowing more variables to be stored and passed through registers.

MICROSOFT RICH HEADER

http://ntcore.com/files/richsign.htm
http://bytepointer.com/articles/the_microsoft_rich_header.htm
An unstated section in the PE header which has been inserted by Microsoft linkers since 1998 and Microsoft Visual Studio 6.0.
It contains statistics about the toolchain that built this binary, such as the number of C,
Nb of C++ object files, Nb of ASM object files, linker version, resource compiler version,
the number of functions in the import, that sort of thing.
This was most likely done for debugging purposes (to debug the build toolchain).
However, you should understand that this header can be used for forensics as a fingerprint.

CROSS-BIT CODE

It is possible to execute both 32-bit code in 64-bit mode and vice versa.
The Windows kernel provides gateways, such as Heaven's Gate, to make system calls from 32-bit mode:
https://medium.com/@fsx30/hooking-heavens-gate-a-wow64-hooking-technique-5235e1aeed73
Another example:
http://blog.rewolf.pl/blog/?p=102
https://github.com/rwfpl/rewolf-wow64ext


PSEUDORANDOM NUMBER GENERATION

Code and parsing of simple PRNG algorithms here https://habr.com/ru/post/499490/
A theoretical review of non-crypto-proof and crypto-proof PRNGs: https://habr.com/ru/post/531750/
Keep in mind that the quality of PRNG (precisely random, without the letter P in the acronym) is the most important link in cryptography.
A good cryptoalgorithm is negated by using a bad PRNG in it (e.g., to generate a gamma, vector IV, etc.).


               LITERATURE

1. M. Russinovich, D. Solomon - The Inside of Microsoft Windows, 6th Edition (Part 1) [2013, PDF, RUS] - https://rutracker.org/forum/viewtopic.php?t=4469765
2. M. Russinovich, D. Solomon, A. Ionescu - The inner workings of Microsoft Windows. Basic OS Subsystems. 6th edition (Part 2) [2014, PDF, RUS] - https://rutracker.org/forum/viewtopic.php?t=4727796
3. developer's library - Love R. - Linux kernel: description of the development process, 3rd ed. [2013, PDF, RUS] - https://rutracker.org/forum/viewtopic.php?t=5169029
4. Programmer's Library - Elger J. - C++ [2008, PDF, RUS] - https://rutracker.org/forum/viewtopic.php?t=694260
5. Chris Kaspersky. Collection of 507 articles - 2017.03.01 [PDF, DOC] - https://rutracker.org/forum/viewtopic.php?t=5375505
6. Understanding Windows Shellcode - mmiller@hick.org - http://www.hick.org/code/skape/papers/win32-shellcode.pdf
7. https://0xpat.github.io/ 0xPat blog Red/purple teamer - "Malware development" article series
