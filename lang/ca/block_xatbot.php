<?php
$string['pluginname'] = 'Bloc Xatbot';
$string['xatbot'] = 'Xatbot';
$string['xatbot:addinstance'] = 'Afegeix un nou Bloc Xatbot';
$string['xatbot:myaddinstance'] = 'Afegeix un nou Bloc Xatbot a la meva pagina de Moodle';
$string['blockstring'] = 'Modifica el text';

//Missatges estàtics complets
$string['fullwelcome1'] = 'Bones! Sóc LSBot, un Xatbot que t\'ajudarà a recuperar informació de Moodle.';
$string['fullwelcome2'] = 'Per a cercar un recurs, utilitza la paraula clau "Recurs", 
        seguit d\'allò que vulguis cercar. Per exemple: "Recurs prova"';
$string['fullfallback'] = 'Ho sento, no he entès què has dit.';
$string['fullresourcematch'] = 'Del tipus "{$a}", s\'han trobat les següents coincidencies:';
$string['fullnoresourcematch'] = 'No s\'han trobat coincidències';
$string['fullaskresourcename'] = 'Quin nom té el recurs?';
$string['fullaskresourcetype'] = 'Quin tipus de recurs vols?';
$string['fullaskresourcecourse'] = 'De quin curs?';
$string['buttonall'] = 'Tots';

//Components de missatges estàtics
$string['compresourcematchcourse'] = ' - Curs: ';

//Peticions úniques a escoltar
$string['hearingresourcerequest'] = '.*(Busca(?<restype1> recurs| fitxer| url| tasca)?|(Busca )?(?<restype2>recurs|fitxer|url|tasca)) (?<resname>.*)';

//Conversacions a escoltar
$string['hearingresourceconver'] = '(Busca|(?<restype2>recurs|fitxer|url|tasca))';

//Esdeveniments
$string['resourcesearchevent'] = 'S\'ha cercat recurs';
$string['fallbackevent'] = 'S\'ha realitzat una petició desconeguda';