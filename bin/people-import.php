<?php

use Nette\Utils\Strings;
use Nette\Utils\Random;

$container = require __DIR__ . '/../app/bootstrap.php';
$manager = $container->getByType('App\Model\Authentication\PasswordAuthenticator');

$loginStripCSV = new loginStripContainer("../resources/logins.csv");

if (($handle = fopen("../resources/origin/contestants.csv", "r")) !== FALSE) {
    while (($person = fgetcsv($handle, 0, ";")) !== FALSE) {
        
        $family_name = $person[1];
        $other_name = $person[0];
        $osloveni = $person[2]; //manulally
        $gender  = $person[3];
        $email = $person[4];
        $name = $person[5]; //manulally
        $race = $person[6]; //manulally
        
        $login = generateLogin($other_name, $family_name);
        $password = generatePassword();
        $token = generateToken();
        
        try {
            $manager->add($login, $password, $token, $name, $email, $gender, $osloveni, $race);
            echo "User $name was added.\n";
        } catch (App\Model\DuplicateNameException $e) {
            echo "Error: duplicate name.\n";
            exit(1);
        }
        
        $loginStripCSV->add($login, $password);
    }
    fclose($handle);
}

abstract class baseContainer{
    protected $logFile;
    
    public function __construct($logFile) {
        $this->logFile = $logFile;
        
        $myfile = fopen($this->logFile, "w");
        fclose($myfile);
    }
    
    protected function writeRecord($record) {
        file_put_contents($this->logFile, "\n" . $record, FILE_APPEND);
    }
}

class loginStripContainer extends baseContainer {
    
    public function __construct($logFile) {
        parent::__construct($logFile);
        
//        $record = "\documentclass[10pt]{article} \n"
//                . "\usepackage[utf8]{inputenc} \n"
//                . "\usepackage[czech]{babel} \n"
//                . "\begin{document} \n"
//                . "\begin{tabular}{"; //DROP if exists, create, ...
//        $this->writeRecord($record);
    }

    public function add($login, $password) {
        $record = "$login;$password";
        $this->writeRecord($record);
    }
}

function generateLogin($name, $surname) {
    $temp = $surname.Strings::substring($name, 0, 1);
    return Strings::webalize($temp);
}

function generatePassword() {
    $pass = '';
    for($i=0; $i<4; $i++) {
        $pass .= generateSou();
        $pass .= generateSamo();
    }
    return $pass;
}

function generateToken() {
    $token = Random::generate(60);
    return $token;
}

function generateSamo(){
    return Random::generate(1, 'aeiyou');
}

function generateSou(){
    return Random::generate(1, 'bcdfghjklmnprstvz');
}