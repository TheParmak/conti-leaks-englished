# Regeln

Es wird dazu ermutigt, dem Kohana [Programmierstil](http://dev.kohanaframework.org/wiki/kohana2/CodingStyle) zu folgen. Dieser benutzt den [Allman/BSD](http://de.wikipedia.org/wiki/Einr%C3%BCckungsstil#Allman_.2F_BSD_.2F_.E2.80.9EEast_Coast.E2.80.9C_.2F_Horstmann)-Stil.

## Klassennamen und Dateilage {#classes}

Das automatische Laden von Klassen wird durch ihre strengen Namensregeln ermöglicht. Die Klassen beginnen mit einem Großbuchstaben und ihre Wšrter werden durch Unterstriche getrennt. Diese sind entscheidend, an welcher Stelle die Klasse im Dateisystem gefunden wird. 

Folgende Regeln gelten:

1. Binnenversalien (camelCase) sollten nicht benutzt werden, außer wenn eine weitere Ordner-Ebene unerwünscht ist
2. alle Datei- und Verzeichnisnamen in Kleinbuchstaben
3. alle Klassen werden im `classes`-Verzeichnis in jeder Ebene des [Kaskaden-Dateisystem](about.filesystem) zusammengefasst

[!!] Im Gegensatz zu Kohana v2.x besteht keine Unterteilung zwischen "Controllern", "Models", "Bibliotheken" und "Helfern". Alle Klassen befinden sich im "classes/"-Verzeichnis, unabhängig ob es statische "Helfer" oder Objekt-"Bibliotheken" sind. Man kann irgendeinen Klassen-Aufbau (statische Klasse, Singleton, Adapter) verwenden, den man mag.

## Beispiele

Denk daran, dass der Unterstrich in Klassennamen eine tiefere Verzeichnisebene bedeutet. Beachte folgende Beispiele:

Klassenname           | Dateipfad
----------------------|-------------------------------
Controller_Template   | classes/controller/template.php
Model_User            | classes/model/user.php
Database              | classes/database.php
Database_Query        | classes/database/query.php
Form                  | classes/form.php

## Programmierstil {#coding_standards}

Um einen sehr konsistenten Quelltext zu produzieren, bitten wir jeden den folgenden Programmierstil so genau wie möglich umzusetzen.

### Klammerung

Bitte benutze den den [Allman/BSD](http://de.wikipedia.org/wiki/Einr%C3%BCckungsstil#Allman_.2F_BSD_.2F_.E2.80.9EEast_Coast.E2.80.9C_.2F_Horstmann)-Stil.

### Namensregeln

Kohana benutzt für Namen Unter_striche, keine BinnenVersalien (camelCase).

#### Klassen

	// Libary
	class Beer {

	// Libary extension, uses Kohana_ prefix
	class Beer extends Kohana_Beer {

	// Controller class, uses Controller_ prefix
	class Controller_Apple extends Controller {

	// Model class, uses Model_ prefix
	class Model_Cheese extends Model {

	// Helper class, cf. libary
	class peanut {

Benutze keine Klammern, wenn eine Klasseninstanz erstellt, aber keine Parameter übergibt:

	// Correct:
	$db = new Database;

	// Incorrect:
	$db = new Database();

#### Funktionen und Methoden

Funktionen sollten kleingeschrieben sein und Unter_striche zur Worttrennung benutzen:

	function drink_beverage($beverage)
	{

#### Variablen

Alle Variablen sollten ebenfalls kleingeschrieben sein und Unter_striche benutzen, keine BinnenVersalien (camelCase):

	// Correct:
	$foo = 'bar';
	$long_example = 'uses underscores';

	// Incorrect:
	$weDontWantThis = 'understood?';

### Einrückung

Du musst zur Einrückung deines Quelltextes Tabulatoren benutzen. Leerzeichen für Tabellarisierung zu verwenden, ist strengstens verboten.

Vertikaler Abstand (bei Mehrzeiligkeit) wird mit Leerzeichen gemacht. Tabulatoren sind schlecht für die vertikale Ausrichtung, weil verschiedene Leute unterschiedliche Tabulatoren-Breiten haben.

	$text = 'this is a long text block that is wrapped. Normally, we aim for '
		  . 'wrapping at 80 chars. Vertical alignment is very important for '
		  . 'code readability. Remember that all indentation is done with tabs,'
		  . 'but vertical alignment should be completed with spaces, after '
		  . 'indenting with tabs.';

### Zeichenkettenverknüpfung

Setze keine Leerzeichen um den Verknüpfungsoperator:

	// Correct:
	$str = 'one'.$var.'two';

	// Incorrect:
	$str = 'one'. $var .'two';
	$str = 'one' . $var . 'two';

### Einzeilige Ausdrücke

Einzeilige IF-Bedingungen sollten nur bei Anweisungen benutzt werden, die die normale Verarbeitung unterbrechen (z.B. return oder continue):

	// Acceptable:
	if ($foo == $bar)
		return $foo;

	if ($foo == $bar)
		continue;

	if ($foo == $bar)
		break;

	if ($foo == $bar)
		throw new Exception('You screwed up!');

	// Not acceptable:
	if ($baz == $bun)
		$baz = $bar + 2;

### Vergleichsoperatoren

Bitte benutze OR and AND:

	// Correct:
	if (($foo AND $bar) OR ($b AND $c))

	// Incorrect:
	if (($foo && $bar) || ($b && $c))
	
Bitte benutze elseif, nicht else if:

	// Correct:
	elseif ($bar)

	// Incorrect:
	else if($bar)

### Switch structures

Each case, break and default should be on a separate line. The block inside a case or default must be indented by 1 tab.

	switch ($var)
	{
		case 'bar':
		case 'foo':
			echo 'hello';
		break;
		case 1:
			echo 'one';
		break;
		default:
			echo 'bye';
		break;
	}

### Parentheses

There should be one space after statement name, followed by a parenthesis. The ! (bang) character must have a space on either side to ensure maximum readability. Except in the case of a bang or type casting, there should be no whitespace after an opening parenthesis or before a closing parenthesis.

	// Correct:
	if ($foo == $bar)
	if ( ! $foo)

	// Incorrect:
	if($foo == $bar)
	if(!$foo)
	if ((int) $foo)
	if ( $foo == $bar )
	if (! $foo)

### Ternaries

All ternary operations should follow a standard format. Use parentheses around expressions only, not around just variables.

	$foo = ($bar == $foo) ? $foo : $bar;
	$foo = $bar ? $foo : $bar;

All comparisons and operations must be done inside of a parentheses group:

	$foo = ($bar > 5) ? ($bar + $foo) : strlen($bar);

When separating complex ternaries (ternaries where the first part goes beyond ~80 chars) into multiple lines, spaces should be used to line up operators, which should be at the front of the successive lines:

	$foo = ($bar == $foo)
		 ? $foo
		 : $bar;

### Type Casting

Type casting should be done with spaces on each side of the cast:

	// Correct:
	$foo = (string) $bar;
	if ( (string) $bar)

	// Incorrect:
	$foo = (string)$bar;

When possible, please use type casting instead of ternary operations:

	// Correct:
	$foo = (bool) $bar;

	// Incorrect:
	$foo = ($bar == TRUE) ? TRUE : FALSE;

When casting type to integer or boolean, use the short format:

	// Correct:
	$foo = (int) $bar;
	$foo = (bool) $bar;

	// Incorrect:
	$foo = (integer) $bar;
	$foo = (boolean) $bar;

### Constants

Always use uppercase for constants:

	// Correct:
	define('MY_CONSTANT', 'my_value');
	$a = TRUE;
	$b = NULL;

	// Incorrect:
	define('MyConstant', 'my_value');
	$a = True;
	$b = null;

Place constant comparisons at the end of tests:

	// Correct:
	if ($foo !== FALSE)

	// Incorrect:
	if (FALSE !== $foo)

This is a slightly controversial choice, so I will explain the reasoning. If we were to write the previous example in plain English, the correct example would read:

	if variable $foo is not exactly FALSE

And the incorrect example would read:

	if FALSE is not exactly variable $foo

Since we are reading left to right, it simply doesn't make sense to put the constant first.

### Comments

#### One-line comments

Use //, preferably above the line of code you're commenting on. Leave a space after it and start with a capital. Never use #.

	// Correct

	//Incorrect
	// incorrect
	# Incorrect

### Regular expressions

When coding regular expressions please use PCRE rather than the POSIX flavor. PCRE is considered more powerful and faster.

	// Correct:
	if (preg_match('/abc/i'), $str)

	// Incorrect:
	if (eregi('abc', $str))

Use single quotes around your regular expressions rather than double quotes. Single-quoted strings are more convenient because of their simplicity. Unlike double-quoted strings they don't support variable interpolation nor integrated backslash sequences like \n or \t, etc.

	// Correct:
	preg_match('/abc/', $str);

	// Incorrect:
	preg_match("/abc/", $str);

When performing a regular expression search and replace, please use the $n notation for backreferences. This is preferred over \\n.

	// Correct:
	preg_replace('/(\d+) dollar/', '$1 euro', $str);

	// Incorrect:
	preg_replace('/(\d+) dollar/', '\\1 euro', $str);

Finally, please note that the $ character for matching the position at the end of the line allows for a following newline character. Use the D modifier to fix this if needed. [More info](http://blog.php-security.org/archives/76-Holes-in-most-preg_match-filters.html).

	$str = "email@example.com\n";

	preg_match('/^.+@.+$/', $str);  // TRUE
	preg_match('/^.+@.+$/D', $str); // FALSE
