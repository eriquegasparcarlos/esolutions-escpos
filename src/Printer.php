<?php

namespace ESolutions\Escpos;

use ESolutions\Escpos\Exceptions\PrinterException;

class Printer
{
    private string  $printer    = '';
    private int     $paperWidth = 48;
    private bool    $cut        = true;
    private bool    $openDrawer = false;
    private string  $baseUrl    = 'http://localhost:8443';

    private function __construct() {}

    // ─── Factory ─────────────────────────────────────────────────────────────

    public static function create(): static
    {
        return new static();
    }

    // ─── Configuración ───────────────────────────────────────────────────────

    public function to(string $printerName): static
    {
        $this->printer = $printerName;
        return $this;
    }

    public function paperWidth(int $chars): static
    {
        $this->paperWidth = $chars;
        return $this;
    }

    public function paper58mm(): static
    {
        return $this->paperWidth(32);
    }

    public function paper80mm(): static
    {
        return $this->paperWidth(48);
    }

    public function withCut(bool $cut = true): static
    {
        $this->cut = $cut;
        return $this;
    }

    public function withDrawer(bool $open = true): static
    {
        $this->openDrawer = $open;
        return $this;
    }

    public function url(string $baseUrl): static
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    // ─── Envío ───────────────────────────────────────────────────────────────

    /**
     * Envía el documento a ApiPeruDevPrint via REST.
     *
     * @throws PrinterException
     */
    public function send(Document $document): void
    {
        $payload = json_encode([
            'printer'    => $this->printer,
            'paperWidth' => $this->paperWidth,
            'cut'        => $this->cut,
            'openDrawer' => $this->openDrawer,
            'commands'   => $document->toArray(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $ch = curl_init($this->baseUrl . '/api/print/commands');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            // Ignorar certificado autofirmado de ApiPeruDevPrint
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new PrinterException("No se pudo conectar con ApiPeruDevPrint: {$curlError}");
        }

        if ($httpCode !== 200) {
            throw new PrinterException("ApiPeruDevPrint respondió HTTP {$httpCode}: {$response}");
        }

        $result = json_decode($response, true);
        if (! ($result['success'] ?? false)) {
            throw new PrinterException("Error de impresión: " . ($result['error'] ?? 'desconocido'));
        }
    }

    // ─── Helper estático ─────────────────────────────────────────────────────

    /**
     * Obtiene la lista de impresoras disponibles en ApiPeruDevPrint.
     *
     * @return string[]
     */
    public static function availablePrinters(string $baseUrl = 'http://localhost:8443'): array
    {
        $ch = curl_init(rtrim($baseUrl, '/') . '/api/printers');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['printers'] ?? [];
    }
}
