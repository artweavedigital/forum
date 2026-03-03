# Module Forum pour ZwiiCMS — v2.0.4

Un forum léger (flat-file) basé sur la logique « sujet = article / réponse = commentaire », avec une vraie ergonomie forum : tri par dernière activité, sections, filtres, badges, citation, modération globale, anti-flood, et un style “chic” intégré.

---

## Compatibilité

- **ZwiiCMS** : 14.x (testé sur 14.1.06)
- **PHP** : 8.3+
- **Stockage** : flat-file JSON (aucune base de données)

---

## Fonctionnalités

### Côté visiteurs
- Lecture des sujets (si la page Forum est accessible aux visiteurs).
- Bouton **Nouvelle discussion** visible : renvoie vers **Connexion** si nécessaire.
- Bouton **Créer un compte** (si configuré) : renvoie vers la page d’auto-inscription.

### Côté membres (connectés)
- **Créer un sujet**
- **Répondre** à un sujet
- **Éditer** son propre sujet (selon réglages)
- **Supprimer** son sujet uniquement s’il n’a **aucune réponse** (sécurité)

### Modération (Éditeur / Admin)
- **Épingler** un sujet
- **Fermer / rouvrir** un sujet
- **Marquer résolu**
- **Modération globale** : file de réponses en attente (si approbation activée)

### Confort & sécurité
- Tri des sujets par **dernière activité**
- **Sections / sous-forums**
- **Filtres + recherche**
- **Citer** une réponse (insertion automatique dans l’éditeur)
- **Anti-flood** (délai entre messages)
- **Honeypot** anti-spam (champ invisible)

---

## Installation

1. Dans l’administration ZwiiCMS : **Extensions → Modules → Installer**
2. Importer le fichier ZIP du module Forum.
3. Vérifier que le dossier est présent :
   - `module/forum/`

> Conseil : lors d’une mise à jour, supprime d’abord l’ancien dossier `module/forum/` pour éviter un mélange de fichiers.

---

## Création de la page « Forum »

1. **Pages → Ajouter une page**
2. Nom : `Forum`
3. Type : sélectionner le module **Forum**
4. Accès :
   - **Visiteur** : forum public en lecture (publication réservée aux comptes)
   - **Membre** : forum privé

> Pour éviter le texte « Contenu de votre nouvelle page », désactive l’option “Afficher le contenu de page” si elle est proposée dans ta configuration de page.

---

## Configuration du module (Options)

Ouvre la configuration du module sur la page Forum (icône engrenage / configuration), puis règle :

### 1) URL d’inscription (important)
Permet au bouton **Créer un compte** de fonctionner correctement.

- Valeur attendue : **slug** de ta page d’inscription (ex. `inscription`)
- Ou une URL complète si tu préfères.

Recommandation :
- Installer le module ZwiiCMS d’auto-inscription, créer une page `inscription`, puis mettre `inscription` ici.

### 2) Sections (sous-forums)
Format simple : une section par ligne, au choix :

- `Général|general`
- `Support|support`
- `Écriture|ecriture`

La partie après `|` sert d’identifiant stable (utile pour tri/filtre).

### 3) Anti-flood
Réglage recommandé (valeurs indicatives) :
- **Sujet** : 60 secondes
- **Réponse** : 20 secondes

---

## Utilisation

### Créer un nouveau sujet
- Sur la page Forum, cliquer **Nouvelle discussion**.
- Si l’utilisateur n’est pas connecté : redirection vers la **Connexion**, puis retour automatique à la création du sujet.

### Répondre
- Ouvrir un sujet → rédiger une réponse → publier.
- Si le sujet est **fermé**, la zone de réponse est remplacée par un message clair.

### Citer une réponse
- Cliquer **Citer** sur une réponse.
- Le module insère automatiquement un bloc de citation dans l’éditeur (avec auteur et date).

---

## Badges et état des sujets

- **Épinglé** : sujet maintenu en haut de liste.
- **Résolu** : utile pour support / questions.
- **Fermé** : empêche les nouvelles réponses.

---

## Modération globale

Une page dédiée permet de traiter toutes les réponses en attente (si approbation activée) :
- Approver / supprimer sans ouvrir chaque sujet.

---

## Personnalisation du style

Le module embarque un style “chic” basé sur des variables CSS (`--primary`, `--secondary`, `--gold`, etc.).

### Adapter le rendu sans casser le module
1. Ajoute tes surcharges dans :
   - `site/data/custom.css` ou `site/data/theme.css` (selon ta structure)
2. Cible uniquement les classes du forum :
   - `.forumWrap`, `.forumRow`, `.forumStats`, `.forumBtn`, `.forumBadge`, etc.

> Évite de modifier directement les fichiers du module si tu veux rester à l’aise lors des mises à jour.

---

## Dépannage

### « Créer un compte » mène à une 404
- Vérifie l’option **URL d’inscription** du module.
- Vérifie que la page d’inscription existe et que son slug est correct.

### Accents cassés (ex. « DerniÃ¨re activité »)
- Mets à jour vers la dernière version du module.
- Vérifie aussi que tes fichiers locaux sont enregistrés en **UTF-8**.

### Affichage “brut” ou style non appliqué
- Vide le cache navigateur : **Ctrl + F5**
- Supprime l’ancien dossier `module/forum/` avant réinstallation du ZIP.

### Les visiteurs peuvent publier
- Vérifie l’accès de la page Forum : lecture possible, mais les actions `/add` et publication restent protégées côté module.
- Vérifie aussi la configuration des rôles (Membre minimum pour poster).

---

## Structure des données (résumé)

- Sujets, options et index stockés en JSON dans les données du site.
- Aucune base SQL, aucune dépendance externe obligatoire.

---

## Licence
Licence GPL-v3

---

Si tu veux, je te fais aussi :
- un **guide utilisateur** (côté membres) en 1 page,
- un **guide modération** (côté admin) avec captures/étapes,

- une **fiche “installation express”** pour le store.
