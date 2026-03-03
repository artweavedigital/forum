<?php

/**
 * This file is part of Zwii.
 * For full copyright and license information, please see the LICENSE
 * file that was distributed with this source code.
 *
 * @author Rémi Jean <remi.jean@outlook.com>
 * @copyright Copyright (C) 2008-2018, Rémi Jean
 * @author Frédéric Tempez <frederic.tempez@outlook.com>
 * @copyright Copyright (C) 2018-2025, Frédéric Tempez
 * @license CC Attribution-NonCommercial-NoDerivatives 4.0 International 
 * @Copyright (C) 2026, Frédéric Tempez
 * @Licensed under the GNU General Public License v3.0 or later.
 * @link http://zwiicms.fr/
 */

class forum extends common
{

	const VERSION = '2.0.0';
	const REALNAME = 'Forum';
	const DELETE = true;
	const UPDATE = '0.0';
	const DATADIRECTORY = ''; // Contenu localisé inclus par défaut (page.json et module.json)

	const EDIT_OWNER = 'owner';
	const EDIT_ROLE = 'role';
	const EDIT_ALL = 'all';

	public static $actions = [
		'add' => self::ROLE_MEMBER,
		'comment' => self::ROLE_EDITOR,
		'commentApprove' => self::ROLE_EDITOR,
		'commentDelete' => self::ROLE_EDITOR,
		'commentDeleteAll' => self::ROLE_EDITOR,
		'config' => self::ROLE_EDITOR,
		'option' => self::ROLE_EDITOR,
		'delete' => self::ROLE_MEMBER,
		'edit' => self::ROLE_MEMBER,
		'index' => self::ROLE_VISITOR,
		'signup' => self::ROLE_VISITOR,
		'moderation' => self::ROLE_EDITOR,
		'togglePinned' => self::ROLE_EDITOR,
		'toggleClose' => self::ROLE_EDITOR,
		'toggleSolved' => self::ROLE_MEMBER,
		'rss' => self::ROLE_VISITOR
	];

	public static $articles = [];

	// Signature du commentaire
	public static $editCommentSignature = '';

	// Contenu du commentaire (pré-remplissage / retour après validation)
	public static $commentContent = '';

	public static $comments = [];

	public static $nbCommentsApproved = 0;

	public static $commentsDelete;

	// Signatures des commentaires déjà saisis
	public static $commentsSignature = [];

	// Modération globale
	public static $moderation = [];

	// Activité des sujets (forum)
	public static $topicLastOn = [];
	public static $topicLastBy = [];

	
	// Sections (sous-forums)
	public static $boards = [];

	// Filtres (liste des sujets)
	public static $filterBoard = '';
	public static $filterQ = '';
	public static $filterFilter = '';
	public static $filterSort = 'activity';

public static $pages;

	public static $states = [
		false => 'Brouillon',
		true => 'Publié'
	];

	public static $pictureSizes = [
		'20' => 'Très petite',
		'30' => 'Petite',
		'40' => 'Grande',
		'50' => 'Très Grande',
		'100' => 'Pleine largeur',
	];

	public static $picturePositions = [
		'left' => 'À gauche',
		'right' => 'À droite ',
	];

	// Nombre d'objets par page
	public static $ArticlesListed = [
		1 => '1 article',
		2 => '2 articles',
		4 => '4 articles',
		6 => '6 articles',
		8 => '8 articles',
		10 => '10 articles',
		12 => '12 articles'
	];

	//Paramètre longueur maximale des commentaires en nb de caractères
	public static $commentsLength = [
		100 => '100 signes',
		250 => '250 signes',
		500 => '500 signes',
		750 => '750 signes'
	];

	public static $articlesLenght = [
		0 => 'Articles complets',
		600 => '600 signes',
		800 => '800 signes',
		1000 => '1000 signes',
		1200 => '1200 signes',
		1400 => '1400 signes',
		1600 => '1600 signes',
		1800 => '1800 signes',
	];

	public static $articlesLayout = [
		false => 'Classique',
		true => 'Moderne',
	];

	// Permissions d'un article
	public static $articleConsent = [
		self::EDIT_ALL => 'Tous les rôles',
		self::EDIT_ROLE => 'Rôle du propriétaire',
		self::EDIT_OWNER => 'Propriétaire'
	];

	public static $dateFormats = [
		'%d %B %Y' => 'DD MMMM YYYY',
		'%d/%m/%Y' => 'DD/MM/YYYY',
		'%m/%d/%Y' => 'MM/DD/YYYY',
		'%d/%m/%y' => 'DD/MM/YY',
		'%m/%d/%y' => 'MM/DD/YY',
		'%d-%m-%Y' => 'DD-MM-YYYY',
		'%m-%d-%Y' => 'MM-DD-YYYY',
		'%d-%m-%y' => 'DD-MM-YY',
		'%m-%d-%y' => 'MM-DD-YY',
	];
	public static $timeFormats = [
		'%H:%M' => 'HH:MM',
		'%I:%M %p' => "HH:MM tt",
	];

	public static $timeFormat = '';
	public static $dateFormat = '';

	// Nombre d'articles dans la page de config:
	public static $itemsperPage = 8;


	public static $users = [];



