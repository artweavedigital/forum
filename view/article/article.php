<?php
	$signupUrl = helper::baseUrl() . $this->getUrl(0) . '/signup';
	$loginToReplyUrl = helper::baseUrl() . 'user/login/' . str_replace('/', '_', $this->getUrl()) . '__comment';
	$lockedTopic = (bool) $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentClose']);
	$pinnedTopic = (bool) $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'pinned']);
	$solvedTopic = (bool) $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'solved']);
	$boardSlug = (string) $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'board']);
?>

<div class="forumTopic">
	<h2 class="forumTopicH"><?php echo $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'title']); ?></h2>

	<div class="forumTopicMeta">
		<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showPseudo']) === true): ?>
			<span class="forumMetaItem">
				<?php echo template::ico('user'); ?>
				<?php echo $this->signature($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'userId'])); ?>
			</span>
		<?php endif; ?>

		<?php if (
			$this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true
			|| $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true
		): ?>
			<span class="forumMetaItem">
				<?php echo template::ico('calendar-empty'); ?>
				<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true): ?>
					<?php echo helper::dateUTF8(forum::$dateFormat, $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'publishedOn']), self::$i18nUI); ?>
				<?php endif; ?>
				<?php if (
					$this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true
					&& $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true
				): ?>
					<?php echo '&nbsp;—&nbsp;'; ?>
				<?php endif; ?>
				<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
					<?php echo helper::dateUTF8(forum::$timeFormat, $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'publishedOn']), self::$i18nUI); ?>
				<?php endif; ?>
			</span>
		<?php endif; ?>

		<span class="forumMetaItem forumMetaBoard">
			<?php echo template::ico('tag'); ?>
			<?php echo isset(forum::$boards[$boardSlug]) ? forum::$boards[$boardSlug] : $boardSlug; ?>
		</span>

		<?php if ($pinnedTopic): ?>
			<span class="forumMetaItem"><span class="forumBadge forumBadgePin"><?php echo template::ico('pin'); ?> Épinglé</span></span>
		<?php endif; ?>
		<?php if ($solvedTopic): ?>
			<span class="forumMetaItem"><span class="forumBadge forumBadgeSolved"><?php echo template::ico('check'); ?> Résolu</span></span>
		<?php endif; ?>
		<?php if ($lockedTopic): ?>
			<span class="forumMetaItem"><span class="forumBadge forumBadgeLock"><?php echo template::ico('lock'); ?> Fermé</span></span>
		<?php endif; ?>

		<span class="forumMetaItem">
			<?php echo template::ico('comment'); ?>
			<?php echo (int) forum::$nbCommentsApproved; ?> réponse<?php echo forum::$nbCommentsApproved > 1 ? 's' : ''; ?>
		</span>

		<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'feeds'])): ?>
			<span class="forumMetaItem">
				<a type="application/rss+xml" href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/rss'; ?>" target="_blank">
					<img src="module/forum/ressource/feed-icon-16.gif" alt="RSS" />
					<?php echo $this->getData(['module', $this->getUrl(0), 'config', 'feedsLabel']); ?>
				</a>
			</span>
		<?php endif; ?>
	</div>

	<div class="forumTopicBody">
		<?php
		$picture = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'picture']);
		if (
			$picture &&
			file_exists($picture) &&
			$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'hidePicture']) == false
		) {
			$pictureSize = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'pictureSize']) === null ? '100' : $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'pictureSize']);
			echo '<img class="blogArticlePicture blogArticlePicture' . $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'picturePosition']) .
				' pict' . $pictureSize . '" src="' . $picture .
				'" alt="' . basename($picture) . '">';
		}
		?>
		<?php echo $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'content']); ?>
	</div>

	<div class="forumTopicNav">
		<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'buttonBack'])): ?>
			<a href="<?php echo helper::baseUrl() . $this->getUrl(0); ?>">
				<?php echo template::ico('left') . helper::translate('Retour'); ?>
			</a>
		<?php endif; ?>

		<?php
		$canEdit = false;
		if ($this->isConnected() === true) {
			// Propriétaire
			$canEdit = (
				($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'editConsent']) === forum::EDIT_OWNER
					&& ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'userId']) === $this->getUser('id')
						|| $this->getUser('role') === self::ROLE_ADMIN)
				)
				|| ($this->getUser('role') >= self::ROLE_EDITOR)
			);
		}
		?>
		<?php if ($canEdit): ?>
			<span class="forumTopicEdit">
				&nbsp;—&nbsp;
				<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/edit/' . $this->getUrl(1); ?>">
					<?php echo template::ico('pencil'); ?> Éditer
				</a>
			</span>
		<?php endif; ?>

		<?php
			$canMod = ($this->isConnected() === true && $this->getUser('role') >= self::ROLE_EDITOR);
			$canSolve = ($this->isConnected() === true && ($this->getUser('role') >= self::ROLE_EDITOR || $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'userId']) === $this->getUser('id')));
		?>
		<div class="forumTopicTools">
			<?php if ($canSolve): ?>
				<a class="forumBtnIcon forumBtnSmall" href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/toggleSolved/' . $this->getUrl(1); ?>" title="Marquer résolu / non résolu">
					<?php echo template::ico('check'); ?> Résolu
				</a>
			<?php endif; ?>
			<?php if ($canMod): ?>
				<a class="forumBtnIcon forumBtnSmall" href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/togglePinned/' . $this->getUrl(1); ?>" title="Épingler / désépingler">
					<?php echo template::ico('pin'); ?> Épingler
				</a>
				<a class="forumBtnIcon forumBtnSmall" href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/toggleClose/' . $this->getUrl(1); ?>" title="Fermer / rouvrir">
					<?php echo template::ico('lock'); ?> Fermer
				</a>
			<?php endif; ?>
		</div>

	</div>
