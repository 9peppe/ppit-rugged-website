#!/usr/bin/php
<?php
error_reporting(E_ALL && E_NOTICE);
$perfstart = microtime(true);

echo "Init: ";

require_once 'settings.conf.php';
require_once 'libpiratewww.class.php';

// create needed dirs
function createdirs($dir=NULL) {
  if ( file_exists($dir) ){
    $comando="mkdir ".$dir.$settings['templates']." ".$dir.$settings['includes']." ".$dir.$settings['htdocs']." ";
    $uscita[0]="Ok";
    $ritorno=1;
    exec($comando,$uscita,$ritorno); // http://it.php.net/manual/en/function.mkdir.php
    if ( $ritorno != 0 ){
      echo "ATTENZIONE: non ho potuto creare le directories necessarie.\n";
      exit(1);
    };
  };
}

// clean previous .html files from htdocs
function cleanprevious($htdocs=NULL) {
  if ( file_exists($htdocs) ){
    $comando="rm ".$settings['basedir'].$settings['htdocs']."*.html";
    $uscita[0]="Ok";
    $ritorno=1;
    exec($comando,$uscita,$ritorno); // eeeehm, http://it.php.net/manual/en/function.unlink.php
    if ( $ritorno != 0 ){
      echo "ATTENZIONE: non ho potuto cancellare i file .html pre-esistenti\n";
      exit(1);
    };
  };
}

echo "OK\n";
echo "Commands: ";

// command parsing and exec
if ($argc > 1) {
  for ($i = 1; $i < $argc; $i++) {
    switch($argv[$i]) {
      
      case "-v":
      case "--version":
        echo $argv[0]." v0.1\n";
        break;
        exit;
      case "-d":
      case "--debug":
        $settings['debug'] = true;
        break;
      case "-t":
      case "--test":
        $settings['test'] = true;
        break;
      case "--base":
      case "--basedir":
      case "--dir":
        $settings['basedir'] = $argv[++$i];
        break;
      case "-p":
      case "--cleanprevious":
        $settings['cleanprevious'] = true;
        break;
      case "-c":
      case "--createdirs":
        createdirs($settings['basedir']);
        exit;
        break;
      case "-?":
      case "-h":
      case "--help":
      default:      
        echo "Create a Pirate WWW in your htdocs starting from the base directory ".$settings['basedir'].".\n";
        echo "\n";
        echo "Usage: ".$argv[0]." <option>\n";
        echo "\n";
        echo "--help, -help, -h, or -?	to get this help.\n";
        echo "--version, -v		to return the version of this file.\n";
        echo "--debug, -d		to turn on output debugging.\n";
        echo "--test, -t		to fake any write operation (filesystem, api).\n";
        echo "--basedir [directory]	to change base directory from ".$settings['basedir']." .\n";
        echo "--createdirs, -c	to create needed dirs starting from basedir.";
        echo "\n";
        echo "Command line options override config files options.\n";
      exit;
      break;
    }; // switch options
  }; // for each option
}; // if is option

// new www
$www = new Piratewww($settings['basedir'], $settings['wikiurl'], $settings['lfapi'], $settings['templates'], $settings['includes'], $settings['htdocs'], $settings['locale']);
if ( $settings['full'] || $settings['quickstart'] ) {
  $last="1";
} else {
  // TODO: scandir to figure out which is the last draft already updated
}
if ( $settings['ff'] || $settings['full'] || $settings['quickstart'] ) $www->updateFormalfoo();
if ( $settings['tribune'] || $settings['full'] || $settings['quickstart'] ) $www->updateTribune();
if ( $settings['report'] || $settings['full'] || $settings['quickstart'] ) $www->updateReport();

echo "OK\n";
echo "Post: ";

// operazioni finali
echo "OK\n";

// cronometro.
$perfstop = microtime(true);
$perfs = $perfstop - $perfstart;
$perfstime = number_format($perfs,5,',','.');
echo "Tempo impiegato dallo script: $perfstime secondi\n";
?>