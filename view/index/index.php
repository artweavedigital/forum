<?php
	$addUrl = helper::baseUrl() . $this->getUrl(0) . '/add';
	$loginToAddUrl = helper::baseUrl() . 'user/login/' . str_replace('/', '_', $this->getUrl(0) . '/add');
	$ctaUrl = $this->isConnected() === true ? $addUrl : $loginToAddUrl;
	$ctaTitle = $this->isConnected() === true ? '' : 'Connexion requise';

	// Inscription : toujours via l’action /signup (évite les 404).
	$signupUrl = helper::baseUrl() . $this->getUrl(0) . '/signup';
	$filtersActive = (
		forum::$filterBoard !== ''
		|| forum::$filterQ !== ''
		|| forum::$filterFilter !== ''
		|| (forum::$filterSort !== '' && forum::$filterSort !== 'activity')
	);
?>

<div class="forumTop">
	<div class="forumTopRow">
		<div class="forumActions">
			<a class="forumBtn forumBtnPrimary" href="<?php echo $ctaUrl; ?>"<?php echo $ctaTitle ? ' title="' . $ctaTitle . '"' : ''; ?>>
				<?php echo template::ico('plus'); ?> Nouvelle discussion
			</a>
			<?php if ($this->isConnected() === false): ?>
				<a class="forumBtn forumBtnGhost" href="<?php echo $signupUrl; ?>">
					<?php echo template::ico('user'); ?> Créer un compte
				</a>
			<?php endif; ?>
		</div>
	</div>

	<details class="forumFilters"<?php echo $filtersActive ? ' open' : ''; ?>>
		<summary class="forumFiltersSummary"><?php echo template::ico('search'); ?> Filtrer et rechercher</summary>
		<div class="forumFilterBar">
			<form method="get" action="">
				<div class="forumFilterGrid">
					<div class="forumFilterCell">
						<label class="forumSrOnly" for="forumBoard">Section</label>
						<select id="forumBoard" name="board" class="forumFilterSelect" aria-label="Section">
							<option value="">Toutes les sections</option>
							<?php foreach (forum::$boards as $slug => $label): ?>
								<option value="<?php echo $slug; ?>"<?php echo forum::$filterBoard === $slug ? ' selected' : ''; ?>><?php echo $label; ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="forumFilterCell forumFilterGrow">
						<label class="forumSrOnly" for="forumQ">Recherche</label>
						<input id="forumQ" class="forumFilterInput" type="search" name="q" value="<?php echo htmlspecialchars(forum::$filterQ, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Rechercher…" aria-label="Recherche">
					</div>

					<div class="forumFilterCell">
						<label class="forumSrOnly" for="forumFilter">Filtre</label>
						<select id="forumFilter" name="filter" class="forumFilterSelect" aria-label="Filtre">
							<option value="">Filtre</option>
							<option value="noreply"<?php echo forum::$filterFilter === 'noreply' ? ' selected' : ''; ?>>Sans réponse</option>
							<option value="open"<?php echo forum::$filterFilter === 'open' ? ' selected' : ''; ?>>Ouverts</option>
							<option value="closed"<?php echo forum::$filterFilter === 'closed' ? ' selected' : ''; ?>>Fermés</option>
							<option value="unsolved"<?php echo forum::$filterFilter === 'unsolved' ? ' selected' : ''; ?>>Non résolus</option>
							<option value="solved"<?php echo forum::$filterFilter === 'solved' ? ' selected' : ''; ?>>Résolus</option>
							<?php if ($this->isConnected()): ?>
								<option value="mine"<?php echo forum::$filterFilter === 'mine' ? ' selected' : ''; ?>>Mes sujets</option>
							<?php endif; ?>
						</select>
					</div>

					<div class="forumFilterCell">
						<label class="forumSrOnly" for="forumSort">Trier</label>
						<select id="forumSort" name="sort" class="forumFilterSelect" aria-label="Trier">
							<option value="activity"<?php echo forum::$filterSort === 'activity' ? ' selected' : ''; ?>>Activité</option>
							<option value="recent"<?php echo forum::$filterSort === 'recent' ? ' selected' : ''; ?>>Récents</option>
							<option value="replies"<?php echo forum::$filterSort === 'replies' ? ' selected' : ''; ?>>Réponses</option>
						</select>
					</div>

					<div class="forumFilterCell forumFilterButtons">
						<button type="submit" class="forumBtn forumBtnPrimary forumBtnSmall"><?php echo template::ico('check'); ?> Appliquer</button>
						<a class="forumBtn forumBtnGhost forumBtnSmall" href="<?php echo helper::baseUrl() . $this->getUrl(0); ?>"><?php echo template::ico('cancel'); ?> Réinitialiser</a>
					</div>
				</div>
			</form>
		</div>
	</details>
