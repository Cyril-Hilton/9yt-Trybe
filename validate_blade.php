<?php
$content = file_get_contents('resources/views/welcome.blade.php');
$lines = explode("\n", $content);

$stack = [];
$directives = [
    'if' => 'endif',
    'auth' => 'endauth',
    'guest' => 'endguest',
    'foreach' => 'endforeach',
    'for' => 'endfor',
    'while' => 'endwhile',
    'isset' => 'endisset',
    'empty' => 'endempty',
    'section' => 'endsection',
    'php' => 'endphp',
    'can' => 'endcan',
    'cannot' => 'endcannot',
    'switch' => 'endswitch',
    'error' => 'enderror',
];

$opened = array_keys($directives);
$closed = array_values($directives);

foreach ($lines as $i => $line) {
    // Process the line for escaped @@
    $line = str_replace('@@', 'IGNORE', $line);
    
    // Find all matches of @tag
    if (preg_match_all('/@(\w+)/', $line, $matches)) {
        foreach ($matches[1] as $d) {
            if (in_array($d, $opened)) {
                $stack[] = ['type' => $d, 'line' => $i + 1, 'content' => $line];
                // Handle @elseif and @else inside @if
            } elseif (in_array($d, $closed)) {
                if (empty($stack)) {
                    echo "Extra @$d at line ".($i+1)."\n";
                    continue;
                }
                $last = array_pop($stack);
                $expected = $directives[$last['type']];
                if ($expected !== $d) {
                    echo "Mismatched closing tag at line ".($i+1).": expected $expected for @$last[type] (from line $last[line]), found @$d\n";
                    // Push back to try to find other matches
                    $stack[] = $last;
                }
            }
        }
    }
}

foreach ($stack as $entry) {
    echo "Unclosed @$entry[type] at line $entry[line]: $entry[content]\n";
}
