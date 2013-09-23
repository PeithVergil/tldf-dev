<?php
global $lang_poll, $weekday_poll, $months_poll, $color_array_poll;
# Advanced Poll Language File (Admin)         #
# Slovenian translation by Martin McDowell, dr.vet.med. #
# E-mail: martin.mcdowell@email.si            #
# Date: 03/01/2002                    #

/* Charset */
$lang_poll["charset"]   = "windows-1250";

/* General */
$lang_poll["Logout"]    = "Odjava";
$lang_poll["FormUndo"]  = "Razveljavi spremembe";
$lang_poll["FromClear"] = "Poèisti";
$lang_poll["FormEnter"] = "Vnesite veljavno uporabniško ime in geslo";
$lang_poll["FormWrong"] = "Napaèno uporabniško ime ali geslo";
$lang_poll["FormOK"]    = "V redu";
$lang_poll["Updated"]   = "Spremembe so shranjene!";
$lang_poll["NoUpdate"]  = "Napaka! Spremembe se niso upoštevale!";
$lang_poll["Confirm"]   = "Ali ste preprièani?";
$lang_poll["NavNext"]   = "Naslednja stran";
$lang_poll["NavPrev"]   = "Prejšnja stran";
$lang_poll["License"]   = "Licenèna pogodba";
$lang_poll["ScrollTxt"] = "Pritisnite gumb NASLEDNJA STRAN za ogled celotne pogodbe.";

/* Templates */
$lang_poll["Templates"]  = "Predloge";
$lang_poll["tpl_exist"]  = "Ta predloga že obstaja.";
$lang_poll["tpl_new"]    = "Dodaj novo predlogo.";
$lang_poll["tpl_succes"] = "Zapis uspešno dodan!"; 
$lang_poll["tpl_bad"]    = "Ime predloge neveljavno!";
$lang_poll["tpl_save"]   = "Shrani";
$lang_poll["preview"]    = "Predogled";
$lang_poll["newtpl"]     = "Nova predloga";

/* Poll List */
$lang_poll["IndexTitle"]  = "Spisek anket";
$lang_poll["IndexQuest"]  = "Vprašanje";
$lang_poll["IndexID"]     = "ID ankete";
$lang_poll["IndexDate"]   = "Datum";
$lang_poll["IndexDays"]   = "Dni";
$lang_poll["IndexExp"]    = "Anketa poteèe dne";
$lang_poll["IndexExpire"] = "Anketa konèana.";
$lang_poll["IndexNever"]  = "nikoli";
$lang_poll["IndexStat"]   = "Statistika";
$lang_poll["IndexCom"]    = "Komentarji";
$lang_poll["IndexAct"]    = "Dejanje";
$lang_poll["IndexDel"]    = "zbriši";

/* Create A New Poll */
$lang_poll["NewTitle"]  = "Ustvari novo anketo";
$lang_poll["NewOption"] = "Opcija";
$lang_poll["NewNoQue"]  = "Pozabili ste vnesti vprašanje";
$lang_poll["NewNoOpt"]  = "Pozabili ste vnesti opcijo";

/* Poll Edit */
$lang_poll["EditStat"]  = "Status";
$lang_poll["EditText"]  = "Uredi anketo";
$lang_poll["EditReset"] = "Ponastavi anketo";
$lang_poll["EditOn"]    = "omogoèeno";
$lang_poll["EditOff"]   = "onemogoèeno";
$lang_poll["EditHide"]  = "skrito";
$lang_poll["EditLgOff"] = "odjavljanje";
$lang_poll["EditLgOn"]  = "prijavljanje";
$lang_poll["EditAdd"]   = "Dodaj možnosti";
$lang_poll["EditNo"]    = "Nobena možnost ni bila dodana!";
$lang_poll["EditOk"]    = "Možnosti so bile dodane!";
$lang_poll["EditSave"]  = "Shrani spremembe";
$lang_poll["EditOp"]    = "Zahtevani st anajmanj dve možnosti!";
$lang_poll["EditMis"]   = "Vprašanja in možnosti niso definirani.!";
$lang_poll["EditDel"]   = "Da odstranite možnost pustite polje Možnost prazno.";
$lang_poll["EditCom"]   = "Dovoli komentarje";

