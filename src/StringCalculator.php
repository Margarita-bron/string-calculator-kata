<?php

declare(strict_types=1);

class StringCalculator
{
  public function add(string $numbersStr): int
  {
    if ($numbersStr === '') {
      return 0;
    }

    $numbersStr = str_replace('\n', "\n", $numbersStr);
    $delimiters = [",", "\n"];

    if (str_starts_with($numbersStr, '//')) {
      $newlinePos = strpos($numbersStr, "\n");
      $delimiterPart = substr($numbersStr, 2, $newlinePos - 2);
      $numbersStr = substr($numbersStr, $newlinePos + 1);

      if (!str_starts_with($delimiterPart, '[')) {
        $delimiters[] = $delimiterPart;
      } else {
        $i = 0;
        $len = strlen($delimiterPart);
        while ($i < $len) {
          if ($delimiterPart[$i] === '[') {
            $i++;
            $level = 1;
            $current = '';
            while ($i < $len && $level > 0) {
              $ch = $delimiterPart[$i];
              if ($ch === '[') {
                $level++;
              } elseif ($ch === ']') {
                $level--;
                if ($level === 0) {
                  $i++;
                  break;
                }
              }
              if ($level > 0) {
                $current .= $ch;
              }
              $i++;
            }
            $delimiters[] = $current;
            continue;
          }
          $i++;
        }
      }
    }

    $escaped = array_map(fn($d) => preg_quote($d, '/'), $delimiters);
    $pattern = '/(' . implode('|', $escaped) . ')/';
    $numbers = preg_split($pattern, $numbersStr);
    $numbers = array_filter($numbers, fn($n) => trim($n) !== '');

    $negatives = [];
    $sum = 0;

    foreach ($numbers as $numStr) {
      $num = (int) trim($numStr);

      if ($num < 0) {
        $negatives[] = $num;
      }
      if ($num <= 1000) {
        $sum += $num;
      }
    }

    if (!empty($negatives)) {
      throw new InvalidArgumentException(
        'Negatives not allowed: ' . implode(', ', $negatives)
      );
    }

    return $sum;
  }
}
