<?php

if (file_exists('TelegramErrorLogger.php')) {
    require_once 'TelegramErrorLogger.php';
}

/**
 * Telegram Bot Class.
 *
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */
class Telegram
{
    /**
     * Constant for type Inline Query.
     */
    const INLINE_QUERY = 'inline_query';
    /**
     * Constant for type Callback Query.
     */
    const CALLBACK_QUERY = 'callback_query';
    /**
     * Constant for type Edited Message.
     */
    const EDITED_MESSAGE = 'edited_message';
    /**
     * Constant for type Reply.
     */
    const REPLY = 'reply';
    /**
     * Constant for type Message.
     */
    const MESSAGE = 'message';
    /**
     * Constant for type Photo.
     */
    const PHOTO = 'photo';
    /**
     * Constant for type Video.
     */
    const VIDEO = 'video';
    /**
     * Constant for type Audio.
     */
    const AUDIO = 'audio';
    /**
     * Constant for type Voice.
     */
    const VOICE = 'voice';
    /**
     * Constant for type animation.
     */
    const ANIMATION = 'animation';
    /**
     * Constant for type sticker.
     */
    const STICKER = 'sticker';
    /**
     * Constant for type Document.
     */
    const DOCUMENT = 'document';
    /**
     * Constant for type Location.
     */
    const LOCATION = 'location';
    /**
     * Constant for type Contact.
     */
    const CONTACT = 'contact';
    /**
     * Constant for type Channel Post.
     */
    const CHANNEL_POST = 'channel_post';
    /**
     * Constant for type New Chat Member.
     */
    const NEW_CHAT_MEMBER = 'new_chat_member';
    /**
     * Constant for type Left Chat Member.
     */
    const LEFT_CHAT_MEMBER = 'left_chat_member';
    /**
     * Constant for type My Chat Member.
     */
    const MY_CHAT_MEMBER = 'my_chat_member';

    private $bot_token = '';
    private $data = [];
    private $updates = [];
    private $log_errors;
    private $proxy;
    private $update_type;

    /// Class constructor

    /**
     * Create a Telegram instance from the bot token
     * \param $bot_token the bot token
     * \param $log_errors enable or disable the logging
     * \param $proxy array with the proxy configuration (url, port, type, auth)
     * \return an instance of the class.
     */
    public function __construct($bot_token, $log_errors = true, array $proxy = [])
    {
        $this->bot_token = $bot_token;
        $this->data = $this->getData(); // Initialize data early
        $this->log_errors = $log_errors;
        $this->proxy = $proxy;
    }

    /// Do requests to Telegram Bot API

    /**
     * Contacts the various API's endpoints
     * \param $api the API endpoint
     * \param $content the request parameters as array
     * \param $post boolean tells if $content needs to be sends
     * \return the JSON Telegram's reply.
     */
    public function endpoint($api, array $content, $post = true)
    {
        $url = 'https://api.telegram.org/bot'.$this->bot_token.'/'.$api;
        if ($post) {
            $reply = $this->sendAPIRequest($url, $content);
        } else {
            $reply = $this->sendAPIRequest($url, [], false);
        }

        // Always return an array, even on decode failure
        $decoded_reply = json_decode($reply, true);
        return is_array($decoded_reply) ? $decoded_reply : ['ok' => false, 'description' => 'Failed to decode API response', 'raw_response' => $reply];
    }

    /// A method for testing your bot.

