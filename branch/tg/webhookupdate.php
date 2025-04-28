<?php
/**
 * Telegram Bot example.
 *
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */
include 'Telegram.php';

// Set the bot TOKEN
$bot_token = '7625094076:AAFzPQCV1l0wiMRiUlho0ushQdmE0Xr2SAY';
$telegram = new Telegram($bot_token);
$chat_id = $telegram->ChatID();
$content = array('chat_id' => $chat_id, 'text' => 'Test');
$telegram->sendMessage($content);
