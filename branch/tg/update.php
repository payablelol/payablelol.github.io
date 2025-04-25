<?php
/**
 * Telegram Bot Example without WebHook.
 * It uses getUpdates Telegram's API.
 *
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */
include 'Telegram.php';

$bot_token = '91552';
$telegram = new Telegram($bot_token);

$last_update_id = 0;
$gc_counter = 0;

while (true) {
    // 只获取新的消息
    $params = [];
    if ($last_update_id) {
        $params['offset'] = $last_update_id + 1;
    }
    $req = $telegram->getUpdates($params);

    $n = $telegram->UpdateCount();
    for ($i = 0; $i < $n; $i++) {
        $telegram->serveUpdate($i);
        $text = $telegram->Text();
        $chat_id = $telegram->ChatID();
        $update_id = $telegram->UpdateID();

        if ($update_id > $last_update_id) {
            $last_update_id = $update_id;
        }

        if ($text == '/start') {
            $reply = 'Working';
            $content = ['chat_id' => $chat_id, 'text' => $reply];
            $telegram->sendMessage($content);
        }
        if ($text == '/test') {
            if ($telegram->messageFromGroup()) {
                $reply = 'Chat Group';
            } else {
                $reply = 'Private Chat';
            }
            $option = [['A', 'B'], ['C', 'D']];
            $keyb = $telegram->buildKeyBoard($option);
            $content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $reply];
            $telegram->sendMessage($content);
        }
        if ($text == '/git') {
            $reply = 'Check me on GitHub: https://github.com/Eleirbag89/TelegramBotPHP';
            $content = ['chat_id' => $chat_id, 'text' => $reply];
            $telegram->sendMessage($content);
        }
    }

    // 每次循环后休息1秒，降低资源消耗
    sleep(1);

    // 定期主动进行垃圾回收
    $gc_counter++;
    if ($gc_counter >= 60) {
        gc_collect_cycles();
        $gc_counter = 0;
    }
}