    /**
     * See <a href="https://core.telegram.org/bots/api#getme">getMe</a>
     * \return the JSON Telegram's reply.
     */
    public function getMe()
    {
        return $this->endpoint('getMe', [], false);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#logout">logOut</a>
     * \return the JSON Telegram's reply.
     */
    public function logOut()
    {
        return $this->endpoint('logOut', [], false);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#close">close</a>
     * \return the JSON Telegram's reply.
     */
    public function close()
    {
        return $this->endpoint('close', [], false);
    }

    /// A method for responding http to Telegram.

    /**
     * \return the HTTP 200 to Telegram.
     */
    public function respondSuccess()
    {
        // Ensure headers are not already sent
        if (!headers_sent()) {
             http_response_code(200);
             header('Content-Type: application/json');
        }
        return json_encode(['status' => 'success']);
    }

    /// Send a message

    /**
     * See <a href="https://core.telegram.org/bots/api#sendmessage">sendMessage</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendMessage(array $content)
    {
        return $this->endpoint('sendMessage', $content);
    }

    /// Copy a message

    /**
     * See <a href="https://core.telegram.org/bots/api#copymessage">copyMessage</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function copyMessage(array $content)
    {
        return $this->endpoint('copyMessage', $content);
    }

    /// Forward a message

    /**
     * See <a href="https://core.telegram.org/bots/api#forwardmessage">forwardMessage</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function forwardMessage(array $content)
    {
        return $this->endpoint('forwardMessage', $content);
    }

    /// Send a photo

    /**
     * See <a href="https://core.telegram.org/bots/api#sendphoto">sendPhoto</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendPhoto(array $content)
    {
        return $this->endpoint('sendPhoto', $content);
    }

    /// Send an audio

    /**
     * See <a href="https://core.telegram.org/bots/api#sendaudio">sendAudio</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendAudio(array $content)
    {
        return $this->endpoint('sendAudio', $content);
    }

    /// Send a document

    /**
     * See <a href="https://core.telegram.org/bots/api#senddocument">sendDocument</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendDocument(array $content)
    {
        return $this->endpoint('sendDocument', $content);
    }

    /// Send an animation

    /**
     * See <a href="https://core.telegram.org/bots/api#sendanimation">sendAnimation</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendAnimation(array $content)
    {
        return $this->endpoint('sendAnimation', $content);
    }

    /// Send a sticker

    /**
     * See <a href="https://core.telegram.org/bots/api#sendsticker">sendSticker</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendSticker(array $content)
    {
        return $this->endpoint('sendSticker', $content);
    }

    /// Send a video

    /**
     * See <a href="https://core.telegram.org/bots/api#sendvideo">sendVideo</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendVideo(array $content)
    {
        return $this->endpoint('sendVideo', $content);
    }

    /// Send a voice message

    /**
     * See <a href="https://core.telegram.org/bots/api#sendvoice">sendVoice</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendVoice(array $content)
    {
        return $this->endpoint('sendVoice', $content);
    }

    /// Send a location

    /**
     * See <a href="https://core.telegram.org/bots/api#sendlocation">sendLocation</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendLocation(array $content)
    {
        return $this->endpoint('sendLocation', $content);
    }

    /// Edit Message Live Location

    /**
     * See <a href="https://core.telegram.org/bots/api#editmessageliveLocation">editMessageLiveLocation</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function editMessageLiveLocation(array $content)
    {
        return $this->endpoint('editMessageLiveLocation', $content);
    }

    /// Stop Message Live Location

    /**
     * See <a href="https://core.telegram.org/bots/api#stopmessagelivelocation">stopMessageLiveLocation</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function stopMessageLiveLocation(array $content)
    {
        return $this->endpoint('stopMessageLiveLocation', $content);
    }

    /// Set Chat Sticker Set

    /**
     * See <a href="https://core.telegram.org/bots/api#setchatstickerset">setChatStickerSet</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setChatStickerSet(array $content)
    {
        return $this->endpoint('setChatStickerSet', $content);
    }

    /// Delete Chat Sticker Set

    /**
     * See <a href="https://core.telegram.org/bots/api#deletechatstickerset">deleteChatStickerSet</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function deleteChatStickerSet(array $content)
    {
        return $this->endpoint('deleteChatStickerSet', $content);
    }

    /// Send Media Group

    /**
     * See <a href="https://core.telegram.org/bots/api#sendmediagroup">sendMediaGroup</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendMediaGroup(array $content)
    {
        // Ensure media key exists and is JSON encoded if needed
        if (isset($content['media']) && is_array($content['media'])) {
             $content['media'] = json_encode($content['media']);
        }
        return $this->endpoint('sendMediaGroup', $content);
    }

    /// Send Venue

    /**
     * See <a href="https://core.telegram.org/bots/api#sendvenue">sendVenue</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendVenue(array $content)
    {
        return $this->endpoint('sendVenue', $content);
    }

    //Send contact

    /**
     * See <a href="https://core.telegram.org/bots/api#sendcontact">sendContact</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendContact(array $content)
    {
        return $this->endpoint('sendContact', $content);
    }

    //Send a poll

    /**
     * See <a href="https://core.telegram.org/bots/api#sendpoll">sendPoll</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendPoll(array $content)
    {
        // Ensure options key exists and is JSON encoded if needed
        if (isset($content['options']) && is_array($content['options'])) {
             $content['options'] = json_encode($content['options']);
        }
        return $this->endpoint('sendPoll', $content);
    }

    //Send a dice

    /**
     * See <a href="https://core.telegram.org/bots/api#senddice">sendDice</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendDice(array $content)
    {
        return $this->endpoint('sendDice', $content);
    }

    /// Send a chat action

    /**
     * See <a href="https://core.telegram.org/bots/api#sendchataction">sendChatAction</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendChatAction(array $content)
    {
        return $this->endpoint('sendChatAction', $content);
    }

    /// Get a list of profile pictures for a user

    /**
     * See <a href="https://core.telegram.org/bots/api#getuserprofilephotos">getUserProfilePhotos</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getUserProfilePhotos(array $content)
    {
        return $this->endpoint('getUserProfilePhotos', $content);
    }

    /// Use this method to get basic info about a file and prepare it for downloading

    /**
     *  Use this method to get basic info about a file and prepare it for downloading. For the moment, bots can download files of up to 20MB in size. On success, a File object is returned. The file can then be downloaded via the link https://api.telegram.org/file/bot<token>/<file_path>, where <file_path> is taken from the response. It is guaranteed that the link will be valid for at least 1 hour. When the link expires, a new one can be requested by calling getFile again.
     * \param $file_id String File identifier to get info about
     * \return the JSON Telegram's reply.
     */
    public function getFile($file_id)
    {
        $content = ['file_id' => $file_id];

        return $this->endpoint('getFile', $content);
    }

    /// Kick Chat Member (Deprecated by Telegram, use banChatMember instead)

    /**
     * Deprecated: Use banChatMember instead.
     * See <a href="https://core.telegram.org/bots/api#banchatmember">banChatMember</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function kickChatMember(array $content)
    {
        // Optionally add a deprecation warning
        trigger_error('kickChatMember() is deprecated; use banChatMember() instead.', E_USER_DEPRECATED);
        // Map to banChatMember parameters if possible, or just call the old endpoint
        return $this->endpoint('kickChatMember', $content); // Or preferably: return $this->banChatMember($content);
    }


    /// Leave Chat

    /**
     * See <a href="https://core.telegram.org/bots/api#leavechat">leaveChat</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function leaveChat(array $content)
    {
        return $this->endpoint('leaveChat', $content);
    }

    /// Ban Chat Member

    /**
     * See <a href="https://core.telegram.org/bots/api#banchatmember">banChatMember</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function banChatMember(array $content)
    {
        return $this->endpoint('banChatMember', $content);
    }

    /// Unban Chat Member

    /**
     * See <a href="https://core.telegram.org/bots/api#unbanchatmember">unbanChatMember</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function unbanChatMember(array $content)
    {
        return $this->endpoint('unbanChatMember', $content);
    }

    /// Get Chat Information

    /**
     * See <a href="https://core.telegram.org/bots/api#getchat">getChat</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getChat(array $content)
    {
        return $this->endpoint('getChat', $content);
    }

    /// Get chat Administrators

    /**
     * See <a href="https://core.telegram.org/bots/api#getchatadministrators">getChatAdministrators</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getChatAdministrators(array $content)
    {
        return $this->endpoint('getChatAdministrators', $content);
    }

    /// Get chat member count

    /**
     * See <a href="https://core.telegram.org/bots/api#getchatmembercount">getChatMemberCount</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getChatMemberCount(array $content)
    {
        return $this->endpoint('getChatMemberCount', $content);
    }

    /**
     * For retrocompatibility (Alias for getChatMemberCount)
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getChatMembersCount(array $content)
    {
        trigger_error('getChatMembersCount() is deprecated; use getChatMemberCount() instead.', E_USER_DEPRECATED);
        return $this->getChatMemberCount($content);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#getchatmember">getChatMember</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getChatMember(array $content)
    {
        return $this->endpoint('getChatMember', $content);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#answerinlinequery">answerInlineQuery</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function answerInlineQuery(array $content)
    {
        // Ensure results key exists and is JSON encoded if needed
        if (isset($content['results']) && is_array($content['results'])) {
             $content['results'] = json_encode($content['results']);
        }
        return $this->endpoint('answerInlineQuery', $content);
    }

    /// Set Game Score

    /**
     * See <a href="https://core.telegram.org/bots/api#setgamescore">setGameScore</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setGameScore(array $content)
    {
        return $this->endpoint('setGameScore', $content);
    }

    /// Get Game Hi Scores

    /**
     * See <a href="https://core.telegram.org/bots/api#getgamehighscores">getGameHighScores</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getGameHighScores(array $content)
    {
        return $this->endpoint('getGameHighScores', $content);
    }

    /// Answer a callback Query

    /**
     * See <a href="https://core.telegram.org/bots/api#answercallbackquery">answerCallbackQuery</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function answerCallbackQuery(array $content)
    {
        return $this->endpoint('answerCallbackQuery', $content);
    }

    /// Set the list of the bot commands

    /**
     * See <a href="https://core.telegram.org/bots/api#setmycommands">setMyCommands</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setMyCommands(array $content)
    {
        // Ensure commands key exists and is JSON encoded if needed
        if (isset($content['commands']) && is_array($content['commands'])) {
             $content['commands'] = json_encode($content['commands']);
        }
        return $this->endpoint('setMyCommands', $content);
    }

    /// Delete the list of the bot commands

    /**
     * See <a href="https://core.telegram.org/bots/api#deletemycommands">deleteMyCommands</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function deleteMyCommands(array $content)
    {
        return $this->endpoint('deleteMyCommands', $content);
    }

    /// Get the list of the bot commands

    /**
     * See <a href="https://core.telegram.org/bots/api#getmycommands">getMyCommands</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getMyCommands(array $content)
    {
        return $this->endpoint('getMyCommands', $content);
    }

    /// Set the chat menu button

    /**
     * See <a href="https://core.telegram.org/bots/api#setchatmenubutton">setChatMenuButton</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setChatMenuButton(array $content)
    {
        // Ensure menu_button key exists and is JSON encoded if needed
        if (isset($content['menu_button']) && is_array($content['menu_button'])) {
             $content['menu_button'] = json_encode($content['menu_button']);
        }
        return $this->endpoint('setChatMenuButton', $content);
    }

    /// Get the chat menu button

    /**
     * See <a href="https://core.telegram.org/bots/api#getchatmenubutton">getChatMenuButton</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getChatMenuButton(array $content)
    {
        return $this->endpoint('getChatMenuButton', $content);
    }

    /// Set the default aministrator rights

    /**
     * See <a href="https://core.telegram.org/bots/api#setmydefaultadministratorrights">setMyDefaultAdministratorRights</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setMyDefaultAdministratorRights(array $content)
    {
        // Ensure rights key exists and is JSON encoded if needed
        if (isset($content['rights']) && is_array($content['rights'])) {
             $content['rights'] = json_encode($content['rights']);
        }
        return $this->endpoint('setMyDefaultAdministratorRights', $content);
    }

    /// Get the default aministrator rights

    /**
     * See <a href="https://core.telegram.org/bots/api#getmydefaultadministratorrights">getMyDefaultAdministratorRights</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function getMyDefaultAdministratorRights(array $content)
    {
        return $this->endpoint('getMyDefaultAdministratorRights', $content);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#editmessagetext">editMessageText</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function editMessageText(array $content)
    {
        return $this->endpoint('editMessageText', $content);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#editmessagecaption">editMessageCaption</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function editMessageCaption(array $content)
    {
        return $this->endpoint('editMessageCaption', $content);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#editmessagemedia">editMessageMedia</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function editMessageMedia(array $content)
    {
        // Ensure media key exists and is JSON encoded if needed
        if (isset($content['media']) && is_array($content['media'])) {
             $content['media'] = json_encode($content['media']);
        }
        return $this->endpoint('editMessageMedia', $content);
    }

    /**
     * See <a href="https://core.telegram.org/bots/api#editmessagereplymarkup">editMessageReplyMarkup</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function editMessageReplyMarkup(array $content)
    {
        // Ensure reply_markup key exists and is JSON encoded if needed
        if (isset($content['reply_markup']) && is_array($content['reply_markup'])) {
            $content['reply_markup'] = json_encode($content['reply_markup']);
        }
        return $this->endpoint('editMessageReplyMarkup', $content);
    }


    /**
     * See <a href="https://core.telegram.org/bots/api#stoppoll">stopPoll</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function stopPoll(array $content)
    {
        // Ensure reply_markup key exists and is JSON encoded if needed
        if (isset($content['reply_markup']) && is_array($content['reply_markup'])) {
            $content['reply_markup'] = json_encode($content['reply_markup']);
        }
        return $this->endpoint('stopPoll', $content);
    }

    /// Use this method to download a file

    /**
     *  Use this method to to download a file from the Telegram servers.
     * \param $telegram_file_path String File path on Telegram servers (obtained from getFile)
     * \param $local_file_path String File path where save the file.
     * \return bool True on success, False on failure.
     */
    public function downloadFile($telegram_file_path, $local_file_path)
    {
        $file_url = 'https://api.telegram.org/file/bot'.$this->bot_token.'/'.$telegram_file_path;
        $in = @fopen($file_url, 'rb'); // Use @ to suppress potential fopen warnings
        if ($in === false) {
            return false; // Failed to open remote file
        }
        $out = @fopen($local_file_path, 'wb');
        if ($out === false) {
            fclose($in);
            return false; // Failed to open local file
        }

        $result = stream_copy_to_stream($in, $out);

        fclose($in);
        fclose($out);

        return $result !== false; // stream_copy_to_stream returns bytes copied or false on error
    }


    /// Set a WebHook for the bot

    /**
     *  Use this method to specify a url and receive incoming updates via an outgoing webhook. Whenever there is an update for the bot, we will send an HTTPS POST request to the specified url, containing a JSON-serialized Update. In case of an unsuccessful request, we will give up after a reasonable amount of attempts.
     *
     * If you'd like to make sure that the Webhook request comes from Telegram, we recommend using a secret path in the URL, e.g. https://www.example.com/<token>. Since nobody else knows your botâ€˜s token, you can be pretty sure itâ€™s us.
     * \param $url String HTTPS url to send updates to. Use an empty string to remove webhook integration
     * \param $certificate String Path to the public key certificate file
     * \param $params Array Additional parameters for setWebhook (e.g., max_connections, allowed_updates)
     * \return the JSON Telegram's reply.
     */
    public function setWebhook($url, $certificate = '', array $params = [])
    {
        $requestBody = ['url' => $url] + $params; // Combine URL with additional params

        // Use CURLFile for certificate upload if available (PHP >= 5.5)
        if ($certificate != '' && class_exists('CURLFile')) {
            $requestBody['certificate'] = new CURLFile(realpath($certificate));
        } elseif ($certificate != '') {
            // Fallback for older PHP versions (may have security implications)
            $requestBody['certificate'] = '@'.realpath($certificate);
        }

        return $this->endpoint('setWebhook', $requestBody, true);
    }


    /// Delete the WebHook for the bot

    /**
     *  Use this method to remove webhook integration if you decide to switch back to <a href="https://core.telegram.org/bots/api#getupdates">getUpdates</a>. Returns True on success. Requires no parameters.
     * \return the JSON Telegram's reply.
     */
    public function deleteWebhook()
    {
        return $this->endpoint('deleteWebhook', [], false);
    }

    /// Get the data of the current message

    /** Get the POST request of a user in a Webhook or the message actually processed in a getUpdates() enviroment.
     * \return array The user's message data as an associative array, or null if no data.
     */
    public function getData()
    {
        // Check if data is already populated (e.g., by constructor or serveUpdate)
        if (!empty($this->data)) {
            return $this->data;
        }

        // Check for webhook input
        $rawData = file_get_contents('php://input');
        if (!empty($rawData)) {
             $decodedData = json_decode($rawData, true);
             if (is_array($decodedData)) {
                 $this->data = $decodedData; // Store for potential reuse
                 return $this->data;
             }
        }

        // No webhook data found or decode failed, return null or previously set data
        return $this->data ?: null; // Return existing data if any, else null
    }


    /// Set the data currently used
    public function setData(array $data)
    {
        $this->data = $data;
        $this->update_type = null; // Reset cached update type when data changes
    }

    /// Get the text of the current message

    /**
     * \return string|null The user's text or callback data, or null if not applicable.
     */
    public function Text()
    {
        $type = $this->getUpdateType();
        $data = $this->getData(); // Ensure we have the latest data

        if ($data === null) return null; // No data available

        switch ($type) {
            case self::CALLBACK_QUERY:
                return $data['callback_query']['data'] ?? null;
            case self::CHANNEL_POST:
                return $data['channel_post']['text'] ?? null;
            case self::EDITED_MESSAGE:
                return $data['edited_message']['text'] ?? null;
            case self::MESSAGE:
                return $data['message']['text'] ?? null;
            default:
                return null;
        }
    }

    /**
     * Get the caption of the current message (photo, video, document, audio).
     *
     * \return string|null The caption text, or null if not applicable.
     */
    public function Caption()
    {
        $type = $this->getUpdateType();
        $data = $this->getData();

        if ($data === null) return null;

        switch ($type) {
            case self::CHANNEL_POST:
                // Channel posts can have captions on various media types
                return $data['channel_post']['caption'] ?? null;
            case self::PHOTO:
            case self::VIDEO:
            case self::AUDIO:
            case self::VOICE:
            case self::ANIMATION:
            case self::DOCUMENT:
                // Regular messages have caption inside 'message'
                return $data['message']['caption'] ?? null;
            default:
                return null;
        }
    }


    /// Get the chat_id of the current message

    /**
     * \return int|string|null The chat ID, or null if not applicable.
     */
    public function ChatID()
    {
        $chat = $this->Chat();
        return $chat['id'] ?? null;
    }

    /**
     * Get the chat object for the current update.
     * \return array|null The chat object, or null if not applicable.
     */
    public function Chat()
    {
        $type = $this->getUpdateType();
        $data = $this->getData();

        if ($data === null) return null;

        switch ($type) {
            case self::CALLBACK_QUERY:
                return $data['callback_query']['message']['chat'] ?? null;
            case self::CHANNEL_POST:
                return $data['channel_post']['chat'] ?? null;
            case self::EDITED_MESSAGE:
                return $data['edited_message']['chat'] ?? null;
            case self::INLINE_QUERY:
                // Inline queries don't have a 'chat', they have 'from' (user)
                return null; // Or return $data['inline_query']['from'] if user info is desired here? Semantically 'chat' seems wrong.
            case self::MY_CHAT_MEMBER:
                 return $data['my_chat_member']['chat'] ?? null;
            case self::MESSAGE:
            case self::PHOTO:
            case self::VIDEO:
            case self::AUDIO:
            case self::VOICE:
            case self::ANIMATION:
            case self::STICKER:
            case self::DOCUMENT:
            case self::LOCATION:
            case self::CONTACT:
            case self::NEW_CHAT_MEMBER:
            case self::LEFT_CHAT_MEMBER:
            case self::REPLY: // Reply is just a property, get chat from the message itself
                return $data['message']['chat'] ?? null;
            default:
                return null;
        }
    }


    /// Get the message_id of the current message

    /**
     * \return int|null The message ID, or null if not applicable.
     */
    public function MessageID()
    {
        $type = $this->getUpdateType();
        $data = $this->getData();

        if ($data === null) return null;

        switch ($type) {
            case self::CALLBACK_QUERY:
                // For inline messages, message_id might be a string
                return $data['callback_query']['message']['message_id'] ?? $data['callback_query']['inline_message_id'] ?? null;
            case self::CHANNEL_POST:
                return $data['channel_post']['message_id'] ?? null;
            case self::EDITED_MESSAGE:
                return $data['edited_message']['message_id'] ?? null;
            case self::MESSAGE:
            case self::PHOTO:
            case self::VIDEO:
            case self::AUDIO:
            case self::VOICE:
            case self::ANIMATION:
            case self::STICKER:
            case self::DOCUMENT:
            case self::LOCATION:
            case self::CONTACT:
            case self::NEW_CHAT_MEMBER:
            case self::LEFT_CHAT_MEMBER:
            case self::REPLY:
                return $data['message']['message_id'] ?? null;
            default:
                // Inline query, my_chat_member don't have a primary message_id in the root
                return null;
        }
    }


    /// Get the reply_to_message message_id of the current message

    /**
     * \return int|null The message_id of the message this one is replying to, or null if not a reply.
     */
    public function ReplyToMessageID()
    {
        $data = $this->getData();
        return $data['message']['reply_to_message']['message_id'] ?? null;
    }


    /// Get the reply_to_message forward_from user_id of the current message

    /**
     * \return int|null The user ID of the original sender if the replied-to message was forwarded, or null otherwise.
     */
    public function ReplyToMessageFromUserID()
    {
        // Ensure the path exists to avoid warnings
        $data = $this->getData();
        if (isset($data['message']['reply_to_message']['forward_from']['id'])) {
             return $data['message']['reply_to_message']['forward_from']['id'];
        }
        return null;
    }


    /// Get the inline_query of the current update

    /**
     * \return array|null The inline_query object, or null if the update is not an inline query.
     */
    public function Inline_Query()
    {
        $data = $this->getData();
        return $data['inline_query'] ?? null;
    }

    /// Get the callback_query of the current update

    /**
     * \return array|null The callback_query object, or null if the update is not a callback query.
     */
    public function Callback_Query()
    {
        $data = $this->getData();
        return $data['callback_query'] ?? null;
    }


    /// Get the callback_query id of the current update

    /**
     * \return string|null The callback_query ID, or null if not applicable.
     */
    public function Callback_ID()
    {
        $data = $this->getData();
        return $data['callback_query']['id'] ?? null;
    }

    /// Get the Get the data of the current callback

    /**
     * \deprecated Use Text() instead for callback data.
     * \return string|null The callback_data, or null if not applicable.
     */
    public function Callback_Data()
    {
        trigger_error('Callback_Data() is deprecated; use Text() instead for callback data.', E_USER_DEPRECATED);
        $data = $this->getData();
        return $data['callback_query']['data'] ?? null;
    }


    /// Get the Get the message of the current callback

    /**
     * \return array|null The Message object associated with the callback query, or null if not applicable.
     */
    public function Callback_Message()
    {
        $data = $this->getData();
        return $data['callback_query']['message'] ?? null;
    }


    /// Get the Get the chat_id of the current callback

    /**
     * \deprecated Use ChatID() instead.
     * \return int|string|null The chat ID from the callback message, or null if not applicable.
     */
    public function Callback_ChatID()
    {
        trigger_error('Callback_ChatID() is deprecated; use ChatID() instead.', E_USER_DEPRECATED);
        $data = $this->getData();
        return $data['callback_query']['message']['chat']['id'] ?? null;
    }


    /// Get the Get the from_id of the current callback

    /**
     * \return int|null The user ID ('from') of the user who initiated the callback query, or null if not applicable.
     */
    public function Callback_FromID()
    {
        $data = $this->getData();
        return $data['callback_query']['from']['id'] ?? null;
    }


    /// Get the date of the current message

    /**
     * \return int|null The message's date (Unix timestamp), or null if not applicable.
     */
    public function Date()
    {
        $type = $this->getUpdateType();
        $data = $this->getData();

        if ($data === null) return null;

        switch ($type) {
            case self::CALLBACK_QUERY:
                return $data['callback_query']['message']['date'] ?? null; // Date of the message the button is attached to
            case self::CHANNEL_POST:
                return $data['channel_post']['date'] ?? null;
            case self::EDITED_MESSAGE:
                return $data['edited_message']['edit_date'] ?? $data['edited_message']['date'] ?? null; // Prefer edit_date
            case self::MESSAGE:
            case self::PHOTO:
            case self::VIDEO:
            case self::AUDIO:
            case self::VOICE:
            case self::ANIMATION:
            case self::STICKER:
            case self::DOCUMENT:
            case self::LOCATION:
            case self::CONTACT:
            case self::NEW_CHAT_MEMBER:
            case self::LEFT_CHAT_MEMBER:
            case self::REPLY:
                return $data['message']['date'] ?? null;
            case self::MY_CHAT_MEMBER:
                 return $data['my_chat_member']['date'] ?? null;
            // Inline query doesn't have a date in the root
            default:
                return null;
        }
    }


    /// Get the first name of the user
    /**
     * \return string|null The first name of the user associated with the update, or null if not applicable.
     */
    public function FirstName()
    {
        $from = $this->From();
        return $from['first_name'] ?? null;
    }


    /// Get the last name of the user
    /**
     * \return string|null The last name of the user associated with the update, or null if not available/applicable.
     */
    public function LastName()
    {
        $from = $this->From();
        return $from['last_name'] ?? null;
    }


    /// Get the username of the user
    /**
     * \return string|null The username of the user associated with the update, or null if not available/applicable.
     */
    public function Username()
    {
        $from = $this->From();
        return $from['username'] ?? null;
    }

     /**
     * Get the 'from' user object for the current update.
     * \return array|null The user object, or null if not applicable.
     */
    public function From()
    {
        $type = $this->getUpdateType();
        $data = $this->getData();

        if ($data === null) return null;

        switch ($type) {
            case self::CALLBACK_QUERY:
                return $data['callback_query']['from'] ?? null;
            case self::CHANNEL_POST:
                // Channel posts might have 'author_signature' instead of 'from' if posted anonymously
                // Or 'sender_chat' if posted on behalf of the channel
                return $data['channel_post']['from'] ?? null;
            case self::EDITED_MESSAGE:
                return $data['edited_message']['from'] ?? null;
            case self::INLINE_QUERY:
                return $data['inline_query']['from'] ?? null;
            case self::MY_CHAT_MEMBER:
                 return $data['my_chat_member']['from'] ?? null;
            case self::MESSAGE:
            case self::PHOTO:
            case self::VIDEO:
            case self::AUDIO:
            case self::VOICE:
            case self::ANIMATION:
            case self::STICKER:
            case self::DOCUMENT:
            case self::LOCATION:
            case self::CONTACT:
            case self::NEW_CHAT_MEMBER: // 'new_chat_member' itself is the user, but 'from' is the user who added them (if applicable)
            case self::LEFT_CHAT_MEMBER: // 'left_chat_member' itself is the user, but 'from' is the user who removed them (if applicable)
            case self::REPLY:
                 // 'sender_chat' if sent on behalf of a channel/group
                 return $data['message']['from'] ?? $data['message']['sender_chat'] ?? null;
            default:
                return null;
        }
    }


    /// Get the location in the message
    /**
     * \return array|null The location object, or null if the message doesn't contain a location.
     */
    public function Location()
    {
        $data = $this->getData();
        return $data['message']['location'] ?? null;
    }

    /// Get the update_id of the message
    /**
     * \return int|null The unique identifier for this update, or null if not applicable (e.g., if data is manually set).
     */
    public function UpdateID()
    {
        $data = $this->getData();
        return $data['update_id'] ?? null;
    }


    /// Get the number of updates fetched by getUpdates
    /**
     * Returns the number of updates currently held in the $this->updates array.
     * Call getUpdates() first.
     * \return int The number of updates.
     */
    public function UpdateCount()
    {
        // Check if updates were fetched successfully and 'result' key exists and is an array
        if (isset($this->updates['ok']) && $this->updates['ok'] === true && isset($this->updates['result']) && is_array($this->updates['result'])) {
            return count($this->updates['result']);
        }
        // Return 0 if updates are invalid or the API call failed
        return 0;
    }

    /// Get user's id of current message
    /**
     * \return int|null The user ID from the 'from' field of the update, or null if not applicable.
     */
    public function UserID()
    {
        $from = $this->From();
        return $from['id'] ?? null;
    }


    /// Get user's id of current forwarded message
    /**
     * \return int|null The user ID from the 'forward_from' field, or null if the message wasn't forwarded from a user.
     */
    public function FromID()
    {
        $data = $this->getData();
        return $data['message']['forward_from']['id'] ?? null;
    }


    /// Get chat's id where current message forwarded from
    /**
     * \return int|string|null The chat ID from the 'forward_from_chat' field, or null if the message wasn't forwarded from a chat.
     */
    public function FromChatID()
    {
        $data = $this->getData();
        return $data['message']['forward_from_chat']['id'] ?? null;
    }


    /// Tell if a message is from a group or user chat

    /**
     * Checks the chat type of the current update.
     * \return bool|null True if the message is from a 'group', 'supergroup', or 'channel'. False if 'private'. Null if chat type is not available.
     */
    public function messageFromGroup()
    {
        $chat = $this->Chat();
        if (isset($chat['type'])) {
            return $chat['type'] !== 'private';
        }
        return null; // Chat type unknown
    }


    /// Get the contact phone number

    /**
     *  \return string|null The phone number from the contact object, or null if the message is not a contact.
     */
    public function getContactPhoneNumber()
    {
        if ($this->getUpdateType() == self::CONTACT) {
            $data = $this->getData();
            return $data['message']['contact']['phone_number'] ?? null;
        }
        return null;
    }


    /// Get the title of the group chat

    /**
     *  \return string|null The title of the chat, or null if it's a private chat or title is not available.
     */
    public function messageFromGroupTitle()
    {
        $chat = $this->Chat();
        if (isset($chat['type']) && $chat['type'] !== 'private') {
            return $chat['title'] ?? null;
        }
        return null;
    }


    /// Set a custom keyboard

    /** This object represents a custom keyboard with reply options
     * \param $options Array of Array of String or KeyboardButton; Array of button rows, each represented by an Array of Strings or KeyboardButton objects
     * \param $onetime Boolean Requests clients to hide the keyboard as soon as it's been used. Defaults to false.
     * \param $resize Boolean Requests clients to resize the keyboard vertically for optimal fit (e.g., make the keyboard smaller if there are just two rows of buttons). Defaults to false, in which case the custom keyboard is always of the same height as the app's standard keyboard.
     * \param $selective Boolean Use this parameter if you want to show the keyboard to specific users only. Targets: 1) users that are @mentioned in the text of the Message object; 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     * \param $input_field_placeholder String Optional. The placeholder to be shown in the input field when the keyboard is active; 1-64 characters
     * \return string The requested keyboard as a JSON string.
     */
    public function buildKeyBoard(array $options, $onetime = false, $resize = false, $selective = true, $input_field_placeholder = null)
    {
        $replyMarkup = [
            'keyboard'          => $options,
            'one_time_keyboard' => $onetime,
            'resize_keyboard'   => $resize,
            'selective'         => $selective,
        ];
        if ($input_field_placeholder !== null) {
            $replyMarkup['input_field_placeholder'] = $input_field_placeholder;
        }
        return json_encode($replyMarkup);
    }


    /// Set an InlineKeyBoard

    /** This object represents an inline keyboard that appears right next to the message it belongs to.
     * \param $options Array of Array of InlineKeyboardButton; Array of button rows, each represented by an Array of InlineKeyboardButton objects
     * \return string The requested keyboard as a JSON string.
     */
    public function buildInlineKeyBoard(array $options)
    {
        $replyMarkup = [
            'inline_keyboard' => $options,
        ];
        return json_encode($replyMarkup);
    }


    /// Create an InlineKeyboardButton

    /** This object represents one button of an inline keyboard. You must use exactly one of the optional fields.
     * \param $text String; Label text on the button
     * \param $url String Optional. HTTP or tg:// url to be opened when button is pressed
     * \param $callback_data String Optional. Data to be sent in a callback query to the bot when button is pressed, 1-64 bytes
     * \param $switch_inline_query String Optional. If set, pressing the button will prompt the user to select one of their chats, open that chat and insert the bot‘s username and the specified inline query in the input field. Can be empty, in which case just the bot’s username will be inserted.
     * \param $switch_inline_query_current_chat String Optional. If set, pressing the button will insert the bot‘s username and the specified inline query in the current chat's input field. Can be empty, in which case only the bot’s username will be inserted.
     * \param $callback_game Array Optional. Description of the game that will be launched when the user presses the button. NOTE: This type of button must always be the first button in the first row.
     * \param $pay Boolean Optional. Specify True, to send a Pay button. NOTE: This type of button must always be the first button in the first row.
     * \param $login_url Array Optional. An HTTP URL used to automatically authorize the user. Can be used as a replacement for the Telegram Login Widget. See LoginUrl object definition.
     * \return array The requested button as an associative array.
     */
    public function buildInlineKeyboardButton(
        $text,
        $url = '',
        $callback_data = '',
        $switch_inline_query = null,
        $switch_inline_query_current_chat = null,
        $callback_game = null, // Changed to null default, expect array if used
        $pay = false, // Changed default to false
        $login_url = null // Added login_url, expect array if used
    ) {
        $replyMarkup = [
            'text' => $text,
        ];
        // Use elseif to ensure only one optional field is set per Telegram rules
        if ($url !== '') {
            $replyMarkup['url'] = $url;
        } elseif ($callback_data !== '') {
            $replyMarkup['callback_data'] = $callback_data;
        } elseif ($switch_inline_query !== null) { // Use !== null for clarity
            $replyMarkup['switch_inline_query'] = $switch_inline_query;
        } elseif ($switch_inline_query_current_chat !== null) { // Use !== null
            $replyMarkup['switch_inline_query_current_chat'] = $switch_inline_query_current_chat;
        } elseif ($callback_game !== null && is_array($callback_game)) { // Check if it's an array
            $replyMarkup['callback_game'] = (object)$callback_game; // Telegram expects an object
        } elseif ($pay === true) { // Explicitly check for true
            $replyMarkup['pay'] = true;
        } elseif ($login_url !== null && is_array($login_url)) { // Check if it's an array
             $replyMarkup['login_url'] = $login_url; // Expects LoginUrl object structure
        }

        return $replyMarkup;
    }


    /// Create a KeyboardButton

    /** This object represents one button of the reply keyboard. For simple text buttons String can be used instead of this object to specify text of the button. Optional fields request_contact, request_location, and request_poll are mutually exclusive.
     * \param $text String; Text of the button. If none of the optional fields are used, it will be sent as a message when the button is pressed
     * \param $request_contact Boolean Optional. If True, the user's phone number will be sent as a contact when the button is pressed. Available in private chats only
     * \param $request_location Boolean Optional. If True, the user's current location will be sent when the button is pressed. Available in private chats only
     * \param $request_poll Array Optional. If specified, the user will be asked to create a poll and send it to the bot when the button is pressed. Available in private chats only. See KeyboardButtonPollType object definition.
     * \param $web_app Array Optional. If specified, the described Web App will be launched when the button is pressed. The Web App will be able to send a “web_app_data” service message. Available in private chats only. See WebAppInfo object definition.
     * \return array The requested button as an associative array.
     */
    public function buildKeyboardButton($text, $request_contact = false, $request_location = false, $request_poll = null, $web_app = null)
    {
        $replyMarkup = [
            'text' => $text,
        ];
        // Use elseif as these are mutually exclusive
        if ($request_contact === true) {
            $replyMarkup['request_contact'] = true;
        } elseif ($request_location === true) {
            $replyMarkup['request_location'] = true;
        } elseif ($request_poll !== null && is_array($request_poll)) {
            $replyMarkup['request_poll'] = $request_poll; // Expects KeyboardButtonPollType structure
        }

        // web_app can be combined with text
        if ($web_app !== null && is_array($web_app)) {
             $replyMarkup['web_app'] = $web_app; // Expects WebAppInfo structure
        }


        return $replyMarkup;
    }


    /// Hide a custom keyboard

    /** Upon receiving a message with this object, Telegram clients will remove the current custom keyboard and display the default letter-keyboard. By default, custom keyboards are displayed until a new keyboard is sent by a bot. An exception is made for one-time keyboards that are hidden immediately after the user presses a button.
     * \param $selective Boolean Use this parameter if you want to remove the keyboard for specific users only. Targets: 1) users that are @mentioned in the text of the Message object; 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     * \return string The requested keyboard removal directive as a JSON string.
     */
    public function buildKeyBoardHide($selective = true)
    {
        $replyMarkup = [
            'remove_keyboard' => true,
            'selective'       => $selective,
        ];
        return json_encode($replyMarkup);
    }


    /// Display a reply interface to the user
    /* Upon receiving a message with this object, Telegram clients will display a reply interface to the user (act as if the user has selected the bot‘s message and tapped ’Reply'). This can be extremely useful if you want to create user-friendly step-by-step interfaces without having to sacrifice privacy mode.
     * \param $selective Boolean Use this parameter if you want to force reply from specific users only. Targets: 1) users that are @mentioned in the text of the Message object; 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     * \param $input_field_placeholder String Optional. The placeholder to be shown in the input field when the reply is active; 1-64 characters
     * \return string The requested force reply directive as a JSON string.
     */
    public function buildForceReply($selective = true, $input_field_placeholder = null)
    {
        $replyMarkup = [
            'force_reply' => true,
            'selective'   => $selective,
        ];
         if ($input_field_placeholder !== null) {
            $replyMarkup['input_field_placeholder'] = $input_field_placeholder;
        }
        return json_encode($replyMarkup);
    }


    // Payments
    /// Send an invoice

    /**
     * See <a href="https://core.telegram.org/bots/api#sendinvoice">sendInvoice</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendInvoice(array $content)
    {
         // Ensure prices & reply_markup are JSON encoded if needed
        if (isset($content['prices']) && is_array($content['prices'])) {
            $content['prices'] = json_encode($content['prices']);
        }
        if (isset($content['reply_markup']) && is_array($content['reply_markup'])) {
            $content['reply_markup'] = json_encode($content['reply_markup']);
        }
        return $this->endpoint('sendInvoice', $content);
    }

    /// Answer a shipping query

    /**
     * See <a href="https://core.telegram.org/bots/api#answershippingquery">answerShippingQuery</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function answerShippingQuery(array $content)
    {
         // Ensure shipping_options are JSON encoded if needed
        if (isset($content['shipping_options']) && is_array($content['shipping_options'])) {
            $content['shipping_options'] = json_encode($content['shipping_options']);
        }
        return $this->endpoint('answerShippingQuery', $content);
    }

    /// Answer a PreCheckout query

    /**
     * See <a href="https://core.telegram.org/bots/api#answerprecheckoutquery">answerPreCheckoutQuery</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function answerPreCheckoutQuery(array $content)
    {
        return $this->endpoint('answerPreCheckoutQuery', $content);
    }

    /// Set Passport data errors

    /**
     * See <a href="https://core.telegram.org/bots/api#setpassportdataerrors">setPassportDataErrors</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setPassportDataErrors(array $content)
    {
         // Ensure errors are JSON encoded if needed
        if (isset($content['errors']) && is_array($content['errors'])) {
            $content['errors'] = json_encode($content['errors']);
        }
        return $this->endpoint('setPassportDataErrors', $content);
    }

    /// Send a Game

    /**
     * See <a href="https://core.telegram.org/bots/api#sendgame">sendGame</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendGame(array $content)
    {
        // Ensure reply_markup key exists and is JSON encoded if needed
        if (isset($content['reply_markup']) && is_array($content['reply_markup'])) {
            $content['reply_markup'] = json_encode($content['reply_markup']);
        }
        return $this->endpoint('sendGame', $content);
    }

    /// Send a video note

    /**
     * See <a href="https://core.telegram.org/bots/api#sendvideonote">sendVideoNote</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function sendVideoNote(array $content)
    {
        // Ensure reply_markup key exists and is JSON encoded if needed
        if (isset($content['reply_markup']) && is_array($content['reply_markup'])) {
            $content['reply_markup'] = json_encode($content['reply_markup']);
        }
        return $this->endpoint('sendVideoNote', $content);
    }

    /// Restrict Chat Member

    /**
     * See <a href="https://core.telegram.org/bots/api#restrictchatmember">restrictChatMember</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function restrictChatMember(array $content)
    {
         // Ensure permissions are JSON encoded if needed
        if (isset($content['permissions']) && is_array($content['permissions'])) {
            $content['permissions'] = json_encode($content['permissions']);
        }
        return $this->endpoint('restrictChatMember', $content);
    }

    /// Promote Chat Member

    /**
     * See <a href="https://core.telegram.org/bots/api#promotechatmember">promoteChatMember</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function promoteChatMember(array $content)
    {
        return $this->endpoint('promoteChatMember', $content);
    }

    /// Set chat Administrator custom title

    /**
     * See <a href="https://core.telegram.org/bots/api#setchatadministratorcustomtitle">setChatAdministratorCustomTitle</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setChatAdministratorCustomTitle(array $content)
    {
        return $this->endpoint('setChatAdministratorCustomTitle', $content);
    }

    /// Ban a channel chat in a super group or channel

    /**
     * See <a href="https://core.telegram.org/bots/api#banchatsenderchat">banChatSenderChat</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function banChatSenderChat(array $content)
    {
        return $this->endpoint('banChatSenderChat', $content);
    }

    /// Unban a channel chat in a super group or channel

    /**
     * See <a href="https://core.telegram.org/bots/api#unbanchatsenderchat">unbanChatSenderChat</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function unbanChatSenderChat(array $content)
    {
        return $this->endpoint('unbanChatSenderChat', $content);
    }

    /// Set default chat permission for all members

    /**
     * See <a href="https://core.telegram.org/bots/api#setchatpermissions">setChatPermissions</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function setChatPermissions(array $content)
    {
         // Ensure permissions are JSON encoded if needed
        if (isset($content['permissions']) && is_array($content['permissions'])) {
            $content['permissions'] = json_encode($content['permissions']);
        }
        return $this->endpoint('setChatPermissions', $content);
    }

    //// Export Chat Invite Link

    /**
     * See <a href="https://core.telegram.org/bots/api#exportchatinvitelink">exportChatInviteLink</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function exportChatInviteLink(array $content)
    {
        return $this->endpoint('exportChatInviteLink', $content);
    }

    //// Create Chat Invite Link

    /**
     * See <a href="https://core.telegram.org/bots/api#createchatinvitelink">createChatInviteLink</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function createChatInviteLink(array $content)
    {
        return $this->endpoint('createChatInviteLink', $content);
    }

    //// Edit Chat Invite Link

    /**
     * See <a href="https://core.telegram.org/bots/api#editchatinvitelink">editChatInviteLink</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function editChatInviteLink(array $content)
    {
        return $this->endpoint('editChatInviteLink', $content);
    }

    //// Revoke Chat Invite Link

    /**
     * See <a href="https://core.telegram.org/bots/api#revokechatinvitelink">revokeChatInviteLink</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function revokeChatInviteLink(array $content)
    {
        return $this->endpoint('revokeChatInviteLink', $content);
    }

    //// Approve chat join request

    /**
     * See <a href="https://core.telegram.org/bots/api#approvechatjoinrequest">approveChatJoinRequest</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function approveChatJoinRequest(array $content)
    {
        return $this->endpoint('approveChatJoinRequest', $content);
    }

    //// Decline chat join request

    /**
     * See <a href="https://core.telegram.org/bots/api#declinechatjoinrequest">declineChatJoinRequest</a> for the input values
     * \param $content the request parameters as array
     * \return the JSON Telegram's reply.
     */
    public function declineChatJoinRequest(array $content)
    {
        return $this->endpoint('declineChatJoinRequest', $content);
    }

    /// Set Chat Photo

    /**
     * See <a href="https://core.telegram.org/bots/api#setchatphoto">setChatPhoto</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id' and 'photo' which should be InputFile)
     * \return the JSON Telegram's reply.
     */
    public function setChatPhoto(array $content)
    {
        // Ensure 'photo' is correctly handled (e.g., using CURLFile)
        if (isset($content['photo']) && is_string($content['photo']) && class_exists('CURLFile')) {
            // Assuming $content['photo'] is a file path
            $filePath = realpath($content['photo']);
            if ($filePath) {
                $content['photo'] = new CURLFile($filePath);
            } else {
                // Handle error: file not found
                return ['ok' => false, 'description' => 'Chat photo file not found'];
            }
        } elseif (isset($content['photo']) && is_string($content['photo'])) {
             // Fallback for older PHP? Requires '@' prefix which might be disabled.
             $content['photo'] = '@' . realpath($content['photo']);
        }
        return $this->endpoint('setChatPhoto', $content);
    }


    /// Delete Chat Photo

    /**
     * See <a href="https://core.telegram.org/bots/api#deletechatphoto">deleteChatPhoto</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id')
     * \return the JSON Telegram's reply.
     */
    public function deleteChatPhoto(array $content)
    {
        return $this->endpoint('deleteChatPhoto', $content);
    }

    /// Set Chat Title

    /**
     * See <a href="https://core.telegram.org/bots/api#setchattitle">setChatTitle</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id' and 'title')
     * \return the JSON Telegram's reply.
     */
    public function setChatTitle(array $content)
    {
        return $this->endpoint('setChatTitle', $content);
    }

    /// Set Chat Description

    /**
     * See <a href="https://core.telegram.org/bots/api#setchatdescription">setChatDescription</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id', 'description' is optional)
     * \return the JSON Telegram's reply.
     */
    public function setChatDescription(array $content)
    {
        return $this->endpoint('setChatDescription', $content);
    }

    /// Pin Chat Message

    /**
     * See <a href="https://core.telegram.org/bots/api#pinchatmessage">pinChatMessage</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id' and 'message_id')
     * \return the JSON Telegram's reply.
     */
    public function pinChatMessage(array $content)
    {
        return $this->endpoint('pinChatMessage', $content);
    }

    /// Unpin Chat Message

    /**
     * See <a href="https://core.telegram.org/bots/api#unpinchatmessage">unpinChatMessage</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id', 'message_id' is optional)
     * \return the JSON Telegram's reply.
     */
    public function unpinChatMessage(array $content)
    {
        return $this->endpoint('unpinChatMessage', $content);
    }

    /// Unpin All Chat Messages

    /**
     * See <a href="https://core.telegram.org/bots/api#unpinallchatmessages">unpinAllChatMessages</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id')
     * \return the JSON Telegram's reply.
     */
    public function unpinAllChatMessages(array $content)
    {
        return $this->endpoint('unpinAllChatMessages', $content);
    }

    /// Get Sticker Set

    /**
     * See <a href="https://core.telegram.org/bots/api#getstickerset">getStickerSet</a> for the input values
     * \param $content the request parameters as array (must include 'name')
     * \return the JSON Telegram's reply.
     */
    public function getStickerSet(array $content)
    {
        return $this->endpoint('getStickerSet', $content);
    }

    /// Upload Sticker File

    /**
     * See <a href="https://core.telegram.org/bots/api#uploadstickerfile">uploadStickerFile</a> for the input values
     * \param $content the request parameters as array (must include 'user_id' and 'sticker' (InputFile))
     * \return the JSON Telegram's reply.
     */
    public function uploadStickerFile(array $content)
    {
        // Ensure 'sticker' is correctly handled (e.g., using CURLFile)
        if (isset($content['sticker']) && is_string($content['sticker']) && class_exists('CURLFile')) {
             $filePath = realpath($content['sticker']);
             if ($filePath) {
                 $content['sticker'] = new CURLFile($filePath);
             } else {
                 return ['ok' => false, 'description' => 'Sticker file not found'];
             }
        } elseif (isset($content['sticker']) && is_string($content['sticker'])) {
             $content['sticker'] = '@' . realpath($content['sticker']);
        }

        // Ensure sticker_format is provided if using CURLFile/newer API versions
        if (!isset($content['sticker_format'])) {
            // Attempt to guess or require it? For now, assume it's provided.
            // Example: $content['sticker_format'] = 'static'; // or 'animated', 'video'
        }

        return $this->endpoint('uploadStickerFile', $content);
    }


    /// Create New Sticker Set

    /**
     * See <a href="https://core.telegram.org/bots/api#createnewstickerset">createNewStickerSet</a> for the input values
     * \param $content the request parameters as array (must include 'user_id', 'name', 'title', 'stickers' array)
     * \return the JSON Telegram's reply.
     */
    public function createNewStickerSet(array $content)
    {
        // Ensure stickers array is JSON encoded
        if (isset($content['stickers']) && is_array($content['stickers'])) {
            $content['stickers'] = json_encode($content['stickers']);
        } else {
             return ['ok' => false, 'description' => "'stickers' array is required and must be an array."];
        }
        return $this->endpoint('createNewStickerSet', $content);
    }


    /// Add Sticker To Set

    /**
     * See <a href="https://core.telegram.org/bots/api#addstickertoset">addStickerToSet</a> for the input values
     * \param $content the request parameters as array (must include 'user_id', 'name', 'sticker' object)
     * \return the JSON Telegram's reply.
     */
    public function addStickerToSet(array $content)
    {
        // Ensure sticker object is JSON encoded
        if (isset($content['sticker']) && is_array($content['sticker'])) {
            $content['sticker'] = json_encode($content['sticker']);
        } else {
             return ['ok' => false, 'description' => "'sticker' object is required and must be an array."];
        }
        return $this->endpoint('addStickerToSet', $content);
    }


    /// Set Sticker Position In Set

    /**
     * See <a href="https://core.telegram.org/bots/api#setstickerpositioninset">setStickerPositionInSet</a> for the input values
     * \param $content the request parameters as array (must include 'sticker' (file_id) and 'position')
     * \return the JSON Telegram's reply.
     */
    public function setStickerPositionInSet(array $content)
    {
        return $this->endpoint('setStickerPositionInSet', $content);
    }

    /// Delete Sticker From Set

    /**
     * See <a href="https://core.telegram.org/bots/api#deletestickerfromset">deleteStickerFromSet</a> for the input values
     * \param $content the request parameters as array (must include 'sticker' (file_id))
     * \return the JSON Telegram's reply.
     */
    public function deleteStickerFromSet(array $content)
    {
        return $this->endpoint('deleteStickerFromSet', $content);
    }

    /// Set Sticker Set Thumbnail (formerly setStickerSetThumb)

    /**
     * See <a href="https://core.telegram.org/bots/api#setstickersetthumbnail">setStickerSetThumbnail</a> for the input values
     * \param $content the request parameters as array (must include 'name', 'user_id', optional 'thumbnail' (InputFile or file_id))
     * \return the JSON Telegram's reply.
     */
    public function setStickerSetThumbnail(array $content)
    {
        // Handle thumbnail upload if path is provided
        if (isset($content['thumbnail']) && is_string($content['thumbnail']) && file_exists($content['thumbnail']) && class_exists('CURLFile')) {
             $filePath = realpath($content['thumbnail']);
             if ($filePath) {
                 $content['thumbnail'] = new CURLFile($filePath);
             } else {
                  // Keep as string (assuming it's a file_id) or handle error
             }
        } elseif (isset($content['thumbnail']) && is_string($content['thumbnail']) && file_exists($content['thumbnail'])) {
             $content['thumbnail'] = '@' . realpath($content['thumbnail']);
        }
        // Rename thumb to thumbnail for API consistency
        if (isset($content['thumb'])) {
             if (!isset($content['thumbnail'])) { // Avoid overwriting if both exist
                 $content['thumbnail'] = $content['thumb'];
             }
             unset($content['thumb']);
        }

        return $this->endpoint('setStickerSetThumbnail', $content); // Use the new endpoint name
    }

    /**
     * @deprecated Use setStickerSetThumbnail instead.
     */
    public function setStickerSetThumb(array $content)
    {
        trigger_error('setStickerSetThumb() is deprecated; use setStickerSetThumbnail() instead.', E_USER_DEPRECATED);
        // Map 'thumb' to 'thumbnail' if necessary for compatibility before calling the new method
         if (isset($content['thumb']) && !isset($content['thumbnail'])) {
             $content['thumbnail'] = $content['thumb'];
             unset($content['thumb']);
         }
        return $this->setStickerSetThumbnail($content);
    }


    /// Delete a message

    /**
     * See <a href="https://core.telegram.org/bots/api#deletemessage">deleteMessage</a> for the input values
     * \param $content the request parameters as array (must include 'chat_id' and 'message_id')
     * \return the JSON Telegram's reply.
     */
    public function deleteMessage(array $content)
    {
        return $this->endpoint('deleteMessage', $content);
    }

    /// Receive incoming messages using polling

    /** Use this method to receive incoming updates using long polling.
     * \param $offset Integer Identifier of the first update to be returned. Must be greater by one than the highest among the identifiers of previously received updates. By default, updates starting with the earliest unconfirmed update are returned. An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id.
     * \param $limit Integer Limits the number of updates to be retrieved. Values between 1—100 are accepted. Defaults to 100
     * \param $timeout Integer Timeout in seconds for long polling. Defaults to 0, i.e. usual short polling. Should be positive, short polling should be used for testing only.
     * \param $allowed_updates Array Optional. A JSON-serialized list of the update types you want your bot to receive. For example, specify [“message”, “edited_channel_post”, “callback_query”] to only receive updates of these types. See Update object for a complete list of available update types. Specify an empty list to receive all update types except chat_member (default). If not specified, the previous setting will be used. Please note that this parameter doesn't affect updates created before the call to the getUpdates, so unwanted updates may be received for a short period of time.
     * \param $confirm Boolean If true, confirms the last received update_id to Telegram after fetching updates. Default to true.
     * \return array The updates response from Telegram as an associative array.
     */
    public function getUpdates($offset = 0, $limit = 100, $timeout = 0, $allowed_updates = null, $confirm = true)
    {
        $content = ['offset' => $offset, 'limit' => $limit, 'timeout' => $timeout];
        if ($allowed_updates !== null && is_array($allowed_updates)) {
            $content['allowed_updates'] = json_encode($allowed_updates);
        }

        // Make the API call
        $this->updates = $this->endpoint('getUpdates', $content, false); // getUpdates uses GET

        // Confirm updates if requested and successful
        if ($confirm && isset($this->updates['ok']) && $this->updates['ok'] === true && !empty($this->updates['result']) && is_array($this->updates['result'])) {
            $updateCount = count($this->updates['result']);
            $lastUpdate = $this->updates['result'][$updateCount - 1];

            // *** FIX: Check if last update has 'update_id' before accessing it ***
            if (isset($lastUpdate['update_id'])) {
                $last_element_id = $lastUpdate['update_id'] + 1;
                $confirmContent = ['offset' => $last_element_id, 'limit' => 1, 'timeout' => $timeout]; // Limit 1 is enough for confirmation
                // Send confirmation request (result usually not needed, but could be checked)
                $this->endpoint('getUpdates', $confirmContent, false);
            } else {
                // Handle case where last update is missing 'update_id' (optional logging/error handling)
                 if ($this->log_errors && class_exists('TelegramErrorLogger')) {
                     TelegramErrorLogger::log(['ok' => false, 'error' => 'Last update missing update_id during confirmation'], [$this->updates]);
                 }
            }
        }

        return $this->updates; // Return the fetched updates
    }



    /// Serve an update

    /** Use this method to use the bultin function like Text() or Username() on a specific update index from the array fetched by getUpdates().
     * \param $update Integer The index of the update in the updates array (0-based).
     * \return bool True if the update was successfully served, False otherwise.
     */
    public function serveUpdate($update)
    {
        // Check if updates were fetched, are OK, and the index is valid
        // *** FIX: The check here was already correct for preventing the warning on line 874 ***
        if (isset($this->updates['ok']) && $this->updates['ok'] === true && isset($this->updates['result']) && is_array($this->updates['result']) && isset($this->updates['result'][$update])) {
             $this->setData($this->updates['result'][$update]); // Use setData to also reset update_type cache
             return true;
        } else {
             // Handle error: Set data to null or an empty array, and reset type
             $this->setData([]); // Set to empty array to avoid null issues later
             return false;
        }
    }


    /// Return current update type

    /**
     * Determines and returns the type of the current update stored in $this->data. Caches the result.
     *
     * @return string|false The update type constant (e.g., self::MESSAGE) or False on failure/unknown type.
     */
    public function getUpdateType()
    {
        // Return cached type if available
        if ($this->update_type !== null) {
            return $this->update_type;
        }

        $update = $this->getData(); // Ensure we have data loaded

        // If data is empty or not an array, cannot determine type
        if (empty($update) || !is_array($update)) {
            $this->update_type = false; // Cache failure
            return false;
        }

        // Determine type based on existing keys
        if (isset($update['inline_query'])) {
            $this->update_type = self::INLINE_QUERY;
        } elseif (isset($update['callback_query'])) {
            $this->update_type = self::CALLBACK_QUERY;
        } elseif (isset($update['edited_message'])) {
            $this->update_type = self::EDITED_MESSAGE;
        } elseif (isset($update['channel_post'])) { // Check channel_post before message
            $this->update_type = self::CHANNEL_POST;
        } elseif (isset($update['message'])) {
            // Message subtypes - order might matter if multiple keys exist (e.g., photo reply)
            $message = $update['message'];
            if (isset($message['photo'])) {
                $this->update_type = self::PHOTO;
            } elseif (isset($message['video'])) {
                $this->update_type = self::VIDEO;
            } elseif (isset($message['audio'])) {
                $this->update_type = self::AUDIO;
            } elseif (isset($message['voice'])) {
                $this->update_type = self::VOICE;
            } elseif (isset($message['animation'])) {
                $this->update_type = self::ANIMATION;
            } elseif (isset($message['sticker'])) {
                $this->update_type = self::STICKER;
            } elseif (isset($message['document'])) {
                $this->update_type = self::DOCUMENT;
            } elseif (isset($message['location'])) {
                $this->update_type = self::LOCATION;
            } elseif (isset($message['contact'])) {
                $this->update_type = self::CONTACT;
            } elseif (isset($message['new_chat_member']) || isset($message['new_chat_members'])) { // Check both singular/plural
                $this->update_type = self::NEW_CHAT_MEMBER;
            } elseif (isset($message['left_chat_member'])) {
                $this->update_type = self::LEFT_CHAT_MEMBER;
            } elseif (isset($message['reply_to_message'])) {
                 // A reply is a property, not a primary type. The primary type (text, photo etc.) is more specific.
                 // We already determined the specific type above. If we reach here, it's likely just text.
                 // If we want a dedicated REPLY type, it needs careful handling.
                 // For now, assume it's a text message if no other type matched.
                 if (isset($message['text'])) {
                     $this->update_type = self::MESSAGE; // Treat text reply as MESSAGE
                 } else {
                     // If it's a reply but not text and didn't match other media? Should be rare.
                     // Fallback to generic MESSAGE or REPLY? Let's stick to MESSAGE for now if text exists.
                     // If no text, maybe REPLY? But that loses info. Let's default to MESSAGE if text exists.
                     // If it's a reply to media, the media type check should have caught it earlier.
                     // Let's prioritize the content type over the reply status for getUpdateType().
                     // If we only have reply_to_message and no other content key:
                     // This case seems unlikely based on API docs.
                     // If we must have REPLY type:
                     // $this->update_type = self::REPLY;
                     // But let's assume text if nothing else fits:
                      if (isset($message['text'])) {
                         $this->update_type = self::MESSAGE;
                      } else {
                          // Fallback if it's a reply with no recognizable content?
                          $this->update_type = self::MESSAGE; // Default assumption
                      }
                 }
            } elseif (isset($message['text'])) { // Must be after other content checks
                $this->update_type = self::MESSAGE;
            } else {
                // Other message types (venue, game, invoice, etc.) not explicitly handled here yet
                 $this->update_type = false; // Unknown message content
            }
        } elseif (isset($update['my_chat_member'])) {
            $this->update_type = self::MY_CHAT_MEMBER;
        } else {
            $this->update_type = false; // Unknown update structure
        }

        return $this->update_type;
    }


    private function sendAPIRequest($url, array $content, $post = true)
    {
        // Handle chat_id in URL for GET requests if necessary, though endpoint() handles this better now.
        // This part might be redundant if endpoint() always prepares the URL correctly.
        // if (!$post && isset($content['chat_id'])) {
        //     $url = $url.'?chat_id='.$content['chat_id'];
        //     unset($content['chat_id']); // No, keep it for the endpoint call if needed there
        // }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false); // Don't include header in output

        // Set method and payload
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            // Check for CURLFile objects (for file uploads)
            $hasFile = false;
            if (!empty($content)) {
                 foreach ($content as $key => $value) {
                     if ($value instanceof CURLFile) {
                         $hasFile = true;
                         break;
                     }
                     // Handle older '@' syntax if needed, though CURLFile is preferred
                     if (is_string($value) && strpos($value, '@') === 0) {
                          // Potentially requires PHP < 5.5 or specific curl settings
                          // $hasFile = true; // May not need explicit flag for '@'
                     }
                 }
                 // Use multipart/form-data for file uploads, otherwise default application/x-www-form-urlencoded
                 curl_setopt($ch, CURLOPT_POSTFIELDS, $hasFile ? $content : http_build_query($content));
            } else {
                 // Send empty POST body if content is empty
                 curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            }

        } else {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            // GET parameters should be in the URL, constructed by endpoint() or manually
        }

        // Proxy settings
        if (!empty($this->proxy)) {
            if (array_key_exists('url', $this->proxy)) {
                curl_setopt($ch, CURLOPT_PROXY, $this->proxy['url']);
            }
            if (array_key_exists('port', $this->proxy)) {
                curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy['port']);
            }
            if (array_key_exists('type', $this->proxy)) {
                // CURLPROXY_HTTP, CURLPROXY_SOCKS4, CURLPROXY_SOCKS5, etc.
                // Ensure the constant value is used if 'type' is a string like 'socks5'
                $proxyType = $this->proxy['type'];
                if (is_string($proxyType)) {
                     if (defined('CURLPROXY_' . strtoupper($proxyType))) {
                         $proxyType = constant('CURLPROXY_' . strtoupper($proxyType));
                     } else {
                         // Handle unknown proxy type string - default or error?
                         // For now, assume it's passed as the correct CURL constant integer
                     }
                }
                curl_setopt($ch, CURLOPT_PROXYTYPE, $proxyType);
            }
            if (array_key_exists('auth', $this->proxy)) { // Format "user:password"
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy['auth']);
            }
        }