	/**
	 * Mise à jour du module
	 * Appelée par les fonctions index et config
	 */
	private function update()
	{
		// Initialisation
		if (is_null($this->getData(['module', $this->getUrl(0), 'config', 'versionData']))) {
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '0.0']);
		}
		// Version 5.0
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '5.0', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'itemsperPage', 6]);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '5.0']);
		}
		// Version 6.0
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '6.0', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'feeds', false]);
			$this->setData(['module', $this->getUrl(0), 'config', 'feedsLabel', '']);
			$this->setData(['module', $this->getUrl(0), 'config', 'articlesLenght', 0]);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '6.0']);
		}
		// Version 6.5
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '6.5', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'dateFormat', '%d %B %Y']);
			$this->setData(['module', $this->getUrl(0), 'config', 'timeFormat', '%H:%M']);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '6.5']);
		}
		// Version 8.0
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '8.0', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'buttonBack', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'showTime', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'showDate', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'showPseudo', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '8.0']);
		}
		// Version 8.1 (Forum)
		if (version_compare($this->getData(['module', $this->getUrl(0), 'config', 'versionData']), '8.1', '<')) {
			$this->setData(['module', $this->getUrl(0), 'config', 'signupUrl', 'inscription']);
			$this->setData(['module', $this->getUrl(0), 'config', 'boards', ['general' => 'Général']]);
			$this->setData(['module', $this->getUrl(0), 'config', 'boardsRaw', "Général|general"]);
			$this->setData(['module', $this->getUrl(0), 'config', 'floodTopicSeconds', 60]);
			$this->setData(['module', $this->getUrl(0), 'config', 'floodReplySeconds', 20]);
			$this->setData(['module', $this->getUrl(0), 'config', 'honeypot', true]);
			$this->setData(['module', $this->getUrl(0), 'config', 'versionData', '8.1']);
		}
	}


	/**
	 * Parse les sections (sous-forums)
	 * Format par ligne : Libellé|slug (slug optionnel)
	 */
	private function parseBoards(string $raw): array
	{
		$raw = str_replace(["\r\n", "\r"], "\n", $raw);
		$lines = array_filter(array_map('trim', explode("\n", $raw)));
		$out = [];
		foreach ($lines as $line) {
			if ($line === '') { continue; }
			// Autorise "Libellé|slug" ou "Libellé"
			$parts = array_map('trim', explode('|', $line, 2));
			$label = $parts[0] ?? '';
			$slug = $parts[1] ?? '';
			if ($label === '') { continue; }
			if ($slug === '') {
				$slug = helper::url($label);
			}
			$slug = preg_replace('/[^a-z0-9_-]/', '', strtolower($slug));
			if ($slug === '') { continue; }
			$out[$slug] = $label;
		}
		if (empty($out)) {
			$out = ['general' => 'Général'];
		}
		return $out;
	}

	private function getBoards(): array
	{
		$boards = $this->getData(['module', $this->getUrl(0), 'config', 'boards']);
		if (is_array($boards) && !empty($boards)) {
			return $boards;
		}
		return ['general' => 'Général'];
	}

	private function getBoardLabel(string $slug): string
	{
		$boards = $this->getBoards();
		return isset($boards[$slug]) ? $boards[$slug] : $slug;
	}

	private function normalizeBoard(?string $slug): string
	{
		$slug = $slug ?? '';
		$slug = preg_replace('/[^a-z0-9_-]/', '', strtolower(trim($slug)));
		if ($slug === '') { $slug = 'general'; }
		$boards = $this->getBoards();
		if (!isset($boards[$slug])) { $slug = array_key_first($boards); }
		return $slug;
	}

	private function getFloodSeconds(string $type): int
	{
		$key = $type === 'reply' ? 'floodReplySeconds' : 'floodTopicSeconds';
		$v = (int) $this->getData(['module', $this->getUrl(0), 'config', $key]);
		return max(0, $v);
	}

	private function checkFlood(string $type): bool
	{
		$delay = $this->getFloodSeconds($type);
		if ($delay <= 0) { return true; }
		$k = 'forum_flood_' . $type . '_' . $this->getUrl(0);
		$now = time();
		$last = isset($_SESSION[$k]) ? (int) $_SESSION[$k] : 0;
		if ($last > 0 && ($now - $last) < $delay) {
			return false;
		}
		$_SESSION[$k] = $now;
		return true;
	}

	private function isHoneypotTripped(string $fieldName): bool
	{
		$enabled = $this->getData(['module', $this->getUrl(0), 'config', 'honeypot']);
		if ($enabled === null) { $enabled = true; }
		if (!$enabled) { return false; }
		$v = (string) $this->getInput($fieldName, null);
		return trim($v) !== '';
	}

	/**
	 * Flux RSS
	 */
	public function rss()
	{
		// Inclure les classes
		include_once 'module/forum/vendor/FeedWriter/Item.php';
		include_once 'module/forum/vendor/FeedWriter/Feed.php';
		include_once 'module/forum/vendor/FeedWriter/RSS2.php';
		include_once 'module/forum/vendor/FeedWriter/InvalidOperationException.php';

		date_default_timezone_set('UTC');
		$feeds = new \FeedWriter\RSS2();

		// En-tête avec nettoyage du contenu
		$pageTitle = $this->getData(['page', $this->getUrl(0), 'title']);
		$feeds->setTitle($pageTitle ? helper::cleanRssText($pageTitle) : '');
		$feeds->setLink(helper::baseUrl() . $this->getUrl(0));
		if ($metaDescription = $this->getData(['page', $this->getUrl(0), 'metaDescription'])) {
			// Channel description should be plain text for best interoperability
			$feeds->setDescription(trim(strip_tags(helper::cleanRssText($metaDescription))));
		} else {
			// Fallback: use page title or base URL so the channel has a description (plain text)
			$fallbackDesc = $pageTitle ? $pageTitle : helper::baseUrl(false);
			$feeds->setDescription(trim(strip_tags(helper::cleanRssText($fallbackDesc))));
		}
		// Add content namespace for full HTML content (use FeedWriter API)
		if (method_exists($feeds, 'addNamespace')) {
			$feeds->addNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
		} else {
			$feeds->setChannelElement('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
		}
		// Determine atom:self based on current request URI when possible
		$scheme = helper::isHttps() ? 'https://' : 'http://';
		if (!empty($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
			// Use the actual request URI so atom:link matches the document location
			$selfHref = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			// Fallback to the canonical feed URL
			$selfHref = helper::baseUrl(false) . $this->getUrl(0) . '/rss';
		}
		// Use FeedWriter API to add atom:self properly
		if (method_exists($feeds, 'setSelfLink')) {
			$feeds->setSelfLink($selfHref);
		} else {
			$feeds->setAtomLink($selfHref, 'self', $feeds->getMIMEType());
		}
		$feeds->setChannelElement('language', 'fr-FR');
		$feeds->setDate(date('r', time()));
		$feeds->addGenerator();
		// Corps des articles
		$articleIdsPublishedOns = helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts']), 'publishedOn', 'SORT_DESC');
		$articleIdsStates = helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts']), 'state', 'SORT_DESC');
		foreach ($articleIdsPublishedOns as $articleId => $articlePublishedOn) {
			if ($articlePublishedOn <= time() and $articleIdsStates[$articleId]) {
				// Récupération des données de l'article
				$articleData = $this->getData(['module', $this->getUrl(0), 'posts', $articleId]);
				$articleTitle = helper::cleanRssText($articleData['title']);
				
				// Miniature
				$thumb = helper::baseUrl(false) . $this->getThumb($articleData['picture']);
				
				// Créer les articles du flux
				$newsArticle = $feeds->createNewItem();
				
				// Signature de l'article
				$author = $this->signature(helper::cleanRssText($articleData['userId'], true));
				
				// Construction du contenu avec nettoyage des données
				// description: plain text (no HTML) for validators; content:encoded holds full HTML
				$contentText = trim(strip_tags(helper::cleanRssText($articleData['content'])));
				// Limiter la description à 300 caractères pour un résumé clair
				if (strlen($contentText) > 300) {
					$plainDesc = substr($contentText, 0, 297) . '...';
				} else {
					$plainDesc = $contentText;
				}
				$fullHtml = '<img src="' . $thumb
					. '" alt="' . helper::cleanRssText($articleTitle, true)
					. '" title="' . helper::cleanRssText($articleTitle, true)
					. '" />' . helper::cleanRssText($articleData['content']);
				$newsArticle->addElementArray([
					'title' => $articleTitle,
					'link' => helper::baseUrl() . $this->getUrl(0) . '/' . $articleId,
					'description' => $plainDesc,
					'content:encoded' => $fullHtml
				]);
				$newsArticle->setAuthor($author, 'no@mail.com');
				$newsArticle->setId(helper::baseUrl() . $this->getUrl(0) . '/' . $articleId);
				$newsArticle->setDate(date('r', $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'publishedOn'])));
				if ($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture'])
					&& file_exists($this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'picture']))) {
					$thumbPath = $this->getThumb($articleData['picture']);
					$imageData = getimagesize($thumbPath);
					$newsArticle->addEnclosure(
						helper::baseUrl(false) . $thumbPath,
						$imageData[0] * $imageData[1],
						$imageData['mime']
					);
				}
				$feeds->addItem($newsArticle);
			}
		}

		// Sections
		self::$boards = $this->getBoards();
		// Valeurs en sortie
		$this->addOutput([
			'display' => self::DISPLAY_RSS,
			'content' => $feeds->generateFeed(),
			'view' => 'rss'
		]);
	}

	/**
	 * Édition
	 */
