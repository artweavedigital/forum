<?php echo template::formOpen('forumModeration'); ?>
<div class="row">
	<div class="col1">
		<?php echo template::button('forumModerationBack', [
			'class' => 'buttonGrey',
			'href' => helper::baseUrl() . $this->getUrl(0) . '/config',
			'value' => template::ico('left')
		]); ?>
	</div>
</div>
<?php echo template::formClose(); ?>

<?php if (forum::$moderation): ?>
	<div class="block">
		<h4><?php echo helper::translate('Réponses en attente'); ?></h4>
		<div class="forumModerationList">
			<?php foreach (forum::$moderation as $row): ?>
				<?php
					$link = helper::baseUrl() . $this->getUrl(0) . '/' . $row['postId'];
					$approve = helper::baseUrl() . $this->getUrl(0) . '/commentApprove/' . $row['postId'] . '/' . $row['commentId'];
					$del = helper::baseUrl() . $this->getUrl(0) . '/commentDelete/' . $row['postId'] . '/' . $row['commentId'];
					$when = (int) $row['createdOn'];
					$whenLabel = helper::dateUTF8(forum::$dateFormat, $when, self::$i18nUI) . ($this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true ? ' — ' . helper::dateUTF8(forum::$timeFormat, $when, self::$i18nUI) : '');
					$excerpt = trim(preg_replace('/\s+/', ' ', strip_tags((string) $row['content'])));
					if (mb_strlen($excerpt) > 180) { $excerpt = mb_substr($excerpt, 0, 180) . '…'; }
				?>
				<div class="forumModerationItem">
					<div class="forumModerationMain">
						<div class="forumModerationTitle">
							<a href="<?php echo $link; ?>" target="_blank"><?php echo htmlspecialchars($row['postTitle'], ENT_QUOTES, 'UTF-8'); ?></a>
						</div>
						<div class="forumModerationMeta">
							<span><?php echo template::ico('user'); ?> <?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></span>
							<span><?php echo template::ico('calendar-empty'); ?> <?php echo $whenLabel; ?></span>
						</div>
						<div class="forumModerationExcerpt"><?php echo htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8'); ?></div>
					</div>
					<div class="forumModerationActions">
						<a class="forumBtnIcon" href="<?php echo $approve; ?>"><?php echo template::ico('check'); ?> Approuver</a>
						<a class="forumBtnIcon" href="<?php echo $del; ?>"><?php echo template::ico('trash'); ?> Supprimer</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php else: ?>
	<?php echo template::speech('Aucune réponse en attente.'); ?>
<?php endif; ?>

<div class="moduleVersion">Version n°
	<?php echo forum::VERSION; ?>
</div>
