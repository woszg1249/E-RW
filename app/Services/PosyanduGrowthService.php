<?php

namespace App\Services;

class PosyanduGrowthService
{
    public const STANDARDS = [
        'Laki-laki' => [
            1  => ['lk' => [35.1, 39.5], 'bb' => [3.4, 5.8],  'tb' => [50.8, 58.6]],
            2  => ['lk' => [36.8, 41.5], 'bb' => [4.3, 7.1],  'tb' => [54.4, 62.4]],
            3  => ['lk' => [38.1, 42.9], 'bb' => [5.0, 8.0],  'tb' => [57.3, 65.5]],
            4  => ['lk' => [39.2, 44.0], 'bb' => [5.6, 8.7],  'tb' => [59.7, 68.0]],
            5  => ['lk' => [40.1, 45.0], 'bb' => [6.0, 9.3],  'tb' => [61.7, 70.1]],
            6  => ['lk' => [40.9, 45.8], 'bb' => [6.4, 9.8],  'tb' => [63.3, 71.9]],
            7  => ['lk' => [41.5, 46.6], 'bb' => [6.7, 10.3], 'tb' => [64.8, 73.5]],
            8  => ['lk' => [42.0, 47.2], 'bb' => [6.9, 10.7], 'tb' => [66.2, 75.0]],
            9  => ['lk' => [42.5, 47.7], 'bb' => [7.1, 11.0], 'tb' => [67.5, 76.5]],
            10 => ['lk' => [42.9, 48.2], 'bb' => [7.4, 11.4], 'tb' => [68.7, 77.9]],
            11 => ['lk' => [43.2, 48.6], 'bb' => [7.6, 11.7], 'tb' => [69.9, 79.2]],
            12 => ['lk' => [43.5, 49.0], 'bb' => [7.7, 12.0], 'tb' => [71.0, 80.5]],
        ],
        'Perempuan' => [
            1  => ['lk' => [34.2, 38.9], 'bb' => [3.2, 5.5],  'tb' => [49.8, 57.6]],
            2  => ['lk' => [35.8, 40.7], 'bb' => [3.9, 6.6],  'tb' => [53.0, 61.1]],
            3  => ['lk' => [37.1, 42.0], 'bb' => [4.5, 7.5],  'tb' => [55.6, 64.0]],
            4  => ['lk' => [38.1, 43.1], 'bb' => [5.0, 8.2],  'tb' => [57.8, 66.4]],
            5  => ['lk' => [38.9, 44.0], 'bb' => [5.4, 8.8],  'tb' => [59.6, 68.5]],
            6  => ['lk' => [39.6, 44.8], 'bb' => [5.7, 9.3],  'tb' => [61.2, 70.3]],
            7  => ['lk' => [40.2, 45.5], 'bb' => [6.0, 9.8],  'tb' => [62.7, 71.9]],
            8  => ['lk' => [40.7, 46.0], 'bb' => [6.3, 10.2], 'tb' => [64.0, 73.5]],
            9  => ['lk' => [41.2, 46.5], 'bb' => [6.5, 10.5], 'tb' => [65.3, 75.0]],
            10 => ['lk' => [41.5, 46.9], 'bb' => [6.7, 10.9], 'tb' => [66.5, 76.4]],
            11 => ['lk' => [41.9, 47.3], 'bb' => [6.9, 11.2], 'tb' => [67.7, 77.8]],
            12 => ['lk' => [42.2, 47.6], 'bb' => [7.0, 11.5], 'tb' => [68.9, 79.2]],
        ],
    ];

    /**
     * Hitung status kesehatan dan indikator tumbuh kembang balita secara otomatis
     */
    public static function evaluate(string $gender, int $usiaBulan, float $bb, ?float $tb = null, ?float $lk = null): array
    {
        $gender = ($gender === 'Perempuan') ? 'Perempuan' : 'Laki-laki';
        $standards = self::STANDARDS[$gender];

        // Jika usia di luar 1-12 bulan, sesuaikan batas terdekat untuk referensi
        $usiaClamped = max(1, min(12, $usiaBulan));
        $std = $standards[$usiaClamped] ?? null;

        $issues = [];
        $statusBb = 'Normal';
        $statusTb = 'Normal';
        $statusLk = 'Normal';

        if ($std) {
            // Evaluasi Berat Badan (BB)
            if ($bb < $std['bb'][0]) {
                $statusBb = 'Kurang';
                $issues[] = 'Berat badan di bawah normal (< ' . $std['bb'][0] . ' kg)';
            } elseif ($bb > $std['bb'][1]) {
                $statusBb = 'Berlebih';
                $issues[] = 'Berat badan di atas normal (> ' . $std['bb'][1] . ' kg)';
            }

            // Evaluasi Tinggi/Panjang Badan (TB)
            if ($tb !== null && $tb > 0) {
                if ($tb < $std['tb'][0]) {
                    $statusTb = 'Pendek';
                    $issues[] = 'Panjang badan di bawah normal (< ' . $std['tb'][0] . ' cm)';
                } elseif ($tb > $std['tb'][1]) {
                    $statusTb = 'Tinggi';
                }
            }

            // Evaluasi Lingkar Kepala (LK)
            if ($lk !== null && $lk > 0) {
                if ($lk < $std['lk'][0]) {
                    $statusLk = 'Kecil';
                    $issues[] = 'Lingkar kepala di bawah normal (< ' . $std['lk'][0] . ' cm)';
                } elseif ($lk > $std['lk'][1]) {
                    $statusLk = 'Besar';
                    $issues[] = 'Lingkar kepala di atas normal (> ' . $std['lk'][1] . ' cm)';
                }
            }
        }

        // Tentukan ringkasan status kesehatan
        if (empty($issues)) {
            $statusFinal = 'Gizi Baik / Normal (Sehat)';
            $badgeClass = 'bg-success bg-opacity-10 text-success';
        } elseif ($statusBb === 'Kurang' && $statusTb === 'Pendek') {
            $statusFinal = 'Gizi Kurang & Risiko Stunting';
            $badgeClass = 'bg-danger bg-opacity-10 text-danger';
        } elseif ($statusBb === 'Kurang') {
            $statusFinal = 'Gizi Kurang (BB Rendah)';
            $badgeClass = 'bg-warning bg-opacity-10 text-warning';
        } elseif ($statusTb === 'Pendek') {
            $statusFinal = 'Panjang Kurang (Pantau TB)';
            $badgeClass = 'bg-warning bg-opacity-10 text-warning';
        } elseif ($statusBb === 'Berlebih') {
            $statusFinal = 'Berat Berlebih (Risiko Overweight)';
            $badgeClass = 'bg-info bg-opacity-10 text-info';
        } elseif ($statusLk !== 'Normal') {
            $statusFinal = 'Pantau Lingkar Kepala (' . $statusLk . ')';
            $badgeClass = 'bg-warning bg-opacity-10 text-warning';
        } else {
            $statusFinal = 'Perlu Perhatian / Konsultasi KIA';
            $badgeClass = 'bg-warning bg-opacity-10 text-warning';
        }

        return [
            'status' => $statusFinal,
            'badgeClass' => $badgeClass,
            'statusBb' => $statusBb,
            'statusTb' => $statusTb,
            'statusLk' => $statusLk,
            'issues' => $issues,
            'standard' => $std,
        ];
    }
}