        // SSL verification - consider making this configurable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Warning: Disabling verification is insecure
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Use 0 in production only if necessary and understand risks

        // Timeout settings
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Connection timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);      // Total execution timeout

        // Execute request
        $result = curl_exec($ch);
        $curl_error_code = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Handle CURL errors
        if ($result === false) {
            $result_data = ['ok' => false, 'curl_error_code' => $curl_error_code, 'curl_error' => $curl_error];
        } else {
            // Attempt to decode JSON, but keep raw result for logging if needed
             $decoded_result = json_decode($result, true);
             if (json_last_error() !== JSON_ERROR_NONE) {
                 // JSON decode failed - could be HTML error page, etc.
                 $result_data = ['ok' => false, 'error_code' => null, 'description' => 'Invalid JSON received from API', 'raw_response' => $result];
             } else {
                 $result_data = $decoded_result; // Use the decoded array
             }
        }


        // Error Logging
        // Log if the request failed (curl error or API error)
        if ($this->log_errors && class_exists('TelegramErrorLogger') && ($result === false || (isset($result_data['ok']) && $result_data['ok'] === false))) {
                $currentData = $this->getData(); // Get current update context if available
                // Prepare context: current update + parameters sent
                $loggerContext = [];
                if ($currentData !== null) $loggerContext['update_data'] = $currentData;
                if (!empty($content)) $loggerContext['request_params'] = $content;

                // Log the result data (which includes error info) and the context
                TelegramErrorLogger::log($result_data, $loggerContext);
        }

        // Return the raw JSON string as previous versions did, endpoint() handles decoding
        return $result === false ? json_encode($result_data) : $result;
    }

} // End of Telegram Class

// Helper for Uploading file using CURL - Compatibility for PHP < 5.5
if (!function_exists('curl_file_create')) {
    /**
     * Create a CURLFile object (or fallback string for older PHP).
     *
     * @param string $filename Path to the file.
     * @param string $mimetype Mime type of the file.
     * @param string $postname Name of the file to be used in the upload data.
     * @return CURLFile|string Returns a CURLFile object or the '@' syntax string.
     */
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
        // Check if CURLFile class exists (PHP >= 5.5)
        if (class_exists('CURLFile')) {
            return new CURLFile($filename, $mimetype, $postname ?: basename($filename));
        }

        // Fallback for PHP < 5.5 (use '@' syntax)
        // Note: This might be disabled by `safe_mode` or `open_basedir`.
        //       It's generally recommended to use PHP >= 5.5 for CURL file uploads.
        $value = "@{$filename}";
        if (!empty($mimetype)) {
            $value .= ';type=' . $mimetype;
        }
        // Filename in POST is handled differently with '@' syntax, often implicit
        // Adding ';filename=' might not always work as expected with '@'
        // $value .= ';filename=' . ($postname ?: basename($filename));

        return $value;
    }
}
