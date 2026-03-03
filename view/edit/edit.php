<?php echo template::formOpen('blogEditForm'); ?>
<?php echo template::hidden('forumHp', ['value' => '']); ?>
<div class="row">
	<div class="col1">
		<?php echo template::button('blogEditBack', [
			'class' => 'buttonGrey',
			'href' => ($this->getUser('role') >= self::ROLE_EDITOR ? helper::baseUrl() . $this->getUrl(0) . '/config' : helper::baseUrl() . $this->getUrl(0)),
			'value' => template::ico('left')
		]); ?>
	</div>
	<div class="col3 offset6">
		<?php echo template::button('blogEditDraft', [
			'uniqueSubmission' => true,
			'value' => 'Brouillon'
		]); ?>
		<?php echo template::hidden('blogEditState', [
			'value' => true
		]); ?>
	</div>
	<div class="col2">
		<?php echo template::submit('blogEditSubmit', [
			'value' => 'Publier',
			'uniqueSubmission' => true
		]); ?>
	</div>
</div>
<div class="row">
	<div class="col12">
		<div class="block">
			<h4><?php echo helper::translate('Paramètres du sujet'); ?></h4>
			<div class="row">
				<div class="col6">
					<?php echo template::text('blogEditTitle', [
						'label' => 'Titre du sujet',
						'value' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'title'])
					]); ?>
				</div>
				<div class="col6">
					<?php echo template::text('blogEditPermalink', [
						'label' => 'Permalink',
						'value' => $this->getUrl(2)
					]); ?>
				</div>
			</div>
				<div class="row">
					<div class="col6">
						<?php echo template::select('forumEditBoard', forum::$boards, [
							'label' => 'Section',
							'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'board']) ? $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'board']) : array_key_first(forum::$boards)
						]); ?>
					</div>
					<?php if ($this->getUser('role') >= self::ROLE_EDITOR): ?>
						<div class="col6">
							<?php echo template::checkbox('forumEditPinned', true, 'Épingler ce sujet', [
								'checked' => (bool) $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'pinned'])
							]); ?>
						</div>
					<?php endif; ?>
				</div>
			<div class="row">
				<div class="col6">
					<?php echo template::file('blogEditPicture', [
						'language' => $this->getData(['user', $this->getUser('id'), 'language']),
						'help' => $this->getData(['theme', 'site', 'width']) !== '100%' ? 'Taille optimale de l\'image de couverture : ' . ((int) substr($this->getData(['theme', 'site', 'width']), 0, -2) - (20 * 2)) . ' x 350 pixels.' : '',
						'label' => 'Image (optionnelle)',
						'type' => 1,
						'value' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picture']),
						'folder' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picture']) ? dirname($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picture'])) : ''
					]); ?>
				</div>
				<div class="col3">
					<?php echo template::select('blogEditPictureSize', forum::$pictureSizes, [
						'label' => 'Largeur de l\'image',
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'pictureSize'])
					]); ?>
				</div>
				<div class="col3">
					<?php echo template::select('blogEditPicturePosition', forum::$picturePositions, [
						'label' => 'Position',
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'picturePosition']),
						'help' => 'Le texte de l\'article est adapté autour de l\'image'
					]); ?>
				</div>
			</div>
			<div class="row">
				<div class="col6">
					<?php echo template::checkbox('blogEditHidePicture', true, 'Masquer l\'image de couverture dans l\'article', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'hidePicture'])
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo template::textarea('blogEditContent', [
	'class' => 'editorWysiwyg',
	'value' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'content'])
]); ?>
<div class="row">
	<div class="col12">
		<div class="block">
			<h4><?php echo helper::translate('Options de publication'); ?></h4>
			<div class="row">
				<div class="col4">
					<?php echo template::select('blogEditUserId', forum::$users, [
						'label' => 'Auteur',
						'selected' => $this->getUser('id'),
						'disabled' => $this->getUser('role') !== self::ROLE_ADMIN ? true : false
					]); ?>
				</div>
				<div class="col4">
					<?php echo template::date('blogEditPublishedOn', [
						'help' => 'L\'article n\'est visible qu\'après la date de publication prévue.',
						'type' => 'datetime-local',
						'label' => 'Date de création',
						'value' => floor($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'publishedOn']) / 60) * 60
					]); ?>
				</div>
				<div class="col4">
					<?php if ($this->getUser('role') < self::ROLE_EDITOR): ?>
						<?php echo template::hidden('blogEditConsent', ['value' => forum::EDIT_OWNER]); ?>
					<?php else: ?>
						<?php echo template::select('blogEditConsent', forum::$articleConsent, [
						'label' => 'Édition — suppression',
						'selected' => is_numeric($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'editConsent'])) ? forum::EDIT_ROLE : $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'editConsent']),
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
			<h4><?php echo helper::translate('Commentaires'); ?></h4>
			<div class="row">
				<div class="col4 ">
					<?php echo template::checkbox('blogEditCommentClose', true, 'Fermer les commentaires', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentClose'])
					]); ?>
				</div>
				<div class="col4 commentOptionsWrapper ">
					<?php echo template::checkbox('blogEditCommentApproved', true, 'Approbation par un modérateur', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentApproved']),
						''
					]); ?>
				</div>
				<div class="col4 commentOptionsWrapper">
					<?php echo template::select('blogEditCommentMaxlength', forum::$commentsLength, [
						'help' => 'Choix du nombre maximum de caractères pour chaque commentaire de l\'article, mise en forme html comprise.',
						'label' => 'Caractères par commentaire',
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentMaxlength'])
					]); ?>
				</div>

			</div>
			<div class="row">
				<div class="col3 commentOptionsWrapper offset2">
					<?php echo template::checkbox('blogEditCommentNotification', true, 'Notification par email', [
						'checked' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentNotification']),
					]); ?>
				</div>
				<div class="col4 commentOptionsWrapper">
					<?php echo template::select('blogEditCommentGroupNotification', forum::$roleNews, [
						'selected' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentGroupNotification']),
					]); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo template::formClose(); ?>