</div>

<?php if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentClose'])): ?>
	<p class="forumClosed"><?php echo template::ico('lock'); ?> Ce sujet est fermé.</p>
<?php else: ?>
	<div class="forumReply" id="comment">
		<h3><?php echo template::ico('comment', ['margin' => 'right']); ?> Répondre</h3>

		<?php if ($this->isConnected() === false): ?>
			<div class="forumAuthInline">
				<div class="forumAuthInlineText">
					<strong>Connexion requise.</strong> <span>Pour répondre ou ouvrir un sujet, connecte-toi ou crée un compte.</span>
				</div>
				<div class="forumAuthInlineActions">
					<a class="forumBtn forumBtnPrimary" href="<?php echo $loginToReplyUrl; ?>"><?php echo template::ico('login'); ?> Connexion</a>
					<a class="forumBtn forumBtnGhost" href="<?php echo $signupUrl; ?>"><?php echo template::ico('user'); ?> Créer un compte</a>
				</div>
			</div>
		<?php else: ?>

		<?php echo template::formOpen('blogArticleForm'); ?>
		<?php echo template::text('blogArticleCommentShow', [
			'placeholder' => 'Écrire une réponse…',
			'readonly' => true
		]); ?>

		<div id="blogArticleCommentWrapper" class="displayNone">
			<?php echo template::text('blogArticleUserName', [
				'label' => 'Nom',
				'readonly' => true,
				'value' => forum::$editCommentSignature
			]); ?>
			<?php echo template::hidden('blogArticleUserId', [
				'value' => $this->getUser('id')
			]); ?>

			<?php echo template::hidden('forumHp', ['value' => '']); ?>
				<?php echo template::textarea('blogArticleContent', [
				'label' => 'Réponse',
				'class' => 'editorWysiwyg',
				'noDirty' => true,
				'value' => forum::$commentContent
			]); ?>

			<div class="row">
				<div class="col12">
					<?php echo template::submit('blogArticleSubmit', [
						'value' => 'Publier'
					]); ?>
				</div>
			</div>
		</div>

		<?php echo template::formClose(); ?>

	<?php endif; ?>
	</div>
<?php endif; ?>

<?php if (forum::$comments): ?>
	<div class="forumReplies">
		<?php foreach (forum::$comments as $commentId => $comment): ?>
			<?php
				$author = forum::$commentsSignature[$commentId];
				$when = (int) $comment['createdOn'];
				$whenLabel = helper::dateUTF8(forum::$dateFormat, $when, self::$i18nUI) . ($this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true ? ' ' . helper::dateUTF8(forum::$timeFormat, $when, self::$i18nUI) : '');
				$quoteText = htmlspecialchars(trim(preg_replace('/\s+/', ' ', strip_tags((string) $comment['content']))), ENT_QUOTES, 'UTF-8');
			?>
			<div class="forumReplyItem" id="reply-<?php echo $commentId; ?>">
				<div class="forumReplyMeta">
					<span class="forumReplyAuthor"><?php echo template::ico('user'); ?> <?php echo $author; ?></span>
					<span class="forumReplyDate"><?php echo template::ico('calendar-empty'); ?> <?php echo $whenLabel; ?></span>
				</div>
				<div class="forumReplyActions">
					<?php if ($this->isConnected() === true): ?>
						<a class="forumBtnIcon forumBtnSmall forumQuoteBtn" href="#comment"
							data-author="<?php echo htmlspecialchars($author, ENT_QUOTES, 'UTF-8'); ?>"
							data-when="<?php echo htmlspecialchars($whenLabel, ENT_QUOTES, 'UTF-8'); ?>"
							data-quote="<?php echo $quoteText; ?>"
						>
							<?php echo template::ico('comment'); ?> Citer
						</a>
					<?php endif; ?>
				</div>
				<div class="forumReplyBody"><?php echo $comment['content']; ?></div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php echo forum::$pages; ?>
<?php endif; ?>
