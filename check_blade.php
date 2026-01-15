<?php
$content = file_get_contents($argv[1]);
$lines = explode("\n", $content);
$stack = [];
$directives = [
    '@if' => '@endif',
    '@auth' => '@endauth',
    '@guest' => '@endguest',
    '@foreach' => '@endforeach',
    '@for' => '@endfor',
    '@while' => '@endwhile',
    '@unless' => '@endunless',
    '@section' => '@endsection',
    '@push' => '@endpush',
    '@can' => '@endcan',
    '@php' => '@endphp',
];

$reverse = array_flip($directives);

foreach ($lines as $i => $line) {
    if (preg_match_all('/(@[a-z]+)(\s*\(.*\))?/', $line, $matches)) {
        foreach ($matches[1] as $d) {
            if (isset($directives[$d])) {
                $stack[] = ['type' => $d, 'line' => $i + 1];
            } elseif (isset($reverse[$d])) {
                if (empty($stack)) {
                    echo "Unexpected $d on line " . ($i + 1) . "\n";
                } else {
                    $last = array_pop($stack);
                    if ($directives[$last['type']] !== $d) {
                        echo "Mismatched $d on line " . ($i + 1) . ". Expected match for " . $last['type'] . " on line " . $last['line'] . "\n";
                    }
                }
            }
        }
    }
}

foreach ($stack as $s) {
    echo "Unclosed " . $s['type'] . " on line " . $s['line'] . "\n";
}
