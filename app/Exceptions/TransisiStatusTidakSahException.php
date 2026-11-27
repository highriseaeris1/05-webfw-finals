<?php

namespace App\Exceptions;

use App\Enums\StatusPermintaan;
use RuntimeException;

class TransisiStatusTidakSahException extends RuntimeException
{
    public static function untuk(string $kodeResi, StatusPermintaan $asal, StatusPermintaan $tujuan): self
    {
        return new self(
            "Permintaan {$kodeResi} berstatus {$asal->label()} tidak dapat diubah menjadi {$tujuan->label()}."
        );
    }
}