</div>

<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'feeds'])): ?>
	<div id="rssFeed">
		<a type="application/rss+xml" href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/rss'; ?>" target="_blank">
			<img src="module/forum/ressource/feed-icon-16.gif" alt="RSS" />
			<?php
			echo $this->getData(['module', $this->getUrl(0), 'config', 'feedsLabel']) ? '<p>' . $this->getData(['module', $this->getUrl(0), 'config', 'feedsLabel']) . '</p>' : '';
			?>
		</a>
	</div>
<?php endif; ?>

<?php if (forum::$articles): ?>
	<div class="forumList" role="list">

		<?php foreach (forum::$articles as $topicId => $topic): ?>
			<?php
				$locked = (bool) $this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'commentClose']);
				$lastOn = isset(forum::$topicLastOn[$topicId]) ? forum::$topicLastOn[$topicId] : (int) $this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'publishedOn']);
				$lastBy = isset(forum::$topicLastBy[$topicId]) ? forum::$topicLastBy[$topicId] : $this->signature($this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'userId']));
			?>
			<div class="forumRow" role="listitem">
				<div class="forumCol forumColTitle">
					<div class="forumTopicTitle">
						<?php if ($locked): ?>
							<span class="forumBadge forumBadgeLock" title="Sujet fermé"><?php echo template::ico('lock'); ?> Fermé</span>
						<?php endif; ?>
						<?php if ((bool) $this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'pinned'])): ?>
							<span class="forumBadge forumBadgePin" title="Épinglé"><?php echo template::ico('pin'); ?> Épinglé</span>
						<?php endif; ?>
						<?php if ((bool) $this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'solved'])): ?>
							<span class="forumBadge forumBadgeSolved" title="Résolu"><?php echo template::ico('check'); ?> Résolu</span>
						<?php endif; ?>
						<span class="forumBadge forumBadgeBoard"><?php echo isset(forum::$boards[$this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'board'])]) ? forum::$boards[$this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'board'])] : forum::$boards[array_key_first(forum::$boards)]; ?></span>
						<a href="<?php echo helper::baseUrl() . $this->getUrl(0) . '/' . $topicId; ?>">
							<?php echo $topic['title']; ?>
						</a>
					</div>

					<div class="forumMeta">
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showPseudo']) === true): ?>
							<?php echo template::ico('user'); ?>
							<?php echo $this->signature($this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'userId'])); ?>
						<?php endif; ?>
						<?php if (
							$this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true
							|| $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true
						): ?>
							<?php echo template::ico('calendar-empty', ['margin' => 'left']); ?>
						<?php endif; ?>
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true): ?>
							<?php echo helper::dateUTF8(forum::$dateFormat, $this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'publishedOn']), self::$i18nUI); ?>
						<?php endif; ?>
						<?php if (
							$this->getData(['module', $this->getUrl(0), 'config', 'showDate']) === true
							&& $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true
						): ?>
							<?php echo '&nbsp;—&nbsp;'; ?>
						<?php endif; ?>
						<?php if ($this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true): ?>
							<?php echo helper::dateUTF8(forum::$timeFormat, $this->getData(['module', $this->getUrl(0), 'posts', $topicId, 'publishedOn']), self::$i18nUI); ?>
						<?php endif; ?>
					</div>
				</div>

				<div class="forumCol forumColStats" aria-label="Statistiques du sujet">
					<div class="forumStat forumStatReplies" title="Nombre de réponses">
						<div class="forumStatNum"><?php echo (int) forum::$comments[$topicId]; ?></div>
						<div class="forumStatLabel">Réponses</div>
					</div>
					<div class="forumStat forumStatLast" title="Dernière activité">
						<div class="forumStatLabel">Dernière activité</div>
						<div class="forumLastOn">
							<?php echo helper::dateUTF8(forum::$dateFormat, $lastOn, self::$i18nUI); ?>
							<?php echo $this->getData(['module', $this->getUrl(0), 'config', 'showTime']) === true ? ' ' . helper::dateUTF8(forum::$timeFormat, $lastOn, self::$i18nUI) : ''; ?>
						</div>
						<div class="forumLastBy"><?php echo $lastBy; ?></div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php echo forum::$pages; ?>
<?php else: ?>
	<?php echo template::speech('Aucun sujet'); ?>
<?php endif; ?>
