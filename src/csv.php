<?php

declare(strict_types=1);

class BasicRum_Import_Csv
{

    public function read($path, $linesCount = false)
    {
        $file = fopen($path, "r");

        $lines = [];

        if ($linesCount === false) {
            while (!feof($file) ) {
                $lines[] = fgetcsv($file, null, "\t");
            }
        } else {
            $counter = 1;
            while (!feof($file) && $linesCount >= $counter) {
                $lines[] = fgetcsv($file, null, "\t");
                $counter++;
            }
        }

        fclose($file);

        return $lines;
    }

}