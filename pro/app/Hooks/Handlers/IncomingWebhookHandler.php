<?php

namespace FluentSupportPro\App\Hooks\Handlers;

use FluentSupport\App\Models\MailBox;
use FluentSupport\App\Models\Meta;
use FluentSupport\App\Services\Helper;
use FluentSupport\Framework\Support\Arr;
use FluentSupportPro\App\Services\Integrations\FluentEmailPiping\ByMailHandler;
use FluentSupportPro\App\Services\ProHelper;

class IncomingWebhookHandler extends ByMailHandler
{
    public function handleIncomingWebhook()
    {
        $request = Helper::FluentSupport('request');
        if ($request->method() != 'POST') {
            return;
        }

        $postData = $request->get();

        $token = sanitize_text_field(Arr::get($postData, 'token'));
        if (empty($token)) {
            wp_send_json_error([
                'message' => __('Webhook token not found', 'fluent-support-pro'),
                'type'    => 'empty_webhook_token'
            ], 423);
        }

        $webhook = Meta::where('key', $token)->first();

        if (!$webhook || !Arr::get($postData, 'sender.email') || !is_email(Arr::get($postData, 'sender.email'))) {
            wp_send_json_error([
                'message' => __('Invalid Webhook URL or email does not exist', 'fluent-support-pro'),
                'type'    => 'invalid_webhook_url'
            ], 423);
        }

        //TODO: may be we can remove this line
        $mailboxId = Arr::get($postData, 'mailbox_id', 0);

        if(!$mailboxId){
            $webhookData = ProHelper::safeUnserialize($webhook->value);
            $mailboxId = (is_array($webhookData) && isset($webhookData['mailbox'])) ? $webhookData['mailbox'] : 0;
        }

        $mailBox = null;
        if($mailboxId) {
            $mailBox = MailBox::where('id', $mailboxId)->first();
        }

        if(!$mailBox) {
            $mailBox = Helper::getDefaultMailBox();
        }

        $postData['sender']['name'] = trim(Arr::get($postData, 'sender.first_name', '') . ' ' . Arr::get($postData, 'sender.last_name', ''));

        $postData['message_id'] = null;

        $postData['subject'] = Arr::get($postData, 'title');

        unset($postData['title']);

        return parent::handleEmailData($postData, $mailBox, 'webhook');
    }
}
