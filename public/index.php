<?php
/**
 * Created by Durov.
 * Date: 7.txt/18/2020
 * Time: 12:40 PM
 */


$token = 'token';  //todo сюда вставить токен

function sendMessage($token, $params)
{
    foreach ($params as $key=>$inputValue)
    {
        if ($key > 3)
        {
            $params[3] = $params[3] . ' ' . $inputValue;
        }
    }
    $action  = $params[1];
    $chatId  = $params[2];
    $message = $params[3];
    if ($action == 'send')
    {
        $params=[
            'chat_id'=>$chatId,
            'text'=>$message,
        ];
        $ch = curl_init("https://api.telegram.org/bot".$token . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $tbot = curl_exec($ch);
        $tbot= json_decode($tbot, 1);
        if ($tbot['ok'] === true)
        {
            return 'Message sent successfully';
        }
        else
            return 'Can`t send message. ' . $tbot['description'].'.';
    }
    elseif ($action == 'get')
    {
        $tbot = file_get_contents("https://api.telegram.org/bot".$token."/getUpdates");
        $tbot = json_decode($tbot, true);
        $chatIds = [];
        if ($tbot['result'] && is_array($tbot['result']))
        {
            $response = '';
            foreach ($tbot['result'] as $chat)
            {
                array_push($chatIds, $chat['message']['from']['id']);
            }
            echo 'Chat id:';
            foreach (array_unique($chatIds) as $item)
            {
                $response .= nl2br('  ' . $item . '  ');
            }
            return $response;
        }
    }
    else
    {
        echo 'invalid action. Enter "send" action to send telegram message or "get" to get available chat id`s';
    }
}

$blinkState = intval(file_get_contents('./ports-info/1'));

$blinkStateLastState = $blinkState;
if (file_exists('./ports-info/1-last')) {
    $blinkStateLastState = intval(file_get_contents('./ports-info/1-last'));
} else {
    file_put_contents('./ports-info/1-last', $blinkState);
}

if ($blinkState !== $blinkStateLastState) {

    $doorText = $blinkState == 1 ? 'Закрылась!' : 'Открылась!';

    $lastMessageTime = 0;
    if (file_exists('./ports-info/last-message')) {
        $lastMessageTime = file_get_contents('./ports-info/last-message');
    } else {
        file_put_contents('./ports-info/last-message', strtotime ('now'));
    }

    $isNeedToSend = true;
    if ($lastMessageTime != 0) {
        $diffecrence = intval(strtotime ('now')) - $lastMessageTime;
        if ($diffecrence < 10)
            $isNeedToSend = false;
    }

    if ($isNeedToSend) {
        if (sendMessage($token, ['', 'send',  512978631,  'Входная дверь ' . $doorText . '']) == 'Message sent successfully') {
            echo 'Входная дверь ' . $blinkState;
            // если сообщение доставлено
            file_put_contents('./ports-info/1-last', $blinkState);

            file_put_contents('./ports-info/last-message', strtotime ('now'));
            sendMessage($token, ['', 'send',  810054949,  'Входная дверь ' . $doorText . '']);
        }
    }


} else {
    echo 'ничего не поменялось';
}

