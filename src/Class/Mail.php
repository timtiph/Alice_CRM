<?php

namespace App\Class;

use Mailjet\Client;
use Mailjet\Resources;



class Mail
{

    public function sendConfirmEmail($api_key_public, $api_key_secret, 
    //$to_email, $to_name, 
    $title, $subject, $content, $sign_key)
    {
             
        $mj = new Client($api_key_public, $api_key_secret, true, ['version' => 'v3.1']); // instance de l'objet email
        $body = [ // création du corps du mail
            'Messages' => [
                [
                    'From' => [
                        'Email' => "tiphany.moine@outlook.com",
                        'Name' => "Alice CRM"
                    ],
                    'To' => [
                        [
                            'Email' => "ugoblackandwhite@gmail.com",
                            'Name' => "Tiphany"
                            ]
                        ],
                    'TemplateID' => 4672993,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'variables' => [
                        'content' => $content,
                        'sign_key' => $sign_key,
                        'title' => $title
                    ]

                    // 'Variables' => json_decode('{
                    //     "content": ""
                    // }', true)
                    ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]); // on passe le corps du mail à $mj->post pour qu'il l'envoi
            $response->success() && var_dump($response->getData()); // on regarde la réponse
    }

    

    public function sendResetPassword($api_key_public, $api_key_secret, 
    //$to_email, $to_name, 
    $title, $subject, $content, $sign_key, $token)
    {
            //  TODO : Faire template pour envoi + vérif les var
        $mj = new Client($api_key_public, $api_key_secret, true, ['version' => 'v3.1']); // instance de l'objet email
        $body = [ // création du corps du mail
            'Messages' => [
                [
                    'From' => [
                        'Email' => "tiphany.moine@outlook.com",
                        'Name' => "Alice CRM"
                    ],
                    'To' => [
                        [
                            'Email' => "ugoblackandwhite@gmail.com",
                            'Name' => "Tiphany"
                            ]
                        ],
                    'TemplateID' => 4684557,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'variables' => [
                        'content' => $content,
                        'sign_key' => $sign_key,
                        'title' => $title,
                        'token' => $token
                    ]

                    // 'Variables' => json_decode('{
                    //     "content": ""
                    // }', true)
                    ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]); // on passe le corps du mail à $mj->post pour qu'il l'envoi
            $response->success() && var_dump($response->getData()); // on regarde la réponse
    }
}