/**
 * Vérifie si l’utilisateur courant peut éditer un sujet (droits forum).
 */
private function canEditPost(string $postId): bool
{
	if ($this->isConnected() === false) {
		return false;
	}
	// Admin : toujours
	if ($this->getUser('role') === self::ROLE_ADMIN) {
		return true;
	}
	$consent = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'editConsent']);
	$userId = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'userId']);

	// Propriétaire
	if ($consent === self::EDIT_OWNER) {
		return $userId === $this->getUser('id');
	}
	// Tout le monde
	if ($consent === self::EDIT_ALL) {
		return true;
	}
	// Consentement chiffré : rôle minimum
	if (is_numeric($consent)) {
		return $this->getUser('role') >= (int) $consent;
	}
	return false;
}

/**
 * Suppression : par défaut, uniquement le propriétaire (et seulement si aucune réponse).
 * Les modérateurs / admins passent par la configuration.
 */
private function canDeletePost(string $postId): bool
{
	if ($this->isConnected() === false) {
		return false;
	}
	if ($this->getUser('role') >= self::ROLE_EDITOR) {
		return true;
	}
	$userId = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'userId']);
	if ($userId !== $this->getUser('id')) {
		return false;
	}
	$comments = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'comment']);
	return empty($comments);
}

	public function add()
	{
		// Soumission du formulaire
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) === true &&
			$this->isPost()
		) {
			// Anti-spam : honeypot + anti-flood
			if ($this->isHoneypotTripped('forumHp')) {
				$this->addOutput([
					'redirect' => helper::baseUrl() . $this->getUrl(0),
					'notification' => 'Message refusé.',
					'state' => false
				]);
				return;
			}
			if ($this->checkFlood('topic') === false) {
				$this->addOutput([
					'redirect' => helper::baseUrl() . $this->getUrl(0) . '/add',
					'notification' => 'Patience — tu postes trop vite.',
					'state' => false
				]);
				return;
			}
			// Modification de l'userId
			if ($this->getUser('role') === self::ROLE_ADMIN) {
				$newuserid = $this->getInput('blogAddUserId', helper::FILTER_STRING_SHORT, true);
			} else {
				$newuserid = $this->getUser('id');
			}
			// Incrémente l'id de l'article
			$articleId = helper::increment($this->getInput('blogAddPermalink'), $this->getData(['page']));
			$articleId = helper::increment($articleId, (array) $this->getData(['module', $this->getUrl(0)]));
			$articleId = helper::increment($articleId, array_keys(self::$actions));
			// Crée l'article
			$this->setData([
				'module',
				$this->getUrl(0),
				'posts',
				$articleId,
				[
					'content' => $this->getRelativePath($this->getInput('blogAddContent', null)),
					'picture' => $this->getNormalizedFilePath($this->getInput('blogAddPicture', helper::FILTER_STRING_SHORT),$this->getUser('id')),
					'hidePicture' => $this->getInput('blogAddHidePicture', helper::FILTER_BOOLEAN),
					'pictureSize' => $this->getInput('blogAddPictureSize', helper::FILTER_STRING_SHORT),
					'picturePosition' => $this->getInput('blogAddPicturePosition', helper::FILTER_STRING_SHORT),
					'publishedOn' => $this->getInput('blogAddPublishedOn', helper::FILTER_DATETIME, true),
					'state' => $this->getInput('blogAddState', helper::FILTER_BOOLEAN),
					'title' => $this->getInput('blogAddTitle', helper::FILTER_STRING_SHORT, true),
					'board' => $this->normalizeBoard($this->getInput('forumAddBoard', helper::FILTER_STRING_SHORT)),
					'pinned' => ($this->getUser('role') >= self::ROLE_EDITOR) ? (bool) $this->getInput('forumAddPinned', helper::FILTER_BOOLEAN) : false,
					'solved' => false,
					'userId' => $newuserid,
					'editConsent' => ($this->getUser('role') < self::ROLE_EDITOR) ? self::EDIT_OWNER : ($this->getInput('blogAddConsent') === self::EDIT_ROLE ? $this->getUser('role') : $this->getInput('blogAddConsent')),
					// Notes : en mode forum, un membre ne peut modifier que ses sujets.
					'commentMaxlength' => $this->getInput('blogAddCommentMaxlength'),
					'commentApproved' => $this->getInput('blogAddCommentApproved', helper::FILTER_BOOLEAN),
					'commentClose' => $this->getInput('blogAddCommentClose', helper::FILTER_BOOLEAN),
					'commentNotification' => $this->getInput('blogAddCommentNotification', helper::FILTER_BOOLEAN),
					'commentGroupNotification' => $this->getInput('blogAddCommentGroupNotification', helper::FILTER_INT),
					'comment' => []
				]
			]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/' . $articleId,
				'notification' => helper::translate('Nouveau sujet créé'),
				'state' => true
			]);
		}
		// Liste des utilisateurs
		self::$users = helper::arrayColumn($this->getData(['user']), 'firstname');
		ksort(self::$users);
		foreach (self::$users as $userId => &$userFirstname) {
			$userFirstname = $userFirstname . ' ' . $this->getData(['user', $userId, 'lastname']);
		}
		unset($userFirstname);
		// Sections
		self::$boards = $this->getBoards();
		// Valeurs en sortie
		$this->addOutput([
			'title' => helper::translate('Nouveau sujet'),
			'vendor' => [
				'tinymce',
				'furl'
			],
			'view' => 'add'
		]);
	}

	/**
	 * Liste des commentaires
	 */
	public function comment()
	{
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		} else {
			$comments = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment']);
			self::$commentsDelete = template::button('blogCommentDeleteAll', [
				'class' => 'blogCommentDeleteAll buttonRed',
				'href' => helper::baseUrl() . $this->getUrl(0) . '/commentDeleteAll/' . $this->getUrl(2),
				'value' => 'Tout effacer'
			]);
			// Ids des commentaires par ordre de création
			$commentIds = array_keys(helper::arrayColumn($comments, 'createdOn', 'SORT_DESC'));
			// Pagination
			$pagination = helper::pagination($commentIds, $this->getUrl(), $this->getData(['module', $this->getUrl(0), 'config', 'itemsperPage']));
			// Liste des pages
			self::$pages = $pagination['pages'];
			// Commentaires en fonction de la pagination
			for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
				// Met en forme le tableau
				$comment = $comments[$commentIds[$i]];
				// Bouton d'approbation
				$buttonApproval = '';
				// Compatibilité avec les commentaires des versions précédentes, les valider
				$comment['approval'] = array_key_exists('approval', $comment) === false ? true : $comment['approval'];
				if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'commentApproved']) === true) {
					$buttonApproval = template::button('blogCommentApproved' . $commentIds[$i], [
						'class' => $comment['approval'] === true ? 'blogCommentRejected buttonGreen' : 'blogCommentApproved buttonRed',
						'href' => helper::baseUrl() . $this->getUrl(0) . '/commentApprove/' . $this->getUrl(2) . '/' . $commentIds[$i],
						'value' => $comment['approval'] === true ? 'A' : 'R',
						'help' => $comment['approval'] === true ? 'Approuvé' : 'Rejeté',
					]);
				}
				self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
				self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
				self::$comments[] = [
					helper::dateUTF8(self::$dateFormat, $comment['createdOn'], self::$i18nUI) . ' - ' . helper::dateUTF8(self::$timeFormat, $comment['createdOn'], self::$i18nUI),
					$comment['content'],
					$comment['userId'] ? $this->getData(['user', $comment['userId'], 'firstname']) . ' ' . $this->getData(['user', $comment['userId'], 'lastname']) : $comment['author'],
					$buttonApproval,
					template::button('blogCommentDelete' . $commentIds[$i], [
						'class' => 'blogCommentDelete buttonRed',
						'href' => helper::baseUrl() . $this->getUrl(0) . '/commentDelete/' . $this->getUrl(2) . '/' . $commentIds[$i],
						'value' => template::ico('trash')
					])
				];
			}
			// Valeurs en sortie
			$this->addOutput([
				'title' => helper::translate('Gestion des commentaires'),
				'view' => 'comment'
			]);
		}
	}


	/**
	 * File de modération globale : toutes les réponses en attente
	 */
	public function moderation()
	{
		if ($this->getUser('permission', __CLASS__, __FUNCTION__) !== true) {
			$this->addOutput(['access' => false]);
			return;
		}
		$pending = [];
		$posts = (array) $this->getData(['module', $this->getUrl(0), 'posts']);
		foreach ($posts as $postId => $post) {
			$comments = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'comment']);
			if (!is_array($comments)) { continue; }
			foreach ($comments as $commentId => $comment) {
				$approval = $comment['approval'] ?? true;
				if ($approval === false) {
					$author = $comment['userId'] ? $this->signature($comment['userId']) : ($comment['author'] ?? '');
					$pending[] = [
						'postId' => $postId,
						'postTitle' => $post['title'] ?? $postId,
						'commentId' => $commentId,
						'author' => $author,
						'createdOn' => (int) ($comment['createdOn'] ?? 0),
						'content' => (string) ($comment['content'] ?? '')
					];
				}
			}
		}
		usort($pending, function($a, $b) {
			return ($b['createdOn'] <=> $a['createdOn']);
		});
		self::$moderation = $pending;
		// Sections
		self::$boards = $this->getBoards();
		// Format de temps
		self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
		self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
		$this->addOutput([
			'showBarEditButton' => true,
			'title' => 'Modération',
			'view' => 'moderation'
		]);
	}

	public function togglePinned()
	{
		$postId = (string) $this->getUrl(2);
		if ($this->getUser('permission', __CLASS__, __FUNCTION__) !== true || $this->getData(['module', $this->getUrl(0), 'posts', $postId]) === null) {
			$this->addOutput(['access' => false]);
			return;
		}
		$cur = (bool) $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'pinned']);
		$this->setData(['module', $this->getUrl(0), 'posts', $postId, 'pinned', !$cur]);
		$this->addOutput([
			'redirect' => helper::baseUrl() . $this->getUrl(0) . '/' . $postId,
			'notification' => !$cur ? 'Sujet épinglé' : 'Épinglage retiré',
			'state' => true
		]);
	}

	public function toggleClose()
	{
		$postId = (string) $this->getUrl(2);
		if ($this->getUser('permission', __CLASS__, __FUNCTION__) !== true || $this->getData(['module', $this->getUrl(0), 'posts', $postId]) === null) {
			$this->addOutput(['access' => false]);
			return;
		}
		$cur = (bool) $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'commentClose']);
		$this->setData(['module', $this->getUrl(0), 'posts', $postId, 'commentClose', !$cur]);
		$this->addOutput([
			'redirect' => helper::baseUrl() . $this->getUrl(0) . '/' . $postId,
			'notification' => !$cur ? 'Sujet fermé' : 'Sujet rouvert',
			'state' => true
		]);
	}

	public function toggleSolved()
	{
		$postId = (string) $this->getUrl(2);
		if ($this->getUser('permission', __CLASS__, __FUNCTION__) !== true || $this->getData(['module', $this->getUrl(0), 'posts', $postId]) === null) {
			$this->addOutput(['access' => false]);
			return;
		}
		$ownerId = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'userId']);
		if (!($this->getUser('role') >= self::ROLE_EDITOR || $ownerId === $this->getUser('id'))) {
			$this->addOutput(['access' => false]);
			return;
		}
		$cur = (bool) $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'solved']);
		$this->setData(['module', $this->getUrl(0), 'posts', $postId, 'solved', !$cur]);
		$this->addOutput([
			'redirect' => helper::baseUrl() . $this->getUrl(0) . '/' . $postId,
			'notification' => !$cur ? 'Sujet marqué comme résolu' : 'Sujet marqué comme non résolu',
			'state' => true
		]);
	}

	/**
	 * Suppression de commentaire
	 */
	public function commentDelete()
	{
		// Le commentaire n'existe pas
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true ||
			$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3)]) === null
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Suppression
		else {
			$this->deleteData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3)]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/comment/' . $this->getUrl(2),
				'notification' => helper::translate('Commentaire supprimé'),
				'state' => true
			]);
		}
	}

	/**
	 * Suppression de tous les commentairess de l'article $this->getUrl(2)
	 */
	public function commentDeleteAll()
	{
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Suppression
		else {
			$this->setData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', []]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/comment',
				'notification' => helper::translate('Commentaires supprimés'),
				'state' => true
			]);
		}
	}

	/**
	 * Approbation oou désapprobation de commentaire
	 */
	public function commentApprove()
	{
		// Le commentaire n'existe pas
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true ||
			$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3)]) === null
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Inversion du statut
		else {
			$approved = !$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'approval']);
			$this->setData([
				'module',
				$this->getUrl(0),
				'posts',
				$this->getUrl(2),
				'comment',
				$this->getUrl(3),
				[
					'author' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'author']),
					'content' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'content']),
					'createdOn' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'createdOn']),
					'userId' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment', $this->getUrl(3), 'userId']),
					'approval' => $approved
				]
			]);

			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/comment/' . $this->getUrl(2),
				'notification' => $approved ? helper::translate('Commentaire approuvé') : helper::translate('Commentaire rejeté'),
				'state' => $approved
			]);
		}
	}

	/**
	 * Configuration
	 */
	public function config()
	{

		// Mise à jour des données de module
		$this->update();
		// Ids des articles par ordre de publication
		$articleIds = array_keys(helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts']), 'publishedOn', 'SORT_DESC'));
		// Gestion des droits d'accès
		$filterData = [];
		foreach ($articleIds as $key => $value) {
			if (
				( // Propriétaire
					$this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent']) === self::EDIT_OWNER
					and ($this->getData(['module', $this->getUrl(0), 'posts', $value, 'userId']) === $this->getUser('id')
						or $this->getUser('role') === self::ROLE_ADMIN)
				)

				or (
					// Rôle
					$this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent']) !== self::EDIT_OWNER
					and $this->getUser('role') >= $this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent'])
				)
				or (
					// Tout le monde
					$this->getData(['module', $this->getUrl(0), 'posts', $value, 'editConsent']) === self::EDIT_ALL
				)
			) {
				$filterData[] = $value;
			}
		}
		$articleIds = $filterData;
		// Pagination
		$pagination = helper::pagination($articleIds, $this->getUrl(), self::$itemsperPage);
		// Liste des pages
		self::$pages = $pagination['pages'];
		// Format de temps
		self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
		self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
		// Articles en fonction de la pagination
		for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
			// Nombre de commentaires à approuver et approuvés
			$approvals = helper::arrayColumn($this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'comment']), 'approval', 'SORT_DESC');
			if (is_array($approvals)) {
				$a = array_values($approvals);
				$toApprove = count(array_keys($a, false));
				$approved = count(array_keys($a, true));
			} else {
				$toApprove = 0;
				$approved = count($this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'comment']));
			}
			// Met en forme le tableau
			$boardSlug = (string) $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'board']);
			$boardSlug = $this->normalizeBoard($boardSlug);
			$boardLabel = $this->getBoardLabel($boardSlug);
			$status = '';
			if ((bool) $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'pinned'])) { $status .= template::ico('pin') . ' '; }
			if ((bool) $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'solved'])) { $status .= template::ico('check') . ' '; }
			if ((bool) $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'commentClose'])) { $status .= template::ico('lock') . ' '; }
			$status = trim($status);
			if ($status === '') { $status = '—'; }

			self::$articles[] = [
				'<a href="' . helper::baseUrl() . $this->getUrl(0) . '/' . $articleIds[$i] . '" target="_blank" >' .
				$this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'title']) .
				'</a>',
				$boardLabel,
				$status,
				helper::dateUTF8(self::$dateFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'publishedOn']), self::$i18nUI) . ' — ' . helper::dateUTF8(self::$timeFormat, $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'publishedOn']), self::$i18nUI),
				self::$states[$this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i], 'state'])],
				// Bouton pour afficher les commentaires du sujet
				template::button('blogConfigComment' . $articleIds[$i], [
					'class' => ($toApprove || $approved) > 0 ? '' : 'buttonGrey',
					'href' => ($toApprove || $approved) > 0 ? helper::baseUrl() . $this->getUrl(0) . '/comment/' . $articleIds[$i] : '',
					'value' => $toApprove > 0 ? $toApprove . '/' . $approved : $approved,
					'help' => ($toApprove || $approved) > 0 ? 'Éditer / approuver une réponse' : ''
				]),
				template::button('blogConfigEdit' . $articleIds[$i], [
					'href' => helper::baseUrl() . $this->getUrl(0) . '/edit/' . $articleIds[$i],
					'value' => template::ico('pencil')
				]),
				template::button('blogConfigDelete' . $articleIds[$i], [
					'class' => 'blogConfigDelete buttonRed',
					'href' => helper::baseUrl() . $this->getUrl(0) . '/delete/' . $articleIds[$i],
					'value' => template::ico('trash')
				])
			];
		}
		// Valeurs en sortie
		$this->addOutput([
			'title' => helper::translate('Configuration du module'),
			'view' => 'config'
		]);
	}

	public function option()
	{


		// Soumission du formulaire
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) === true &&
			$this->isPost()
		) {
			// Sections (format lignes: Libellé|slug)
			$boardsRaw = (string) $this->getInput('forumOptionBoards', null);
			$boards = $this->parseBoards($boardsRaw);

			$this->setData([
				'module',
				$this->getUrl(0),
				'config',
				[
					'feeds' => $this->getInput('blogOptionShowFeeds', helper::FILTER_BOOLEAN),
					'feedsLabel' => $this->getInput('blogOptionFeedslabel', helper::FILTER_STRING_SHORT),
					'layout' => $this->getInput('blogOptionArticlesLayout', helper::FILTER_BOOLEAN),
					'articlesLenght' => $this->getInput('blogOptionArticlesLayout', helper::FILTER_BOOLEAN) === false ? $this->getInput('blogOptionArticlesLenght', helper::FILTER_INT) : 0,
					'itemsperPage' => $this->getInput('blogOptionItemsperPage', helper::FILTER_INT, true),
					'dateFormat' => $this->getInput('blogOptionDateFormat'),
					'timeFormat' => $this->getInput('blogOptionTimeFormat'),
					'buttonBack' => $this->getInput('blogOptionButtonBack', helper::FILTER_BOOLEAN),
					'showDate' => $this->getInput('blogOptionShowDate', helper::FILTER_BOOLEAN),
					'showTime' => $this->getInput('blogOptionShowTime', helper::FILTER_BOOLEAN),
					'showPseudo' => $this->getInput('blogOptionShowPseudo', helper::FILTER_BOOLEAN),
					'signupUrl' => $this->getInput('blogOptionSignupUrl', helper::FILTER_STRING_SHORT),
					'boards' => $boards,
					'boardsRaw' => $boardsRaw,
					'floodTopicSeconds' => max(0, (int) $this->getInput('forumOptionFloodTopic', helper::FILTER_INT)),
					'floodReplySeconds' => max(0, (int) $this->getInput('forumOptionFloodReply', helper::FILTER_INT)),
					'honeypot' => (bool) $this->getInput('forumOptionHoneypot', helper::FILTER_BOOLEAN),
					'versionData' => $this->getData(['module', $this->getUrl(0), 'config', 'versionData']),
				]
			]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/option',
				'notification' => helper::translate('Modifications enregistrées'),
				'state' => true
			]);
			return;
		}
		// Valeurs en sortie
		$this->addOutput([
			'title' => helper::translate('Options de configuration'),
			'view' => 'option'
		]);

	}

	/**
	 * Suppression
	 */
	public function delete()
	{
		$postId = (string) $this->getUrl(2);
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true ||
			$this->getData(['module', $this->getUrl(0), 'posts', $postId]) === null ||
			$this->canDeletePost($postId) === false
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Suppression
		else {
			$this->deleteData(['module', $this->getUrl(0), 'posts', $this->getUrl(2)]);
			// Valeurs en sortie
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0),
				'notification' => helper::translate('Sujet supprimé'),
				'state' => true
			]);
		}
	}

	/**
	 * Édition
	 */
	public function edit()
	{
		if (
			$this->getUser('permission', __CLASS__, __FUNCTION__) !== true
		) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// L'article n'existe pas
		if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2)]) === null) {
			// Valeurs en sortie
			$this->addOutput([
				'access' => false
			]);
		}
		// Le sujet existe
		else {
			// Droits : un membre ne peut éditer que ses sujets
			if ($this->getUser('role') < self::ROLE_EDITOR && $this->canEditPost((string) $this->getUrl(2)) === false) {
				$this->addOutput(['access' => false]);
				return;
			}
			// Soumission du formulaire
			if (
				$this->getUser('permission', __CLASS__, __FUNCTION__) === true &&
				$this->isPost()
			) {
				if ($this->getUser('role') === self::ROLE_ADMIN) {
					$newuserid = $this->getInput('blogEditUserId', helper::FILTER_STRING_SHORT, true);
				} else {
					$newuserid = $this->getUser('id');
				}
				$articleId = $this->getInput('blogEditPermalink', null, true);
				// Incrémente le nouvel id de l'article
				if ($articleId !== $this->getUrl(2)) {
					$articleId = helper::increment($articleId, $this->getData(['page']));
					$articleId = helper::increment($articleId, $this->getData(['module', $this->getUrl(0), 'posts']));
					$articleId = helper::increment($articleId, array_keys(self::$actions));
				}
				$this->setData([
					'module',
					$this->getUrl(0),
					'posts',
					$articleId,
					[
						'title' => $this->getInput('blogEditTitle', helper::FILTER_STRING_SHORT, true),
					'board' => $this->normalizeBoard($this->getInput('forumEditBoard', helper::FILTER_STRING_SHORT)),
					'pinned' => ($this->getUser('role') >= self::ROLE_EDITOR) ? (bool) $this->getInput('forumEditPinned', helper::FILTER_BOOLEAN) : (bool) $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'pinned']),
					'solved' => (bool) $this->getData(['module', $this->getUrl(0), 'posts', $articleId, 'solved']),
						'comment' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'comment']),
						'content' => $this->getRelativePath($this->getInput('blogEditContent', null)),
						'picture' => $this->getNormalizedFilePath($this->getInput('blogEditPicture', helper::FILTER_STRING_SHORT), $this->getUser('id')),
						'hidePicture' => $this->getInput('blogEditHidePicture', helper::FILTER_BOOLEAN),
						'pictureSize' => $this->getInput('blogEditPictureSize', helper::FILTER_STRING_SHORT),
						'picturePosition' => $this->getInput('blogEditPicturePosition', helper::FILTER_STRING_SHORT),
						'publishedOn' => $this->getInput('blogEditPublishedOn', helper::FILTER_DATETIME, true),
						'state' => $this->getInput('blogEditState', helper::FILTER_BOOLEAN),
						'userId' => $newuserid,
						'editConsent' => ($this->getUser('role') < self::ROLE_EDITOR) ? self::EDIT_OWNER : ($this->getInput('blogEditConsent') === self::EDIT_ROLE ? $this->getUser('role') : $this->getInput('blogEditConsent')),
					// Notes : en mode forum, un membre ne peut modifier que ses sujets.
						'commentMaxlength' => $this->getInput('blogEditCommentMaxlength'),
						'commentApproved' => $this->getInput('blogEditCommentApproved', helper::FILTER_BOOLEAN),
						'commentClose' => $this->getInput('blogEditCommentClose', helper::FILTER_BOOLEAN),
						'commentNotification' => $this->getInput('blogEditCommentNotification', helper::FILTER_BOOLEAN),
						'commentGroupNotification' => $this->getInput('blogEditCommentGroupNotification', helper::FILTER_INT)
					]
				]);
				// Supprime l'ancien article
				if ($articleId !== $this->getUrl(2)) {
					$this->deleteData(['module', $this->getUrl(0), 'posts', $this->getUrl(2)]);
				}
				// Valeurs en sortie
				$this->addOutput([
					'redirect' => helper::baseUrl() . $this->getUrl(0) . '/config',
					'notification' => helper::translate('Modifications enregistrées'),
					'state' => true
				]);
			}
			// Liste des utilisateurs
			self::$users = helper::arrayColumn($this->getData(['user']), 'firstname');
			ksort(self::$users);
			foreach (self::$users as $userId => &$userFirstname) {
				$userFirstname = $userFirstname . ' ' . $this->getData(['user', $userId, 'lastname']) . ' (' . self::$roleEdits[$this->getData(['user', $userId, 'role'])] . ')';
			}
			unset($userFirstname);
			// Sections
			self::$boards = $this->getBoards();
			// Valeurs en sortie
			$this->addOutput([
				'title' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(2), 'title']),
				'vendor' => [
					'tinymce',
					'furl'
				],
				'view' => 'edit'
			]);
		}
	}

	/**
	 * Accueil (deux affichages en un pour éviter une url à rallonge)
	 */
	public function index()
	{
		// Mise à jour des données de module
		$this->update();
		// Affichage d'un article
		if (
			$this->getUrl(1)
			// Protection pour la pagination, un ID ne peut pas être un entier, une page oui
			and intval($this->getUrl(1)) === 0
		) {
			// L'article n'existe pas
			if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1)]) === null) {
				// Valeurs en sortie
				$this->addOutput([
					'access' => false
				]);
			}
			// L'article existe
			else {
				// Soumission du formulaire
				if (
					$this->isPost()
				) {
					// Auth requise en mode forum
					if ($this->isConnected() === false) {
						$this->addOutput([
							'redirect' => helper::baseUrl() . 'user/login/' . str_replace('/', '_', $this->getUrl()) . '__comment',
							'notification' => 'Connexion requise',
							'state' => false
						]);
						return;
					} else {

						// Anti-spam : honeypot + anti-flood
						if ($this->isHoneypotTripped('forumHp')) {
							$this->addOutput([
								'redirect' => helper::baseUrl() . $this->getUrl() . '#comment',
								'notification' => 'Message refusé.',
								'state' => false
							]);
							return;
						}
						if ($this->checkFlood('reply') === false) {
							self::$commentContent = $this->getInput('blogArticleContent', null, true);
							$this->addOutput([
								'redirect' => helper::baseUrl() . $this->getUrl() . '#comment',
								'notification' => 'Patience — tu réponds trop vite.',
								'state' => false
							]);
							return;
						}

						// Création du commentaire et notifcation par email
						$commentId = helper::increment(uniqid(), $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment']));
						$content = $this->getInput('blogArticleContent', null, true);
						$this->setData([
							'module',
							$this->getUrl(0),
							'posts',
							$this->getUrl(1),
							'comment',
							$commentId,
							[
								'author' => '',
								'content' => $this->getRelativePath($content),
								'createdOn' => time(),
								'userId' => $this->getUser('id'),
								'approval' => !$this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentApproved']) // true commentaire publié false en attente de publication
							]
						]);
						// Envoi d'une notification aux administrateurs
						// Init tableau
						$to = [];
						// Liste des destinataires
						foreach ($this->getData(['user']) as $userId => $user) {
							if ($user['role'] >= $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentGroupNotification'])) {
								$to[] = $user['mail'];
								$firstname[] = $user['firstname'];
								$lastname[] = $user['lastname'];
							}
						}
						// Envoi du mail $sent code d'erreur ou de réussite
						$notification = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentApproved']) === true ? 'Commentaire déposé en attente d\'approbation' : 'Commentaire déposé';
						if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'commentNotification']) === true) {
							$error = 0;
							foreach ($to as $key => $adress) {
								$sent = $this->sendMail(
									$adress,
									'Nouveau commentaire déposé',
									'<p>Bonjour' . ' <strong>' . $firstname[$key] . ' ' . $lastname[$key] . '</strong>,</p>' .
									'<p>L\'article <a href="' . helper::baseUrl() . $this->getUrl(0) . '/' . $this->getUrl(1) . '">' . $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'title']) . '</a> a  reçu un nouveau commentaire rédigé par <strong>' .
									$this->signature($this->getUser('id')) . '</strong></p>' .
									'<p>' . $content.'</p>',
									null,
									$this->getData(['config', 'smtp', 'from'])
								);
								if ($sent === false)
									$error++;
							}
							// Valeurs en sortie
							$this->addOutput([
								'redirect' => helper::baseUrl() . $this->getUrl() . '#comment',
								'notification' => ($error === 0 ? $notification . '<br/>Une notification a été envoyée.' : $notification . '<br/> Erreur de notification : ' . $sent),
								'state' => ($sent === true ? true : null)
							]);
						} else {
							// Valeurs en sortie
							$this->addOutput([
								'redirect' => helper::baseUrl() . $this->getUrl() . '#comment',
								'notification' => $notification,
								'state' => true
							]);
						}
					}
				}
				// Ids des commentaires approuvés par ordre de publication
				$commentsApproved = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment']);
				if ($commentsApproved) {
					foreach ($commentsApproved as $key => $value) {
						if ($value['approval'] === false)
							unset($commentsApproved[$key]);
					}
					// Ligne suivante si affichage du nombre total de commentaires approuvés sous l'article
					self::$nbCommentsApproved = count($commentsApproved);
				}
				$commentIds = array_keys(helper::arrayColumn($commentsApproved, 'createdOn', 'SORT_ASC'));
				// Pagination
				$pagination = helper::pagination($commentIds, $this->getUrl(), $this->getData(['module', $this->getUrl(0), 'config', 'itemsperPage']), '#comment');
				// Liste des pages
				self::$pages = $pagination['pages'];
				// Signature du commentaire édité
				if ($this->isConnected() === true) {
					self::$editCommentSignature = $this->signature($this->getUser('id'));
				}
				// Commentaires en fonction de la pagination
				for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
					// Signatures des commentaires
					$e = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i], 'userId']);
					if ($e) {
						self::$commentsSignature[$commentIds[$i]] = $this->signature($e);
					} else {
						self::$commentsSignature[$commentIds[$i]] = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i], 'author']);
					}
					// Données du commentaire si approuvé
					if ($this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i], 'approval']) === true) {
						self::$comments[$commentIds[$i]] = $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'comment', $commentIds[$i]]);
					}
				}
				// Sections
				self::$boards = $this->getBoards();
				// Format de temps
				self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
				self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
				// Valeurs en sortie
				$this->addOutput([
					'showBarEditButton' => true,
					'title' => $this->getData(['module', $this->getUrl(0), 'posts', $this->getUrl(1), 'title']),
					'vendor' => [
						'tinymce'
					],
					'view' => 'article'
				]);
			}
		}
		// Liste des articles
		else {
			
// Liste des sujets : triés par activité (ou filtres)
$posts = (array) $this->getData(['module', $this->getUrl(0), 'posts']);
self::$boards = $this->getBoards();

// Filtres GET
$board = isset($_GET['board']) ? (string) $_GET['board'] : '';
$q = isset($_GET['q']) ? (string) $_GET['q'] : '';
$filter = isset($_GET['filter']) ? (string) $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'activity';

$board = preg_replace('/[^a-z0-9_-]/', '', strtolower(trim($board)));
$q = trim(mb_substr($q, 0, 80));
$filter = preg_replace('/[^a-z0-9_-]/', '', strtolower(trim($filter)));
$sort = preg_replace('/[^a-z0-9_-]/', '', strtolower(trim($sort)));
if ($sort === '') { $sort = 'activity'; }

self::$filterBoard = $board;
self::$filterQ = $q;
self::$filterFilter = $filter;
self::$filterSort = $sort;

$meta = [];
$ids = [];
foreach ($posts as $postId => $post) {
	if (!isset($post['publishedOn'], $post['state'])) { continue; }
	if ($post['publishedOn'] > time() || (bool) $post['state'] !== true) { continue; }

	$topicBoard = isset($post['board']) ? (string) $post['board'] : 'general';
	$topicBoard = $this->normalizeBoard($topicBoard);
	$locked = (bool) ($post['commentClose'] ?? false);
	$solved = (bool) ($post['solved'] ?? false);
	$pinned = (bool) ($post['pinned'] ?? false);

	// Filtre section
	if ($board !== '' && $topicBoard !== $board) { continue; }

	// Recherche simple
	if ($q !== '') {
		$hay = mb_strtolower((string) ($post['title'] ?? '') . ' ' . strip_tags((string) ($post['content'] ?? '')));
		if (mb_stripos($hay, mb_strtolower($q)) === false) { continue; }
	}

	// Réponses + dernière activité
	$lastOn = (int) ($post['publishedOn'] ?? 0);
	$lastBy = $this->signature($post['userId'] ?? '');
	$replies = 0;
	$comments = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'comment']);
	if (is_array($comments)) {
		foreach ($comments as $commentId => $commentValue) {
			$approved = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'comment', $commentId, 'approval']);
			if ($approved) {
				$replies++;
				$cOn = (int) $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'comment', $commentId, 'createdOn']);
				if ($cOn > $lastOn) {
					$lastOn = $cOn;
					$cUserId = $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'comment', $commentId, 'userId']);
					$lastBy = $cUserId ? $this->signature($cUserId) : (string) $this->getData(['module', $this->getUrl(0), 'posts', $postId, 'comment', $commentId, 'author']);
				}
			}
		}
	}
	self::$topicLastOn[$postId] = $lastOn;
	self::$topicLastBy[$postId] = $lastBy;
	self::$comments[$postId] = $replies;

	// Filtres métiers
	if ($filter === 'open' && $locked) { continue; }
	if ($filter === 'closed' && !$locked) { continue; }
	if ($filter === 'solved' && !$solved) { continue; }
	if ($filter === 'unsolved' && $solved) { continue; }
	if ($filter === 'noreply' && $replies > 0) { continue; }
	if ($filter === 'mine' && $this->isConnected() && ($post['userId'] ?? '') !== $this->getUser('id')) { continue; }
	if ($filter === 'mine' && !$this->isConnected()) { continue; }

	$meta[$postId] = [
		'lastOn' => $lastOn,
		'publishedOn' => (int) ($post['publishedOn'] ?? 0),
		'replies' => $replies,
		'board' => $topicBoard,
		'locked' => $locked,
		'solved' => $solved,
		'pinned' => $pinned,
	];
	$ids[] = $postId;
}

