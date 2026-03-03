<?php echo template::formOpen('blogConfig'); ?>
	<div class="row">
		<div class="col1">
			<?php echo template::button('blogConfigBack', [
				'class' => 'buttonGrey',
				'href' => helper::baseUrl() . 'page/edit/' . $this->getUrl(0) . '/' . self::$siteContent,
				'value' => template::ico('left')
			]); ?>
		</div>
		<div class="col1 offset9">
			<?php echo template::button('blogConfigOption', [
				'href' => helper::baseUrl() . $this->getUrl(0) . '/option',
				'value' => template::ico('sliders'),
				'help' => 'Options de configuration'
			]); ?>

			<?php echo template::button('blogConfigModeration', [
				'href' => helper::baseUrl() . $this->getUrl(0) . '/moderation',
				'value' => template::ico('comment'),
				'help' => 'File de modération'
			]); ?>

		</div>
		<div class="col1">
			<?php echo template::button('blogConfigAdd', [
				'href' => helper::baseUrl() . $this->getUrl(0) . '/add',
				'value' => template::ico('plus'),
				'class' => 'buttonGreen',
				'help' => 'Nouveau sujet'
			]); ?>
		</div>
	</div>
<?php echo template::formClose(); ?>
<?php if(forum::$articles): ?>
	<?php echo template::table([3, 2, 1, 2, 1, 1, 1, 1], forum::$articles, ['Titre', 'Section', 'Statut', 'Publication', 'État', 'Réponses', '', '']); ?>
	<?php echo forum::$pages; ?>
<?php else: ?>
	<?php echo template::speech('Aucun article'); ?>
<?php endif; ?>
<div class="moduleVersion">Version n°
	<?php echo forum::VERSION; ?>
</div>

