<?php

const S_NOAPI = 'API is currently disabled';
const S_NOTCONFIGURED = 'Imageboard must be configured before usage';					//If config.php does not exist
const S_LOCKDOWN = 'Board is currently disabled. Please check back later';				//Lockdown
const S_HOME = 'Strona Główna'; // Przenosi na główną
const S_ADMIN = 'Panel Moderatora'; // Przenosi na panel moderatora (przycisk nie jest dostępny)
const S_RETURN = 'Powrót'; // retourne au tableau d'image
const S_POSTING = 'Tryb postowania: Odpowiedź'; // Imprime le message dans la barre rouge en haut de l'écran de réponse
const S_NOTAGS = 'znaki HTML są dozwolone.'; // Imprime un message sur le conseil d'administration
const S_NEWTHREAD = 'Nowy post'; // En-tête pour le nouveau formulaire de thread
const S_NAME = 'Nick'; // Décrit le champ du nom
const S_CAPCODE = 'Capcode'; // Décrit le champ capcode (admin)
const S_REPLYTO = 'Odpowiedz do'; // Décrit la réponse au champ (admin)
const S_EMAIL = 'E-mail'; // Décrit le champ e-mail
const S_SUBJECT = 'Temat'; // Décrit le sujet
const S_SUBMIT = 'Utwórz'; // Décrit le bouton d'envoi
const S_COMMENT = 'Wiadomość'; // Décrit le champ de commentaire
const S_OEKAKI = 'Oekaki';
const S_OEKAKILOAD = 'Cliquez pour charger oekaki (n oubliez pas de sauvegarder!)';
const S_UPLOADFILE = 'Dodaj plik'; // Décrit le champ du fichier
const S_DELPASS = 'Hasło'; // Décrit le mot de passe
const S_DELEXPL = '(do usuwania posta) '; // Imprime l explication du mot de passe (à droite)
const S_RULES = '<ul> <li>Tworzymy posty zgodne z polskim prawem.</ li>
<li> Wspierane formaty plików to JPG, PNG i GIF. </ li>
<li> Mogą ważyc maksymalnie '.MAX_KB.' KB. </ Li>
<li> Obrazki większe od '.MAX_W.'x'.MAX_H.' będą pomniejszane. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_RULES_SWF = '<ul> <li>Tworzymy posty zgodne z polskim prawem.</ li>
<li> Wspierane formaty plików to: GIF, JPG, PNG, SWF </ li>
<li> Mogą ważyc maksymalnie '.MAX_KB.' KB. </ Li>
<li> Obrazki większe od '.MAX_W.'x'.MAX_H.' będą pomniejszane. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_RULES_WEBM = '<ul><li> Tworzymy posty zgodne z polskim prawem. </ li>
<li> Usuwamy spam. </ li>
<li> Wspierane formaty plików to JPG, PNG, GIF i WEBM. </ li>
<li> Pliki mogą ważyc maksymalnie  '.MAX_KB.' KB. </ Li>
<li> Obrazki większe od '.MAX_W.'x'.MAX_H.' będą pomniejszane. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_RULES_BOTH = '<ul> <li>Tworzymy posty zgodne z polskim prawem.</ li>
<li> Wspierane formaty plików to: GIF, JPG, PNG, SWF, WEBM </ li>
<li> Mogą ważyc maksymalnie '.MAX_KB.' KB. </ Li>
<li> Obrazki większe od '.MAX_W.'x'.MAX_H.' będą pomniejszane. </ li> </ ul> '; // Imprime les règles sous la section de publication
const S_SWF_DISABLED = "Błąd. Pliki SWF nie są wspierane";
const S_WEBM_DISABLED = "Błąd. Pliki WEBM nie są wspierane (a powinny)";
const S_REPORTERR = 'Błąd. Nie można znaleźć odpowiedzi.'; // Retourne une erreur lorsqu'une réponse (res) est introuvable
const S_THUMB = ''; // Imprime les instructions pour visualiser les sources réelles
const S_PICNAME = 'Plik:'; // Imprime le texte avant de télécharger le nom / lien
const S_REPLY = 'Odpowiedz'; // Imprime le texte du lien de réponse
const S_ABBR = ' postów ominięto. Kliknij przycisk [Odpowiedź] aby je pokazać.'; // Imprime le texte à afficher lorsque les réponses sont masquées
const S_REPDEL = 'Usuń post'; // Imprime le texte à côté de S_DELPICONLY (à gauche)
const S_DELPICONLY = 'Tylko plik'; // Imprime le texte à côté de la case à cocher pour la suppression du fichier (droite)
const S_DELKEY = 'Hasło'; // Imprime le texte à côté du champ mot de passe pour suppression (à gauche)
const S_DELETE = 'Usuń'; // Définit le nom du bouton de suppression
const S_PREV = 'Poprzedni'; // Définit le bouton précédent
const S_NEXT = 'Następny'; // Définit le bouton suivant
const S_REFRESH = 'Odśwież'; // Définit le bouton rafraîchir
const S_FOOT = '- GazouBBS + <a href="http://www.2chan.net/" target="_blank"> futaba </a> + <a href = "http://www.1chan.net/ futallaby / "target =" _ blank "> futallaby </a> + <a href="https://github.com/knarka/fikaba" target="_blank"> fikaba </a> + <a href ="https://github.com/MrBn100ful/fikaba" target =" _ blank "> Neonroot </a> -'; // Imprime le pied de page (laisse ces crédits)
const S_UPFAIL = 'Błąd. Nie udało się wysłać pliku'; // Retourne une erreur en cas d'échec du téléchargement (motif: inconnu?)
const S_NOREC = 'Błąd. Nie udało się znaleźć nagrania.'; // Retourne une erreur lorsque l'enregistrement est introuvable
const S_SAMEPIC = 'Błąd. Wrzucono już taki sam plik.'; // Retourne une erreur quand une dupe de somme de contrôle md5 est détectée
const S_TOOBIG = 'Błąd. Plik waży zbyt dużo! ';
const S_TOOBIGORNONE = 'Błąd. Nie dodano obrazka, lub jest zbyt duży. ';
const S_NODETECT = 'Nie można odnaleźć pliku! Prawdopodobnie jego typ nie jest wspierany.';
const S_UPGOOD = 'Dodano plik! <br /> <br />'; // Définit le message à afficher lorsque le fichier est chargé avec succès
const S_STRREF = 'Error: String refused.'; // retourne une erreur lorsqu'une chaîne est refusée
const S_UNJUST = 'Error: Unjust POST.'; // Retourne une erreur sur un POST injuste - empêche les floodbots ou les moyens de ne pas utiliser la méthode POST?
const S_NOPIC = 'Błąd. Nie wybrano pliku.'; // Retourne l'erreur pour aucun fichier sélectionné et annule la case à cocher
const S_NOTEXT = 'Błąd. Nie dodano wiadomości.'; // Renvoie une erreur pour aucun texte entré dans sujet / commentaire
const S_MANAGEMENT = 'Manager:'; // Définit le préfixe du nom du responsable du poste
const S_DELETION = 'Usuwanie'; // Imprime le message de suppression avec des guillemets?
const S_TOOLONG = 'Błąd. Zbyt dużo tekstu.'; // Retourne une erreur pour trop de caractères dans un champ donné
const S_UNUSUAL = 'Błąd. Nieprawidłowa wiadomość.'; // retourne une erreur trop longue pour $ resto ou $ url (ne devrait jamais arriver)
const S_BADHOST = 'Błąd. Jesteś zbanowany! ;_;'; // Retourne une erreur pour l'hôte interdit (chaîne $ badip)
const S_PROXY80 = 'Błąd. Wykryto proxy! port: 80.'; // Retourne une erreur pour la détection de proxy sur le port 80
const S_PROXY8080 = 'Błąd. Wykryto proxy! port: 8080.'; // Retourne une erreur pour la détection de proxy sur le port 8080
const S_SUN = 'Nd'; // Définit l'abréviation utilisée pour "dimanche"
const S_MON = 'Pon'; // Définit l'abréviation utilisée pour "lundi"
const S_TUE = 'Wt'; // Définit l'abréviation utilisée pour "mardi"
const S_WED = 'Śr'; // Définit l'abréviation utilisée pour "mercredi"
const S_THU = 'Czw'; // Définit l'abréviation utilisée pour "jeudi"
const S_FRI = 'Pt'; // Définit l'abréviation utilisée pour "vendredi"
const S_SAT = 'Sob'; // Définit l'abréviation utilisée pour "samedi"
const S_ANONAME = 'Anonymous'; // Définit quoi imprimer s'il n'y a pas de texte entré dans le champ du nom
const S_ANOTEXT = 'brak tekstu'; // Définit quoi imprimer s'il n'y a pas de texte entré dans le champ de commentaire
const S_ANOTITLE = 'brak tematu'; // Définit quoi imprimer s'il n'y a pas de texte entré dans le champ sujet
const S_ANOFILE = 'nieznana nazwa pliku';
const S_RENZOKU = 'Błąd. Możesz pisać co 20 sekund'; // Retourne une erreur pour le filtre $ sec / post spam
const S_RENZOKU2 = 'Błąd. Wykryto spam. Plik został usunięty.'; // Retourne une erreur pour le filtre spam de $ sec / upload
const S_RENZOKU3 = 'Błąd. Wykryto spam.'; // Retourne une erreur pour flood? (je ne connais pas les détails)
const S_DUPE = 'Błąd. Wykryto duplikat posta.'; // Retourne une erreur pour un fichier dupliqué (même nom de téléchargement ou même tim / time)
const S_NOTHREADERR = 'Błąd. Dany post nie istnieje.'; // Retourne une erreur en cas d'accès à un thread inexistant
const S_SCRCHANGE = 'Odświeżam stronę...'; // Définit le message à afficher lorsque la publication est réussie //
const S_BADDELPASS = 'Błąd. Złe hasło.'; // Retourne une erreur pour un mot de passe incorrect (lorsque l'utilisateur essaie de supprimer un fichier)
const S_WRONGPASS = 'Błąd. Złe hasło do panelu ADM.'; // Retourne une erreur si le mot de passe est incorrect (lors d'une tentative d'accès aux modes du gestionnaire)
const S_MANALOGGEDIN = 'Zalogowano (zostaniesz za chwilę przeniesiony)';
const S_RETURNS = 'Powrót'; // retourne au fichier HTML au lieu de PHP - donc pas de mise à jour du journal / SQLDB
const S_LOGUPD = 'Rebuild'; // Met à jour le journal / SQLDB en accédant au fichier PHP
const S_MANAMODE = 'Manager Mode'; // Imprime l'en-tête en haut de la page du gestionnaire.
const S_LOGOUT = 'Logout'; // bouton de déconnexion dans le panneau de gestion
const S_MANAREPDEL = 'Manager Post'; // Bouton Définir le panneau de gestion - permet à l'utilisateur de visualiser le panneau de gestion (aperçu de tous les articles)
const S_MANABAN = 'Ban Panel'; // Bouton d'interdiction du gestionnaire
const S_MANAPOST = 'Post Manager'; // Bouton Définir la publication du gestionnaire - permet à l'utilisateur de publier en utilisant le code HTML dans la zone de commentaire.
const S_MANAACCS = 'Account Management'; // Bouton Définir pour ajouter / supprimer des comptes de gestionnaires
const S_MANASUB = 'Submit'; // Définit le nom du bouton d'envoi en mode gestionnaire
const S_ITDELETES = 'Delete'; // Définit le bouton de suppression dans le panneau de gestion
const S_MDONLYPIC = 'File Only'; // Définit s'il faut ou non supprimer uniquement le fichier ou l'intégralité du post / thread
const S_MDTABLE1 = '<th> Delete? </ th> <th> Post No. </ th> <th> Time </ th> <th> Subject </ th>'; // Explai
const S_MDTABLE2 = '<th> Name </ th> <th> IP </ th> <th> Message </ th> <th> Host </ th> <th> Size <br /> (bytes) </ th > <th> md5 </ th> <th> # Reply </ th> <th> Timestamp (s) </ th> <th> Timestamp (ms) </ th> '; // Explique les noms du panneau de gestion (Nom-> md5)
const S_RESET = 'Reset'; // Définit le nom du bouton de réinitialisation de champ (global)
const S_IMGSPACEUSAGE = 'Space used:'; // Imprime l'espace utilisé en Ko par le conseil sous le panneau de gestion
const S_SQLCONF = 'MySQL error'; // échec de la connexion MySQL
const S_SQLDBSF = 'SQL parameters error <br />'; // échec de sélection de base de données
const S_TCREATE = "Création de la table! <br /> \ n"; // Création de table
const S_FCREATE = "Création du dossier! <br /> \ n"; // Création de table
const S_TCREATEF = 'Impossible de créer la table! <br />'; // La création de la table a échoué
const S_SQLFAIL = 'Problème SQL critique! <br />'; // Échec SQL
const S_BANRENZOKU = 'Błąd: Zostałeś zbanowany!. Wiadomość usunięto. <a href="?mode=banned">Szczegóły</a>. '; // Erreur affichée pour l'utilisateur banni lorsqu'il tente de poster
const S_BANNEDMESSAGE = 'Zostałeś zbanowany! ;_;';
const S_NOTBANNED = 'Nie jesteś zbanowany. Twoje IP: ';
const S_BANTIME = 'Czas bana:';
const S_BANEXPIRE = 'Ban skończy się:';
const S_BANSUCCESS = 'Zbanowano pomyślnie';
const S_UNBANSUCCESS = 'Odbanowano pomyślnie';
const S_MANABANIP = 'IP or post No.:';
const S_MANABANEXP = 'Expire:';
const S_MANABANPUBMSG = 'Powód publiczny:';
const S_MANABANPRIVMSG = 'Powód prywatny:';
const S_MANARMP = 'Usunąć post? ';
const S_MANARMALLP = 'Usunąc posty utworzone przez to IP? ';
const S_MANAUNBAN = 'Unban zamiast bana';
const S_BANEXPERROR = 'Wystąpił błąd; czas bana.';
const S_NOSUCHPOST = 'Ten post nie istnieje. ';
const S_BANEXPIRED = 'Czas bana się skończył.';
const S_BANNEDMSG = 'UŻYTKOWNIK ZOSTAŁ ZBANOWANY ZA TEN POST'; // Message public par défaut pour les interdictions
const S_CATALOG = "Typ przeglądania: Katalog";
const S_CATALOGBUTTON = "Katalog";
const S_NOPERMISSION = 'Nie masz uprawnień.'; // texte affiché lors d'une tentative d'accès illégal à une partie du panneau de gestion
const S_ACCCREATED = 'Konto zostało utworzone!';
const S_ACCDEL = 'Może usuwać posty? ';
const S_ACCBAN = 'Może banować? ';
const S_ACCCAP = 'Może używać capcode? ';
const S_ACCACC = 'Może tworzyć konta? ';
const S_VERSION = 'version';
const S_NAMEVERSION= 'Beta 1.4';