usort($ids, function($a, $b) use ($meta, $sort) {
	$pa = $meta[$a]['pinned'] ? 1 : 0;
	$pb = $meta[$b]['pinned'] ? 1 : 0;
	if ($pa !== $pb) { return $pb <=> $pa; }
	if ($sort === 'recent') {
		if ($meta[$a]['publishedOn'] !== $meta[$b]['publishedOn']) { return $meta[$b]['publishedOn'] <=> $meta[$a]['publishedOn']; }
	}
	if ($sort === 'replies') {
		if ($meta[$a]['replies'] !== $meta[$b]['replies']) { return $meta[$b]['replies'] <=> $meta[$a]['replies']; }
	}
	// défaut: activité
	if ($meta[$a]['lastOn'] !== $meta[$b]['lastOn']) { return $meta[$b]['lastOn'] <=> $meta[$a]['lastOn']; }
	return strcmp($b, $a);
});

$articleIds = $ids;

// Pagination
			$pagination = helper::pagination($articleIds, $this->getUrl(), $this->getData(['module', $this->getUrl(0), 'config', 'itemsperPage']), '#article');
			// Liste des pages
			self::$pages = $pagination['pages'];
			// Articles en fonction de la pagination
			for ($i = $pagination['first']; $i < $pagination['last']; $i++) {
				self::$articles[$articleIds[$i]] = $this->getData(['module', $this->getUrl(0), 'posts', $articleIds[$i]]);
			}
			// Sections
			self::$boards = $this->getBoards();
			// Format de temps
			self::$dateFormat = $this->getData(['module', $this->getUrl(0), 'config', 'dateFormat']);
			self::$timeFormat = $this->getData(['module', $this->getUrl(0), 'config', 'timeFormat']);
			// Valeurs en sortie
			$this->addOutput([
				'showBarEditButton' => true,
				'showPageContent' => false,
				'view' => 'index'
			]);
		}
	}


	/**
	 * Redirection vers la page d’inscription (Auto Inscription / Suscribe)
	 *
	 * Le bouton public « Créer un compte » pointe vers /forum/signup.
	 * Cette action évite les 404 :
	 *  - si une URL est fournie dans les options, redirection directe
	 *  - si un slug de page est fourni, redirection si la page existe
	 *  - sinon, en admin : redirection vers les options ; en visiteur : message minimal
	 */
	public function signup()
	{
		$signupPath = trim((string) $this->getData(['module', $this->getUrl(0), 'config', 'signupUrl']));

		// URL complète
		if ($signupPath !== '' && preg_match('~^https?://~', $signupPath)) {
			$this->addOutput([
				'redirect' => $signupPath
			]);
			return;
		}

		// Slug interne (page Zwii)
		$slug = ltrim($signupPath !== '' ? $signupPath : 'inscription', '/');
		$pageExists = ($slug !== '' && $this->getData(['page', $slug]) !== null);

		if ($pageExists) {
			$this->addOutput([
				'redirect' => helper::baseUrl() . $slug
			]);
			return;
		}

		// Admin : guide vers la configuration (évite une 404)
		if ($this->isConnected() && $this->getUser('role') >= self::ROLE_EDITOR) {
			$this->addOutput([
				'redirect' => helper::baseUrl() . $this->getUrl(0) . '/option',
				'notification' => 'Inscription à configurer (Options du module)',
				'state' => false
			]);
			return;
		}

		// Visiteur : message minimal
		$this->addOutput([
			'title' => 'Créer un compte',
			'view' => 'signup'
		]);
	}

}