<div class="text-center">
<ul class="pagination pagination-sm" style="margin-top: 0; margin-bottom: 0;">
	<?php if ($first_page !== FALSE): ?>
		<li>
			<a href="<?php echo HTML::chars($page->url($first_page)) ?>" rel="first">
				<?php echo __('&laquo;') ?>
			</a>
		</li>
	<?php else: ?>
		<li class="disabled">
			<a>
				<?php echo __('&laquo;') ?>
			</a>
		</li>
	<?php endif ?>

	<?php if ($previous_page !== FALSE): ?>
		<li>
			<a href="<?php echo HTML::chars($page->url($previous_page)) ?>" rel="prev">
				<?php echo __('<') ?>
			</a>
		</li>
	<?php else: ?>
		<li class="disabled">
			<a>
				<?php echo __('<') ?>
			</a>
		</li>
	<?php endif ?>

	<?php $my_limit = 3 ?>
	<?php $my_limit_end = 1 ?>

	<?php if($total_pages > $my_limit){ ?>
		<?php if($current_page - $my_limit_end > 1){?>
			<li>
				<a href="<?php echo HTML::chars($page->url(1)) ?>">
					<?php echo 1 ?>
				</a>
			</li>
			<li class="disabled">
				<a>...</a>
			</li>
		<?php } ?>
		<?php if($current_page - $my_limit_end > 0 && $current_page + $my_limit_end >= $total_pages){?>
			<li>
				<a href="<?php echo HTML::chars($page->url($current_page-2)) ?>">
					<?php echo $current_page-2 ?>
				</a>
			</li>
		<?php } ?>
		<?php if($current_page - $my_limit_end > 0){?>
			<li>
				<a href="<?php echo HTML::chars($page->url($current_page-1)) ?>">
					<?php echo $current_page-1 ?>
				</a>
			</li>
		<?php } ?>
		<li class="active">
			<a>
				<?php echo $current_page ?>
			</a>
		</li>
		<?php if($current_page + $my_limit_end <= $total_pages){?>
			<li>
				<a href="<?php echo HTML::chars($page->url($current_page+1)) ?>">
					<?php echo $current_page+1 ?>
				</a>
			</li>
		<?php } ?>
		<?php if($current_page + $my_limit_end < $total_pages && $current_page - $my_limit_end <= 0){?>
			<li>
				<a href="<?php echo HTML::chars($page->url($current_page+2)) ?>">
					<?php echo $current_page+2 ?>
				</a>
			</li>
		<?php } ?>
		<?php if($current_page + $my_limit_end < $total_pages - 1){?>
			<li class="disabled">
				<a>...</a>
			</li>
			<li>
				<a href="<?php echo HTML::chars($page->url($total_pages)) ?>">
					<?php echo $total_pages ?>
				</a>
			</li>
		<?php } ?>


	<?php }else{ ?>
		<?php for ($i = 1; $i <= $total_pages; $i++): ?>
			<?php if ($i == $current_page): ?>
				<li class="disabled">
					<a>
						<?php echo $i ?>
					</a>
				</li>
			<?php else: ?>
				<li>
					<a href="<?php echo HTML::chars($page->url($i)) ?>">
						<?php echo $i ?>
					</a>
				</li>
			<?php endif ?>
		<?php endfor ?>
	<?php } ?>

	<?php if ($next_page !== FALSE): ?>
		<li>
			<a href="<?php echo HTML::chars($page->url($next_page)) ?>" rel="next">
				<?php echo __('>') ?>
			</a>
		</li>
	<?php else: ?>
		<li class="disabled">
			<a>
				<?php echo __('>') ?>
			</a>
		</li>
	<?php endif ?>

	<?php if ($last_page !== FALSE): ?>
		<li>
			<a href="<?php echo HTML::chars($page->url($last_page)) ?>" rel="last">
				<?php echo __('&raquo;') ?>
			</a>
		</li>
	<?php else: ?>
		<li class="disabled">
			<a>
				<?php echo __('&raquo;') ?>
			</a>
		</li>
	<?php endif ?>

</ul>
</div>