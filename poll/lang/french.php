<?php
global $lang_poll, $weekday_poll, $months_poll, $color_array_poll;
# Advanced Poll Language File (Admin) #
# French translation by Bruno Kerouanton
# E-mail: contact@infraspec.com
# Date: 26 dec 2001
 

/* Charset */ 
$lang_poll["charset"]   = "iso-8859-1"; 

/* General */ 
$lang_poll["Logout"]    = "Se d�connecter"; 
$lang_poll["FormUndo"]  = "Annuler"; 
$lang_poll["FromClear"] = "Effacer"; 
$lang_poll["FormEnter"] = "Veuillez entrer un nom et un mot de passe valides :"; 
$lang_poll["FormWrong"] = "Utilisateur ou mot de passe erron�s"; 
$lang_poll["FormOK"]    = "OK"; 
$lang_poll["Updated"]   = "Changements mis � jour!"; 
$lang_poll["NoUpdate"]  = "Une erreur est survenue! Aucun changement n'a �t� fait!"; 
$lang_poll["Confirm"]   = "Etes-vous s�r?"; 
$lang_poll["NavNext"]   = "Page suivante"; 
$lang_poll["NavPrev"]   = "Page pr�c�dente"; 
$lang_poll["License"]   = "Contrat de License"; 
$lang_poll["ScrollTxt"] = "Appuyer sur PAGE DOWN pour lire le reste du contrat."; 

/* Templates */ 
$lang_poll["Templates"]  = "Mod�les"; 
$lang_poll["tpl_exist"]  = "Le nom de ce mod�le existe d�j�."; 
$lang_poll["tpl_new"]    = "Ajouter un nouveau mod�le."; 
$lang_poll["tpl_succes"] = "Enregistrement ajout�!"; 
$lang_poll["tpl_bad"]    = "Le nom du mod�le est invalide!"; 
$lang_poll["tpl_save"]   = "Sauver"; 
$lang_poll["preview"]    = "Aper�u"; 
$lang_poll["newtpl"]     = "Nouveau mod�le"; 

/* Poll List */ 
$lang_poll["IndexTitle"]  = "Liste des sondages"; 
$lang_poll["IndexQuest"]  = "Question"; 
$lang_poll["IndexID"]     = "ID du sondage"; 
$lang_poll["IndexDate"]   = "Date"; 
$lang_poll["IndexDays"]   = "Jours"; 
$lang_poll["IndexExp"]    = "Date d'expiration"; 
$lang_poll["IndexExpire"] = "p�rim�"; 
$lang_poll["IndexNever"]  = "jamais"; 
$lang_poll["IndexStat"]   = "Stats"; 
$lang_poll["IndexCom"]    = "Commentaires"; 
$lang_poll["IndexAct"]    = "Action"; 
$lang_poll["IndexDel"]    = "supprimer"; 

/* Create A New Poll */ 
$lang_poll["NewTitle"]  = "Cr�er un nouveau sondage"; 
$lang_poll["NewOption"] = "Option"; 
$lang_poll["NewNoQue"]  = "Vous n'avez pas rempli le champ Question"; 
$lang_poll["NewNoOpt"]  = "Vous n'avez pas rempli le champ Option"; 

/* Poll Edit */ 
$lang_poll["EditStat"]  = "Etat"; 
$lang_poll["EditText"]  = "Editer le sondage"; 
$lang_poll["EditReset"] = "R�initialiser le sondage"; 
$lang_poll["EditOn"]    = "activ�"; 
$lang_poll["EditOff"]   = "d�sactiv�"; 
$lang_poll["EditHide"]  = "masqu�"; 
$lang_poll["EditLgOff"] = "journalisation d�sactiv�e"; 
$lang_poll["EditLgOn"]  = "journalisation activ�e"; 
$lang_poll["EditAdd"]   = "Ajouter des options"; 
$lang_poll["EditNo"]    = "Aucune option n'a �t� ajout�e!"; 
$lang_poll["EditOk"]    = "Options ajout�es!"; 
$lang_poll["EditSave"]  = "Sauvegarder les changements"; 
$lang_poll["EditOp"]    = "Au moins deux options sont n�cessaires!"; 
$lang_poll["EditMis"]   = "La question et les options ne sont pas d�finies!"; 
$lang_poll["EditDel"]   = "Pour supprimer une option laisser le champ correspondant vide"; 
$lang_poll["EditCom"]   = "Autoriser les commentaires"; 

