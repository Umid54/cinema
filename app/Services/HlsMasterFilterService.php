// app/Services/HlsMasterFilterService.php

namespace App\Services;

use App\Models\User;

class HlsMasterFilterService
{
    public static function filter(string $content, ?User $user): string
    {
        $maxHeight = match (true) {
            !$user                      => 360,
            $user->account_status === 'FREE'   => 360,
            $user->account_status === 'TRIAL'  => 720,
            $user->account_status === 'PREMIUM'=> 1080,
            default => 360,
        };

        $lines = explode("\n", $content);
        $result = [];

        for ($i = 0; $i < count($lines); $i++) {
            $line = trim($lines[$i]);

            if (str_starts_with($line, '#EXT-X-STREAM-INF')) {
                preg_match('/RESOLUTION=\d+x(\d+)/', $line, $m);
                $height = (int)($m[1] ?? 0);

                if ($height > $maxHeight) {
                    $i++; // пропускаем URI
                    continue;
                }

                $result[] = $line;
                $result[] = $lines[++$i] ?? '';
                continue;
            }

            $result[] = $line;
        }

        return implode("\n", $result);
    }
}
