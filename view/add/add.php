<?php echo template::formOpen('blogAddForm'); ?>
<?php echo template::hidden('forumHp', ['value' => '']); ?>
	<div class="row">
		<div class="col1">
			<?php echo template::button('blogAddBack', [
				'class' => 'buttonGrey',
				'href' => ($this->getUser('role') >= self::ROLE_EDITOR ? helper::baseUrl() . $this->getUrl(0) . '/config' : helper::baseUrl() . $this->getUrl(0)),
				'value' => template::ico('left')
			]); ?>
		</div>
		<div class="col2 offset7">
			<?php echo template::button('blogAddDraft', [
				'uniqueSubmission' => true,
				'value' => 'Brouillon'
			]); ?>
			<?php echo template::hidden('blogAddState', [
				'value' => true
			]); ?>
		</div>
		<div class="col2">
			<?php echo template::submit('blogAddPublish', [
				'value' => 'Publier',
				'uniqueSubmission' => true
			]); ?>
		</div>
	</div>
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4><?php echo helper::translate('Informations générales');?></h4>
				<div class="row">
					<div class="col6">
						<?php echo template::text('blogAddTitle', [
							'label' => 'Titre du sujet'
						]); ?>
					</div>
					<div class="col6">
						<?php echo template::text('blogAddPermalink', [
							'label' => 'Permalink'
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col6">
						<?php echo template::select('forumAddBoard', forum::$boards, [
							'label' => 'Section',
							'selected' => array_key_first(forum::$boards)
						]); ?>
					</div>
					<?php if ($this->getUser('role') >= self::ROLE_EDITOR): ?>
						<div class="col6">
							<?php echo template::checkbox('forumAddPinned', true, 'Épingler ce sujet', [
								'checked' => false
							]); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="row">
					<div class="col4">
						<?php echo template::file('blogAddPicture', [
							'language' => $this->getData(['user', $this->getUser('id'), 'language']),
							'help' => 'Taille optimale de l\'image de couverture : ' . ((int) substr($this->getData(['theme', 'site', 'width']), 0, -2) - (20 * 2)) . ' x 350 pixels.',
							'label' => 'Image (optionnelle)',
							'type' => 1
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::select('blogAddPictureSize', forum::$pictureSizes, [
							'label' => 'Largeur de l\'image'
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::select('blogAddPicturePosition', forum::$picturePositions, [
							'label' => 'Position',
							'help' => 'Le texte de l\'article est adapté autour de l\'image'
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col6">
						<?php echo template::checkbox('blogAddHidePicture', true, 'Masquer l\'image de couverture dans l\'article', [
							'checked' => true
						]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo template::textarea('blogAddContent', [
		'class' => 'editorWysiwyg'
	]); ?>
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4><?php echo helper::translate('Options de publication'); ?></h4>
				<div class="row">
					<div class="col4">
						<?php echo template::select('blogAddUserId', forum::$users, [
							'label' => 'Auteur',
							'selected' => $this->getUser('id'),
							'disabled' => $this->getUser('role') !== self::ROLE_ADMIN ? true : false
						]); ?>
					</div>
					<div class="col4">
						<?php echo template::date('blogAddPublishedOn', [
							'help' => 'L\'article n\'est visible qu\'après la date de publication prévue.',
							'label' => 'Date de publication',
							'type' => 'datetime-local',
							'format' => $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']) . $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']),
							'value' => floor(strtotime('now') / 60) * 60
						]); ?>
					</div>
					<div class="col4">
						<?php if ($this->getUser('role') < self::ROLE_EDITOR): ?>
						<?php echo template::hidden('blogAddConsent', ['value' => forum::EDIT_OWNER]); ?>
					<?php else: ?>
						<?php echo template::select('blogAddConsent', forum::$articleConsent  , [
							'label' => 'Édition - Suppression',
							'selected' => forum::EDIT_ALL,
							'help' => 'Les utilisateurs des rôles supérieurs accèdent à l\'article sans restriction'
						]); ?>
					<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4><?php echo helper::translate('Commentaires');?></h4>
				<div class="row">
					<div class="col4 ">
						<?php echo template::checkbox('blogAddCommentClose', true, 'Fermer les commentaires'); ?>
					</div>
					<div class="col4 commentOptionsWrapper ">
						<?php echo template::checkbox('blogAddCommentApproved', true, 'Approbation par un modérateur'); ?>
					</div>
					<div class="col4 commentOptionsWrapper">
						<?php echo template::select('blogAddCommentMaxlength', forum::$commentsLength,[
							'help' => 'Choix du nombre maximum de caractères pour chaque commentaire de l\'article, mise en forme html comprise.',
							'label' => 'Caractères par commentaire'
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col3 commentOptionsWrapper offset2">
						<?php echo template::checkbox('blogAddCommentNotification', true, 'Notification par email'); ?>
					</div>
					<div class="col4 commentOptionsWrapper">
						<?php echo template::select('blogAddCommentGroupNotification', forum::$roleNews); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php echo template::formClose(); ?>
