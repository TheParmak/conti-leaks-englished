# Using Codebench

[!!] The contents of this page are taken (with some minor changes) from <http://www.geertdedeckere.be/article/introducing-codebench> and are copyright Geert De Deckere.

For a long time I have been using a quick-and-dirty `benchmark.php` file to optimize bits of PHP code, many times regex-related stuff. The file contained not much more than a [gettimeofday](http://php.net/gettimeofday) function wrapped around a `for` loop. It worked, albeit not very efficiently. Something more solid was needed. I set out to create a far more usable piece of software to aid in the everlasting quest to squeeze every millisecond out of those regular expressions.

## Codebench Goals

### Benchmark multiple regular expressions at once

Being able to compare the speed of an arbitrary amount of regular expressions would be tremendously useful. In case you are wondering—yes, I had been writing down benchmark times for each regex, uncommenting them one by one. You get the idea. Those days should be gone forever now.

### Benchmark multiple subjects at once

What gets overlooked too often when testing and optimizing regular expressions is the fact that speed can vastly differ depending on the subjects, also known as input or target strings. Just because your regular expression matches, say, a valid email address quickly, does not necessarily mean it will quickly realize when an invalid email is provided. I plan to write a follow-up article with hands-on regex examples to demonstrate this point. Anyway, Codebench allows you to create an array of subjects which will be passed to each benchmark.

### Make it flexible enough to work for all PCRE functions

Initially I named the module “Regexbench”. I quickly realized, though, it would be flexible enough to benchmark all kinds of PHP code, hence the change to “Codebench”. While tools specifically built to help profiling PCRE functions, like [preg_match](http://php.net/preg_match) or [preg_replace](http://php.net/preg_replace), definitely have their use, more flexibility was needed here. You should be able to compare all kinds of constructions like combinations of PCRE functions and native PHP string functions.

### Create clean and portable benchmark cases

Throwing valuable benchmark data away every time I needed to optimize another regular expression had to stop. A clean file containing the complete set of all regex variations to compare, together with the set of subjects to test them against, would be more than welcome. Moreover, it would be easy to exchange benchmark cases with others.

### Visualize the benchmarks

Obviously providing a visual representation of the benchmark results, via simple graphs, would make interpreting them easier. Having not to think about Internet Explorer for once, made writing CSS a whole lot more easy and fun. It resulted in some fine graphs which are fully resizable.

Below are two screenshots of Codebench in action. `Valid_Color` is a class made for benchmarking different ways to validate hexadecimal HTML color values, e.g. `#FFF`. If you are interested in the story behind the actual regular expressions, take a look at [this topic in the Kohana forums](http://forum.kohanaphp.com/comments.php?DiscussionID=2192).

![Benchmarking several ways to validate HTML color values](codebench_screenshot1.png)
**Benchmarking seven ways to validate HTML color values**

![Collapsable results per subject for each method](codebench_screenshot2.png)
**Collapsable results per subject for each method**

## Working with Codebench

Codebench is included in Kohana 3, but if you need you [can download it](http://github.com/kohana/codebench/) from GitHub. Be sure Codebench is activated in your `application/bootstrap.php`.

Creating your own benchmarks is just a matter of creating a class that extends the Codebench class.  The class should go in `classes/bench` and the class name should have the `Bench_` prefix.  Put the code parts you want to compare into separate methods. Be sure to prefix those methods with `bench_`, other methods will not be benchmarked. Glance at the files in `modules/codebench/classes/bench/` for more examples.

Here is another short example with some extra explanations.
	
	// classes/bench/ltrimdigits.php
	class Bench_LtrimDigits extends Codebench {
	
		// Some optional explanatory comments about the benchmark file.
		// HTML allowed. URLs will be converted to links automatically.
		public $description = 'Chopping off leading digits: regex vs ltrim.';
	
		// How many times to execute each method per subject.
		// Total loops = loops * number of methods * number of subjects
		public $loops = 100000;
	
		// The subjects to supply iteratively to your benchmark methods.
		public $subjects = array
		(
			'123digits',
			'no-digits',
		);
	
		public function bench_regex($subject)
		{
			return preg_replace('/^\d+/', '', $subject);
		}
	
		public function bench_ltrim($subject)
		{
			return ltrim($subject, '0..9');
		}
	}
	
	

And the winner is… [ltrim](http://php.net/ltrim). Happy benchmarking!