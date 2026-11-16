<?php

namespace Module\Vendor\Markdown;

/**
 * Markdown 转 HTML（自研实现，不依赖 league/commonmark、webuni 表格扩展）
 *
 * 支持：标题、粗体/斜体/删除线、行内代码、代码块、链接、图片、引用、
 *      有序/无序列表、表格、分割线、段落与换行。
 */
class Markdown
{
    /**
     * @param string $text
     * @return string
     */
    public static function toHtml($text)
    {
        $text = str_replace(["\r\n", "\r"], "\n", strval($text));
        $codeBlocks = [];
        // 提取代码块（避免内部内容被解析）
        $text = preg_replace_callback('/^```([a-zA-Z0-9_+-]*)\n(.*?)\n```$/ms', function ($m) use (&$codeBlocks) {
            $lang = trim($m[1]);
            $code = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
            $class = '' === $lang ? '' : ' class="language-' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '"';
            $key = "\x02CB" . count($codeBlocks) . "\x03";
            $codeBlocks[$key] = '<pre><code' . $class . '>' . $code . '</code></pre>';
            return $key;
        }, $text);

        $lines = explode("\n", $text);
        $html = [];
        $i = 0;
        $count = count($lines);
        while ($i < $count) {
            $line = $lines[$i];
            $trim = trim($line);
            if ('' === $trim) {
                $i++;
                continue;
            }
            // 代码块占位
            if (isset($codeBlocks[$trim])) {
                $html[] = $codeBlocks[$trim];
                $i++;
                continue;
            }
            // 分割线
            if (preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trim)) {
                $html[] = '<hr />';
                $i++;
                continue;
            }
            // 标题
            if (preg_match('/^(#{1,6})\s+(.*)$/', $trim, $m)) {
                $level = strlen($m[1]);
                $html[] = '<h' . $level . '>' . self::inline(trim($m[2])) . '</h' . $level . '>';
                $i++;
                continue;
            }
            // 表格
            if (false !== strpos($trim, '|') && $i + 1 < $count && preg_match('/^\|?\s*:?-{2,}:?\s*(\|\s*:?-{2,}:?\s*)*\|?$/', trim($lines[$i + 1]))) {
                $rows = [];
                $rows[] = self::tableCells($trim);
                $i += 2;
                while ($i < $count && false !== strpos($lines[$i], '|') && '' !== trim($lines[$i])) {
                    $rows[] = self::tableCells($lines[$i]);
                    $i++;
                }
                $tableHtml = '<table class="ub-table"><thead><tr>';
                foreach ($rows[0] as $cell) {
                    $tableHtml .= '<th>' . self::inline($cell) . '</th>';
                }
                $tableHtml .= '</tr></thead><tbody>';
                for ($r = 1; $r < count($rows); $r++) {
                    $tableHtml .= '<tr>';
                    foreach ($rows[0] as $index => $_) {
                        $cell = isset($rows[$r][$index]) ? $rows[$r][$index] : '';
                        $tableHtml .= '<td>' . self::inline($cell) . '</td>';
                    }
                    $tableHtml .= '</tr>';
                }
                $tableHtml .= '</tbody></table>';
                $html[] = $tableHtml;
                continue;
            }
            // 引用
            if (preg_match('/^>\s?(.*)$/', $trim, $m)) {
                $quotes = [];
                while ($i < $count && preg_match('/^>\s?(.*)$/', trim($lines[$i]), $mm)) {
                    $quotes[] = self::inline($mm[1]);
                    $i++;
                }
                $html[] = '<blockquote>' . implode('<br />', $quotes) . '</blockquote>';
                continue;
            }
            // 有序列表
            if (preg_match('/^\d+\.\s+/', $trim)) {
                $items = [];
                while ($i < $count && preg_match('/^\d+\.\s+(.*)$/', trim($lines[$i]), $mm)) {
                    $items[] = '<li>' . self::inline($mm[1]) . '</li>';
                    $i++;
                }
                $html[] = '<ol>' . implode('', $items) . '</ol>';
                continue;
            }
            // 无序列表
            if (preg_match('/^[-*+]\s+/', $trim)) {
                $items = [];
                while ($i < $count && preg_match('/^[-*+]\s+(.*)$/', trim($lines[$i]), $mm)) {
                    $items[] = '<li>' . self::inline($mm[1]) . '</li>';
                    $i++;
                }
                $html[] = '<ul>' . implode('', $items) . '</ul>';
                continue;
            }
            // 段落（连续非空行，软换行转 <br />）
            $paragraph = [];
            while ($i < $count && '' !== trim($lines[$i])
                && !preg_match('/^(#{1,6})\s+|^>\s?|^\d+\.\s+|^[-*+]\s+|^(-{3,}|\*{3,}|_{3,})$/', trim($lines[$i]))
                && !isset($codeBlocks[trim($lines[$i])])) {
                $paragraph[] = self::inline(trim($lines[$i]));
                $i++;
            }
            if (!empty($paragraph)) {
                $html[] = '<p>' . implode('<br />', $paragraph) . '</p>';
            } else {
                $i++;
            }
        }
        $result = implode("\n", $html);
        if (!empty($codeBlocks)) {
            $result = strtr($result, $codeBlocks);
        }
        return $result;
    }

    /**
     * 拆分表格单元格
     * @param string $line
     * @return array
     */
    private static function tableCells($line)
    {
        $line = trim($line);
        $line = trim($line, '|');
        $cells = preg_split('/\s*\|\s*/', $line);
        return array_map('trim', (array)$cells);
    }

    /**
     * 行内元素解析
     * @param string $text
     * @return string
     */
    private static function inline($text)
    {
        $text = strval($text);
        // 行内代码优先保护
        $codes = [];
        $text = preg_replace_callback('/`([^`]+)`/', function ($m) use (&$codes) {
            $key = "\x02IC" . count($codes) . "\x03";
            $codes[$key] = '<code>' . htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8') . '</code>';
            return $key;
        }, $text);
        // 图片整体保护：alt 与地址不参与后续行内解析，避免地址中的 _ * ~ 被当成强调标记
        $text = preg_replace_callback('/!\[([^\]]*)\]\(([^\)\s]+)(?:\s+(?:&quot;[^&]*&quot;|"[^"]*"|\'[^\']*\'))?\)/', function ($m) use (&$codes) {
            $key = "\x02IM" . count($codes) . "\x03";
            $codes[$key] = '<img src="' . $m[2] . '" alt="' . $m[1] . '" />';
            return $key;
        }, $text);
        // 链接仅保护目标地址（链接文字仍参与行内解析）
        $urls = [];
        $text = preg_replace_callback('/\[([^\]]+)\]\(([^\)\s]+)\)/', function ($m) use (&$urls) {
            $key = "\x02IU" . count($urls) . "\x03";
            $urls[$key] = $m[2];
            return '[' . $m[1] . '](' . $key . ')';
        }, $text);
        $text = preg_replace('/\[([^\]]+)\]\(([^\)\s]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);
        // 粗体 / 斜体 / 删除线（_ 按 CommonMark 规则不在词内生效，如 file_name_1.png）
        $text = preg_replace('/\*\*([^\*]+)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/(?<![\w_])__([^_\n]+)__(?![\w_])/', '<strong>$1</strong>', $text);
        $text = preg_replace('/(?<!\*)\*([^\*\n]+)\*(?!\*)/', '<em>$1</em>', $text);
        $text = preg_replace('/(?<![\w_])_([^_\n]+)_(?![\w_])/', '<em>$1</em>', $text);
        $text = preg_replace('/~~([^~]+)~~/', '<del>$1</del>', $text);
        if (!empty($urls)) {
            $text = strtr($text, $urls);
        }
        if (!empty($codes)) {
            $text = strtr($text, $codes);
        }
        return $text;
    }
}