/* General Settings */ 
$lang_poll["SetTitle"]   = "Param�tres G�n�raux"; 
$lang_poll["SetOption"]  = "Options des tables, fontes et couleurs"; 
$lang_poll["SetMisc"]    = "Divers"; 
$lang_poll["SetText"]    = "Modifier les param�tres g�n�raux"; 
$lang_poll["SetURL"]     = "URL vers le r�pertoire des images"; 
$lang_poll["SetBURL"]    = "URL vers le r�pertoire des sontages"; 
$lang_poll["SetNo"]      = "Ne pas finir pas un slash"; 
$lang_poll["SetLang"]    = "Langue"; 
$lang_poll["SetPoll"]    = "Titre du sondage"; 
$lang_poll["SetButton"]  = "Bouton de vote"; 
$lang_poll["SetResult"]  = "Lien vers les r�esultats"; 
$lang_poll["SetVoted"]   = "A d�j� vot�"; 
$lang_poll["SetComment"] = "Envoyez votre commentaire"; 
$lang_poll["SetTab"]     = "Largeur de table"; 
$lang_poll["SetBarh"]    = "Hauteur de barre"; 
$lang_poll["SetBarMax"]  = "Longeur maxi. de barre"; 
$lang_poll["SetTabBg"]   = "Couleur de fond de la table"; 
$lang_poll["SetFrmCol"]  = "Couleur de la frame"; 
$lang_poll["SetFontCol"] = "Couleur de la fonte"; 
$lang_poll["SetFace"]    = "Type de la Fonte"; 
$lang_poll["SetShow"]    = "Afficher les r�sultats"; 
$lang_poll["SetPerc"]    = "pourcentages"; 
$lang_poll["SetVotes"]   = "votes"; 
$lang_poll["SetCheck"]   = "V�rifier"; 
$lang_poll["SetNoCheck"] = "ne pas v�rifier"; 
$lang_poll["SetIP"]      = "Table des IP"; 
$lang_poll["CheckIP"]       = "Check IP";
$lang_poll["CheckUsername"] = "Check username";
$lang_poll["SetTime"]    = "D�lai de verrouillage"; 
$lang_poll["SetHours"]   = "heures"; 
$lang_poll["SetOffset"]  = "Offset de temps du serveur"; 
$lang_poll["SetEntry"]   = "Nombre de commentaires par page"; 
$lang_poll["SetSubmit"]  = "Envoyer les r�glages"; 
$lang_poll["SetEmpty"]   = "Valeur invalide"; 
$lang_poll["SetSort"]    = "Ordre d'affichage"; 
$lang_poll["SetAsc"]     = "ascendant"; 
$lang_poll["SetDesc"]    = "descendant"; 
$lang_poll["Setusort"]   = "ne pas trier"; 
$lang_poll["SetOptions"] = "Options autoris�es dans les nouveaux sondages"; 
$lang_poll["SetPolls"]   = "Sondages par page"; 

/* Change Password */ 
$lang_poll["PwdTitle"] = "Changer de mot de passe"; 
$lang_poll["PwdText"]  = "Changer d'utilisateur ou de mot de passe"; 
$lang_poll["PwdUser"]  = "Utilisateur"; 
$lang_poll["PwdPass"]  = "Mot de passe"; 
$lang_poll["PwdConf"]  = "Confirmer le mot de passe"; 
$lang_poll["PwdNoUsr"] = "Vous n'avez pas rempli le champ Utilisateur"; 
$lang_poll["PwdNoPwd"] = "Vous n'avez pas rempli le champ Mot de passe"; 
$lang_poll["PwdBad"]   = "Les mots de passe ne sont pas identiques"; 

/* Poll Stats */ 
$lang_poll["StatCrea"]  = "Cr��"; 
$lang_poll["StatAct"]   = "Actif"; 
$lang_poll["StatReset"] = "Remise � z�ro du journal des statistiques"; 
$lang_poll["StatDis"]   = "journalisation d�sactiv�e pour ce sondage"; 
$lang_poll["StatTotal"] = "Nombre de votes"; 
$lang_poll["StatDay"]   = "votes par jour"; 

/* Poll Comments */ 
$lang_poll["ComTotal"]  = "Nombre de commentaires"; 
$lang_poll["ComName"]   = "Non"; 
$lang_poll["ComPost"]   = "post�"; 
$lang_poll["ComDel"]    = "Voulez-vous vraiment effacer ce message?"; 

/* Help */ 
$lang_poll["Help"]       = "Aide"; 
$lang_poll["HelpPoll"]   = "Pour int�grer un sondage dans une page web copier le code ci-dessous"; 
$lang_poll["HelpRand"]   = "Il est �galement possible d'afficher un sondage au hasard"; 
$lang_poll["HelpNew"]    = "Toujours afficher le dernier sondage"; 
$lang_poll["HelpSyntax"] = "Syntaxe"; 

/* Days */ 
$weekday_poll[0] = "Dimanche"; 
$weekday_poll[1] = "Lundi"; 
$weekday_poll[2] = "Mardi"; 
$weekday_poll[3] = "Mercredi"; 
$weekday_poll[4] = "Jeudi"; 
$weekday_poll[5] = "Vendredi"; 
$weekday_poll[6] = "Samedi"; 

/* Months */ 
$months_poll[0]  = "Janvier"; 
$months_poll[1]  = "Fevrier"; 
$months_poll[2]  = "Mars"; 
$months_poll[3]  = "Avril"; 
$months_poll[4]  = "Mai"; 
$months_poll[5]  = "Juin"; 
$months_poll[6]  = "Juillet"; 
$months_poll[7]  = "Ao�t"; 
$months_poll[8]  = "Septembre"; 
$months_poll[9]  = "Octobre"; 
$months_poll[10] = "Novembre"; 
$months_poll[11] = "Decembre"; 

/* Colors */ 
$color_array_poll[0]  = "turquoise"; 
$color_array_poll[1]  = "bleu"; 
$color_array_poll[2]  = "marron"; 
$color_array_poll[3]  = "vert fonc�"; 
$color_array_poll[4]  = "dor�"; 
$color_array_poll[5]  = "vert"; 
$color_array_poll[6]  = "gris"; 
$color_array_poll[7]  = "orange"; 
$color_array_poll[8]  = "rose"; 
$color_array_poll[9]  = "violet"; 
$color_array_poll[10] = "rouge"; 
$color_array_poll[11] = "jaune"; 

?>