<?php

namespace App\Services;

class DocumentProcessorService
{
    protected int $chunkSize = 3000;

    public function extract(string $filePath, string $fileType): string
    {
        return match(strtolower($fileType)) {
            'pdf'  => $this->extractFromPdf($filePath),
            'docx' => $this->extractFromDocx($filePath),
            'txt'  => file_get_contents($filePath),
            default => throw new \Exception("Formato de ficheiro não suportado: {$fileType}"),
        };
    }

    public function chunk(string $text): array
    {
        $text   = preg_replace('/\s+/', ' ', trim($text));
        $chunks = [];
        $words  = explode(' ', $text);
        $current = '';

        foreach ($words as $word) {
            if (strlen($current) + strlen($word) + 1 > $this->chunkSize) {
                if ($current) {
                    $chunks[] = trim($current);
                }
                $current = $word;
            } else {
                $current .= ($current ? ' ' : '') . $word;
            }
        }

        if ($current) {
            $chunks[] = trim($current);
        }

        return $chunks;
    }

    public function extractAndChunk(string $filePath, string $fileType): array
    {
        $text = $this->extract($filePath, $fileType);
        return $this->chunk($text);
    }

    private function extractFromPdf(string $path): string
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($path);
        return $pdf->getText();
    }

    private function extractFromDocx(string $path): string
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
        $text    = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
                if (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $text .= $child->getText() . ' ';
                        }
                    }
                }
            }
        }

        return $text;
    }
}
