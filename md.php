<?php
/*
 * md.php - A MediaWiki extension which adds Markdown syntax.
 * @author Michael McCouman jr.
 * @version 1.5
 * @copyright Copyright (C) 2013 C3ltaX Media
 * @license The MIT License - http://www.opensource.org/licenses/mit-license.php 
 * @addtogroup Extensions
 * -----------------------------------------------------------------------
 * Description:
 *     This is a MediaWiki (http://www.mediawiki.org/) extension which adds support
 *     for Markdown syntax.
 * Installation:
 *     1. Create a folder in your $IP/extensions directory called Markdown.
 *         Note: $IP is your MediaWiki install dir.
 *         You have something like this: $IP/extensions/Markdown/
 *     2. Download Michel Fortin's PHP Markdown, unzip and look for the file markdown.php.
 *         Note: Don't download PHP Markdown Extra. Only PHP Markdown is supported. PHP Markdown Extra may be supported in a future release
 *     3. Drop markdown.php into $IP/extensions/Markdown/
 *     4. Download MarkdownSyntax.php and drop it into $IP/extensions/Markdown/ also.
 *     5. Enable the extension by adding this line to your LocalSettings.php:
 *            require_once( "{$IP}/extensions/Markdown.php" );
 * Usage:
 *     See http://daringfireball.net/projects/markdown/syntax
 * Version Notes:
 *     version 1.5:
 *         Switched to ParserBeforeStrip hook.
 *         Hacked html links produced by markdown.php into mediawiki links.
 *     version 0.1:
 *         Initial release.
 * -----------------------------------------------------------------------
 * Copyright (c) 2013 C3ltaX Media
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights to 
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of 
 * the Software, and to permit persons to whom the Software is furnished to do 
 * so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES 
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
 * OTHER DEALINGS IN THE SOFTWARE. 
 * -----------------------------------------------------------------------
 */
 
# Confirm MediaWiki environment
if (!defined('MEDIAWIKI')) die();
 
# Credits
$wgExtensionCredits['other'][] = array(
    'name'=>'MarkdownSyntax',
    'author'=>'Michael McCouman jr.',
    'url'=>'[[Extension:Markdown]]',
    'description'=>'markdown syntax editoring.',
    'version'=>'1.5'
);
 
# Attach Hook
$wgHooks['ParserBeforeStrip'][] = 'wfProcessMarkdownSyntax';
 
/**
 * Processes any Markdown sytnax in the text.
 * Usage: $wgHooks['ParserBeforeStrip'][] = 'wfProcessMarkdownSyntax';
 * @param Parser $parser Handle to the Parser object currently processing text.
 * @param String $text The text being processed.
 */
 
# includes Michel Fortin's PHP Markdown: http://www.michelf.com/projects/php-markdown/
require_once("markdown.php");
#require_once( dirname( __FILE__ ) . '/markdown.php' );

function wfProcessMarkdownSyntax($parser, $text) {
 
    # Perform Markdown syntax processing on provided $text from markdown.php line 43 function
        $text = Markdown($text);
        // <a href="http://example.com/" title="Title">an example</a>
 
        /* After running the text through this parser, mediawiki converts <a> tags to &lt...
                So, here we convert such links to mediawiki format links so they will be properly rendered. */
 
        /*
                pattern: <a href="(.+?)"( title="(.+?)")?>(.+?)</a>
                escaped quotes and slash: <a href=\"(.+?)\"( title=\"(.+?)\")?>(.+?)<\/a>
                regexed and quoted: "/<a href=\"(.+?)\"( title=\"(.+?)\")?>(.+?)<\/a>/i"
                split php end tag: "/<a href=\"(.+?)\"( title=\"(.+?)\")?".">(.+?)<\/a>/i"
        */
        $pattern = "/<a href=\"(.+?)\"( title=\"(.+?)\")?".">(.+?)<\/a>/i";
        $replacement = '[$1 $4]';
 
        $text = preg_replace($pattern, $replacement, $text);
        return true;
}
