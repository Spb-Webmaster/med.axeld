<?php

namespace Support\Traits;

trait EmailAddressCollector
{


    /** Метод создания массива для использования его в отправке ** */

    public function emails():array
    {

        settype($emails, "array");
        if (config2('moonshine.setting.json_emails')) {
            $manager_emails = config2('moonshine.setting.json_emails');
            foreach ($manager_emails as $e) {
                $emails[] = $e['json_email'];
            }
        }
        array_push($emails, (config('app.mail_admin')) ?? []);

        if (count($emails)) {
            return $emails;
        }
        return [];
    }
}
