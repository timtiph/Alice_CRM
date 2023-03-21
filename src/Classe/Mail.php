<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
    private $api_key_public = 'f7526a1971e3fffac331c7a626f14c93';
    private $api_key_secret ='bca076eba477fa727bd956e8fa1436b0';

    public function sendConfirmEmail($to_email, $to_name, $subject, $content)
    {
             
        $mj = new Client($this->api_key_public, $this->api_key_secret, true, ['version' => 'v3.1']); // instance de l'objet email
        //$mj = new Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PUBLIC'), true,['version' => 'v3.1']);
        
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
                    ]

                    // 'Variables' => json_decode('{
                    //     "content": ""
                    // }', true)
                    ]
                ]
            ];
            $response = $mj->post(Resources::$Email, ['body' => $body]); // on passe le corps du mail à $mj->post pour qu'il l'envoi
            $response->success() && var_dump($response->getData()); // on regarde la réponse
            //dd($mj);

    }
}