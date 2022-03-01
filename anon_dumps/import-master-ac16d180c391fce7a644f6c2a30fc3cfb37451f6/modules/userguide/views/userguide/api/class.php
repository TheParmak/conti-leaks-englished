<h1>
	<?php echo $doc->modifiers, $doc->class->name ?>
	<?php foreach ($doc->parents as $parent): ?>
	<br/><small>extends <?php echo HTML::anchor($route->uri(array('class' => $parent->name)), $parent->name, NULL, NULL, TRUE) ?></small>
	<?php endforeach; ?>
</h1>

<?php if ($interfaces = $doc->class->getInterfaceNames()): ?>
<p class="interfaces"><small>
Implements:
<?php
for ($i = 0, $split = FALSE, $count = count($interfaces); $i < $count; $i++, $split = " | ")
{
    echo $split . HTML::anchor($route->uri(array('class' => $interfaces[$i])), $interfaces[$i], NULL, NULL, TRUE);
}
?></small>
</p>
<?php endif; ?>

<?php if ($child = $doc->is_transparent($doc->class->name)):?>
<p class="note">
This class is a transparent base class for <?php echo HTML::anchor($route->uri(array('class'=>$child)),$child) ?> and
should not be accessed directly.
</p>
<?php endif;?>

<?php echo $doc->description() ?>

<?php if ($doc->tags): ?>
<dl class="tags">
<?php foreach ($doc->tags() as $name => $set): ?>
<dt><?php echo $name ?></dt>
<?php foreach ($set as $tag): ?>
<dd><?php echo $tag ?></dd>
<?php endforeach ?>
<?php endforeach ?>
</dl>
<?php endif; ?>

<p class="note">
<?php if ($path = $doc->class->getFilename()): ?>
Class declared in <tt><?php echo Debug::path($path) ?></tt> on line <?php echo $doc->class->getStartLine() ?>.
<?php else: ?>
Class is not declared in a file, it is probably an internal <?php echo html::anchor('http://php.net/manual/class.'.strtolower($doc->class->name).'.php', 'PHP class') ?>.
<?php endif ?>
</p>

<div class="toc">
	<div class="constants">
		<h3><?php echo 'Constants'; ?></h3>
		<ul>
		<?php if ($doc->constants): ?>
		<?php foreach ($doc->constants as $name => $value): ?>
			<li><a href="#constant:<?php echo $name ?>"><?php echo $name ?></a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
	<div class="properties">
		<h3><?php echo 'Properties'; ?></h3>
		<ul>
		<?php if ($properties = $doc->properties()): ?>
		<?php foreach ($properties as $prop): ?>
			<li><a href="#property:<?php echo $prop->property->name ?>">$<?php echo $prop->property->name ?></a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
	<div class="methods">
		<h3><?php echo 'Methods'; ?></h3>
		<ul>
		<?php if ($methods = $doc->methods()): ?>
		<?php foreach ($methods as $method): ?>
			<li><a href="#<?php echo $method->method->name ?>"><?php echo $method->method->name ?>()</a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
</div>

<div class="clearfix"></div>

<?php if ($doc->constants): ?>
<div class="constants">
<h1 id="constants"><?php echo 'Constants'; ?></h1>
<dl>
<?php foreach ($doc->constants() as $name => $value): ?>
<dt><h4 id="constant:<?php echo $name ?>"><?php echo $name ?></h4></dt>
<dd><?php echo $value ?></dd>
<?php endforeach; ?>
</dl>
</div>
<?php endif ?>

<?php if ($properties = $doc->properties()): ?>
<h1 id="properties"><?php echo 'Properties'; ?></h1>
<div class="properties">
<dl>
<?php foreach ($properties as $prop): ?>
<dt><h4 id="property:<?php echo $prop->property->name ?>"><?php echo $prop->modifiers ?> <code><?php echo $prop->type ?></code> $<?php echo $prop->property->name ?></h4></dt>
<dd><?php echo $prop->description ?></dd>
<dd><?php echo $prop->value ?></dd>
<?php if ($prop->default !== $prop->value): ?>
<dd><small><?php echo __('Default value:') ?></small><br/><?php echo $prop->default ?></dd>
<?php endif ?>
<?php endforeach ?>
</dl>
</div>
<?php endif ?>

<?php if ($methods = $doc->methods()): ?>
<h1 id="methods"><?php echo 'Methods'; ?></h1>
<div class="methods">
<?php foreach ($methods as $method): ?>
<?php echo View::factory('userguide/api/method')->set('doc', $method)->set('route', $route) ?>
<?php endforeach ?>
</div>
<?php endif ?>
