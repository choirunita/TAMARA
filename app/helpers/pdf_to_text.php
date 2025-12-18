<?php
function pdf_to_text($pdfPath) {
    $poppler = "C:\\poppler\\Library\\bin\\pdftotext.exe";

    if (!file_exists($poppler)) {
        return null;
    }

    // output text file
    $txtFile = $pdfPath . ".txt";
    $cmd = "\"$poppler\" -layout \"$pdfPath\" \"$txtFile\"";
    shell_exec($cmd);

    if (file_exists($txtFile)) {
        return file_get_contents($txtFile);
    }
    return null;
}
