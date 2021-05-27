<?php
/**
 * @copyright Copyright (C) 2008-10, Sean Werkema. All rights reserved.
 * @copyright 2016 Vanilla Forums Inc. (changes only)
 * @license BSDv2
 */

namespace Nbbc;

//-----------------------------------------------------------------------------
//
//  This file is part of NBBC, the New BBCode Parser.
//
//  NBBC implements a fully-validating, high-speed, extensible parser for the
//  BBCode document language.  Its output is XHTML 1.0 Strict conformant no
//  matter what its input is.  NBBC supports the full standard BBCode language,
//  as well as comments, columns, enhanced quotes, spoilers, acronyms, wiki
//  links, several list styles, justification, indentation, and smileys, among
//  other advanced features.
//
//-----------------------------------------------------------------------------
//
//  Copyright (c) 2008-9, the Phantom Inker.  All rights reserved.
//
//  Redistribution and use in source and binary forms, with or without
//  modification, are permitted provided that the following conditions
//  are met:
//
//    * Redistributions of source code must retain the above copyright
//       notice, this list of conditions and the following disclaimer.
//
//    * Redistributions in binary form must reproduce the above copyright
//       notice, this list of conditions and the following disclaimer in
//       the documentation and/or other materials provided with the
//       distribution.
//
//  THIS SOFTWARE IS PROVIDED BY THE PHANTOM INKER "AS IS" AND ANY EXPRESS
//  OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
//  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
//  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
//  LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
//  CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
//  SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
//  BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
//  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
//  OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
//  IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
//
//-----------------------------------------------------------------------------
//

//
//-----------------------------------------------------------------------------

/**
 * Represents the default implementation of most BBCode tags.
 *
 * This class implements the standard BBCode language and our extensions,
 * as well as a default set of smileys.  While this is not strictly necessary
 * for the parser to work, without these definitions, the parser has nothing
 * to do.  Generally, the definitions in this file are sufficient for most
 * needs; however, if your needs differ, you don't want to change this file:
 * If you want additional definitions, create a BBCode object and add them
 * manually afterward:
 *
 * ```
 * $bbcode = new BBCode();
 * $bbcode->addRule(...);
 * $bbcode->addSmiley(...);
 * ```
 */
class BBCodeLibrary {

