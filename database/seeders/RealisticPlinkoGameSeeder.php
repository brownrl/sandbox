<?php

namespace Database\Seeders;

use App\Models\PlinkoGame;
use Illuminate\Database\Seeder;

class RealisticPlinkoGameSeeder extends Seeder
{
    // Physics constants (matching frontend for realism)
    const ROWS = 12;
    const COLS = 9;
    const PEG_RADIUS = 5;
    const CHIP_RADIUS = 10;
    const GRAVITY = 0.6;
    const BOUNCE = 0.6;
    const HORIZONTAL_BOUNCE = 0.8;
    const CANVAS_WIDTH = 600;
    const CANVAS_HEIGHT = 700;

    // Slot prizes (from center outward)
    const SLOT_PRIZES = [100, 500, 1000, 0, 10000, 0, 1000, 500, 100];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pegs = $this->generatePegPositions();

        for ($i = 0; $i < 1000; $i++) {
            $this->simulateGame($pegs);
        }
    }

    private function generatePegPositions(): array
    {
        $pegs = [];
        $pegSpacingX = self::CANVAS_WIDTH / self::COLS;
        $pegSpacingY = (self::CANVAS_HEIGHT - 150) / (self::ROWS + 1);

        for ($row = 0; $row < self::ROWS; $row++) {
            $isEvenRow = $row % 2 === 0;
            $pegsInRow = $isEvenRow ? self::COLS : self::COLS - 1;
            
            $totalPegsWidth = ($pegsInRow - 1) * $pegSpacingX;
            $rowOffset = (self::CANVAS_WIDTH - $totalPegsWidth) / 2;

            for ($col = 0; $col < $pegsInRow; $col++) {
                $pegs[] = [
                    'x' => $rowOffset + $pegSpacingX * $col,
                    'y' => 100 + $pegSpacingY * $row,
                ];
            }
        }

        return $pegs;
    }

    private function simulateGame(array $pegs): void
    {
        $drop_x = mt_rand(self::CHIP_RADIUS, self::CANVAS_WIDTH - self::CHIP_RADIUS);
        $chip = [
            'x' => $drop_x,
            'y' => 40,
            'vx' => (mt_rand() / mt_getrandmax() - 0.5) * 3,
            'vy' => 0,
            'radius' => self::CHIP_RADIUS,
            'path' => [],
            'start_time' => microtime(true) * 1000,
            'peg_collisions' => 0,
        ];

        $animationFrames = 0;
        while ($chip['y'] < self::CANVAS_HEIGHT - 100) {
            $animationFrames++;
            $chip['path'][] = ['x' => $chip['x'], 'y' => $chip['y']];

            $chip['vy'] += self::GRAVITY;
            $chip['x'] += $chip['vx'];
            $chip['y'] += $chip['vy'];

            foreach ($pegs as $peg) {
                $dx = $chip['x'] - $peg['x'];
                $dy = $chip['y'] - $peg['y'];
                $distance = sqrt($dx * $dx + $dy * $dy);

                if ($distance < $chip['radius'] + self::PEG_RADIUS) {
                    $chip['peg_collisions']++;

                    $angle = atan2($dy, $dx);
                    $speed = sqrt($chip['vx'] * $chip['vx'] + $chip['vy'] * $chip['vy']);
                    
                    $chip['vx'] = cos($angle) * $speed * self::BOUNCE + (mt_rand() / mt_getrandmax() - 0.5) * self::HORIZONTAL_BOUNCE;
                    $chip['vy'] = sin($angle) * $speed * self::BOUNCE;

                    $overlap = $chip['radius'] + self::PEG_RADIUS - $distance;
                    $chip['x'] += cos($angle) * $overlap;
                    $chip['y'] += sin($angle) * $overlap;
                }
            }

            if ($chip['x'] - $chip['radius'] < 0) {
                $chip['x'] = $chip['radius'];
                $chip['vx'] = -$chip['vx'] * self::BOUNCE;
            }
            if ($chip['x'] + $chip['radius'] > self::CANVAS_WIDTH) {
                $chip['x'] = self::CANVAS_WIDTH - $chip['radius'];
                $chip['vx'] = -$chip['vx'] * self::BOUNCE;
            }
        }

        $final_x = $chip['x'];
        $slotSpacing = self::CANVAS_WIDTH / self::COLS;
        $finalSlot = floor($final_x / $slotSpacing);
        $clampedSlot = max(0, min(self::COLS - 1, $finalSlot));
        $score = self::SLOT_PRIZES[$clampedSlot];
        $fall_time_ms = (microtime(true) * 1000) - $chip['start_time'];
        $horizontal_distance = abs($final_x - $drop_x);

        PlinkoGame::create([
            'user_id' => null, // Seed as guest user
            'score' => $score,
            'final_slot' => $clampedSlot,
            'drop_x' => $drop_x,
            'final_x' => $final_x,
            'horizontal_distance' => $horizontal_distance,
            'path' => $chip['path'],
            'fall_time_ms' => $fall_time_ms,
            'peg_collisions' => $chip['peg_collisions'],
            'created_at' => now()->subMinutes(mt_rand(1, 60 * 24 * 30)), // Random time in last month
            'updated_at' => now(),
        ]);
    }
}