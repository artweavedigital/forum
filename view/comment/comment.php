<div class="row">
	<div class="col2">
		<?php echo template::button('blogCommentBack', [
			'class' => 'buttonGrey',
			'href' => helper::baseUrl() . $this->getUrl(0) . '/config',
			'ico' => 'left',
			'value' => 'Retour'
		]); ?>
	</div>

<?php if(forum::$comments): ?>
	<div class="col2 offset8">
			<?php echo forum::$commentsDelete; ?>
	</div>

</div>
	<?php echo template::table([3, 5, 2, 1, 1], forum::$comments, ['Date', 'Contenu', 'Auteur', '', '']); ?>
	<?php echo forum::$pages.'<br/>'; ?>
<?php else: ?>
</div>
	<?php echo template::speech('Aucun commentaire'); ?>
<?php endif; ?>