    /**
     * @var array Standard library of smiley definitions.
     */
    public $default_smileys = [
        ':)' => 'smile.gif', ':-)' => 'smile.gif',
        '=)' => 'smile.gif', '=-)' => 'smile.gif',
        ':(' => 'frown.gif', ':-(' => 'frown.gif',
        '=(' => 'frown.gif', '=-(' => 'frown.gif',
        ':D' => 'bigsmile.gif', ':-D' => 'bigsmile.gif',
        '=D' => 'bigsmile.gif', '=-D' => 'bigsmile.gif',
        '>:(' => 'angry.gif', '>:-(' => 'angry.gif',
        '>=(' => 'angry.gif', '>=-(' => 'angry.gif',
        'D:' => 'angry.gif', 'D-:' => 'angry.gif',
        'D=' => 'angry.gif', 'D-=' => 'angry.gif',
        '>:)' => 'evil.gif', '>:-)' => 'evil.gif',
        '>=)' => 'evil.gif', '>=-)' => 'evil.gif',
        '>:D' => 'evil.gif', '>:-D' => 'evil.gif',
        '>=D' => 'evil.gif', '>=-D' => 'evil.gif',
        '>;)' => 'sneaky.gif', '>;-)' => 'sneaky.gif',
        '>;D' => 'sneaky.gif', '>;-D' => 'sneaky.gif',
        'O:)' => 'saint.gif', 'O:-)' => 'saint.gif',
        'O=)' => 'saint.gif', 'O=-)' => 'saint.gif',
        ':O' => 'surprise.gif', ':-O' => 'surprise.gif',
        '=O' => 'surprise.gif', '=-O' => 'surprise.gif',
        ':?' => 'confuse.gif', ':-?' => 'confuse.gif',
        '=?' => 'confuse.gif', '=-?' => 'confuse.gif',
        ':s' => 'worry.gif', ':-S' => 'worry.gif',
        '=s' => 'worry.gif', '=-S' => 'worry.gif',
        ':|' => 'neutral.gif', ':-|' => 'neutral.gif',
        '=|' => 'neutral.gif', '=-|' => 'neutral.gif',
        ':I' => 'neutral.gif', ':-I' => 'neutral.gif',
        '=I' => 'neutral.gif', '=-I' => 'neutral.gif',
        ':/' => 'irritated.gif', ':-/' => 'irritated.gif',
        '=/' => 'irritated.gif', '=-/' => 'irritated.gif',
        ':\\' => 'irritated.gif', ':-\\' => 'irritated.gif',
        '=\\' => 'irritated.gif', '=-\\' => 'irritated.gif',
        ':P' => 'tongue.gif', ':-P' => 'tongue.gif',
        '=P' => 'tongue.gif', '=-P' => 'tongue.gif',
        'X-P' => 'tongue.gif',
        '8)' => 'bigeyes.gif', '8-)' => 'bigeyes.gif',
        'B)' => 'cool.gif', 'B-)' => 'cool.gif',
        ';)' => 'wink.gif', ';-)' => 'wink.gif',
        '^_^' => 'anime.gif', '^^;' => 'sweatdrop.gif',
        '>_>' => 'lookright.gif', '>.>' => 'lookright.gif',
        '<_<' => 'lookleft.gif', '<.<' => 'lookleft.gif',
        'XD' => 'laugh.gif', 'X-D' => 'laugh.gif',
        ';D' => 'bigwink.gif', ';-D' => 'bigwink.gif',
        ':3' => 'smile3.gif', ':-3' => 'smile3.gif',
        '=3' => 'smile3.gif', '=-3' => 'smile3.gif',
        ';3' => 'wink3.gif', ';-3' => 'wink3.gif',
        '<g>' => 'teeth.gif', '<G>' => 'teeth.gif',
        'o.O' => 'boggle.gif', 'O.o' => 'boggle.gif',
        ':blue:' => 'blue.gif',
        ':zzz:' => 'sleepy.gif',
        '<3' => 'heart.gif',
        ':star:' => 'star.gif',
    ];

