<style type="text/css">
#kohana_error { background: #ddd; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }
#kohana_error h1,
#kohana_error h2 { margin: 0; padding: 1em; font-size: 1em; font-weight: normal; background: #911; color: #fff; }
	#kohana_error h1 a,
	#kohana_error h2 a { color: #fff; }
#kohana_error h2 { background: #222; }
#kohana_error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
#kohana_error p { margin: 0; padding: 0.2em 0; }
#kohana_error a { color: #1b323b; }
#kohana_error pre { overflow: auto; white-space: pre-wrap; }
#kohana_error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
	#kohana_error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }
#kohana_error div.content { padding: 0.4em 1em 1em; overflow: hidden; }
#kohana_error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }
	#kohana_error pre.source span.line { display: block; }
	#kohana_error pre.source span.highlight { background: #f0eb96; }
		#kohana_error pre.source span.line span.number { color: #666; }
#kohana_error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }
	#kohana_error ol.trace li { margin: 0; padding: 0; }
</style>
<script type="text/javascript">
document.write('<style type="text/css"> .collapsed { display: none; } </style>');
function koggle(elem)
{
	elem = document.getElementById(elem);

	if (elem.style && elem.style['display'])
		// Only works with the "style" attr
		var disp = elem.style['display'];
	else if (elem.currentStyle)
		// For MSIE, naturally
		var disp = elem.currentStyle['display'];
	else if (window.getComputedStyle)
		// For most other browsers
		var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');

	// Toggle the state of the "display" style
	elem.style.display = disp == 'block' ? 'none' : 'block';
	return false;
}
</script>
<div id="kohana_error">
	<h1><span class="type">Kohana_View_Exception [ 0 ]:</span> <span class="message">The requested view site could not be found</span></h1>
	<div id="error4ac2453378034" class="content">
		<p><span class="file">SYSPATH/classes/kohana/view.php [ 215 ]</span></p>
		<pre class="source"><code><span class="line"><span class="number">210</span> 	 */

