<?php
	$loginUrl = helper::baseUrl() . 'user/login';
	$forumUrl = helper::baseUrl() . $this->getUrl(0);
?>

<div class="forumAuthInline forumAuthInlineStandalone">
	<div class="forumAuthInlineText">
		<strong>Inscription indisponible.</strong>
		<span>La page d’inscription n’est pas encore configurée.</span>
	</div>
	<div class="forumAuthInlineActions">
		<a class="forumBtn forumBtnPrimary" href="<?php echo $loginUrl; ?>">
			<?php echo template::ico('login'); ?> Connexion
		</a>
		<a class="forumBtn forumBtnGhost" href="<?php echo $forumUrl; ?>">
			<?php echo template::ico('left'); ?> Retour
		</a>
	</div>
</div>
