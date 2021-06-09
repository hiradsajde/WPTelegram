<?php

require 'vendor/autoload.php';
require 'translates/fa.php';

use sobot\telegram\bot;
use wpbot\conector\wp_json as wp;

$bot = new bot("1230541965:AAGayNwDajPmwZQNHkeHS9zV6OH7_7Y7R2E");
$bot->setWebhook("https://41d94a0bb810.ngrok.io");
$bot->getUpdate();

$wp = new wp("https://mizbanfa.net/blog");

function categoryKeyboard($page=1, $per_page=10)
{
    global $wp;
    $dinamic_keyboard = [];
    foreach ($wp->getCategories($page, $per_page) as $category) {
        $dinamic_keyboard[] = [['text' => $category->name , 'callback_data' => 'blog-category-'.$category->id]];
    }
    $dinamic_keyboard[] = [
        ['text' => '⬅️' , 'callback_data' => 'blog-page-'.($page-1)],
        ['text' => '➡' , 'callback_data' => 'blog-page-'.($page+1)]
    ];
    return $dinamic_keyboard;
}
function getPosts($category, $page)
{
    global $bot,$wp,$LANG;
    if ($page > 0) {
        $post = $wp->getPosts($category, $page)[0];
        if (isset($post)) {
            $title = $post->title->rendered;
            $summary = str_replace('&hellip;', '...', strip_tags($post->excerpt->rendered));
            $link = $post->guid->rendered;
            $bot->editMessageText("<b>$title</b>".PHP_EOL.PHP_EOL." <i>$summary</i>", [
            'parse_mode' => 'html',
            'reply_markup' => $bot->inline_keyboard([
                [['text' => $LANG['SHOW_MORE'] , 'url' => $link]],
                [
                    ['text' => '⬅️' , 'callback_data' => 'blog-category-'.$category.'-'.($page-1)],
                    ['text' => '➡️' , 'callback_data' => 'blog-category-'.$category.'-'.($page+1)],
                ],
                [['text' => $LANG['BACK'] , 'callback_data' => 'blog']],
            ]),
            ]);
        } else {
            $bot->answerCallbackQuery($LANG['not_page']);
        }
    } else {
        $bot->answerCallbackQuery($LANG['not_page']);
    }
}
//text manager

switch ($bot->text) {
    case '/start':
        $bot->sendMessage($LANG['start'], [
            'disable_web_page_preview' => true,
            'reply_markup' => $bot->keyboard([
                [['text' => $LANG['BLOG']]],
                [
                    ['text' => $LANG['ABOUT']],
                    ['text' => $LANG['CONTACT']],
                ]
            ]),
        ]);
    break;
    case $LANG['BLOG']:
        $dinamic_keyboard = categoryKeyboard(1, 4);
        $bot->sendMessage($LANG['blog'], [
            'reply_markup' => $bot->inline_keyboard($dinamic_keyboard),
        ]);
    break;
    case $LANG['ABOUT']:
        $bot->sendMessage($LANG['about']);
    break;
    case $LANG['CONTACT']:
        $bot->sendMessage($LANG['contact']);
    break;
}

// data manager

if (strpos($bot->data, 'blog-') !== false) {
    $exp = explode('-', $bot->data);
    $type = $exp[1];
    if ($type == 'page') {
        $page = $exp[2];
        if ($page <= 0) {
            $bot->answerCallbackQuery($LANG['not_page']);
        } else {
            $dinamic_keyboard = categoryKeyboard($page, 4);
            if (count($dinamic_keyboard) > 1) {
                $bot->editMessageText($LANG['blog'], [
                'reply_markup' => $bot->inline_keyboard($dinamic_keyboard),
            ]);
            } else {
                $bot->answerCallbackQuery($LANG['not_page']);
            }
        }
    } elseif ($type == 'category') {
        $category = $exp[2];
        $page = $exp[3] ?? 1;
        getPosts($category, $page);
    }
} else {
    switch ($bot->data) {
        case 'blog':
        $dinamic_keyboard = categoryKeyboard(1, 4);
        $bot->editMessageText($LANG['blog'], [
            'reply_markup' => $bot->inline_keyboard($dinamic_keyboard),
        ]);
        break;
    }
}
