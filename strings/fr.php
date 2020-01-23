<?php

const S_NOAPI = 'API is currently disabled';
const S_NOTCONFIGURED = 'Imageboard must be configured before usage';					//If config.php does not exist
const S_LOCKDOWN = 'Board is currently disabled. Please check back later';				//Lockdown
const S_HOME = 'Dernier poste'; // Transférer à la page d'accueil
const S_ADMIN = 'Admin'; // Transférer au panneau de gestion
const S_RETURN = 'Retour'; // retourne au tableau d'image
const S_POSTING = 'Mode de publication: Réponse'; // Imprime le message dans la barre rouge en haut de l'écran de réponse
const S_NOTAGS = 'Les balises HTML sont rapides.'; // Imprime un message sur le conseil d'administration
const S_NEWTHREAD = 'Nouvelle discussion'; // En-tête pour le nouveau formulaire de thread
const S_NAME = 'nom'; // Décrit le champ du nom
const S_CAPCODE = 'Capcode'; // Décrit le champ capcode (admin)
const S_REPLYTO = 'Répondre à'; // Décrit la réponse au champ (admin)
const S_EMAIL = 'E-mail'; // Décrit le champ e-mail
const S_SUBJECT = 'Sujet'; // Décrit le sujet
const S_SUBMIT = 'Poster'; // Décrit le bouton d'envoi
const S_COMMENT = 'Comment'; // Décrit le champ de commentaire
const S_OEKAKI = 'Oekaki';
const S_OEKAKILOAD = 'Cliquez pour charger oekaki (n oubliez pas de sauvegarder!)';
const S_UPLOADFILE = 'Fichier'; // Décrit le champ du fichier
const S_DELPASS = 'Mot de passe'; // Décrit le mot de passe
const S_DELEXPL = '(Mot de passe utilisé pour la suppression du fichier) '; // Imprime l explication du mot de passe (à droite)
const S_RULES = '<ul> <li>Vous pouvez poster tout ce que vous voulez tant que c est légal en France.</ li>
<li> Les formats de fichier supportés sont JPG, PNG et GIF. </ li>
<li> La taille de fichier maximal autorisé est '.MAX_KB.' KB. </ Li>
<li> Les images supérieures à '.MAX_W.'x'.MAX_H.' seront réduites. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_RULES_SWF = '<ul> <li>Vous pouvez poster tout ce que vous voulez tant que c est légal en France.</ li>
<li> Les types de fichiers pris en charge sont: GIF, JPG, PNG, SWF </ li>
<li> La taille de fichier maximale autorisée est '.MAX_KB.' KB. </ Li>
<li> Les images supérieures à '.MAX_W.'x'.MAX_H.' les pixels seront miniatures. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_RULES_WEBM = '<ul><li> Pas de contenue illégale en France ( Gore, Racisme, PedoPorn). </ li>
<li> Les spams/pubs seront supprimers. </ li>
<li> Les formats de fichier supportés sont JPG, PNG, GIF et WEBM. </ li>
<li> La taille de fichier maximal autorisé est '.MAX_KB.' KB. </ Li>
<li> Les images supérieures à '.MAX_W.'x'.MAX_H.' seront réduites. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_RULES_BOTH = '<ul> <li>Vous pouvez poster tout ce que vous voulez tant que c est légal en France.</ li>
<li> Les types de fichiers pris en charge sont: GIF, JPG, PNG, SWF, WEBM </ li>
<li> La taille de fichier maximale autorisée est '.MAX_KB.' KB. </ Li>
<li> Les images supérieures à '.MAX_W.'x'.MAX_H.' les pixels seront miniatures. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_SWF_DISABLED = "Erreur: le téléchargement des fichiers SWF est actuellement désactivé.";
const S_WEBM_DISABLED = "Erreur: le téléchargement des fichiers WebM est actuellement désactivé.";
const S_REPORTERR = 'Erreur: réponse introuvable.'; // Retourne une erreur lorsqu'une réponse (res) est introuvable
const S_THUMB = ''; // Imprime les instructions pour visualiser les sources réelles
const S_PICNAME = 'Fichier:'; // Imprime le texte avant de télécharger le nom / lien
const S_REPLY = 'Répondre'; // Imprime le texte du lien de réponse
const S_ABBR = ' Messages tronqués. Cliquez sur Répondre pour affichers '; // Imprime le texte à afficher lorsque les réponses sont masquées
const S_REPDEL = 'Supprimer un message'; // Imprime le texte à côté de S_DELPICONLY (à gauche)
const S_DELPICONLY = 'Fichier seulement'; // Imprime le texte à côté de la case à cocher pour la suppression du fichier (droite)
const S_DELKEY = 'Mot de passe'; // Imprime le texte à côté du champ mot de passe pour suppression (à gauche)
const S_DELETE = 'Supprimer'; // Définit le nom du bouton de suppression
const S_PREV = 'Précédent'; // Définit le bouton précédent
const S_NEXT = 'Suivant'; // Définit le bouton suivant
const S_REFRESH = 'Rafraîchir'; // Définit le bouton rafraîchir
const S_FOOT = '- GazouBBS + <a href="http://www.2chan.net/" target="_blank"> futaba </a> + <a href = "http://www.1chan.net/ futallaby / "target =" _ blank "> futallaby </a> + <a href="https://github.com/knarka/fikaba" target="_blank"> fikaba </a> + <a href ="https://github.com/MrBn100ful/fikaba" target =" _ blank "> Neonroot </a> - '; // Imprime le pied de page (laisse ces crédits)
const S_UPFAIL = 'Erreur: le téléchargement a échoué.'; // Retourne une erreur en cas d'échec du téléchargement (motif: inconnu?)
const S_NOREC = 'Erreur: enregistrement introuvable.'; // Retourne une erreur lorsque l'enregistrement est introuvable
const S_SAMEPIC = 'Erreur: somme de contrôle md5 en double détectée.'; // Retourne une erreur quand une dupe de somme de contrôle md5 est détectée
const S_TOOBIG = 'Cette image est trop grande! Téléchargez quelque chose de plus petit! ';
const S_TOOBIGORNONE = 'Soit cette image est trop grande ou il n y a pas d image du tout. Ouais. ';
const S_NODETECT = 'impossible de détecter le type d image (probablement parce que son type n est pas pris en charge)';
const S_UPGOOD = 'installé! <br /> <br />'; // Définit le message à afficher lorsque le fichier est chargé avec succès
const S_STRREF = 'Erreur: chaîne refusée.'; // retourne une erreur lorsqu'une chaîne est refusée
const S_UNJUST = 'Erreur: POST injuste.'; // Retourne une erreur sur un POST injuste - empêche les floodbots ou les moyens de ne pas utiliser la méthode POST?
const S_NOPIC = 'Erreur: Aucun fichier sélectionné.'; // Retourne l'erreur pour aucun fichier sélectionné et annule la case à cocher
const S_NOTEXT = 'Erreur: Aucun texte entré.'; // Renvoie une erreur pour aucun texte entré dans sujet / commentaire
const S_MANAGEMENT = 'Manager:'; // Définit le préfixe du nom du responsable du poste
const S_DELETION = 'Suppression'; // Imprime le message de suppression avec des guillemets?
const S_TOOLONG = 'Erreur: champ trop long.'; // Retourne une erreur pour trop de caractères dans un champ donné
const S_UNUSUAL = 'Erreur: Réponse anormale.'; // retourne une erreur trop longue pour $ resto ou $ url (ne devrait jamais arriver)
const S_BADHOST = 'Erreur: l hôte est banni.'; // Retourne une erreur pour l'hôte interdit (chaîne $ badip)
const S_PROXY80 = 'Erreur: Proxy détecté sur: 80.'; // Retourne une erreur pour la détection de proxy sur le port 80
const S_PROXY8080 = 'Erreur: proxy détecté sur: 8080.'; // Retourne une erreur pour la détection de proxy sur le port 8080
const S_SUN = 'Dim'; // Définit l'abréviation utilisée pour "dimanche"
const S_MON = 'Lun'; // Définit l'abréviation utilisée pour "lundi"
const S_TUE = 'Mar'; // Définit l'abréviation utilisée pour "mardi"
const S_WED = 'Mer'; // Définit l'abréviation utilisée pour "mercredi"
const S_THU = 'Jeu'; // Définit l'abréviation utilisée pour "jeudi"
const S_FRI = 'Ven'; // Définit l'abréviation utilisée pour "vendredi"
const S_SAT = 'Sam'; // Définit l'abréviation utilisée pour "samedi"
const S_ANONAME = 'Anonymous'; // Définit quoi imprimer s'il n'y a pas de texte entré dans le champ du nom
const S_ANOTEXT = 'Pas de texte'; // Définit quoi imprimer s'il n'y a pas de texte entré dans le champ de commentaire
const S_ANOTITLE = 'Pas de sujet'; // Définit quoi imprimer s'il n'y a pas de texte entré dans le champ sujet
const S_ANOFILE = 'nom de fichier inconnu';
const S_RENZOKU = 'Erreur: Spam détectée, post rejetée.'; // Retourne une erreur pour le filtre $ sec / post spam
const S_RENZOKU2 = 'Erreur: Spam détectée, fichier ignoré.'; // Retourne une erreur pour le filtre spam de $ sec / upload
const S_RENZOKU3 = 'Erreur: Spam détectée.'; // Retourne une erreur pour flood? (je ne connais pas les détails)
const S_DUPE = 'Erreur: Une entrée de fichier dupliquée a été détectée.'; // Retourne une erreur pour un fichier dupliqué (même nom de téléchargement ou même tim / time)
const S_NOTHREADERR = 'Erreur: le thread spécifié n existe pas.'; // Retourne une erreur en cas d'accès à un thread inexistant
const S_SCRCHANGE = 'Mise à jour de la page.'; // Définit le message à afficher lorsque la publication est réussie //
const S_BADDELPASS = 'Erreur: mot de passe incorrect.'; // Retourne une erreur pour un mot de passe incorrect (lorsque l'utilisateur essaie de supprimer un fichier)
const S_WRONGPASS = 'Erreur: mot de passe de gestion incorrect.'; // Retourne une erreur si le mot de passe est incorrect (lors d'une tentative d'accès aux modes du gestionnaire)
const S_MANALOGGEDIN = 'Vous êtes maintenant connecté.';
const S_RETURNS = 'Retour'; // retourne au fichier HTML au lieu de PHP - donc pas de mise à jour du journal / SQLDB
const S_LOGUPD = 'Reconstruire'; // Met à jour le journal / SQLDB en accédant au fichier PHP
const S_MANAMODE = 'Mode gestionnaire'; // Imprime l'en-tête en haut de la page du gestionnaire.
const S_LOGOUT = 'Déconnexion'; // bouton de déconnexion dans le panneau de gestion
const S_MANAREPDEL = 'Panneau de suppression'; // Bouton Définir le panneau de gestion - permet à l'utilisateur de visualiser le panneau de gestion (aperçu de tous les articles)
const S_MANABAN = 'Panneau d interdiction'; // Bouton d'interdiction du gestionnaire
const S_MANAPOST = 'Post Manager'; // Bouton Définir la publication du gestionnaire - permet à l'utilisateur de publier en utilisant le code HTML dans la zone de commentaire.
const S_MANAACCS = 'Gestion de compte'; // Bouton Définir pour ajouter / supprimer des comptes de gestionnaires
const S_MANASUB = 'Soumettre'; // Définit le nom du bouton d'envoi en mode gestionnaire
const S_ITDELETES = 'Supprimer'; // Définit le bouton de suppression dans le panneau de gestion
const S_MDONLYPIC = 'Fichier seulement'; // Définit s'il faut ou non supprimer uniquement le fichier ou l'intégralité du post / thread
const S_MDTABLE1 = '<th> Supprimer? </ th> <th> N ° de publication </ th> <th> Heure </ th> <th> Sujet </ th>'; // Explai
const S_MDTABLE2 = '<th> Nom </ th> <th> IP </ th> <th> Commentaire </ th> <th> Hôte </ th> <th> Taille <br /> (Octets) </ th > <th> md5 </ th> <th> # de réponse </ th> <th> horodatage (s) </ th> <th> horodatage (ms) </ th> '; // Explique les noms du panneau de gestion (Nom-> md5)
const S_RESET = 'Réinitialiser'; // Définit le nom du bouton de réinitialisation de champ (global)
const S_IMGSPACEUSAGE = 'Espace utilisé:'; // Imprime l'espace utilisé en Ko par le conseil sous le panneau de gestion
const S_SQLCONF = 'Echec de la connexion MySQL'; // échec de la connexion MySQL
const S_SQLDBSF = 'Erreur de base de données, vérifiez les paramètres SQL <br />'; // échec de sélection de base de données
const S_TCREATE = "Création de la table! <br /> \ n"; // Création de table
const S_FCREATE = "Création du dossier! <br /> \ n"; // Création de table
const S_TCREATEF = 'Impossible de créer la table! <br />'; // La création de la table a échoué
const S_SQLFAIL = 'Problème SQL critique! <br />'; // Échec SQL
const S_BANRENZOKU = 'Erreur: vous êtes banni. Message mis au rebut. Vérifiez le statut de votre interdiction <a href="?mode=banned"> ici </a>. '; // Erreur affichée pour l'utilisateur banni lorsqu'il tente de poster
const S_BANNEDMESSAGE = 'Vous êtes banni!';
const S_NOTBANNED = 'Vous n êtes pas banni. IP: ';
const S_BANTIME = 'Vous avez été banni:';
const S_BANEXPIRE = 'Votre interdiction expire le:';
const S_BANSUCCESS = 'Utilisateur banni';
const S_UNBANSUCCESS = 'Utilisateur non planifié';
const S_MANABANIP = 'IP ou post n °:';
const S_MANABANEXP = 'Expire dans (jours):';
const S_MANABANPUBMSG = 'Raison publique (seulement si non interdite):';
const S_MANABANPRIVMSG = 'Raison privée:';
const S_MANARMP = 'Supprimer le post? ';
const S_MANARMALLP = 'Supprimer tous les messages de cette adresse IP? ';
const S_MANAUNBAN = 'Unban au lieu de ban';
const S_BANEXPERROR = 'Merci de donner un certain nombre de jours pour interdire cet utilisateur pour.';
const S_NOSUCHPOST = 'Le message pour lequel vous essayez de bannir n \' existe pas. ';
const S_BANEXPIRED = 'Votre interdiction a expiré et a été supprimée de la base de données.';
const S_BANNEDMSG = 'l utilisateur a été banni pour ce poste'; // Message public par défaut pour les interdictions
const S_CATALOG = "Mode d'affichage: Catalogue";
const S_CATALOGBUTTON = "Catalogue";
const S_NOPERMISSION = 'Vous ne disposez pas des autorisations nécessaires pour le faire.'; // texte affiché lors d'une tentative d'accès illégal à une partie du panneau de gestion
const S_ACCCREATED = 'Compte créé avec succès!';
const S_ACCDEL = 'Peut supprimer des publications? ';
const S_ACCBAN = 'Peut interdire les utilisateurs? ';
const S_ACCCAP = 'Peut-on poster avec capcode? ';
const S_ACCACC = 'Peut créer de nouveaux comptes? ';
const S_VERSION = 'version';
const S_NAMEVERSION= 'Beta 1.4';