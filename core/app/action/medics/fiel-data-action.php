<?php declare(strict_types=1);
$cerFile = 'storage_data/medics/'.$medic->id.'/'.$medic->fiel_certificate_path;
$pemKeyFile = 'storage_data/medics/'.$medic->id.'/'.$medic->fiel_key_path;
$passPhrase = $medic->fiel_key_password; // contraseña para abrir la llave privada

$key = PhpCfdi\Credentials\PrivateKey::openFile($pemKeyFile, $passPhrase);
$privateKey = $key->pem();

/*
//USE CERTIFICATE
$fiel = PhpCfdi\Credentials\Credential::openFiles($cerFile, $pemKeyFile, $passPhrase);

$sourceString = 'texto a firmar';
// alias de privateKey/sign/verify
$signature = $fiel->sign($sourceString);
echo base64_encode($signature), PHP_EOL;

// alias de certificado/publicKey/verify
$verify = $fiel->verify($sourceString, $signature);
var_dump($verify); // bool(true)

// objeto certificado
$certificado = $fiel->certificate();
echo $certificado->rfc(), PHP_EOL; // el RFC del certificado
echo $certificado->legalName(), PHP_EOL; // el nombre del propietario del certificado
echo $certificado->branchName(), PHP_EOL; // el nombre de la sucursal (en CSD, en FIEL está vacía)
echo $certificado->serialNumber()->bytes(), PHP_EOL; // número de serie del certificado
*/
?>