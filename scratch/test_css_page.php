<?php
require 'c:/Projects/Laravel/zayin/vendor/autoload.php';

function testCssPage($css) {
    $html = "<!DOCTYPE html><html><head><style>$css</style></head><body>Hello</body></html>";
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $canvas = $dompdf->getCanvas();
    echo "CSS: $css\n";
    echo "Width: " . $canvas->get_width() . ", Height: " . $canvas->get_height() . "\n";
    echo "MediaBox: " . ($canvas->get_width() * 25.4 / 72) . "mm x " . ($canvas->get_height() * 25.4 / 72) . "mm\n\n";
}

testCssPage("@page { size: 148mm 210mm; }");
testCssPage("@page { size: a5 portrait; }");
testCssPage("@page { size: a4 portrait; }");
testCssPage("@page { size: 420pt 595pt; }");
testCssPage("");

