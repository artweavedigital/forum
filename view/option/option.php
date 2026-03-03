<?php echo template::formOpen('blogOption'); ?>
<div class="row">
	<div class="col1">
		<?php echo template::button('blogOptionBack', [
			'class' => 'buttonGrey',
			'href' => helper::baseUrl() . $this->getUrl(0) . '/config',
			'value' => template::ico('left')
		]); ?>
	</div>
	<div class="col2 offset9">
		<?php echo template::submit('blogOptionSubmit'); ?>
	</div>
</div>
<div class="row">
	<div class="col12">
		<div class="block">
			<h4>
				<?php echo helper::translate('Paramètres'); ?>
			</h4>
			<div class="row">
				<div class="col6">
					<?php echo template::checkbox('blogOptionButtonBack', true, 'Bouton de retour', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'config', 'buttonBack'])
					]); ?>
				</div>
				<div class="col6">
					<?php echo template::checkbox('blogOptionShowPseudo', true, 'Signature', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'config', 'showPseudo'])
					]); ?>
				</div>
			</div>

			<div class="row">
				<div class="col12">
					<?php echo template::text('blogOptionSignupUrl', [
						'label' => 'URL d’inscription',
						'placeholder' => 'inscription',
						'value' => $this->getData(['module', $this->getUrl(0), 'config', 'signupUrl'])
					]); ?>
					<p class="small">Indique le slug de ta page “Inscription” (module Auto Inscription) ou une URL complète.</p>
				</div>
			</div>
			
			<div class="row">
				<div class="col12">
					<?php echo template::textarea('forumOptionBoards', [
						'label' => 'Sections (sous-forums)',
						'placeholder' => "Général|general\nSupport|support\nÉcriture|ecriture",
						'value' => $this->getData(['module', $this->getUrl(0), 'config', 'boardsRaw'])
					]); ?>
					<p class="small">1 ligne = 1 section. Format : <em>Libellé|slug</em> (slug optionnel).</p>
				</div>
			</div>

			<div class="row">
				<div class="col4">
					<?php echo template::number('forumOptionFloodTopic', [
						'label' => 'Anti-flood — nouveaux sujets (secondes)',
						'value' => (int) $this->getData(['module', $this->getUrl(0), 'config', 'floodTopicSeconds']),
						'help' => '0 = désactivé'
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::number('forumOptionFloodReply', [
						'label' => 'Anti-flood — réponses (secondes)',
						'value' => (int) $this->getData(['module', $this->getUrl(0), 'config', 'floodReplySeconds']),
						'help' => '0 = désactivé'
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::checkbox('forumOptionHoneypot', true, 'Anti-spam honeypot', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'config', 'honeypot']) === null ? true : (bool) $this->getData(['module', $this->getUrl(0), 'config', 'honeypot'])
					]); ?>
				</div>
			</div>

<div class="row">
				<div class="col3">
					<?php echo template::checkbox('blogOptionShowDate', true, 'Afficher la date', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'config', 'showDate']),
					]); ?>
				</div>
				<div class="col3">
					<?php echo template::select('blogOptionDateFormat', forum::$dateFormats, [
						'label' => 'Format des dates',
						'selected' => $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat'])
					]); ?>
				</div>
				<div class="col3 timeWrapper">
					<?php echo template::checkbox('blogOptionShowTime', true, 'Afficher l\'heure', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'config', 'showTime']),
					]); ?>
				</div>
				<div class="col3 timeWrapper">
					<?php echo template::select('blogOptionTimeFormat', forum::$timeFormats, [
						'label' => 'Format des heures',
						'selected' => $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat'])
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col4">
					<?php echo template::select('blogOptionArticlesLayout', forum::$articlesLayout, [
						'label' => 'Disposition',
						'selected' => $this->getData(['module', $this->getUrl(0), 'config', 'layout'])
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::select('blogOptionArticlesLenght', forum::$articlesLenght, [
						'label' => 'Aperçus',
						'selected' => $this->getData(['module', $this->getUrl(0), 'config', 'articlesLenght'])
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::select('blogOptionItemsperPage', forum::$ArticlesListed, [
						'label' => 'Articles par page',
						'selected' => $this->getData(['module', $this->getUrl(0), 'config', 'itemsperPage'])
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col6">
					<?php echo template::checkbox('blogOptionShowFeeds', true, 'Lien du flux RSS', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'config', 'feeds']),
					]); ?>
				</div>
				<div class="col6">
					<?php echo template::text('blogOptionFeedslabel', [
						'label' => 'Texte de l\'étiquette RSS',
						'value' => $this->getData(['module', $this->getUrl(0), 'config', 'feedsLabel'])
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo template::formClose(); ?>
<div class="moduleVersion">Version n°
	<?php echo forum::VERSION; ?>
</div>