    /**
     * @var array Standard rules for what to do when a BBCode tag is encountered.
     */
    public $default_tag_rules = [
        'b' => [
            'simple_start' => "<b>",
            'simple_end' => "</b>",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'plain_start' => "<b>",
            'plain_end' => "</b>",
            'allow_params' => false,
        ],
        'i' => [
            'simple_start' => "<i>",
            'simple_end' => "</i>",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'plain_start' => "<i>",
            'plain_end' => "</i>",
            'allow_params' => false,
        ],
        'u' => [
            'simple_start' => "<u>",
            'simple_end' => "</u>",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'plain_start' => "<u>",
            'plain_end' => "</u>",
            'allow_params' => false,
        ],
        's' => [
            'simple_start' => "<strike>",
            'simple_end' => "</strike>",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'plain_start' => "<i>",
            'plain_end' => "</i>",
            'allow_params' => false,
        ],
        'font' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'allow' => ['_default' => '/^[a-zA-Z0-9._ -]+$/'],
            'method' => 'doFont',
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ],
        'color' => [
            'mode' => BBCode::BBCODE_MODE_ENHANCED,
            'allow' => ['_default' => '/^#?[a-zA-Z0-9._ -]+$/'],
            'template' => '<span style="color:{$_default/tw}">{$_content/v}</span>',
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ],
        'size' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'allow' => ['_default' => '/^[0-9.]+$/D'],
            'method' => 'doSize',
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ],
        'sup' => [
            'simple_start' => "<sup>",
            'simple_end' => "</sup>",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'allow_params' => false,
        ],
        'sub' => [
            'simple_start' => "<sub>",
            'simple_end' => "</sub>",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'allow_params' => false,
        ],
        'spoiler' => [
            'simple_start' => "<span class=\"bbcode_spoiler\">",
            'simple_end' => "</span>",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ],
        'acronym' => [
            'mode' => BBCode::BBCODE_MODE_ENHANCED,
            'template' => '<span class="bbcode_acronym" title="{$_default/e}">{$_content/v}</span>',
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ],
        'url' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'method' => 'doURL',
            'class' => 'link',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
            'content' => BBCode::BBCODE_REQUIRED,
            'plain_start' => "<a href=\"{\$link}\">",
            'plain_end' => "</a>",
            'plain_content' => ['_content', '_default'],
            'plain_link' => ['_default', '_content'],
        ],
        'email' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'method' => 'doEmail',
            'class' => 'link',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
            'content' => BBCode::BBCODE_REQUIRED,
            'plain_start' => "<a href=\"mailto:{\$link}\">",
            'plain_end' => "</a>",
            'plain_content' => ['_content', '_default'],
            'plain_link' => ['_default', '_content'],
        ],
        'wiki' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'method' => "doWiki",
            'class' => 'link',
            'allow_in' => ['listitem', 'block', 'columns', 'inline'],
            'end_tag' => BBCode::BBCODE_PROHIBIT,
            'content' => BBCode::BBCODE_PROHIBIT,
            'plain_start' => "<b>[",
            'plain_end' => "]</b>",
            'plain_content' => ['title', '_default'],
            'plain_link' => ['_default', '_content'],
        ],
        'img' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'method' => "doImage",
            'class' => 'image',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'end_tag' => BBCode::BBCODE_REQUIRED,
            'content' => BBCode::BBCODE_OPTIONAL,
            'plain_start' => "[image]",
            'plain_content' => [],
        ],
        'rule' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'method' => "doRule",
            'class' => 'block',
            'allow_in' => ['listitem', 'block', 'columns'],
            'end_tag' => BBCode::BBCODE_PROHIBIT,
            'content' => BBCode::BBCODE_PROHIBIT,
            'before_tag' => "sns",
            'after_tag' => "sns",
            'plain_start' => "\n-----\n",
            'plain_end' => "",
            'plain_content' => [],
        ],
        'br' => [
            'mode' => BBCode::BBCODE_MODE_SIMPLE,
            'simple_start' => "<br>\n",
            'simple_end' => "",
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
            'end_tag' => BBCode::BBCODE_PROHIBIT,
            'content' => BBCode::BBCODE_PROHIBIT,
            'before_tag' => "s",
            'after_tag' => "s",
            'plain_start' => "\n",
            'plain_end' => "",
            'plain_content' => [],
        ],
        'left' => [
            'simple_start' => "\n<div class=\"bbcode_left\" style=\"text-align:left\">\n",
            'simple_end' => "\n</div>\n",
            'allow_in' => ['listitem', 'block', 'columns'],
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n",
            'plain_end' => "\n",
        ],
        'right' => [
            'simple_start' => "\n<div class=\"bbcode_right\" style=\"text-align:right\">\n",
            'simple_end' => "\n</div>\n",
            'allow_in' => ['listitem', 'block', 'columns'],
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n",
            'plain_end' => "\n",
        ],
        'center' => [
            'simple_start' => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n",
            'simple_end' => "\n</div>\n",
            'allow_in' => ['listitem', 'block', 'columns'],
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n",
            'plain_end' => "\n",
        ],
        'rtl' => [
            'simple_start' => '<div style="direction:rtl;">',
            'simple_end' => '</div>',
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ],
        'indent' => [
            'simple_start' => "\n<div class=\"bbcode_indent\" style=\"margin-left:4em\">\n",
            'simple_end' => "\n</div>\n",
            'allow_in' => ['listitem', 'block', 'columns'],
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n",
            'plain_end' => "\n",
        ],
        'columns' => [
            'simple_start' => "\n<table class=\"bbcode_columns\"><tbody><tr><td class=\"bbcode_column bbcode_firstcolumn\">\n",
            'simple_end' => "\n</td></tr></tbody></table>\n",
            'class' => 'columns',
            'allow_in' => ['listitem', 'block', 'columns'],
            'end_tag' => BBCode::BBCODE_REQUIRED,
            'content' => BBCode::BBCODE_REQUIRED,
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n",
            'plain_end' => "\n",
        ],
        'nextcol' => [
            'simple_start' => "\n</td><td class=\"bbcode_column\">\n",
            'class' => 'nextcol',
            'allow_in' => ['columns'],
            'end_tag' => BBCode::BBCODE_PROHIBIT,
            'content' => BBCode::BBCODE_PROHIBIT,
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n",
            'plain_end' => "",
        ],
        'code' => [
            'mode' => BBCode::BBCODE_MODE_ENHANCED,
            'template' => "\n<div class=\"bbcode_code\">\n<div class=\"bbcode_code_head\">Code:</div>\n<div class=\"bbcode_code_body\" style=\"white-space:pre\">{\$_content/v}</div>\n</div>\n",
            'class' => 'code',
            'allow_in' => ['listitem', 'block', 'columns'],
            'content' => BBCode::BBCODE_VERBATIM,
            'before_tag' => "sns",
            'after_tag' => "sn",
            'before_endtag' => "sn",
            'after_endtag' => "sns",
            'plain_start' => "\n<b>Code:</b>\n",
            'plain_end' => "\n",
        ],
        'quote' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'method' => "doQuote",
            'allow_in' => ['listitem', 'block', 'columns'],
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n<b>Quote:</b>\n",
            'plain_end' => "\n",
        ],
        'list' => [
            'mode' => BBCode::BBCODE_MODE_LIBRARY,
            'method' => 'doList',
            'class' => 'list',
            'allow_in' => ['listitem', 'block', 'columns'],
            'before_tag' => "sns",
            'after_tag' => "sns",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n",
            'plain_end' => "\n",
        ],
        '*' => [
            'simple_start' => "<li>",
            'simple_end' => "</li>\n",
            'class' => 'listitem',
            'allow_in' => ['list'],
            'end_tag' => BBCode::BBCODE_OPTIONAL,
            'before_tag' => "s",
            'after_tag' => "s",
            'before_endtag' => "sns",
            'after_endtag' => "sns",
            'plain_start' => "\n * ",
            'plain_end' => "\n",
        ],
    ];

    /**
     * @var array The file extensions that are considered valid local images.
     */
    protected $imageExtensions = ['gif', 'jpg', 'jpeg', 'png', 'svg'];

    /**
     * Format a [url] tag by producing an <a>...</a> element.
     *
     * The URL only allows http, https, mailto, and ftp protocols for safety.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string Returns the full HTML url.
     */
    public function doURL(BBCode $bbcode, $action, $name, $default, $params, $content) {
        // We can't check this with BBCODE_CHECK because we may have no URL
        // before the content has been processed.
        if ($action == BBCode::BBCODE_CHECK) {
            return true;
        }

        $url = is_string($default)
            ? $default
            : $bbcode->unHTMLEncode(strip_tags($content));

        if ($bbcode->isValidURL($url)) {
            if ($bbcode->debug) {
                Debugger::debug('ISVALIDURL');
            }

            if ($bbcode->getURLTargetable() !== false && isset($params['target'])) {
                $target = ' target="'.htmlspecialchars($params['target']).'"';
            } else {
                $target = '';
            }

            if ($bbcode->getURLTarget() !== false && empty($target)) {
                $target = ' target="'.htmlspecialchars($bbcode->getURLTarget()).'"';
            }

            // If $detect_urls is on, it's possble the $content is already
            // enclosed in an <a href> tag. Remove that if that is the case.
            $content = preg_replace('/^\\<a [^\\>]*\\>(.*?)<\\/a>$/', "\\1", $content);

            return $bbcode->fillTemplate($bbcode->getURLTemplate(), array("url" => $url, "target" => $target, "content" => $content));
        } else {
            return htmlspecialchars($params['_tag']).$content.htmlspecialchars($params['_endtag']);
        }
    }


    /**
     * Format an [email] tag by producing an <a href="mailto:...">...</a> element.
     *
     * The e-mail address must be a valid address including at least a '@' and a valid domain
     * name or IPv4 or IPv6 address after the '@'.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string Returns the email link HTML.
     */
    public function doEmail(BBCode $bbcode, $action, $name, $default, $params, $content) {
        // We can't check this with BBCODE_CHECK because we may have no URL
        // before the content has been processed.
        if ($action == BBCode::BBCODE_CHECK) {
            return true;
        }

        $email = is_string($default)
            ? $default
            : $bbcode->unHTMLEncode(strip_tags($content));

        if ($bbcode->isValidEmail($email)) {
            return $bbcode->fillTemplate($bbcode->getEmailTemplate(), array("email" => $email, "content" => $content));
        } else {
            return htmlspecialchars($params['_tag']).$content.htmlspecialchars($params['_endtag']);
        }
    }


    /**
     * Format a [size] tag by producing a <span> with a style with a different font-size.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string Returns a span with the font size CSS.
     */
    public function doSize(BBCode $bbcode, $action, $name, $default, $params, $content) {
        switch ($default) {
            case '0':
                $size = '.5em';
                break;
            case '1':
                $size = '.67em';
                break;
            case '2':
                $size = '.83em';
                break;
            case '3':
                $size = '1.0em';
                break;
            case '4':
                $size = '1.17em';
                break;
            case '5':
                $size = '1.5em';
                break;
            case '6':
                $size = '2.0em';
                break;
            case '7':
                $size = '2.5em';
                break;
            default:
                $size = (int)$default;
                if ($size < 11 || $size > 48) {
                    $size = '1.0em';
                } else {
                    $size .= 'px';
                }
                break;
        }
        return '<span style="font-size:'.$size.'">'.$content.'</span>';
    }

    /**
     * Format a [font] tag by producing a <span> with a style with a different font-family.
     *
     * This is complicated by the fact that we have to recognize the five special font
     * names and quote all the others.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string Returns a span with the font family CSS.
     */
    public function doFont(BBCode $bbcode, $action, $name, $default, $params, $content) {
        $fonts = explode(",", $default);
        $result = "";
        $special_fonts = [
            'serif' => 'serif',
            'sans-serif' => 'sans-serif',
            'sans serif' => 'sans-serif',
            'sansserif' => 'sans-serif',
            'sans' => 'sans-serif',
            'cursive' => 'cursive',
            'fantasy' => 'fantasy',
            'monospace' => 'monospace',
            'mono' => 'monospace',
        ];
        foreach ($fonts as $font) {
            $font = trim($font);
            if (isset($special_fonts[$font])) {
                if (strlen($result) > 0) {
                    $result .= ",";
                }
                $result .= $special_fonts[$font];
            } else if (strlen($font) > 0) {
                if (strlen($result) > 0) {
                    $result .= ",";
                }
                $result .= "'$font'";
            }
        }
        return "<span style=\"font-family:$result\">$content</span>";
    }


    /**
     * Format a [wiki] tag by producing an <a>...</a> element.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string Returns a link to the wiki.
     */
    public function doWiki(BBCode $bbcode, $action, $name, $default, $params, $content) {
        $name = $bbcode->wikify($default);

        if ($action == BBCode::BBCODE_CHECK) {
            return strlen($name) > 0;
        }

        if (isset($params['title']) && strlen(trim($params['title']))) {
            $title = trim($params['title']);
        } else {
            $title = trim($default);
        }

        $wikiURL = $bbcode->getWikiURL();
        return $bbcode->fillTemplate($bbcode->getWikiURLTemplate(), array("wikiURL" => $wikiURL, "name" => $name, "title" => $title));
    }


    /**
     * Format an [img] tag.  The URL only allows http, https, and ftp protocols for safety.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string Returns the image tag.
     */
    public function doImage(BBCode $bbcode, $action, $name, $default, $params, $content) {
        // We can't validate this until we have its content.
        if ($action == BBCode::BBCODE_CHECK) {
            return true;
        }

        $content = trim($bbcode->unHTMLEncode(strip_tags($content)));
        if (empty($content) && $default) {
            $content = $default;
        }

        $urlParts = parse_url($content);


        if (is_array($urlParts)) {
            if (!empty($urlParts['path']) &&
                empty($urlParts['scheme']) &&
                !preg_match('`^\.{0,2}/`', $urlParts['path']) &&
                in_array(pathinfo($urlParts['path'], PATHINFO_EXTENSION), $this->imageExtensions)
            ) {

                $localImgURL = $bbcode->getLocalImgURL();

                return "<img src=\""
                .htmlspecialchars((empty($localImgURL) ? '' : $localImgURL.'/').ltrim($urlParts['path'], '/')).'" alt="'
                .htmlspecialchars(basename($content)).'" class="bbcode_img" />';
            } elseif ($bbcode->isValidURL($content, false)) {
                // Remote URL, or at least we don't know where it is.
                return '<img src="'.htmlspecialchars($content).'" alt="'
                .htmlspecialchars(basename($content)).'" class="bbcode_img" />';
            }
        }

        return htmlspecialchars($params['_tag']).htmlspecialchars($content).htmlspecialchars($params['_endtag']);
    }

    /**
     * Format a [rule] tag.
     *
     * This substitutes the content provided by the BBCode object, whatever that may be.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return bool|string Returns the rule HTML or **true** if {@link $action} is **BBCode::BBCODE_CHECK**.
     */
    public function doRule(BBCode $bbcode, $action, $name, $default, $params, $content) {
        if ($action == BBCode::BBCODE_CHECK) {
            return true;
        } else {
            return $bbcode->getRuleHTML();
        }
    }

    /**
     * Format a [quote] tag.
     *
     * This tag can come in a variety of flavors:
     *
     * ```
     * [quote]...[/quote]
     * [quote=Tom]...[/quote]
     * [quote name="Tom"]...[/quote]
     * ```
     *
     * In the third form, you can also add a date="" parameter to display the date
     * on which Tom wrote it, and you can add a url="" parameter to turn the author's
     * name into a link.  A full example might be:
     *
     * ```
     * [quote name="Tom" date="July 4, 1776 3:48 PM" url="http://www.constitution.gov"]...[/quote]
     * ```
     *
     * The URL only allows http, https, mailto, gopher, ftp, and feed protocols for safety.
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return bool|string Returns the quote HTML or **true** if {@link $action} is **BBCode::BBCODE_CHECK**.
     */
    public function doQuote(BBCode $bbcode, $action, $name, $default, $params, $content) {
        if ($action == BBCode::BBCODE_CHECK) {
            return true;
        }

        if (isset($params['name'])) {
            $title = htmlspecialchars(trim($params['name']))." wrote";
            if (isset($params['date'])) {
                $title .= " on ".htmlspecialchars(trim($params['date']));
            }
            $title .= ":";
            if (isset($params['url'])) {
                $url = trim($params['url']);
                if ($bbcode->isValidURL($url)) {
                    $title = "<a href=\"".htmlspecialchars($params['url'])."\">".$title."</a>";
                }
            }
        } elseif (!is_string($default)) {
            $title = "Quote:";
        } else {
            $title = htmlspecialchars(trim($default))." wrote:";
        }

        return $bbcode->fillTemplate($bbcode->getQuoteTemplate(), array("title" => $title, "content" => $content));
    }

    /**
     * Format a [list] tag.
     *
     * This  is complicated by the number of different
     * ways a list can be started.  The following parameters are allowed:
     *
     * ```
     * [list]           Unordered list, using default marker
     * [list=circle]    Unordered list, using circle marker
     * [list=disc]      Unordered list, using disc marker
     * [list=square]    Unordered list, using square marker
     *
     * [list=1]         Ordered list, numeric, starting at 1
     * [list=A]         Ordered list, capital letters, starting at A
     * [list=a]         Ordered list, lowercase letters, starting at a
     * [list=I]         Ordered list, capital Roman numerals, starting at I
     * [list=i]         Ordered list, lowercase Roman numerals, starting at i
     * [list=greek]     Ordered list, lowercase Greek letters, starting at alpha
     * [list=01]        Ordered list, two-digit numeric with 0-padding, starting at 01
     * ```
     *
     * @param BBCode $bbcode The {@link BBCode} object doing the parsing.
     * @param int $action The current action being performed on the tag.
     * @param string $name The name of the tag.
     * @param string $default The default value passed to the tag in the form: `[tag=default]`.
     * @param array $params All of the parameters passed to the tag.
     * @param string $content The content of the tag. Only available when {@link $action} is **BBCODE_OUTPUT**.
     * @return string|bool Returns the list HTML or a boolen result when {@link $action} is **BBCode::BBCODE_CHECK**.
     */
    public function doList(BBCode $bbcode, $action, $name, $default, $params, $content) {

        // Allowed list styles, striaght from the CSS 2.1 spec.  The only prohibited
        // list style is that with image-based markers, which often slows down web sites.
        $listStyles = [
            '1' => 'decimal',
            '01' => 'decimal-leading-zero',
            'i' => 'lower-roman',
            'I' => 'upper-roman',
            'a' => 'lower-alpha',
            'A' => 'upper-alpha',
        ];
        $ciListStyles = [
            'circle' => 'circle',
            'disc' => 'disc',
            'square' => 'square',
            'greek' => 'lower-greek',
            'armenian' => 'armenian',
            'georgian' => 'georgian',
        ];
        $ulTypes = [
            'circle' => 'circle',
            'disc' => 'disc',
            'square' => 'square',
        ];

        $default = trim($default);

        if ($action == BBCode::BBCODE_CHECK) {
            if (!is_string($default) || strlen($default) == "") {
                return true;
            } elseif (isset($listStyles[$default])) {
                return true;
            } elseif (isset($ciListStyles[strtolower($default)])) {
                return true;
            } else {
                return false;
            }
        }

        // Choose a list element (<ul> or <ol>) and a style.
        if (!is_string($default) || strlen($default) == "") {
            $elem = 'ul';
            $type = '';
        } elseif ($default == '1') {
            $elem = 'ol';
            $type = '';
        } elseif (isset($listStyles[$default])) {
            $elem = 'ol';
            $type = $listStyles[$default];
        } else {
            $default = strtolower($default);
            if (isset($ulTypes[$default])) {
                $elem = 'ul';
                $type = $ulTypes[$default];
            } elseif (isset($ciListStyles[$default])) {
                $elem = 'ol';
                $type = $ciListStyles[$default];
            }
        }

        // Generate the HTML for it.
        if (strlen($type)) {
            return "\n<$elem class=\"bbcode_list\" style=\"list-style-type:$type\">\n$content</$elem>\n";
        } else {
            return "\n<$elem class=\"bbcode_list\">\n$content</$elem>\n";
        }
    }
}