/* General Settings */
$lang_poll["SetTitle"]   = "Splošne Nastavitve";
$lang_poll["SetOption"]  = "Možnosti Tabele, Pisave (fonta) ter Barv";
$lang_poll["SetMisc"]    = "Razno";
$lang_poll["SetText"]    = "Uredi splošne nastavitve";
$lang_poll["SetURL"]     = "URL naslov za mapo z grafiko";
$lang_poll["SetBURL"]    = "URL naslov za mapo z anketo";
$lang_poll["SetNo"]      = "Brez \ oz. / na koncu";
$lang_poll["SetLang"]    = "Jezik";
$lang_poll["SetPoll"]    = "Naslov ankete";
$lang_poll["SetButton"]  = "Gumb za glasovanje";
$lang_poll["SetResult"]  = "Povezava za rezultate";
$lang_poll["SetVoted"]   = "Ste že glasovali";
$lang_poll["SetComment"] = "Pošljite vaš komentar";
$lang_poll["SetTab"]     = "Širina Tabele";
$lang_poll["SetBarh"]    = "Višina èrte (rezultatov)";
$lang_poll["SetBarMax"]  = "Najveèja dolžnina èrte";
$lang_poll["SetTabBg"]   = "Barva ozadja tabele";
$lang_poll["SetFrmCol"]  = "Barva okvirja";
$lang_poll["SetFontCol"] = "Barva pisave (fonta)";
$lang_poll["SetFace"]    = "Oblika pisave (font)";
$lang_poll["SetShow"]    = "Prikaži rezultat kot";
$lang_poll["SetPerc"]    = "odstotke";
$lang_poll["SetVotes"]   = "glasove";
$lang_poll["SetCheck"]   = "Preverjanje (check)";
$lang_poll["SetNoCheck"] = "brez preverjanja (no checking)";
$lang_poll["SetIP"]      = "IP tabela";
$lang_poll["CheckIP"]       = "Check IP";
$lang_poll["CheckUsername"] = "Check username";
$lang_poll["SetTime"]    = "locking timeout";
$lang_poll["SetHours"]   = "ure";
$lang_poll["SetOffset"]  = "Server time offset";
$lang_poll["SetEntry"]   = "Število komentarjev na stran";
$lang_poll["SetSubmit"]  = "Pošlji Nastavitve";
$lang_poll["SetEmpty"]   = "Nepravilna (invalid) vrednost";
$lang_poll["SetSort"]    = "Vrstni red prikaza";
$lang_poll["SetAsc"]     = "narašèajoèe";
$lang_poll["SetDesc"]    = "padajoèe";
$lang_poll["Setusort"]   = "ne razvršèaj";
$lang_poll["SetOptions"] = "Nastavitve dovoljene v novih anketah";
$lang_poll["SetPolls"]   = "Število anket na stran";

/* Change Password */
$lang_poll["PwdTitle"] = "Spremeni geslo";
$lang_poll["PwdText"]  = "Spremenite uporabniško ime ali geslo";
$lang_poll["PwdUser"]  = "Uporabniško ime";
$lang_poll["PwdPass"]  = "Geslo";
$lang_poll["PwdConf"]  = "Potrdite Geslo";
$lang_poll["PwdNoUsr"] = "Pozabili ste vnesti uporabniško ime";
$lang_poll["PwdNoPwd"] = "Pozabili ste vnesti geslo";
$lang_poll["PwdBad"]   = "Vpisani gesli se ne ujemata!";

/* Poll Stats */
$lang_poll["StatCrea"]  = "Ustvarjeno";
$lang_poll["StatAct"]   = "Aktivno";
$lang_poll["StatReset"] = "Ponastavite log datoteko za statistiko";
$lang_poll["StatDis"]   = "logiranje je onemogoèeno za to anketo";
$lang_poll["StatTotal"] = "Skušno število glasovanj";
$lang_poll["StatDay"]   = "Skupno število dnevnih glasovanj";

/* Poll Comments */
$lang_poll["ComTotal"]  = "Vsi komentarji";
$lang_poll["ComName"]   = "Ime in priimek";
$lang_poll["ComPost"]   = "poslano";
$lang_poll["ComDel"]    = "Ste preprièani, da želite izbrisati to sporoèilo?";

/* Help */
$lang_poll["Help"]       = "Pomoè";
$lang_poll["HelpPoll"]   = "Za vkljuèitev ankete v spletno stran vstavite spodnjo kodo.";
$lang_poll["HelpRand"]   = "Možnost imate tudi prikaz nakljuèno izbrane ankete.";
$lang_poll["HelpNew"]    = "Vedno prikaži najnovejšo anketo.";
$lang_poll["HelpSyntax"] = "Sintaksa";

/* Days */
$weekday_poll[0] = "nedelja";
$weekday_poll[1] = "ponedeljek";
$weekday_poll[2] = "torek";
$weekday_poll[3] = "sreda";
$weekday_poll[4] = "èetrtek";
$weekday_poll[5] = "petek";
$weekday_poll[6] = "sobota";

/* Months */
$months_poll[0]  = "januar";
$months_poll[1]  = "februar";
$months_poll[2]  = "marec";
$months_poll[3]  = "april";
$months_poll[4]  = "maj";
$months_poll[5]  = "junij";
$months_poll[6]  = "julij";
$months_poll[7]  = "avgust";
$months_poll[8]  = "september";
$months_poll[9]  = "oktober";
$months_poll[10] = "november";
$months_poll[11] = "december";

/* Colors */
$color_array_poll[0]  = "aqua";
$color_array_poll[1]  = "blue";
$color_array_poll[2]  = "brown";
$color_array_poll[3]  = "darkgreen";
$color_array_poll[4]  = "gold";
$color_array_poll[5]  = "green";
$color_array_poll[6]  = "grey";
$color_array_poll[7]  = "orange";
$color_array_poll[8]  = "pink";
$color_array_poll[9]  = "purple";
$color_array_poll[10] = "red";
$color_array_poll[11] = "yellow";

?>