</span><span class="line"><span class="number">211</span> 	public function set_filename($file)
</span><span class="line"><span class="number">212</span> 	{
</span><span class="line"><span class="number">213</span> 		if (($path = Kohana::find_file('views', $file)) === FALSE)
</span><span class="line"><span class="number">214</span> 		{
</span><span class="line highlight"><span class="number">215</span> 			throw new Kohana_View_Exception('The requested view :file could not be found', array(
</span><span class="line"><span class="number">216</span> 				':file' =&gt; $file,

</span><span class="line"><span class="number">217</span> 			));
</span><span class="line"><span class="number">218</span> 		}
</span><span class="line"><span class="number">219</span> 
</span><span class="line"><span class="number">220</span> 		// Store the file path locally
</span></code></pre>		<ol class="trace">
					<li>
				<p>

					<span class="file">
													<a href="#error4ac2453378034source0" onclick="return koggle('error4ac2453378034source0')">SYSPATH/classes/kohana/view.php [ 115 ]</a>
											</span>
					&raquo;
					Kohana_View->set_filename(<a href="#error4ac2453378034args0" onclick="return koggle('error4ac2453378034args0')">arguments</a>)
				</p>
								<div id="error4ac2453378034args0" class="collapsed">
					<table cellspacing="0">

											<tr>
							<td><code>file</code></td>
							<td><pre><small>string</small><span>(4)</span> "site"</pre></td>
						</tr>
										</table>
				</div>

													<pre id="error4ac2453378034source0" class="source collapsed"><code><pre class="source"><code><span class="line"><span class="number">110</span> 	 */
</span><span class="line"><span class="number">111</span> 	public function __construct($file = NULL, array $data = NULL)
</span><span class="line"><span class="number">112</span> 	{
</span><span class="line"><span class="number">113</span> 		if ($file !== NULL)
</span><span class="line"><span class="number">114</span> 		{
</span><span class="line highlight"><span class="number">115</span> 			$this-&gt;set_filename($file);

</span><span class="line"><span class="number">116</span> 		}
</span><span class="line"><span class="number">117</span> 
</span><span class="line"><span class="number">118</span> 		if ( $data !== NULL)
</span><span class="line"><span class="number">119</span> 		{
</span><span class="line"><span class="number">120</span> 			// Add the values to the current data
</span></code></pre></code></pre>
							</li>

								<li>
				<p>
					<span class="file">
													<a href="#error4ac2453378034source1" onclick="return koggle('error4ac2453378034source1')">SYSPATH/classes/kohana/view.php [ 26 ]</a>
											</span>
					&raquo;
					Kohana_View->__construct(<a href="#error4ac2453378034args1" onclick="return koggle('error4ac2453378034args1')">arguments</a>)
				</p>

								<div id="error4ac2453378034args1" class="collapsed">
					<table cellspacing="0">
											<tr>
							<td><code>file</code></td>
							<td><pre><small>string</small><span>(4)</span> "site"</pre></td>
						</tr>

											<tr>
							<td><code>data</code></td>
							<td><pre><small>NULL</small></pre></td>
						</tr>
										</table>
				</div>
													<pre id="error4ac2453378034source1" class="source collapsed"><code><pre class="source"><code><span class="line"><span class="number">21</span> 	 * @param   array   array of values

</span><span class="line"><span class="number">22</span> 	 * @return  View
</span><span class="line"><span class="number">23</span> 	 */
</span><span class="line"><span class="number">24</span> 	public static function factory($file = NULL, array $data = NULL)
</span><span class="line"><span class="number">25</span> 	{
</span><span class="line highlight"><span class="number">26</span> 		return new View($file, $data);
</span><span class="line"><span class="number">27</span> 	}

</span><span class="line"><span class="number">28</span> 
</span><span class="line"><span class="number">29</span> 	/**
</span><span class="line"><span class="number">30</span> 	 * Captures the output that is generated when a view is included.
</span><span class="line"><span class="number">31</span> 	 * The view data will be extracted to make local variables. This method
</span></code></pre></code></pre>
							</li>
								<li>
				<p>

					<span class="file">
													<a href="#error4ac2453378034source2" onclick="return koggle('error4ac2453378034source2')">SYSPATH/classes/kohana/controller/template.php [ 32 ]</a>
											</span>
					&raquo;
					Kohana_View::factory(<a href="#error4ac2453378034args2" onclick="return koggle('error4ac2453378034args2')">arguments</a>)
				</p>
								<div id="error4ac2453378034args2" class="collapsed">
					<table cellspacing="0">

											<tr>
							<td><code>file</code></td>
							<td><pre><small>string</small><span>(4)</span> "site"</pre></td>
						</tr>
										</table>
				</div>

													<pre id="error4ac2453378034source2" class="source collapsed"><code><pre class="source"><code><span class="line"><span class="number">27</span> 	public function before()
</span><span class="line"><span class="number">28</span> 	{
</span><span class="line"><span class="number">29</span> 		if ($this-&gt;auto_render === TRUE)
</span><span class="line"><span class="number">30</span> 		{
</span><span class="line"><span class="number">31</span> 			// Load the template

</span><span class="line highlight"><span class="number">32</span> 			$this-&gt;template = View::factory($this-&gt;template);
</span><span class="line"><span class="number">33</span> 		}
</span><span class="line"><span class="number">34</span> 	}
</span><span class="line"><span class="number">35</span> 
</span><span class="line"><span class="number">36</span> 	/**
</span><span class="line"><span class="number">37</span> 	 * Assigns the template as the request response.

</span></code></pre></code></pre>
							</li>
								<li>
				<p>
					<span class="file">
													{PHP internal call}
											</span>
					&raquo;
					Kohana_Controller_Template->before()
				</p>

											</li>
								<li>
				<p>
					<span class="file">
													<a href="#error4ac2453378034source4" onclick="return koggle('error4ac2453378034source4')">SYSPATH/classes/kohana/request.php [ 840 ]</a>
											</span>
					&raquo;
					ReflectionMethod->invoke(<a href="#error4ac2453378034args4" onclick="return koggle('error4ac2453378034args4')">arguments</a>)
				</p>

								<div id="error4ac2453378034args4" class="collapsed">
					<table cellspacing="0">
											<tr>
							<td><code>object</code></td>
							<td><pre><small>object</small> <span>Controller_Hello(3)</span> <code>{
    <small>public</small> template => <small>string</small><span>(4)</span> "site"
    <small>public</small> auto_render => <small>bool</small> TRUE
    <small>public</small> request => <small>object</small> <span>Request(9)</span> <code>{
        <small>public</small> route => <small>object</small> <span>Route(4)</span> <code>{
            <small>protected</small> _uri => <small>string</small><span>(32)</span> "(&lt;controller&gt;(/&lt;action&gt;(/&lt;id&gt;)))"
            <small>protected</small> _regex => <small>array</small><span>(0)</span> 
            <small>protected</small> _defaults => <small>array</small><span>(2)</span> <span>(
                "controller" => <small>string</small><span>(7)</span> "welcome"
                "action" => <small>string</small><span>(5)</span> "index"
            )</span>

            <small>protected</small> _route_regex => <small>string</small><span>(87)</span> "#^(?:(?P&lt;controller&gt;[^/.,;?]++)(?:/(?P&lt;action&gt;[^/.,;?]++)(?:/(?P&lt;id&gt;[^/.,;?]++))?)?)?$#"
        }</code>
        <small>public</small> status => <small>integer</small> 500
        <small>public</small> response => <small>string</small><span>(0)</span> ""
        <small>public</small> headers => <small>array</small><span>(1)</span> <span>(
            "Content-Type" => <small>string</small><span>(24)</span> "text/html; charset=utf-8"
        )</span>

        <small>public</small> directory => <small>string</small><span>(0)</span> ""
        <small>public</small> controller => <small>string</small><span>(5)</span> "hello"
        <small>public</small> action => <small>string</small><span>(5)</span> "index"
        <small>public</small> uri => <small>string</small><span>(5)</span> "hello"
        <small>protected</small> _params => <small>array</small><span>(0)</span> 
    }</code>

}</code></pre></td>
						</tr>
										</table>
				</div>
													<pre id="error4ac2453378034source4" class="source collapsed"><code><pre class="source"><code><span class="line"><span class="number">835</span> 
</span><span class="line"><span class="number">836</span> 			// Create a new instance of the controller
</span><span class="line"><span class="number">837</span> 			$controller = $class-&gt;newInstance($this);

</span><span class="line"><span class="number">838</span> 
</span><span class="line"><span class="number">839</span> 			// Execute the "before action" method
</span><span class="line highlight"><span class="number">840</span> 			$class-&gt;getMethod('before')-&gt;invoke($controller);
</span><span class="line"><span class="number">841</span> 
</span><span class="line"><span class="number">842</span> 			// Determine the action to use
</span><span class="line"><span class="number">843</span> 			$action = empty($this-&gt;action) ? Route::$default_action : $this-&gt;action;

</span><span class="line"><span class="number">844</span> 
</span><span class="line"><span class="number">845</span> 			// Execute the main action with the parameters
</span></code></pre></code></pre>
							</li>
								<li>
				<p>
					<span class="file">
													<a href="#error4ac2453378034source5" onclick="return koggle('error4ac2453378034source5')">APPPATH/bootstrap.php [ 76 ]</a>

											</span>
					&raquo;
					Kohana_Request->execute()
				</p>
													<pre id="error4ac2453378034source5" class="source collapsed"><code><pre class="source"><code><span class="line"><span class="number">71</span> /**
</span><span class="line"><span class="number">72</span>  * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
</span><span class="line"><span class="number">73</span>  * If no source is specified, the URI will be automatically detected.

</span><span class="line"><span class="number">74</span>  */
</span><span class="line"><span class="number">75</span> echo Request::instance()
</span><span class="line highlight"><span class="number">76</span> 	-&gt;execute()
</span><span class="line"><span class="number">77</span> 	-&gt;send_headers()
</span><span class="line"><span class="number">78</span> 	-&gt;response;

</span></code></pre></code></pre>
							</li>
								<li>
				<p>
					<span class="file">
													<a href="#error4ac2453378034source6" onclick="return koggle('error4ac2453378034source6')">DOCROOT/index.php [ 106 ]</a>
											</span>
					&raquo;
					require(<a href="#error4ac2453378034args6" onclick="return koggle('error4ac2453378034args6')">arguments</a>)
				</p>

								<div id="error4ac2453378034args6" class="collapsed">
					<table cellspacing="0">
											<tr>
							<td><code>0</code></td>
							<td><pre><small>string</small><span>(49)</span> "/var/www/kohana/testing/application/bootstrap.php"</pre></td>
						</tr>

										</table>
				</div>
													<pre id="error4ac2453378034source6" class="source collapsed"><code><pre class="source"><code><span class="line"><span class="number">101</span> 	// Load empty core extension
</span><span class="line"><span class="number">102</span> 	require SYSPATH.'classes/kohana'.EXT;
</span><span class="line"><span class="number">103</span> }
</span><span class="line"><span class="number">104</span> 
</span><span class="line"><span class="number">105</span> // Bootstrap the application

</span><span class="line highlight"><span class="number">106</span> require APPPATH.'bootstrap'.EXT;
</span></code></pre></code></pre>
							</li>
							</ol>
	</div>
	<h2><a href="#error4ac2453378034environment" onclick="return koggle('error4ac2453378034environment')">Environment</a></h2>
	<div id="error4ac2453378034environment" class="content collapsed">
				<h3><a href="#error4ac2453378034environment_included" onclick="return koggle('error4ac2453378034environment_included')">Included files</a> (31)</h3>

		<div id="error4ac2453378034environment_included" class="collapsed">
			<table cellspacing="0">
								<tr>
					<td><code>DOCROOT/index.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/base.php</code></td>
				</tr>

								<tr>
					<td><code>SYSPATH/classes/kohana/core.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana.php</code></td>
				</tr>
								<tr>
					<td><code>APPPATH/bootstrap.php</code></td>

				</tr>
								<tr>
					<td><code>SYSPATH/classes/profiler.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/profiler.php</code></td>
				</tr>
								<tr>

					<td><code>SYSPATH/classes/kohana/log.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/config.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/log/file.php</code></td>

				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/log/writer.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/config/file.php</code></td>
				</tr>
								<tr>

					<td><code>SYSPATH/classes/kohana/config/reader.php</code></td>
				</tr>
								<tr>
					<td><code>MODPATH/codebench/init.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/route.php</code></td>

				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/route.php</code></td>
				</tr>
								<tr>
					<td><code>/var/www/kohana/userguide/init.php</code></td>
				</tr>
								<tr>

					<td><code>SYSPATH/classes/request.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/request.php</code></td>
				</tr>
								<tr>
					<td><code>APPPATH/classes/controller/hello.php</code></td>

				</tr>
								<tr>
					<td><code>SYSPATH/classes/controller/template.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/controller/template.php</code></td>
				</tr>
								<tr>

					<td><code>SYSPATH/classes/controller.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/controller.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/view.php</code></td>

				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/view.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/view/exception.php</code></td>
				</tr>
								<tr>

					<td><code>SYSPATH/classes/kohana/exception.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/i18n.php</code></td>
				</tr>
								<tr>
					<td><code>SYSPATH/classes/kohana/i18n.php</code></td>

				</tr>
								<tr>
					<td><code>SYSPATH/views/kohana/error.php</code></td>
				</tr>
							</table>
		</div>
				<h3><a href="#error4ac2453378034environment_loaded" onclick="return koggle('error4ac2453378034environment_loaded')">Loaded extensions</a> (41)</h3>

		<div id="error4ac2453378034environment_loaded" class="collapsed">
			<table cellspacing="0">
								<tr>
					<td><code>zip</code></td>
				</tr>
								<tr>
					<td><code>xmlwriter</code></td>
				</tr>

								<tr>
					<td><code>libxml</code></td>
				</tr>
								<tr>
					<td><code>xml</code></td>
				</tr>
								<tr>
					<td><code>wddx</code></td>

				</tr>
								<tr>
					<td><code>tokenizer</code></td>
				</tr>
								<tr>
					<td><code>sysvshm</code></td>
				</tr>
								<tr>

					<td><code>sysvsem</code></td>
				</tr>
								<tr>
					<td><code>sysvmsg</code></td>
				</tr>
								<tr>
					<td><code>session</code></td>

				</tr>
								<tr>
					<td><code>SimpleXML</code></td>
				</tr>
								<tr>
					<td><code>sockets</code></td>
				</tr>
								<tr>

					<td><code>soap</code></td>
				</tr>
								<tr>
					<td><code>SPL</code></td>
				</tr>
								<tr>
					<td><code>shmop</code></td>

				</tr>
								<tr>
					<td><code>standard</code></td>
				</tr>
								<tr>
					<td><code>Reflection</code></td>
				</tr>
								<tr>

					<td><code>posix</code></td>
				</tr>
								<tr>
					<td><code>mime_magic</code></td>
				</tr>
								<tr>
					<td><code>mbstring</code></td>

				</tr>
								<tr>
					<td><code>json</code></td>
				</tr>
								<tr>
					<td><code>iconv</code></td>
				</tr>
								<tr>

					<td><code>hash</code></td>
				</tr>
								<tr>
					<td><code>gettext</code></td>
				</tr>
								<tr>
					<td><code>ftp</code></td>

				</tr>
								<tr>
					<td><code>filter</code></td>
				</tr>
								<tr>
					<td><code>exif</code></td>
				</tr>
								<tr>

					<td><code>dom</code></td>
				</tr>
								<tr>
					<td><code>dba</code></td>
				</tr>
								<tr>
					<td><code>date</code></td>

				</tr>
								<tr>
					<td><code>ctype</code></td>
				</tr>
								<tr>
					<td><code>calendar</code></td>
				</tr>
								<tr>

					<td><code>bz2</code></td>
				</tr>
								<tr>
					<td><code>bcmath</code></td>
				</tr>
								<tr>
					<td><code>zlib</code></td>

				</tr>
								<tr>
					<td><code>pcre</code></td>
				</tr>
								<tr>
					<td><code>openssl</code></td>
				</tr>
								<tr>

					<td><code>xmlreader</code></td>
				</tr>
								<tr>
					<td><code>apache2handler</code></td>
				</tr>
								<tr>
					<td><code>curl</code></td>

				</tr>
								<tr>
					<td><code>PDO</code></td>
				</tr>
							</table>
		</div>
																<h3><a href="#error4ac2453378034environment_server" onclick="return koggle('error4ac2453378034environment_server')">$_SERVER</a></h3>
		<div id="error4ac2453378034environment_server" class="collapsed">

			<table cellspacing="0">
								<tr>
					<td><code>HTTP_HOST</code></td>
					<td><pre><small>string</small><span>(9)</span> "localhost"</pre></td>
				</tr>
								<tr>

					<td><code>HTTP_USER_AGENT</code></td>
					<td><pre><small>string</small><span>(105)</span> "Mozilla/5.0 (X11; U; Linux i686; en-GB; rv:1.9.0.14) Gecko/2009090216 Ubuntu/9.04 (jaunty) Firefox/3.0.14"</pre></td>
				</tr>
								<tr>
					<td><code>HTTP_ACCEPT</code></td>
					<td><pre><small>string</small><span>(63)</span> "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"</pre></td>

				</tr>
								<tr>
					<td><code>HTTP_ACCEPT_LANGUAGE</code></td>
					<td><pre><small>string</small><span>(14)</span> "en-gb,en;q=0.5"</pre></td>
				</tr>
								<tr>

					<td><code>HTTP_ACCEPT_ENCODING</code></td>
					<td><pre><small>string</small><span>(12)</span> "gzip,deflate"</pre></td>
				</tr>
								<tr>
					<td><code>HTTP_ACCEPT_CHARSET</code></td>
					<td><pre><small>string</small><span>(30)</span> "ISO-8859-1,utf-8;q=0.7,*;q=0.7"</pre></td>

				</tr>
								<tr>
					<td><code>HTTP_KEEP_ALIVE</code></td>
					<td><pre><small>string</small><span>(3)</span> "300"</pre></td>
				</tr>
								<tr>

					<td><code>HTTP_CONNECTION</code></td>
					<td><pre><small>string</small><span>(10)</span> "keep-alive"</pre></td>
				</tr>
								<tr>
					<td><code>PATH</code></td>
					<td><pre><small>string</small><span>(28)</span> "/usr/local/bin:/usr/bin:/bin"</pre></td>

				</tr>
								<tr>
					<td><code>SERVER_SIGNATURE</code></td>
					<td><pre><small>string</small><span>(110)</span> "&lt;address&gt;Apache/2.2.11 (Ubuntu) PHP/5.2.6-3ubuntu4.2 with Suhosin-Patch Server at localhost Port 80&lt;/address&gt;
"</pre></td>

				</tr>
								<tr>
					<td><code>SERVER_SOFTWARE</code></td>
					<td><pre><small>string</small><span>(62)</span> "Apache/2.2.11 (Ubuntu) PHP/5.2.6-3ubuntu4.2 with Suhosin-Patch"</pre></td>
				</tr>
								<tr>

					<td><code>SERVER_NAME</code></td>
					<td><pre><small>string</small><span>(9)</span> "localhost"</pre></td>
				</tr>
								<tr>
					<td><code>SERVER_ADDR</code></td>
					<td><pre><small>string</small><span>(3)</span> "::1"</pre></td>

				</tr>
								<tr>
					<td><code>SERVER_PORT</code></td>
					<td><pre><small>string</small><span>(2)</span> "80"</pre></td>
				</tr>
								<tr>

					<td><code>REMOTE_ADDR</code></td>
					<td><pre><small>string</small><span>(3)</span> "::1"</pre></td>
				</tr>
								<tr>
					<td><code>DOCUMENT_ROOT</code></td>
					<td><pre><small>string</small><span>(8)</span> "/var/www"</pre></td>

				</tr>
								<tr>
					<td><code>SERVER_ADMIN</code></td>
					<td><pre><small>string</small><span>(19)</span> "webmaster@localhost"</pre></td>
				</tr>
								<tr>

					<td><code>SCRIPT_FILENAME</code></td>
					<td><pre><small>string</small><span>(33)</span> "/var/www/kohana/testing/index.php"</pre></td>
				</tr>
								<tr>
					<td><code>REMOTE_PORT</code></td>
					<td><pre><small>string</small><span>(5)</span> "39409"</pre></td>

				</tr>
								<tr>
					<td><code>GATEWAY_INTERFACE</code></td>
					<td><pre><small>string</small><span>(7)</span> "CGI/1.1"</pre></td>
				</tr>
								<tr>

					<td><code>SERVER_PROTOCOL</code></td>
					<td><pre><small>string</small><span>(8)</span> "HTTP/1.1"</pre></td>
				</tr>
								<tr>
					<td><code>REQUEST_METHOD</code></td>
					<td><pre><small>string</small><span>(3)</span> "GET"</pre></td>

				</tr>
								<tr>
					<td><code>QUERY_STRING</code></td>
					<td><pre><small>string</small><span>(0)</span> ""</pre></td>
				</tr>
								<tr>

					<td><code>REQUEST_URI</code></td>
					<td><pre><small>string</small><span>(31)</span> "/kohana/testing/index.php/hello"</pre></td>
				</tr>
								<tr>
					<td><code>SCRIPT_NAME</code></td>
					<td><pre><small>string</small><span>(25)</span> "/kohana/testing/index.php"</pre></td>

				</tr>
								<tr>
					<td><code>PATH_INFO</code></td>
					<td><pre><small>string</small><span>(6)</span> "/hello"</pre></td>
				</tr>
								<tr>

					<td><code>PATH_TRANSLATED</code></td>
					<td><pre><small>string</small><span>(14)</span> "/var/www/hello"</pre></td>
				</tr>
								<tr>
					<td><code>PHP_SELF</code></td>
					<td><pre><small>string</small><span>(31)</span> "/kohana/testing/index.php/hello"</pre></td>

				</tr>
								<tr>
					<td><code>REQUEST_TIME</code></td>
					<td><pre><small>integer</small> 1254245682</pre></td>
				</tr>
								<tr>
					<td><code>argv</code></td>

					<td><pre><small>array</small><span>(0)</span> </pre></td>
				</tr>
								<tr>
					<td><code>argc</code></td>
					<td><pre><small>integer</small> 0</pre></td>

				</tr>
							</table>
		</div>
			</div>
</div>
