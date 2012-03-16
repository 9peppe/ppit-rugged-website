<?php

// locale
$settings['LOCALE']='IT';

// basedir
$settings['BASEDIR']='./';

// dove sono i template (ie: wikipages, tribuna, verbale, etc)
$settings['TEMPLATES'] = 'templates/';

// dove sono gli include (ie: ppheader, ppfooter,etc)
$settings['INCLUDES'] = 'includes/';

// dove scrivere i file html finali
$settings['HTDOCS'] = 'html2/';

// dove prelevare i testi
$settings['WIKIURL'] = 'https://dev.partitopirata.org/projects/ppit/wiki/';

// dove prelevare i lavori assembleari
$settings['LFAPIURL'] = 'http://apitest.liquidfeedback.org:25520/';

// Composizione Gazzetta
$settings['FORMALFOO'][] = array('Il_Partito_Pirata', 28);
$settings['FORMALFOO'][] = array('Manifesto', 13);
$settings['FORMALFOO'][] = array('Statuto', 45); 
$settings['FORMALFOO'][] = array('Garanzia_di_Iscrizione_ed_Esclusione', 11);
$settings['FORMALFOO'][] = array('Lettera_di_Intento_Pirata', 4);
$settings['FORMALFOO'][] = array('Lettera_di_Assunzione_Responsabilità_Artistiche',5);
$settings['FORMALFOO'][] = array('Lettera_di_Assunzione_Responsabilità_Tecniche', 27);
$settings['FORMALFOO'][] = array('Lettera_di_Assunzione_Responsabilità_Uomini_Pubblicamente_Armati', 5);
$settings['FORMALFOO'][] = array('Modulo_Iscrizione_e_Certificazione',5);
$settings['FORMALFOO'][] = array('Modulo_Personale_del_Certificatore',5);
$settings['FORMALFOO'][] = array('Modulo_Contabile_del_Certificatore',3);

// staticonf.
$settings['DEBUG'] = false;
$settings['TEST'] = false;
$settings['CLEAN'] = false;
$settings['FF'] = false;
$settings['TRIBUNE'] = false;
$settings['REPORT'] = true;
$settings['FULL'] = false; 
$settings['QUICKSTART'] = false;   
?>