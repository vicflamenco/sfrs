<?php
/**
 * Created by PhpStorm.
 * User: Víctor
 * Date: 31/10/2016
 * Time: 20:49
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/sfrs/lib/PHPMailer/PHPMailerAutoload.php';

function sendEmail($template, $to, $params){

    //===========================================================================
    //      Configuración de cuenta de correo electrónico y enlace a plataforma
    //===========================================================================
        $emailAddress = 'sfrs.php.udb@gmail.com';
        $password = 'sfrs123456';
        $fromName = 'SFRS';
        $SFRS_link = 'http://ec2-54-201-136-28.us-west-2.compute.amazonaws.com/sfrs/index.php';

    //=======================================================================
    //      Configuración de PHPMailer
    //=======================================================================
        $mail = new PHPMailer();
        $mail->isSMTP();
        //$mail->SMTPDebug = 1;
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->Username = $emailAddress;
        $mail->Password = $password;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->isHTML();
        $mail->From = $emailAddress;
        $mail->FromName = $fromName;
        $mail->addReplyTo($emailAddress);

    //=======================================================================
    //      Obteniendo plantilla de correo electronico
    //=======================================================================
    $pdo = getDbConnection();
    $stmt_params = array(':name' => $template);
    $stmt = $pdo->prepare('SELECT filename, subject FROM email_template WHERE name = :name');
    if ($stmt->execute($stmt_params)){
        if ($row = $stmt->fetch()){
            $filename = $row['filename'];
            $subject = $row['subject'];
            //=======================================================================
            //      Abriendo archivo de plantilla
            //=======================================================================
            try {
                $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/sfrs/email_templates/" . $filename);
                //=======================================================================
                //      Reemplazando parámetros de correo electrónico
                //=======================================================================
                array_push($params,$SFRS_link);
                $find = array();
                $replace = array();
                for ($i = 0; $i < sizeof($params); $i++){
                    array_push($find,'{'.$i.'}');
                    array_push($replace,$params[$i]);
                }
                $emailBody = str_replace($find,$replace,$file);
            } catch(Exception $ex){

                return emailError($ex->getMessage());
            }
            //=======================================================================
            //     Envío de mensaje de correo electrónico
            //=======================================================================
            $mail->Subject = $subject;
            $mail->Body = $emailBody;
            $mail->addAddress($to);
            if(!$mail->send()) {
                return emailError('Error al enviar el mensaje de correo electronico');
            } else {
                return emailSuccess('Mensaje de correo electronico enviado satisfactoriamente.');
            }
        } else {
            return emailError('Plantilla de correo electronico no existe.');
        }
    } else {
        return emailError('Error al consultar plantilla de correo electronico.');
    }
}

function emailError($message){
    return array('status' => false, 'message' => $message);
}

function emailSuccess($message){
    return array('status' => true, 'message' => $message);
}
